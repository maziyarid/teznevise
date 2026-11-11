#!/usr/bin/env python3
"""Read-only content and rendered-copy checks. Network failures fail closed."""
import argparse
import csv
import hashlib
import json
import re
import sys
from concurrent.futures import ThreadPoolExecutor
from html.parser import HTMLParser
from pathlib import Path
from urllib.error import HTTPError
from urllib.parse import urljoin, urlsplit
from urllib.request import Request, build_opener, HTTPRedirectHandler

ROOT = Path(__file__).resolve().parents[2]
CONTENT = ROOT / 'seo/20261110/content'
ORIGIN = 'https://teznevise.ir'
BASE_PATHS = ['/', '/thesis/', '/thesis/phd/', '/proposal/', '/proposal/phd/',
              '/service-project/', '/thesis/humanities/psychology/',
              '/thesis/humanities/management/']
MONEY = {'/thesis/': 'انجام پایان نامه', '/thesis/master/': 'انجام پایان نامه کارشناسی ارشد',
         '/thesis/phd/': 'انجام پایان نامه دکتری', '/proposal/': 'انجام پروپوزال',
         '/proposal/phd/': 'انجام پروپوزال دکتری', '/service-project/': 'انجام پروژه های دانشجویی'}

class NoRedirect(HTTPRedirectHandler):
    def redirect_request(self, req, fp, code, msg, headers, newurl):
        return None

class Rendered(HTMLParser):
    def __init__(self):
        super().__init__(convert_charrefs=True)
        self.stack = []
        self.elements = []
    def handle_starttag(self, tag, attrs):
        attrs = dict(attrs)
        for key in ('title', 'aria-label'):
            if attrs.get(key):
                self.elements.append((f'{tag}[{key}]', attrs[key], attrs.get('href', '')))
        if tag == 'meta' and attrs.get('name', '').lower() == 'description':
            self.elements.append(('meta description', attrs.get('content', ''), ''))
        if tag in ('a', 'h1', 'title'):
            self.stack.append([tag, [], attrs.get('href', '')])
    def handle_data(self, data):
        for entry in self.stack:
            entry[1].append(data)
    def handle_endtag(self, tag):
        for i in range(len(self.stack) - 1, -1, -1):
            if self.stack[i][0] == tag:
                tag, pieces, href = self.stack.pop(i)
                self.elements.append((tag, ' '.join(''.join(pieces).split()), href))
                break

def fetch(url):
    try:
        with build_opener(NoRedirect()).open(Request(url, headers={'User-Agent': 'Teznevise-draft-audit/1.0'}), timeout=35) as response:
            data = response.read()
            return response.status, data, None
    except HTTPError as exc:
        return exc.code, exc.read(), str(exc)
    except Exception as exc:
        return 0, b'', str(exc)

def scoped(path):
    return path.startswith(('/thesis/', '/proposal/', '/project/', '/service-project/'))

def rendered_errors(url, html):
    parser = Rendered()
    parser.feed(html)
    rows = []
    for element, text, target in parser.elements:
        general = element == 'a' or element.endswith(('[title]', '[aria-label]'))
        if 'مشاوره' in text and (general or scoped(urlsplit(url).path)):
            rows.append({'url': url, 'element': element, 'current_text': text,
                         'target': target, 'proposed_text': 'ثبت درخواست' if element == 'a' and ('inquiry' in target or target.startswith('#')) else 'نیاز به تایید مازیار'})
    return rows

def body_errors(path, text, minimum):
    errors = []
    def fail(message): errors.append(f'{path}: {message}')
    if '\u200c' in text: fail('U+200C present')
    if not re.search(r'^#\s+\S', text, re.M): fail('missing H1')
    if not re.search(r'^##\s+.*(?:پرسش|سوال)', text, re.M): fail('missing visible FAQ')
    prose = re.sub(r'```.*?```', '', text, flags=re.S)
    prose = re.sub(r'\[([^\]]+)\]\([^)]*\)', r'\1', prose)
    for line in prose.splitlines():
        if re.search(r'[\u0600-\u06ff]', line) and re.search(r'[0-9]', line): fail('Latin digit in Persian prose: ' + line[:120])
    for word in ('ضمانت', 'تضمین', '۱۰۰٪', 'بهترین'):
        if word in prose: fail('unsupported claim token ' + word)
    if len(prose.split()) < minimum: fail(f'word count {len(prose.split())} below {minimum}')
    anchors = re.findall(r'\[([^\]]+)\]\(([^)]+)\)', text)
    exact = 0
    for anchor, target in anchors:
        if anchor.strip() == 'اینجا' or 'مشاوره' in anchor: fail('forbidden anchor: ' + anchor)
        parsed = urlsplit(urljoin(ORIGIN, target))
        if parsed.netloc != urlsplit(ORIGIN).netloc: fail('external hyperlink: ' + target)
        if parsed.path == '/thesis/master/': fail('unpublished master page linked')
        if anchor == MONEY.get(parsed.path): exact += 1
    if exact > 1: fail(f'{exact} exact anchors')
    if any('مشاوره' in h for h in re.findall(r'^#\s+(.+)$', text, re.M)): fail('forbidden H1')
    blocks = re.findall(r'```(?:json|jsonld)\s*(.*?)```', text, re.S)
    types = set()
    def visit(obj):
        if isinstance(obj, dict):
            t = obj.get('@type', [])
            types.update([t] if isinstance(t, str) else t)
            for k, v in obj.items():
                if k in ('aggregateRating', 'review', 'ratingValue'): fail('prohibited schema property ' + k)
                visit(v)
        elif isinstance(obj, list):
            for v in obj: visit(v)
    for block in blocks:
        try: visit(json.loads(block))
        except (ValueError, TypeError): fail('invalid JSON-LD')
    if not {'Service', 'FAQPage', 'BreadcrumbList'}.issubset(types): fail('missing required schema types')
    if '[درخواست بررسی پروژه](/inquiry/)' not in text: fail('missing request CTA')
    if '## منابع' not in text: fail('missing sources')
    return errors

def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--rendered', action='store_true')
    ap.add_argument('--base-url', default=ORIGIN)
    ap.add_argument('--output', type=Path)
    ap.add_argument('--snapshot-dir', type=Path)
    ap.add_argument('--pr456-manifest', type=Path)
    ap.add_argument('--rendered-only', action='store_true')
    args = ap.parse_args()
    errors, offenders, snapshots = [], [], []
    manifest_path = CONTENT / 'manifest.json'
    manifest = json.loads(manifest_path.read_text()) if manifest_path.exists() else {'pages': []}
    if not args.rendered_only:
        if not manifest['pages']: errors.append('No completed content drafts in manifest; content waves are incomplete')
        for page in manifest['pages']:
            p = CONTENT / page['file']
            errors.extend(body_errors(page['path'], p.read_text(), page['minimum_words']))
            if page.get('status') == 'publish': errors.append('Published status forbidden: ' + page['path'])
    graph_path = CONTENT / 'link-graph.csv'
    graph = list(csv.DictReader(graph_path.open())) if graph_path.exists() else []
    by_source = {}
    for row in graph:
        by_source[row['source']] = by_source.get(row['source'], 0) + int(row['anchor_type'] == 'exact')
    for source, count in by_source.items():
        if count > 1: errors.append(f'{source}: {count} exact anchors in graph')
    if args.pr456_manifest:
        original = json.loads(args.pr456_manifest.read_text())
        for page in original.get('pages', []):
            if page.get('path') == '/' and 'مشاوره' in page.get('description', ''):
                print('FACT: PR #456 homepage metadata contains forbidden wording; DROP before merge')
    paths = set(BASE_PATHS) if args.rendered else set()
    targets = {urljoin(args.base_url, r['target']) for r in graph}
    if args.rendered:
        paths.update(r['source'] for r in graph)
        paths.update(r['target'] for r in graph)
    urls = sorted(targets | {urljoin(args.base_url, p) for p in paths})
    with ThreadPoolExecutor(max_workers=5) as pool:
        for url, result in zip(urls, pool.map(fetch, urls)):
            status, data, error = result
            snapshots.append({'url': url, 'http_status': status, 'sha256': hashlib.sha256(data).hexdigest(), 'error': error})
            if status != 200: errors.append(f'{url}: HTTP {status}; redirects and failed fetches are forbidden')
            if args.snapshot_dir:
                args.snapshot_dir.mkdir(parents=True, exist_ok=True)
                (args.snapshot_dir / (hashlib.sha256(url.encode()).hexdigest()+'.html')).write_bytes(data)
            if args.rendered and status == 200:
                rows = rendered_errors(url, data.decode('utf-8', errors='replace'))
                offenders.extend(rows)
                errors.extend(f"{r['url']} {r['element']}: {r['current_text']}" for r in rows)
    report = {'passed': not errors, 'errors': errors, 'offenders': offenders, 'snapshots': snapshots,
              'drafts_checked': len(manifest['pages']) if not args.rendered_only else 0,
              'note': 'HTTP snapshots prove only the observed response bytes, not absence of all database changes.'}
    if args.output:
        args.output.parent.mkdir(parents=True, exist_ok=True)
        args.output.write_text(json.dumps(report, ensure_ascii=False, indent=2)+'\n')
    print(json.dumps(report, ensure_ascii=False, indent=2))
    return bool(errors)

if __name__ == '__main__':
    sys.exit(main())
