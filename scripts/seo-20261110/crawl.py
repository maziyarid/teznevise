#!/usr/bin/env python3
"""Read-only same-origin sitemap + anchor crawler; Python stdlib + lxml.
No credentials, writes or WordPress actions. Audit ignores robots for status checks.
URL query combinations are visited exactly; only fragments are removed.
"""
import argparse, concurrent.futures, csv, gzip, hashlib, json, re, time
from collections import defaultdict
from datetime import datetime, timezone
from pathlib import Path
from urllib.parse import urlsplit, urlunsplit, urljoin, quote, unquote
from urllib.request import Request, build_opener, HTTPRedirectHandler
from urllib.error import HTTPError, URLError
from lxml import html, etree

ROOT = Path(__file__).resolve().parents[2]
OUT = ROOT / 'seo/20261110/audit'
CACHE = ROOT / 'work/seo-20261110/crawl-cache'
ORIGIN = 'https://teznevise.ir'
AGENT = 'Teznevise-Owner-SEO-Audit/20261110 (read-only; github.com/maziyarid/teznevise)'
HEADERS = {'User-Agent': AGENT, 'Accept': 'text/html,application/xml,text/xml,*/*;q=0.5'}


def clean(s): return str(s).replace('\u200c', ' ')
def norm(url, base=ORIGIN+'/'):
    p=urlsplit(urljoin(base,url.strip()))
    if p.scheme not in ('http','https') or p.hostname not in ('teznevise.ir','www.teznevise.ir'): return None
    path=quote(unquote(p.path or '/'),safe='/%:@!$&\'()*+,;=-._~')
    return urlunsplit((p.scheme,p.netloc.lower(),path,p.query,''))

class Redirects(HTTPRedirectHandler):
    def __init__(self): self.chain=[]
    def redirect_request(self,req,fp,code,msg,headers,newurl):
        self.chain.append({'url':req.full_url,'status':code,'target':newurl})
        return super().redirect_request(req,fp,code,msg,headers,newurl)

def fetch(url):
    started=time.monotonic(); red=Redirects(); opener=build_opener(red)
    row={'url':url,'status':None,'final_url':url,'redirects':[],'content_type':'','error':'','title':'','h1':[],'words':0,'article_words':0,'wp_id':None,'canonical':'','robots_meta':'','links':[],'assets':[],'sitemap_locs':[],'faq_count':0}
    try:
        try: r=opener.open(Request(url,headers=HEADERS),timeout=25)
        except HTTPError as e: r=e
        row.update(status=r.status,final_url=r.url,redirects=red.chain,content_type=r.headers.get('Content-Type',''))
        data=r.read(8_000_001)
        if len(data)>8_000_000: row['error']='body truncated above 8 MB'
        is_html='html' in row['content_type']; is_xml=any(t in row['content_type'].split(';')[0] for t in ('application/xml','text/xml')) or urlsplit(url).path.endswith('.xml')
        if is_html or is_xml or url.endswith('/robots.txt'):
            (CACHE/(hashlib.sha256(url.encode()).hexdigest()+'.body')).write_bytes(data)
        if is_xml:
            try:
                tree=etree.fromstring(data)
                row['sitemap_locs']=[str(x) for x in tree.xpath('//*[local-name()="loc"]/text()')]
            except Exception as e: row['error']='XML parse: '+str(e)
        if 'text/css' in row['content_type']:
            row['assets']=[urljoin(r.url,m[1]) for m in re.findall(r"url\(\s*(['\"]?)([^)\s'\"]+)\1\s*\)",data.decode('utf-8','replace')) if not m[1].startswith('data:')]
        if is_html:
            doc=html.fromstring(data,base_url=r.url)
            row['title']=clean(' '.join(doc.xpath('//title/text()')).strip())
            row['h1']=[clean(' '.join(x.itertext()).strip()) for x in doc.xpath('//h1')]
            bodyclass=' '.join(doc.xpath('//body/@class'))
            pid=re.search(r'(?:postid|page-id)-(\d+)',bodyclass)
            row['wp_id']=int(pid.group(1)) if pid else None
            row['canonical']=' '.join(doc.xpath('//link[@rel="canonical"]/@href'))
            row['robots_meta']=' '.join(doc.xpath('//meta[translate(@name,"ROBOTS","robots")="robots"]/@content'))
            for a in doc.xpath('//a[@href]'):
                href=urljoin(r.url,a.get('href')).split('#')[0]
                if href.startswith(('http://','https://')):
                    row['links'].append({'target':norm(href) or href,'anchor':clean(' '.join(a.itertext()).strip()),'rel':a.get('rel','')})
            row['assets']=list(set(urljoin(r.url,x) for x in doc.xpath('//img/@src | //script/@src | //link[@rel="stylesheet" or @rel="preload" or @rel="icon"]/@href | //source/@src | //video/@poster | //iframe/@src | //audio/@src | //video/@src')))
            for srcset in doc.xpath('//img/@srcset | //source/@srcset'):
                row['assets'].extend(urljoin(r.url,piece.strip().split()[0]) for piece in srcset.split(',') if piece.strip() and not piece.strip().startswith('data:'))
            row['assets']=sorted(set(row['assets']))
            row['faq_count']=len(re.findall(r'"@type"\s*:\s*"FAQPage"',data.decode('utf-8','replace')))
            for x in doc.xpath('//script|//style|//nav|//header|//footer|//noscript'): x.drop_tree()
            body=doc.find('body')
            if body is None: body=doc
            row['words']=len(' '.join(body.itertext()).split())
            articles=doc.xpath('//article[contains(@class,"article") or contains(@class,"post")]|//main//article|//*[contains(concat(" ", normalize-space(@class), " "), " longcopy ")]')
            if not articles: articles=doc.xpath('//main')
            # Largest content surface avoids counting nested wrappers twice.
            row['article_words']=max([len(' '.join(x.itertext()).split()) for x in articles] or [row['words']])
    except Exception as e: row['error']=type(e).__name__+': '+str(e)
    row['elapsed_s']=round(time.monotonic()-started,3)
    return row

def dump(rows,edges,seeds,started):
    OUT.mkdir(parents=True,exist_ok=True)
    meta={'started_utc':started,'updated_utc':datetime.now(timezone.utc).isoformat(),'user_agent':AGENT,'normalization':'Fragments removed; percent encoding normalized; U+200C replaced with spaces in extracted text evidence only. Raw bodies are uncommitted cache.'}
    # Full resumable discovery state stays out of the reviewable public evidence.
    state={**meta,'rows':sorted(rows.values(),key=lambda x:x['url'])}
    (ROOT/'work/seo-20261110/crawl-state.json').write_text(json.dumps(state,ensure_ascii=False))
    public=[]
    for row in state['rows']:
        r={k:v for k,v in row.items() if k not in ('links','assets')}
        r['initial_status']=row['redirects'][0]['status'] if row['redirects'] else row['status']
        r['link_count']=len(row['links']); r['asset_reference_count']=len(row['assets'])
        public.append(r)
    (OUT/'crawl.json').write_text(json.dumps({**meta,'rows':public},ensure_ascii=False,indent=2)+'\n')
    with gzip.open(OUT/'crawl-edges.csv.gz','wt',encoding='utf-8',newline='') as f:
        w=csv.DictWriter(f,fieldnames=['source_url','target_url','anchor','rel'],lineterminator='\n');w.writeheader();w.writerows(edges)
    with gzip.open(OUT/'external-edges.csv.gz','wt',encoding='utf-8',newline='') as f:
        w=csv.DictWriter(f,fieldnames=['source_url','target_url','anchor','rel'],lineterminator='\n');w.writeheader()
        for row in rows.values():
            for e in row['links']:
                if not norm(e['target']):w.writerow({'source_url':row['url'],'target_url':clean(e['target']),'anchor':e['anchor'],'rel':e['rel']})
    with gzip.open(OUT/'resource-edges.csv.gz','wt',encoding='utf-8',newline='') as f:
        w=csv.writer(f);w.writerow(['source_url','target_url'])
        for row in rows.values():
            for asset in sorted(set(row['assets'])):
                n=norm(asset)
                if n:w.writerow([row['url'],n])
    (OUT/'sitemap-urls.json').write_text(json.dumps(seeds,ensure_ascii=False,indent=2)+'\n')

def main():
    p=argparse.ArgumentParser();p.add_argument('--workers',type=int,default=8);p.add_argument('--resume',action='store_true'); args=p.parse_args()
    OUT.mkdir(parents=True,exist_ok=True);CACHE.mkdir(parents=True,exist_ok=True)
    started=datetime.now(timezone.utc).isoformat();rows={};edges=[];seeds={}
    if args.resume and (ROOT/'work/seo-20261110/crawl-state.json').exists():
        old=json.loads((ROOT/'work/seo-20261110/crawl-state.json').read_text());rows={r['url']:r for r in old['rows']};started=old['started_utc']
        if (OUT/'sitemap-urls.json').exists():seeds=json.loads((OUT/'sitemap-urls.json').read_text())
    # Explicit requested money + old aliases supplement discovery.
    todo={ORIGIN+p for p in ['/','/robots.txt','/sitemap_index.xml','/wp-sitemap.xml','/service-thesis/','/service-proposal/','/thesis/','/thesis/phd/','/proposal/','/proposal/phd/','/service-project/','/thesis/master/','/student-projects/','/order/']}
    for r in rows.values():
        for loc in r.get('sitemap_locs',[]):
            n=norm(loc)
            if n: todo.add(n)
        for asset in r.get('assets',[]):
            n=norm(asset)
            if n: todo.add(n)
        for e in r.get('links',[]):
            n=norm(e['target'])
            if n:todo.add(n)
    post_seed=ROOT/'work/seo-20261110/wp-posts.json'
    if post_seed.exists():
        for post in json.loads(post_seed.read_text()):
            n=norm(post['link'])
            if n: todo.add(n)
    todo-=rows.keys();batch=0
    with concurrent.futures.ThreadPoolExecutor(max_workers=args.workers) as pool:
        while todo:
            batch+=1; pending=sorted(todo);todo=set()
            print(f'batch {batch}: fetching {len(pending)}, completed {len(rows)}',flush=True)
            for row in pool.map(fetch,pending):
                rows[row['url']]=row
                for loc in row.get('sitemap_locs',[]):
                    n=norm(loc)
                    if n:
                        seeds.setdefault(n,[])
                        if row['url'] not in seeds[n]:seeds[n].append(row['url'])
                        if n not in rows and n not in pending:todo.add(n)
                for asset in row.get('assets',[]):
                    n=norm(asset)
                    if n and n not in rows and n not in pending:todo.add(n)
                for e in row['links']:
                    n=norm(e['target'])
                    if n and n not in rows and n not in pending:todo.add(n)
            edges=[]
            for r in rows.values():
                for e in r['links']:
                    n=norm(e['target'])
                    if n:edges.append({'source_url':r['url'],'target_url':n,'anchor':e['anchor'],'rel':e['rel']})
            dump(rows,edges,seeds,started)
    edges=[{'source_url':r['url'],'target_url':norm(e['target']),'anchor':e['anchor'],'rel':e['rel']} for r in rows.values() for e in r['links'] if norm(e['target'])]
    dump(rows,edges,seeds,started)
    print(f'complete: {len(rows)} URLs, {len(edges)} edges',flush=True)

if __name__=='__main__':main()
