#!/usr/bin/env python3
"""Build a reviewed, source-scoped navigation href cleanup from captured evidence.

No network or WordPress mutations. Regenerate after reviewing GSC decisions.
"""
import csv
import gzip
import json
from pathlib import Path
from urllib.parse import quote, unquote, urlsplit, urlunsplit

ROOT = Path(__file__).resolve().parents[2]
AUDIT = ROOT / 'seo/20261110/audit'
ORIGIN = 'https://teznevise.ir'


def norm(url):
    p = urlsplit(url)
    if p.scheme not in ('http', 'https') or p.hostname not in ('teznevise.ir', 'www.teznevise.ir'): return None
    path = quote(unquote(p.path or '/'), safe="/%:@!$&'()*+,;=-._~")
    return urlunsplit((p.scheme.lower(), p.netloc.lower(), path, p.query, ''))


def read(name):
    with (AUDIT / name).open(newline='', encoding='utf-8') as file: return list(csv.DictReader(file))


def main():
    decisions = read('gsc-decisions-20261110.csv')
    routes = {}
    reasons = {}
    for row in read('redirect-map.csv'):
        routes[norm(row['old_url'])] = norm(row['new_url'])
        reasons[norm(row['old_url'])] = 'Base on-page reviewed redirect: ' + row['reason']
    for row in decisions:
        source = norm(row['request_url'])
        if '301' in row['action'].split('|'):
            routes[source] = norm(row['target_url'])
            reasons[source] = 'GSC reviewed redirect: ' + row['reason']
        elif row['action'] == 'keep' and row['observed_initial_status'] == '301':
            # Keep only already-reviewed single-hop permanent canonical aliases.
            final = row.get('target_url') or row.get('expected_canonical')
            if final and norm(final) != source:
                routes[source] = norm(final)
                reasons[source] = 'Existing healthy permanent alias: ' + row['reason']
    # Include permanent baseline aliases not present in the historical sample list.
    crawl = json.loads((AUDIT / 'crawl.json').read_text())['rows']
    for row in crawl:
        source = norm(row['url'])
        if source in routes or row.get('initial_status') != 301: continue
        chain = row.get('redirects', [])
        if len(chain) == 1 and row['status'] == 200:
            routes[source] = norm(row['final_url'])
            reasons[source] = 'Captured single-hop permanent alias to its live destination.'
    routes = {k: v for k, v in routes.items() if k and v and k != v}
    def final(source):
        seen = set()
        target = source
        while target in routes:
            if target in seen: raise ValueError('Redirect loop in reviewed route graph: ' + source)
            seen.add(target)
            target = routes[target]
        return target
    for source in list(routes): routes[source] = final(source)
    rows = {}
    with gzip.open(AUDIT / 'crawl-edges.csv.gz', 'rt', encoding='utf-8', newline='') as file:
        for edge in csv.DictReader(file):
            old = norm(edge['target_url'])
            if old not in routes: continue
            source = norm(edge['source_url'])
            if not source: continue
            # Binary downloads and scripts/styles are not HTML navigation routes.
            if Path(unquote(urlsplit(old).path)).suffix.lower() in ('.pdf', '.doc', '.docx', '.zip', '.png', '.jpg', '.webp', '.svg', '.js', '.css', '.woff2'): continue
            key = (source, old)
            rows[key] = {'source_url': source, 'old_url': old, 'new_url': routes[old], 'reason': reasons[old], 'batch': 'links'}
    output = AUDIT / 'internal-redirect-links.csv'
    with output.open('w', encoding='utf-8', newline='') as file:
        writer = csv.DictWriter(file, fieldnames=['source_url', 'old_url', 'new_url', 'reason', 'batch'], lineterminator='\n')
        writer.writeheader()
        writer.writerows(rows[key] for key in sorted(rows))
    print(json.dumps({'rules': len(rows), 'source_pages': len({r['source_url'] for r in rows.values()}), 'old_urls': len({r['old_url'] for r in rows.values()}), 'output': str(output.relative_to(ROOT))}))


if __name__ == '__main__': main()
