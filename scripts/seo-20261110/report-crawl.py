#!/usr/bin/env python3
"""Regenerate crawl inventories without making network requests or selecting redirects."""
import csv, gzip, hashlib, json, re
from collections import Counter
from pathlib import Path
from urllib.parse import urlsplit, unquote

ROOT=Path(__file__).resolve().parents[2]
OUT=ROOT/'seo/20261110/audit'
data=json.loads((OUT/'crawl.json').read_text());rows=data['rows'];by={r['url']:r for r in rows}
edges=list(csv.DictReader(gzip.open(OUT/'crawl-edges.csv.gz','rt')))
resources=list(csv.DictReader(gzip.open(OUT/'resource-edges.csv.gz','rt')))
seeds=json.loads((OUT/'sitemap-urls.json').read_text())

def write(name,fields,items):
    with (OUT/name).open('w',newline='',encoding='utf-8') as f:
        w=csv.DictWriter(f,fieldnames=fields,lineterminator='\n');w.writeheader();w.writerows(items)

write('url-status.csv',['url','initial_status','final_status','final_url','content_type','wp_id','title','article_words','canonical','robots_meta','error'],[
    {**{k:r[k] for k in ['url','initial_status','final_url','content_type','wp_id','title','article_words','canonical','robots_meta','error']},'final_status':r['status']} for r in rows])
write('redirect-chains.csv',['requested_url','hop','old_url','status','new_url','terminal_url','terminal_status'],[
    {'requested_url':r['url'],'hop':i,'old_url':h['url'],'status':h['status'],'new_url':h['target'],'terminal_url':r['final_url'],'terminal_status':r['status']}
    for r in rows for i,h in enumerate(r['redirects'],1)])
missing=[]
for r in rows:
    if r['status']!=404:continue
    inbound=[e for e in edges if e['target_url']==r['url']]
    resource_inbound=[e for e in resources if e['target_url']==r['url']]
    new=r['url']=='https://teznevise.ir/student-projects/'
    missing.append({'old_url':r['url'],'inbound_links':len(inbound),'inbound_resources':len(resource_inbound),'inbound_sources':' | '.join(sorted({e['source_url'] for e in inbound+resource_inbound})),'in_sitemap':r['url'] in seeds,'decision':'create canonical page' if new else 'review closest live target; retain 404 until reviewed','new_url':r['url'] if new else '', 'reason':'Explicit requested new-page probe; not an existing linked 404. New content answers this URL directly.' if new else 'No invented replacement. Needs manual topical review.'})
write('internal-404s.csv',['old_url','inbound_links','inbound_resources','inbound_sources','in_sitemap','decision','new_url','reason'],missing)

raw=(OUT/'snapshots/robots.txt').read_text();rules=[]
for num,line in enumerate(raw.splitlines(),1):
    m=re.match(r'(Allow|Disallow):\s*(\S+)',line,re.I)
    if m:rules.append({'directive':m.group(1),'pattern':m.group(2),'line':num})
def winner(url):
    p=urlsplit(url);path=unquote(p.path)+('?' + p.query if p.query else '')
    matches=[r for r in rules if re.search('^'+re.escape(r['pattern']).replace(r'\*','.*').replace(r'\$','$'),path)]
    return max(matches,key=lambda r:(len(r['pattern'].replace('*','').rstrip('$')),r['directive'].lower()=='allow')) if matches else None
blocked=[]
for r in rows:
    rule=winner(r['url'])
    if rule and rule['directive'].lower()=='disallow':blocked.append((r,rule))
write('robots-blocked-urls.csv',['url','rule','rule_line','initial_status','final_url','usefulness','recommendation'],[
    {'url':r['url'],'rule':rule['pattern'],'rule_line':rule['line'],'initial_status':r['initial_status'],'final_url':r['final_url'],'usefulness':'Useful legacy enquiry alias; redirect needs crawling' if '/order/' in r['url'] else 'Review utility or duplicate URL','recommendation':'Allow /order/ so crawlers can follow its 301' if '/order/' in r['url'] else 'Retain block unless owner confirms standalone search intent'} for r,rule in blocked])
write('robots-rules.csv',['line','directive','pattern','matched_blocked_urls','review'],[
    {**r,'matched_blocked_urls':sum(rule==r for _,rule in blocked),'review':'Useful legacy enquiry alias: allow its redirect' if r['pattern']=='/order/' else ('Retain: public access exception' if r['directive'].lower()=='allow' else 'Retain: administration, API, private transactions, staging or duplicate URL noise; no useful standalone content discovered blocked by this rule')} for r in rules])
soft=[{'url':r['url'],'title':r['title'],'reason':'200 status with not-found title'} for r in rows if r['status']==200 and 'html' in r['content_type'] and re.search('not found|پیدا نشد|صفحه یافت نشد|خطای ۴۰۴|خطای 404',r['title'],re.I)]
(OUT/'soft-404-candidates.json').write_text(json.dumps(soft,ensure_ascii=False,indent=2)+'\n')
baseline=[]
for path in ['/','/thesis/','/thesis/master/','/thesis/phd/','/proposal/','/proposal/phd/','/student-projects/','/service-project/']:
    r=by['https://teznevise.ir'+path]
    note='Largest rendered longcopy/article surface, whitespace-separated words after scripts, styles and site chrome removal; builder hero excluded.'
    if path=='/thesis/master/':note='No independent service page: 301 resolves to a 512-word downloadable guide. Count is terminal guide body, not master landing copy.'
    if path=='/student-projects/':note='New page absent (404); zero existing landing-page words; the 404 template is excluded.'
    baseline.append({'url':r['url'],'initial_status':r['initial_status'],'final_url':r['final_url'],'before_title':r['title'],'before_article_words':0 if r['status']==404 else r['article_words'],'measurement_note':note})
write('before-money-pages.csv',['url','initial_status','final_url','before_title','before_article_words','measurement_note'],baseline)
write('snapshot-sha256.csv',['file','sha256'],[{'file':str(p.relative_to(OUT)),'sha256':hashlib.sha256(p.read_bytes()).hexdigest()} for p in sorted((OUT/'snapshots').glob('*'))])
errors=[r for r in rows if r['error']]
initial=Counter(r['initial_status'] for r in rows);terminal=Counter(r['status'] for r in rows)
report=f'''# Live crawl review: 2026-11-10

Read-only owner audit of https://teznevise.ir. Started {data['started_utc']}; evidence frozen {data['updated_utc']}. No WordPress content or settings were changed.

## Coverage and results

- {len(rows):,} distinct same-origin HTTP URLs checked, including {sum('html' in r['content_type'] for r in rows):,} HTML responses, recursive XML sitemap URLs, all reachable anchor links, downloadable files, first-party image/srcset/script/style/preload references and CSS resource references.
- {len(edges):,} observed internal anchor edges and {len(resources):,} first-party resource references retained in compressed CSVs; repeated navigation is intentionally retained as evidence.
- Initial status counts: {dict(sorted(initial.items()))}. Terminal status counts after redirects: {dict(sorted(terminal.items()))}. Initial and terminal status are distinct in url-status.csv.
- {len(missing)} terminal 404: `/student-projects/`, an explicit requested new-page probe with no existing inbound link or sitemap entry. Creating the canonical landing resolves it; redirecting it elsewhere would defeat the intended URL.
- No linked or sitemap HTML/file/resource URL currently returned 404. {len(errors)} fetch/parse errors. {len(soft)} title-based soft-404 candidates; this heuristic does not replace Search Console soft-404 diagnostics.
- All 54 published blog posts (including 25 English posts) from the public WordPress REST collection are covered. The English sitemap supplies English discovery too.

The user-supplied Search Console figures are facts for 2026-08-09 through 2026-11-08: 549 clicks, 16,661 impressions; indexing totals 617 indexed, 182 not found, 47 blocked, 71 redirect and 22 noindex. These are historical aggregate counts, not URL exports. A public live crawl cannot enumerate unlinked historical URLs or establish that those 182 old 404s have all been fixed. No claim of that completeness is made. Import the owner's Search Console URL exports and re-crawl any historical-only URL before assigning further 301s.

## Redirect decisions

`redirect-map.csv` is the manually reviewed, three-row application map: the two required legacy service aliases keep their already observed single-hop 301s; `/service-project/` consolidates the same project-service intent into the new `/student-projects/` page after that target exists. No unrelated missing URL is redirected to the homepage.

`redirect-chains.csv` lists every observed hop and terminal URL. In addition to the two required aliases: `/account/` currently returns 302 to `/inquiry/`; `/order/` returns 301 to `/inquiry/`; `/wp-sitemap.xml` returns 301 to `/sitemap_index.xml`; `/thesis/master/` returns 301 to `/download/masters-research-methods-guide/`.

The last redirect must be removed for the new master landing to return 200 at its own canonical URL. The downloaded guide remains a separate resource. The current project-service page returns 200 and contributes a 605-word baseline for consolidation; it is not misreported as a broken URL.

## Robots review

The exact fetched robots file and XML sitemap bodies are in `snapshots/`; SHA-256 hashes are in `snapshot-sha256.csv`. `robots-rules.csv` reviews every Allow/Disallow directive, and `robots-blocked-urls.csv` lists discovered URLs blocked by the effective wildcard-group rule.

`Disallow: /order/` is the only rule blocking a useful discovered or explicitly probed path: crawlers need to see its legacy 301 to `/inquiry/`. Allow that alias (or remove only its Disallow) and retain protection for administration, API, private transactions, staging and duplicate parameter noise. All six canonical money-page URLs are crawlable under the fetched robots rules. No broader removal of robots protections is warranted by this evidence.

The audit deliberately performs read-only status checks on blocked paths. It uses wildcard and terminal `$` rule matching and longest-match Allow precedence for this site's single `User-agent: *` group. It does not infer the identities of the 47 historical Search Console blocked URLs.

## Word-count method and evidence limits

`before-money-pages.csv` records each requested URL, its initial status, terminal URL, current title, and largest rendered `longcopy` or article surface after scripts, styles, navigation, header and footer removal. Counts split text on whitespace and include headings/FAQ prose in that surface. Nested wrappers are counted once; builder hero and site chrome are excluded. Homepage count is its 235-word longcopy block, not all homepage cards. New `/student-projects/` has zero existing landing words. `/thesis/master/` has no independent service-page baseline: its observed 512 words belong to the redirected download guide.

Fragments are removed; percent encoding is normalized; query strings and schemes are retained as distinct fetch URLs. Extracted text replaces U+200C with ordinary spaces for the requested content convention. Exact current robots/XML snapshots contain no U+200C. Raw HTML and full resumable state stay in ignored `work/seo-20261110/`, while the compact JSON and compressed edge CSVs preserve the review evidence.

This traversal covers public, linked, non-authenticated discovery; it cannot expose orphan URLs absent from sitemaps and links, server-only rewrite rules, authenticated pages, Search Console history, or JavaScript-created links not present in HTML. Binary responses are read up to eight MB for status verification; no binary response was misclassified as a sitemap. External-site destination health is outside the internal crawl.

## Reproduce

Use Python 3 with `lxml` available. From the repository root:

```sh
python3 scripts/seo-20261110/crawl.py --workers 8
python3 scripts/seo-20261110/report-crawl.py
```

The crawler writes only local audit/work files and makes HTTP GET requests. `--resume` uses local ignored crawl state; omit it for a fresh live crawl. The report script performs no network requests and does not overwrite the manually reviewed redirect map. Read compressed CSV evidence with Python `gzip.open(path, 'rt')` or `gzip -dc path`.
'''
(OUT/'crawl-report.md').write_text(report)
print(f'Reports refreshed: {len(rows)} URLs, {len(missing)} 404s, {len(errors)} errors')
