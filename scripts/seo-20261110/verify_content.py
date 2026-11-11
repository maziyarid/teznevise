#!/usr/bin/env python3
"""Repository and server-rendered public-copy gates. Production is report-only."""
import argparse
from concurrent.futures import ThreadPoolExecutor
from datetime import datetime, timezone
import gettext
from html import unescape
from html.parser import HTMLParser
import json
from pathlib import Path
import re
import subprocess
import struct
import sys
from urllib.error import HTTPError
from urllib.parse import urljoin, urlsplit
from urllib.request import Request, build_opener, HTTPRedirectHandler
import xml.etree.ElementTree as ET

ROOT = Path(__file__).resolve().parents[2]
NOTES = ROOT / 'seo/20261110/content'
# Only historical documentation and this PR's evidence/inputs are exempt.
ALLOWLIST = ('CHANGELOG.md', 'docs/')
PR_NOTES = 'seo/20261110/content/'
WORD = re.compile(r'\u0645\u0634\u0627\u0648\u0631\w*|\bconsul' + r't\w*|\u0645\u0648\u0633\u0633\u0647', re.I)
TYPO = re.compile(r'[\u200c\u064a\u0643\u0660-\u0669]|\u0633\u0624\u0627\u0644')
CLAIM = re.compile(r'\u0636\u0645\u0627\u0646\w*|\u062a\u0636\u0645\u06cc\u0646\w*|\u0628\u0647\u062a\u0631\u06cc\u0646|\u06f1\u06f0\u06f0(?:\u066a|\s+\u062f\u0631\u0635\u062f)|\u0631\u062a\u0628\u0647\s+\u06cc\u06a9|\u0633\u0631\u06cc\u0639\s+\u062a\u0631\u06cc\u0646|\u0627\u0631\u0632\u0627\u0646|\u062a\u062e\u0635\u0635\u06cc\s+\u062a\u0631\u06cc\u0646|(?:[0-9\u06f0-\u06f9]+(?:[.,\u066b\u066c][0-9\u06f0-\u06f9]+)*|\u0686\u0646\u062f|\u06cc\u06a9|\u062f\u0648|\u0633\u0647)\s*(?:\u062a\u0648\u0645\u0627\u0646|\u0631\u06cc\u0627\u0644|\u0633\u0627\u0639\u062a|\u0631\u0648\u0632)')
REQUIRED = ['/', '/thesis/', '/thesis/phd/', '/proposal/', '/proposal/phd/', '/service-project/', '/thesis/humanities/psychology/', '/thesis/humanities/management/', '/contact-us/', '/inquiry/', '/about-us/', '/gams/']
VOID = {'area','base','br','col','embed','hr','img','input','link','meta','param','source','track','wbr'}


def findings(text, location, lexicon=True):
    rows = []
    for label, pattern in [('typography', TYPO), ('claim', CLAIM)] + ([('lexicon', WORD)] if lexicon else []):
        for match in pattern.finditer(text):
            rows.append({'location': location, 'rule': label, 'match': match.group(), 'offset': match.start()})
    return rows


def leaves(value, pointer='$'):
    if isinstance(value, dict):
        for key, child in value.items():
            yield pointer + '.key', str(key)
            yield from leaves(child, pointer + '.' + str(key))
    elif isinstance(value, list):
        for index, child in enumerate(value):
            yield from leaves(child, pointer + f'[{index}]')
    elif isinstance(value, str):
        yield pointer, value


class Rendered(HTMLParser):
    def __init__(self, url):
        super().__init__(convert_charrefs=True)
        self.url, self.frames, self.elements, self.errors = url, [], [], []
        self.schema_sources, self.schemas, self.visible_faq = set(), [], []
        self.title = ''
        self.main_depth = 0
        self.main_anchors = []
    def record(self, kind, value):
        self.elements.append({'element': kind, 'text': value})
        for error in findings(value, self.url+' '+kind):
            error['element_index'] = len(self.elements)-1
            self.errors.append(error)
    def handle_starttag(self, tag, attrs):
        attrs = dict(attrs)
        if tag == 'main': self.main_depth += 1
        for name in ('title','aria-label','placeholder','alt'):
            if name in attrs: self.record(f'{tag}[{name}]', attrs[name])
        if tag == 'input' and 'value' in attrs: self.record('input[value]', attrs['value'])
        if tag == 'meta':
            name = attrs.get('name',attrs.get('property','')).lower()
            if name == 'description' or name.startswith(('og:','twitter:')):
                self.record('meta['+name+']', attrs.get('content',''))
        capture = tag in ('a','button','label','option','title','h1') or attrs.get('role') == 'menuitem' or any('breadcrumb' in name or name in ('menu-title','menu-item-title') for name in attrs.get('class','').split()) or attrs.get('itemprop')=='name'
        ld = tag == 'script' and attrs.get('type','').split(';',1)[0].strip().lower() == 'application/ld+json'
        if capture or ld:
            marker = ' '.join([attrs.get('class',''),attrs.get('id','')]).lower()
            owner = 'rank_math' if 'rank-math' in marker else 'yoast' if 'yoast' in marker else 'hand_written'
            self.frames.append({'tag':tag,'parts':[],'ld':ld,'owner':owner,'in_main':self.main_depth>0,'href':attrs.get('href','')})
    def handle_data(self, data):
        for frame in self.frames: frame['parts'].append(data)
    def handle_endtag(self, tag):
        if tag == 'main': self.main_depth = max(0,self.main_depth-1)
        for i in range(len(self.frames)-1,-1,-1):
            if self.frames[i]['tag'] != tag: continue
            frame = self.frames.pop(i)
            text = ''.join(frame['parts'])
            if frame['ld']:
                self.schema_sources.add(frame['owner'])
                try:
                    schema = json.loads(unescape(text))
                    self.schemas.append(schema)
                    for pointer,value in leaves(schema): self.record('JSON-LD'+pointer,value)
                except ValueError:
                    self.errors.append({'location':self.url+' JSON-LD','rule':'invalid-json','match':'unparseable JSON'})
            else:
                text=' '.join(text.split())
                self.record(tag,text)
                if tag=='title': self.title=text
                if tag=='a' and frame['in_main']: self.main_anchors.append({'text':text,'target':frame['href']})
            break
    def finish(self):
        if len(self.schema_sources)>1:
            self.errors.append({'location':self.url,'rule':'mixed-schema-sources','match':', '.join(sorted(self.schema_sources))})
        money={'/thesis/':'انجام پایان نامه','/thesis/master/':'انجام پایان نامه کارشناسی ارشد','/thesis/phd/':'انجام پایان نامه دکتری','/proposal/':'انجام پروپوزال','/proposal/phd/':'انجام پروپوزال دکتری','/service-project/':'انجام پروژه های دانشجویی'}
        exact=[a for a in self.main_anchors if a['text'] in money.values()]
        if len(exact)>1:
            self.errors.append({'location':self.url+' main','rule':'exact-anchor-budget','match':str(len(exact))})
        # Optional FAQ schema must be an exact subset of visible Q/A text.
        rendered_text=' '.join(e['text'] for e in self.elements if not e['element'].startswith('JSON-LD'))
        for schema in self.schemas:
            self.check_faq(schema, rendered_text)
            self.check_ratings(schema)
    def check_ratings(self, obj):
        if isinstance(obj,dict):
            types=obj.get('@type',[])
            types=[types] if isinstance(types,str) else types
            if set(types).intersection({'Review','Rating','AggregateRating'}) or any(k in obj for k in ('aggregateRating','ratingValue','review')):
                self.errors.append({'location':self.url,'rule':'prohibited-rating-schema','match':'review/rating payload'})
            for value in obj.values():self.check_ratings(value)
        elif isinstance(obj,list):
            for value in obj:self.check_ratings(value)

    def check_faq(self, obj, visible):
        if isinstance(obj,dict):
            if obj.get('@type')=='FAQPage':
                for question in obj.get('mainEntity',[]):
                    answer=question.get('acceptedAnswer',{}).get('text','')
                    for text in (question.get('name',''),answer):
                        # Visible answers are collected separately by the all-text parser below.
                        normalized=' '.join(unescape(re.sub('<[^>]+>','',text)).split())
                        if not normalized or normalized not in self.all_visible:
                            self.errors.append({'location':self.url,'rule':'faq-schema-mismatch','match':normalized})
            for value in obj.values(): self.check_faq(value,visible)
        elif isinstance(obj,list):
            for value in obj: self.check_faq(value,visible)


class Visible(HTMLParser):
    def __init__(self): super().__init__(convert_charrefs=True); self.skip=0; self.parts=[]
    def handle_starttag(self,t,a):
        if t in ('script','style','template'): self.skip+=1
        if not self.skip and t in ('p','div','section','h1','h2','h3','li','br','td','th','button'):self.parts.append(' ')
    def handle_endtag(self,t):
        if t in ('script','style','template'): self.skip=max(0,self.skip-1)
        if not self.skip and t in ('p','div','section','h1','h2','h3','li','td','th','button'):self.parts.append(' ')
    def handle_data(self,s):
        if not self.skip: self.parts.append(s)


def rendered(url,html):
    visible=Visible();visible.feed(html)
    parser=Rendered(url);parser.all_visible=' '.join(''.join(visible.parts).split())
    parser.feed(html);parser.finish()
    return parser


def repo_scan():
    paths=subprocess.check_output(['git','ls-files','--cached','--others','--exclude-standard','-z'],cwd=ROOT).decode().split('\0')
    errors=[]; scanned=0
    for rel in sorted(set(paths)):
        if not rel or any(rel==p or rel.startswith(p) for p in ALLOWLIST): continue
        if rel.startswith(PR_NOTES) and not rel.startswith(PR_NOTES+'pages/'): continue
        # Credentials/configurations are never opened by this public-copy checker.
        if Path(rel).name=='wp-config.php' or Path(rel).name.startswith('.env'): continue
        path=ROOT/rel
        if not path.is_file(): continue
        try:
            if path.suffix=='.mo':
                with path.open('rb') as stream: catalog=gettext.GNUTranslations(stream)._catalog
                values=[(str(k),v) for k,v in catalog.items() if isinstance(v,str)] + [('msgid',k) for k in catalog if isinstance(k,str)]
            else:
                raw=path.read_bytes()
                if b'\0' in raw and path.suffix not in ('.po','.pot'): continue
                text=raw.decode('utf-8')
                if path.suffix=='.json': values=list(leaves(json.loads(text)))
                else: values=[('text',text)]
            scanned+=1
            for pointer,text in values:
                for error in findings(text,rel+' '+pointer):
                    error['line']=text.count('\n',0,error.get('offset',0))+1
                    errors.append(error)
        except UnicodeDecodeError as exc:
            if path.suffix in ('.po','.pot','.mo'):
                errors.append({'location':rel,'rule':'decode-error','match':str(exc)})
            continue
        except (ValueError,OSError,EOFError,struct.error) as exc:
            errors.append({'location':rel,'rule':'decode-error','match':str(exc)})
    return {'files_scanned':scanned,'allowlist':list(ALLOWLIST)+[PR_NOTES+' notes/inputs only; pages/** never exempt'],'errors':errors}


class NoRedirect(HTTPRedirectHandler):
    def redirect_request(self,*args,**kwargs): return None


def fetch(url):
    try:
        with build_opener(NoRedirect()).open(Request(url,headers={'User-Agent':'Teznevise-Wave0-readonly/1.0'}),timeout=25) as response:
            return response.status,response.read().decode('utf-8',errors='replace'),None
    except HTTPError as exc: return exc.code,'',str(exc)
    except Exception as exc: return 0,'',str(exc)


def sitemap_urls(base):
    pending=[urljoin(base,'/sitemap_index.xml'),urljoin(base,'/wp-sitemap.xml')]
    seen=set();urls=set();errors=[];sources=[]
    while pending:
        url=pending.pop()
        if url in seen: continue
        seen.add(url)
        status,body,error=fetch(url)
        sources.append({'url':url,'http_status':status,'error':error})
        if status!=200: continue
        try: tree=ET.fromstring(body)
        except ET.ParseError: continue
        locs=[n.text for entry in tree for n in entry if n.tag.split('}')[-1]=='loc' and n.text]
        if tree.tag.split('}')[-1]=='sitemapindex':
            pending.extend(u for u in locs if urlsplit(u).netloc==urlsplit(base).netloc)
        else: urls.update(u for u in locs if urlsplit(u).netloc==urlsplit(base).netloc)
    if not urls: errors.append({'location':base,'rule':'sitemap-unavailable','match':'no same-site page URLs obtained'})
    return urls,errors,sources


def legal_gate(manifest):
    pages=manifest.get('pages',[])
    missing=[p.get('path','UNKNOWN') for p in pages if p.get('legal_review',{}).get('status')!='done' or p.get('legal_review',{}).get('reviewed_by')!='Maziyar' or not p.get('legal_review',{}).get('approval_reference') or not p.get('legal_review',{}).get('reviewed_at')]
    return [{'location':p,'rule':'legal-review-not-done','match':'not publishable'} for p in missing] or ([] if pages else [{'location':'manifest','rule':'missing-pages','match':'no review records'}])


def main():
    ap=argparse.ArgumentParser()
    ap.add_argument('--tier',choices=['repo','local','production','publish'],required=True)
    ap.add_argument('--base-url')
    ap.add_argument('--output',type=Path)
    ap.add_argument('--crawl-sitemap',action='store_true')
    args=ap.parse_args()
    report={'tier':args.tier,'checked_at_utc':datetime.now(timezone.utc).isoformat(),'blocking':args.tier!='production','errors':[]}
    if args.tier=='repo': report.update(repo_scan())
    elif args.tier=='publish':
        report['errors']=legal_gate(json.loads((NOTES/'manifest.json').read_text()))
    else:
        if not args.base_url:
            ap.error('--base-url is required for rendered tiers')
        urls={urljoin(args.base_url,p) for p in REQUIRED};sources=[]
        if args.crawl_sitemap:
            found,errs,sources=sitemap_urls(args.base_url);urls.update(found);report['errors'].extend(errs)
        report['sitemaps']=sources;report['pages']=[]
        with ThreadPoolExecutor(max_workers=8) as pool:
            for url,result in zip(sorted(urls),pool.map(fetch,sorted(urls))):
                status,body,error=result
                page={'url':url,'http_status':status,'fetch_error':error,'errors':[]}
                if status==200:
                    parsed=rendered(url,body);page['title']=parsed.title;page['schema_sources']=sorted(parsed.schema_sources);page['errors']=parsed.errors
                    indexes={e['element_index'] for e in parsed.errors if 'element_index' in e}
                    page['offending_elements']=[{'element_index':i,**e} for i,e in enumerate(parsed.elements) if i in indexes]
                    page['main_anchors']=parsed.main_anchors
                else: page['errors']=[{'location':url,'rule':'http','match':str(status)}]
                report['pages'].append(page);report['errors'].extend(page['errors'])
                print(f'{status} {url} offenders={len(page["errors"])}',file=sys.stderr,flush=True)
    report['run_status']=('not run' if report.get('pages') and all(p['http_status']==0 for p in report['pages']) else 'partial' if any(p['http_status']==0 for p in report.get('pages',[])) else 'ran')
    report['passed']=not report['errors'];report['error_count']=len(report['errors'])
    if args.output:
        args.output.parent.mkdir(parents=True,exist_ok=True);args.output.write_text(json.dumps(report,ensure_ascii=False,indent=2)+'\n')
    print(json.dumps(report,ensure_ascii=False,indent=2))
    return int(report['blocking'] and not report['passed'])

if __name__=='__main__': sys.exit(main())
