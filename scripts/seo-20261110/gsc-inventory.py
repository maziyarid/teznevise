#!/usr/bin/env python3
"""Read-only live inventory of exact Search Console URL samples.

Never expands '*' samples, writes WordPress, or changes existing crawl evidence.
Every seed row is reported; identical HTTP request URLs share one fetch.
Python 3 + lxml. Raw/cache artifacts remain under ignored work/seo-20261110/gsc/.
"""
import argparse
import concurrent.futures
import csv
import gzip
import hashlib
import json
import re
import time
from collections import defaultdict
from datetime import datetime, timezone
from pathlib import Path
from urllib.error import HTTPError
from urllib.parse import quote, urljoin, urlsplit, urlunsplit
from urllib.request import HTTPRedirectHandler, Request, build_opener
from lxml import html

ROOT=Path(__file__).resolve().parents[2]
AUDIT=ROOT/'seo/20261110/audit'
WORK=ROOT/'work/seo-20261110/gsc'
ORIGIN='https://teznevise.ir'
AGENT='Teznevise-Owner-GSC-Inventory/20261110 (read-only; github.com/maziyarid/teznevise)'
LIMIT=4_000_000
FIELDS=['row_id','source_bucket','original_input_json','request_url','normalization_notes','initial_status','terminal_status','final_url','redirect_chain_json','canonical_url','link_canonical_header','robots_meta','x_robots_tag','robots_allowed','robots_matched_rule','robots_rule_line','robots_user_agent','content_type','title','inbound_link_count','inbound_source_count','inbound_sources_json','in_sitemap','fetch_error','fetched_utc','body_truncated','body_sha256','seed_metadata_json']


def encoded_url(value):
    """Encode invalid URI characters, retaining scheme/path/query and literal '*'."""
    value=str(value).strip().replace(r'\*','*').replace(r'\_','_')
    if value.startswith('//'):
        # GSC //statistics/ is this origin's double-slash path, not another host.
        value=ORIGIN+value
    elif value.startswith('/'):
        value=ORIGIN+value
    elif re.match(r'^(?:www\.)?teznevise\.ir(?:[/?#]|$)',value,re.I):
        value='https://'+value
    elif not re.match(r'^https?://',value,re.I):
        value=ORIGIN+'/'+value
    p=urlsplit(value)
    if p.scheme.lower() not in ('http','https') or not p.netloc:
        raise ValueError('Expected a concrete HTTP(S) URL')
    if p.username or p.password:
        raise ValueError('Credential-bearing URLs are not fetched')
    path=quote(re.sub(r'%(?![0-9A-Fa-f]{2})','%25',p.path or '/'),safe="/%:@!$&'()*+,;=-._~")
    query=quote(re.sub(r'%(?![0-9A-Fa-f]{2})','%25',p.query),safe="%=&?/:@!$'()*+,;=-._~")
    return urlunsplit((p.scheme.lower(),p.netloc.encode('idna').decode('ascii'),path,query,''))


def input_rows(value,bucket=''):
    if isinstance(value,list):
        for row in value:
            yield from input_rows(row,bucket)
    elif isinstance(value,str):
        yield {'url':value,'source_bucket':bucket}
    elif isinstance(value,dict):
        if any(k in value for k in ('url','input_url','original_url','old_url','request_url')):
            yield {**value,'source_bucket':value.get('source_bucket',value.get('bucket',value.get('category',value.get('report',bucket))))}
        else:
            for k,v in value.items():
                if isinstance(v,(list,dict)):
                    yield from input_rows(v,bucket if k in ('rows','seeds','urls','items') else k)


class Redirects(HTTPRedirectHandler):
    def __init__(self):
        self.chain=[]
    def redirect_request(self,req,fp,code,msg,headers,newurl):
        destination=encoded_url(newurl)
        self.chain.append({'url':encoded_url(req.full_url),'status':code,'target':destination,'location':headers.get('Location',''),'x_robots_tag':headers.get('X-Robots-Tag','')})
        return super().redirect_request(req,fp,code,msg,headers,destination)


def get(url):
    key=hashlib.sha256(url.encode()).hexdigest()
    row={'request_url':url,'initial_status':'','terminal_status':'','final_url':url,'redirect_chain':[],'canonical_url':'','link_canonical_header':'','robots_meta':'','x_robots_tag':'','content_type':'','title':'','fetch_error':'','fetched_utc':datetime.now(timezone.utc).isoformat(),'body_truncated':False,'body_sha256':'','retry_after':''}
    redirect=Redirects()
    try:
        try:
            response=build_opener(redirect).open(Request(url,headers={'User-Agent':AGENT,'Accept':'text/html,application/xml,text/plain,*/*;q=0.5'}),timeout=25)
        except HTTPError as error:
            response=error
        row['initial_status']=redirect.chain[0]['status'] if redirect.chain else response.status
        row['terminal_status']=response.status
        row['final_url']=encoded_url(response.url)
        row['redirect_chain']=redirect.chain
        row['x_robots_tag']=' | '.join(response.headers.get_all('X-Robots-Tag',[]))
        row['content_type']=response.headers.get('Content-Type','')
        row['retry_after']=response.headers.get('Retry-After','')
        row['link_canonical_header']=' | '.join(response.headers.get_all('Link',[]))
        body=response.read(LIMIT+1)
        row['body_truncated']=len(body)>LIMIT
        body=body[:LIMIT]
        row['body_sha256']=hashlib.sha256(body).hexdigest()
        (WORK/(key+'.body')).write_bytes(body)
        if 'html' in row['content_type'] and body.strip():
            document=html.fromstring(body)
            row['title']=' '.join(document.xpath('//title/text()')).strip().replace('\u200c',' ')
            row['robots_meta']=' | '.join(document.xpath('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="robots"]/@content'))
            canonicals=document.xpath('//link[contains(concat(" ",normalize-space(translate(@rel,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz"))," ")," canonical ")]/@href')
            if canonicals:
                row['canonical_url']=' | '.join(encoded_url(urljoin(row['final_url'],link)) for link in canonicals)
    except Exception as error:
        row['fetch_error']=type(error).__name__+': '+str(error)
        row['redirect_chain']=redirect.chain
        if redirect.chain:
            row['initial_status']=redirect.chain[0]['status']
    # All text evidence is escaped on serialization; no actual U+200C is emitted.
    (WORK/(key+'.json')).write_text(json.dumps(row,ensure_ascii=True,indent=2)+'\n')
    return row


def robots_groups(text):
    groups=[];agents=[];rules=[]
    for number,line in enumerate(text.splitlines(),1):
        line=line.split('#',1)[0].strip()
        if ':' not in line:continue
        key,value=(piece.strip() for piece in line.split(':',1))
        if key.lower()=='user-agent':
            if rules:
                groups.append((agents,rules));agents=[];rules=[]
            agents.append(value.lower())
        elif key.lower() in ('allow','disallow') and agents and value:
            rules.append({'directive':key.title(),'pattern':value,'line':number})
    if agents:groups.append((agents,rules))
    return groups


def robots_match(text,url,agent='Googlebot'):
    choices=[]
    for agents,rules in robots_groups(text):
        lengths=[0 if a=='*' else len(a) for a in agents if a=='*' or a in agent.lower()]
        if lengths:choices.append((max(lengths),agents,rules))
    if not choices:return True,'','',agent
    best=max(x[0] for x in choices)
    rules=[rule for specificity,agents,group in choices if specificity==best for rule in group]
    p=urlsplit(url);path=p.path+('?' + p.query if p.query else '')
    matches=[]
    for rule in rules:
        pattern=rule['pattern']
        expression='^'+re.escape(pattern).replace(r'\*','.*').replace(r'\$','$')
        if re.search(expression,path):matches.append(rule)
    if not matches:return True,'','',agent
    rule=max(matches,key=lambda x:(len(x['pattern'].replace('*','').rstrip('$')),x['directive']=='Allow'))
    return rule['directive']=='Allow',rule['directive']+': '+rule['pattern'],rule['line'],agent


def run():
    parser=argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--seeds',type=Path,default=AUDIT/'gsc-seeds-20261110.json')
    parser.add_argument('--output',type=Path,default=AUDIT/'gsc-indexing-20261110.csv')
    parser.add_argument('--workers',type=int,default=8)
    parser.add_argument('--resume',action='store_true',help='Reuse local ignored per-URL results; omit for fresh live evidence')
    parser.add_argument('--retry-transient',action='store_true',help='With --resume, make one ordinary fresh request for cached 429/502/503/504 samples; preserve prior evidence')
    args=parser.parse_args();WORK.mkdir(parents=True,exist_ok=True)
    seeds=list(input_rows(json.loads(args.seeds.read_text())))
    normalized=[]
    for i,seed in enumerate(seeds,1):
        transport=next(str(seed[k]) for k in ('request_url','url','input_url','original_url','old_url') if k in seed)
        original=str(seed.get('original_url',transport))
        notes=[str(seed['normalization_notes'])] if seed.get('normalization_notes') else [];request='';error=''
        if r'\*' in original:notes.append('Markdown escape before asterisk removed; literal asterisk retained without expansion')
        elif '*' in original:notes.append('Literal asterisk is one supplied URL sample; no wildcard expansion')
        if r'\_' in original:notes.append('Markdown escape before underscore removed')
        if original.startswith('//'):notes.append('Leading double slash retained as this origin path, not interpreted as external host')
        try:
            request=encoded_url(transport)
            if '#' in original:notes.append('Fragment removed from HTTP request')
            if request!=original:notes.append('URI characters encoded safely; original text preserved in ASCII-escaped JSON field')
        except Exception as exc:error=type(exc).__name__+': '+str(exc)
        normalized.append({'row_id':str(seed.get('row_id',seed.get('id',f'gsc-{i:04d}'))),'source_bucket':str(seed.get('source_bucket','')),'original_input_json':json.dumps(original,ensure_ascii=True),'request_url':request,'normalization_notes':' | '.join(notes),'seed_metadata_json':json.dumps(seed,ensure_ascii=True,sort_keys=True),'fetch_error':error})
    unique=sorted({s['request_url'] for s in normalized if s['request_url']})
    responses={};pending=[]
    for url in unique:
        file=WORK/(hashlib.sha256(url.encode()).hexdigest()+'.json')
        if args.resume and file.exists():
            previous=json.loads(file.read_text())
            if args.retry_transient and previous.get('terminal_status') in (429,502,503,504):
                history=WORK/'history'/file.stem
                history.mkdir(parents=True,exist_ok=True)
                stamp=previous.get('fetched_utc','previous').replace(':','-')
                (history/(stamp+'.json')).write_bytes(file.read_bytes())
                body=file.with_suffix('.body')
                if body.exists():(history/(stamp+'.body')).write_bytes(body.read_bytes())
                retry_after=previous.get('retry_after','')
                wait=0
                if retry_after.isdigit():
                    elapsed=time.time()-__import__('datetime').datetime.fromisoformat(previous['fetched_utc']).timestamp()
                    wait=max(0,int(retry_after)-elapsed)
                elif retry_after:
                    try:
                        from email.utils import parsedate_to_datetime
                        wait=max(0,parsedate_to_datetime(retry_after).timestamp()-time.time())
                    except (TypeError,ValueError):pass
                if wait>60:
                    print(f'Deferred transient URL because Retry-After has {wait:.0f}s remaining: {url}',flush=True)
                    responses[url]=previous
                else:
                    if wait:time.sleep(wait)
                    pending.append(url)
            else:responses[url]=previous
        else:pending.append(url)
    print(f'{len(normalized)} seed rows; {len(unique)} unique request URLs; fetching {len(pending)}',flush=True)
    with concurrent.futures.ThreadPoolExecutor(max_workers=max(1,min(args.workers,8))) as pool:
        for n,result in enumerate(pool.map(get,pending),1):
            responses[result['request_url']]=result
            if n%25==0 or n==len(pending):print(f'fetched {n}/{len(pending)}',flush=True)
    origins=sorted({urlunsplit((*urlsplit(url)[:2],'','','')) for url in unique})
    robots={}
    for origin in origins:
        url=origin+'/robots.txt';file=WORK/(hashlib.sha256(url.encode()).hexdigest()+'.json')
        result=json.loads(file.read_text()) if args.resume and file.exists() else get(url)
        bodyfile=WORK/(hashlib.sha256(url.encode()).hexdigest()+'.body')
        text=bodyfile.read_text(errors='replace') if bodyfile.exists() else ''
        robots[origin]=(result,text)
    inbound=defaultdict(list)
    edgefile=AUDIT/'crawl-edges.csv.gz'
    if edgefile.exists():
        for e in csv.DictReader(gzip.open(edgefile,'rt')):
            try:inbound[encoded_url(e['target_url'])].append(e['source_url'])
            except ValueError:pass
    sitemap=json.loads((AUDIT/'sitemap-urls.json').read_text()) if (AUDIT/'sitemap-urls.json').exists() else {}
    results=[]
    for seed in normalized:
        result={k:'' for k in FIELDS};result.update(seed);url=seed['request_url']
        if url:
            fetched=responses[url]
            for k in FIELDS:
                if k in fetched:result[k]=fetched[k]
            result['redirect_chain_json']=json.dumps(fetched['redirect_chain'],ensure_ascii=True,separators=(',',':'))
            origin=urlunsplit((*urlsplit(url)[:2],'','',''));robot,text=robots[origin]
            if robot['terminal_status']==200:
                allowed,rule,line,agent=robots_match(text,url)
                result.update(robots_allowed=str(allowed).lower(),robots_matched_rule=rule,robots_rule_line=line,robots_user_agent=agent)
            elif robot['terminal_status'] in (404,410):result.update(robots_allowed='true',robots_matched_rule='robots.txt absent (HTTP 404/410)',robots_user_agent='Googlebot')
            else:result.update(robots_allowed='unknown',robots_matched_rule='robots.txt fetch failed: '+str(robot['terminal_status'])+' '+robot['fetch_error'],robots_user_agent='Googlebot')
            sources=inbound[url];result.update(inbound_link_count=len(sources),inbound_source_count=len(set(sources)),inbound_sources_json=json.dumps(sorted(set(sources)),ensure_ascii=True),in_sitemap=str(url in sitemap).lower())
        # CSV fields keep Persian readable while escaping the forbidden zero-width char.
        for key,value in result.items():
            if isinstance(value,str):result[key]=value.replace('\u200c',r'\u200c')
        results.append(result)
    args.output.parent.mkdir(parents=True,exist_ok=True)
    with args.output.open('w',encoding='utf-8',newline='') as file:
        writer=csv.DictWriter(file,fieldnames=FIELDS,lineterminator='\n');writer.writeheader();writer.writerows(results)
    if args.output.resolve()==(AUDIT/'gsc-indexing-20261110.csv').resolve():
        review_copy=ROOT/'audit/gsc-indexing-20261110.csv'
        review_copy.parent.mkdir(parents=True,exist_ok=True)
        review_copy.write_bytes(args.output.read_bytes())
    (WORK/'robots-evidence.json').write_text(json.dumps({k:{'fetch':v[0],'robots_txt':v[1]} for k,v in robots.items()},ensure_ascii=True,indent=2)+'\n')
    print(f'Wrote {args.output}: {len(results)} rows, {sum(bool(r["fetch_error"]) for r in results)} fetch errors',flush=True)


if __name__=='__main__':run()
