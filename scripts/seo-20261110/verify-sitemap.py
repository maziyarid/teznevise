#!/usr/bin/env python3
"""Read-only recursive sitemap acceptance check: direct 200, self canonical, indexable."""
import argparse
from concurrent.futures import ThreadPoolExecutor
import csv
import importlib.util
import json
from pathlib import Path
from urllib.error import HTTPError
from urllib.parse import urljoin, urlsplit, urlunsplit, unquote
from urllib.request import Request, build_opener
import xml.etree.ElementTree as ET

ROOT = Path(__file__).resolve().parents[2]
SPEC = importlib.util.spec_from_file_location('gsc_verify', Path(__file__).with_name('verify-gsc.py'))
GSC = importlib.util.module_from_spec(SPEC)
SPEC.loader.exec_module(GSC)


def comparable(url):
    p = urlsplit(url)
    return urlunsplit((p.scheme.lower(), p.netloc.lower(), unquote(p.path or '/'), p.query, ''))


def collect(start):
    expected_host = urlsplit(start).netloc.lower()
    pending = [start]
    seen = set()
    pages = set()
    problems = []
    opener = build_opener(GSC.NoRedirect())
    while pending:
        url = pending.pop()
        if url in seen: continue
        seen.add(url)
        try:
            if GSC.url_error(url): raise ValueError(GSC.url_error(url))
            response = opener.open(Request(url, headers={'User-Agent': GSC.AGENT}), timeout=30)
            if response.status != 200: raise ValueError('Sitemap response ' + str(response.status))
            data = response.read(10_000_001)
            if len(data) > 10_000_000: raise ValueError('Sitemap exceeds 10 MB limit')
            tree = ET.fromstring(data)
            kind = tree.tag.rsplit('}', 1)[-1]
            if kind not in ('sitemapindex', 'urlset'): raise ValueError('Unexpected XML root ' + kind)
            item_name = 'sitemap' if kind == 'sitemapindex' else 'url'
            for item in tree:
                if item.tag.rsplit('}', 1)[-1] != item_name: continue
                loc = next((child.text for child in item if child.tag.rsplit('}', 1)[-1] == 'loc'), None)
                if not loc: raise ValueError('Missing direct loc child')
                target = GSC.transport_url(urljoin(url, loc.strip()))
                if GSC.url_error(target): raise ValueError('Invalid loc: ' + target)
                if urlsplit(target).netloc.lower() != expected_host: raise ValueError('Cross-origin sitemap loc: ' + target)
                if kind == 'sitemapindex': pending.append(target)
                else: pages.add(target)
        except (HTTPError, OSError, ValueError, ET.ParseError) as error:
            problems.append({'url': url, 'result': 'FAIL', 'issues': str(error)})
    return sorted(pages), sorted(seen), problems


def evaluate(url, row, robots):
    problems = []
    if row['fetch_error']: problems.append(row['fetch_error'])
    if row['initial_status'] != 200 or row['terminal_status'] != 200 or row['redirect_chain']:
        problems.append('Sitemap page must return direct 200 without a redirect')
    if not row['is_html']: problems.append('Page loc must be an HTML document')
    if len(row['canonical_urls']) != 1 or comparable(row['canonical_urls'][0]) != comparable(url):
        problems.append('Expected exactly one self canonical')
    if row['noindex']: problems.append('Noindex directive present')
    allowed = GSC.robots_decision(url, robots)
    if allowed.get('allowed') is not True: problems.append('Robots does not confirm crawling allowed')
    return {'url': url, 'result': 'FAIL' if problems else 'PASS', 'initial_status': row['initial_status'], 'final_url': row['final_url'],
            'canonical': ' | '.join(row['canonical_urls']), 'robots_allowed': allowed.get('allowed'), 'robots_rule': allowed.get('rule', ''),
            'issues': ' | '.join(problems)}


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--sitemap', default='https://teznevise.ir/sitemap_index.xml')
    parser.add_argument('--workers', type=int, default=6)
    parser.add_argument('--require-money-pages', action='store_true', help='Owner post-apply check that all seven canonical money/home URLs appear')
    parser.add_argument('--output-prefix', type=Path, default=ROOT / 'work/seo-20261110/sitemap-verification')
    args = parser.parse_args()
    origin = urlsplit(args.sitemap)
    if origin.scheme != 'https' or not origin.hostname or origin.username or origin.password or origin.port:
        parser.error('--sitemap requires a credentialless HTTPS URL without an explicit port')
    GSC.HOSTS.add(origin.hostname.lower())
    urls, sitemaps, xml_errors = collect(args.sitemap)
    origin = urlsplit(args.sitemap)
    robots = GSC.fetch_chain(urlunsplit((origin.scheme, origin.netloc, '/robots.txt', '', '')))
    with ThreadPoolExecutor(max_workers=max(1, min(args.workers, 8))) as pool:
        observations = list(pool.map(GSC.fetch_chain, urls))
    rows = [evaluate(url, row, robots) for url, row in zip(urls, observations)]
    missing = []
    if args.require_money_pages:
        manifest = json.loads((ROOT / 'seo/20261110/manifest.json').read_text())
        current = {comparable(url) for url in urls}
        missing = [page['path'] for page in manifest['pages'] if comparable(urljoin(args.sitemap, page['path'])) not in current]
    failed = sum(row['result'] == 'FAIL' for row in rows)
    summary = {'result': 'FAIL' if failed or xml_errors or missing or not urls else 'PASS', 'sitemap_files': len(sitemaps), 'page_urls': len(urls), 'pass': len(rows) - failed, 'fail': failed, 'xml_errors': len(xml_errors), 'missing_money_pages': missing}
    report = {'fetched_utc': GSC.utc_now(), 'summary': summary, 'sitemaps': sitemaps, 'xml_errors': xml_errors, 'rows': rows}
    args.output_prefix.parent.mkdir(parents=True, exist_ok=True)
    args.output_prefix.with_suffix('.json').write_text(json.dumps(report, ensure_ascii=True, indent=2) + '\n')
    with args.output_prefix.with_suffix('.csv').open('w', newline='', encoding='utf-8') as file:
        writer = csv.DictWriter(file, fieldnames=['url', 'result', 'initial_status', 'final_url', 'canonical', 'robots_allowed', 'robots_rule', 'issues'], lineterminator='\n')
        writer.writeheader()
        writer.writerows(rows)
    print(json.dumps(summary))
    return 0 if summary['result'] == 'PASS' else 1


if __name__ == '__main__': raise SystemExit(main())
