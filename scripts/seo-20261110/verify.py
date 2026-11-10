#!/usr/bin/env python3
"""Offline contract verification; --live checks an owner-approved installed site."""
import argparse
import csv
import html
import gzip
from html.parser import HTMLParser
import json
from pathlib import Path
import re
import sys
import urllib.request
from urllib.error import HTTPError
from urllib.parse import urljoin, urlsplit, unquote

ROOT = Path(__file__).resolve().parents[2]
PACKAGE = ROOT / 'seo/20261110'
KEYWORDS = {
    '/thesis/': 'انجام پایان نامه',
    '/thesis/master/': 'انجام پایان نامه کارشناسی ارشد',
    '/thesis/phd/': 'انجام پایان نامه دکتری',
    '/proposal/': 'انجام پروپوزال',
    '/proposal/phd/': 'انجام پروپوزال دکتری',
    '/student-projects/': 'انجام پروژه های دانشجویی',
}

class Document(HTMLParser):
    def __init__(self, source):
        super().__init__(convert_charrefs=True)
        self.hidden = 0
        self.text = []
        self.scripts = []
        self.current_script = None
        self.h1 = 0
        self.faq = []
        self.links = []
        self.title = []
        self.in_title = False
        self.canonicals = []
        self.descriptions = []
        self.robots = []
        self.anchor = None
        self.feed(source)
    def handle_starttag(self, tag, attrs):
        a = dict(attrs)
        if tag in ('script', 'style', 'noscript'):
            self.hidden += 1
        if tag == 'script' and a.get('type') == 'application/ld+json':
            self.current_script = []
        if tag == 'h1': self.h1 += 1
        if tag == 'a':
            self.anchor = a
            self.anchor['text'] = ''
            self.links.append(a)
        if tag == 'title': self.in_title = True
        if tag == 'link' and a.get('rel') == 'canonical': self.canonicals.append(a.get('href', ''))
        if tag == 'meta' and a.get('name') == 'description': self.descriptions.append(a.get('content', ''))
        if tag == 'meta' and a.get('name') == 'robots': self.robots.append(a.get('content', ''))
    def handle_endtag(self, tag):
        if tag == 'script' and self.current_script is not None:
            self.scripts.append(json.loads(''.join(self.current_script)))
            self.current_script = None
        if tag in ('script', 'style', 'noscript'): self.hidden = max(0, self.hidden - 1)
        if tag == 'title': self.in_title = False
        if tag == 'a': self.anchor = None
    def handle_data(self, data):
        if self.current_script is not None: self.current_script.append(data)
        if self.in_title: self.title.append(data)
        if self.anchor is not None: self.anchor['text'] += data
        if not self.hidden: self.text.append(data)
    @property
    def visible(self): return ' '.join(' '.join(self.text).split())
    @property
    def words(self): return len(self.visible.split())

def schema_nodes(value):
    if isinstance(value, dict):
        yield value
        for child in value.values(): yield from schema_nodes(child)
    elif isinstance(value, list):
        for child in value: yield from schema_nodes(child)

def read_csv(name):
    with (PACKAGE / 'audit' / name).open(encoding='utf-8', newline='') as f:
        return list(csv.DictReader(f))

def check(condition, message):
    if not condition: raise AssertionError(message)

def offline():
    manifest = json.loads((PACKAGE / 'manifest.json').read_text())
    pages = manifest['pages']
    money = {p['path']: p for p in pages if p['path'] != '/'}
    check(set(money) == set(KEYWORDS), 'Exactly six money page targets required')
    check(len(pages) == 7 and sum(p['path'] == '/' for p in pages) == 1, 'Homepage metadata required')
    counts = {}
    for page in pages:
        check(page['seo_title'] and page['description'] and page['title'], f"Missing metadata {page['path']}")
        if page['path'] == '/':
            home = Document((PACKAGE / page['content_file']).read_text())
            check(home.h1 == 0 and not home.scripts, 'Homepage must not introduce H1 or FAQ schema')
            check(not re.search(r'[0-9]', home.visible), 'ASCII digit in homepage prose')
            counts['/'] = home.words
            continue
        check(page['keyword'] == KEYWORDS[page['path']], f"Keyword target mismatch {page['path']}")
        check(page['keyword'] in page['seo_title'], f"Primary term missing from title {page['path']}")
        content = (PACKAGE / page['content_file']).read_text()
        d = Document(content)
        limit = 5000 if page['path'] in ('/student-projects/', '/thesis/master/') else 6000
        check(3000 <= d.words <= limit, f"Word count {page['path']}: {d.words}")
        check(d.h1 == 0, 'Template must own the sole H1')
        check(not re.search(r'[0-9]', d.visible), f"ASCII digit in prose {page['path']}")
        faq = [n for s in d.scripts for n in schema_nodes(s) if n.get('@type') == 'FAQPage']
        check(len(faq) == 1, f"Exactly one FAQPage required {page['path']}")
        pairs = faq[0]['mainEntity']
        check(len(pairs) >= 5, f"At least five FAQs {page['path']}")
        for q in pairs:
            question = ' '.join(html.unescape(q['name']).split())
            answer = Document(q['acceptedAnswer']['text']).visible
            check(question in d.visible and answer in d.visible, f"FAQ not visible verbatim {page['path']}: {question}")
        counts[page['path']] = d.words
    redirects = read_csv('redirect-map.csv')
    pairs = {urlsplit(r['old_url']).path.rstrip('/'): urlsplit(r['new_url']).path for r in redirects}
    check(len(pairs) == len(redirects), 'Duplicate redirect sources')
    check(pairs.get('/service-thesis') == '/thesis/', 'Required thesis redirect')
    check(pairs.get('/service-proposal') == '/proposal/', 'Required proposal redirect')
    check(pairs.get('/service-project') == '/student-projects/', 'Consolidate project intent')
    for row in redirects:
        check(row['reason'].strip(), 'Redirect requires reviewed reason')
        for key in ('old_url', 'new_url'):
            u = urlsplit(row[key])
            check(u.scheme == 'https' and u.hostname == 'teznevise.ir' and not u.query and not u.fragment, 'Redirect must be exact first-party HTTPS path')
        check(urlsplit(row['new_url']).path.rstrip('/') not in pairs, 'Redirect chain or cycle in map')
        check(urlsplit(row['old_url']).path not in KEYWORDS, 'Money target cannot redirect')
    links = read_csv('internal-links.csv')
    check(len(links) > 54, 'Expected blogs plus calculator/download link sources')
    exact = 0
    seen = set()
    for row in links:
        check(urlsplit(row['target_url']).path in KEYWORDS, 'Internal link target outside money map')
        check(urlsplit(row['source_url']).hostname == 'teznevise.ir', 'Non-site link source')
        key = (row['source_url'], row['target_url'], row['anchor'])
        check(key not in seen and row['anchor'].strip(), 'Duplicate/empty link')
        seen.add(key)
        exact += row['anchor'].strip() in KEYWORDS.values()
    check(exact / len(links) <= 0.05, 'Exact-match anchors exceed 5%')
    sources = {r['source_url'] for r in links}
    posts = read_csv('rest-post-inventory.csv')
    check(len(posts) == 54 and len({p['post_id'] for p in posts}) == 54, 'Expected 54 independently inventoried blog posts')
    check({p['source_url'] for p in posts} <= sources, 'Missing blog post link coverage')
    rewrites = read_csv('exact-anchor-rewrites.csv')
    rewrite_set = {(r['source_url'], r['target_url'], r['old_anchor']) for r in rewrites}
    for row in rewrites:
        check(row['source_url'] in sources and row['new_anchor'] not in KEYWORDS.values(), 'Invalid exact-anchor rewrite')
    existing = total = residual = 0
    with gzip.open(PACKAGE / 'audit/crawl-edges.csv.gz', 'rt', encoding='utf-8', newline='') as f:
        for row in csv.DictReader(f):
            if row['source_url'] not in sources or row['target_url'] not in {'https://teznevise.ir' + p for p in KEYWORDS}: continue
            total += 1
            anchor = ' '.join(html.unescape(row['anchor']).replace('\u200c', ' ').split())
            if anchor in KEYWORDS.values():
                existing += 1
                residual += (row['source_url'], row['target_url'], anchor) not in rewrite_set
    check((residual + exact) / (total + len(links)) <= 0.05, 'Combined existing and proposed exact-match anchors exceed 5%')
    outbound = read_csv('bluethesis-links.csv')
    check(len({r['source_url'] for r in outbound}) == 12 and len(outbound) == 13, 'Expected all 13 outbound anchors on 12 pages')
    # Scan all package additions, not the unrelated legacy theme copy.
    paths = list(PACKAGE.rglob('*')) + list((ROOT / 'scripts/seo-20261110').rglob('*'))
    paths += list((ROOT / 'inc').glob('seo-onpage-20261110*.php'))
    paths += [ROOT / 'inc/seo-gsc-20261110.php', ROOT / 'audit/gsc-indexing-20261110.csv']
    for path in paths:
        if path.is_file() and '__pycache__' not in str(path):
            content = gzip.open(path, 'rt', encoding='utf-8').read() if path.suffix == '.gz' else path.read_text()
            check('\u200c' not in content, f"Forbidden half space: {path.relative_to(ROOT)}")
    print(json.dumps({'result': 'PASS', 'visible_words': counts, 'redirects': len(redirects), 'internal_links': len(links), 'exact_match_anchors': exact, 'exact_match_percent': round(exact / len(links) * 100, 2), 'blog_posts_covered': len(posts), 'existing_exact_anchors_rewritten': existing - residual, 'combined_projected_exact_match_percent': round((residual + exact) / (total + len(links)) * 100, 2)}, ensure_ascii=False, indent=2))
    return manifest

def live(manifest, base):
    """Rendered checks, for use only after owner approves an installation."""
    for page in manifest['pages']:
        url = urljoin(base.rstrip('/') + '/', page['path'].lstrip('/'))
        response = urllib.request.urlopen(url, timeout=40)
        d = Document(response.read().decode('utf-8'))
        check(response.status == 200 and urlsplit(response.url).path == page['path'], f"Unexpected status/redirect: {url}")
        check(''.join(d.title).strip() == page['seo_title'], f"Title mismatch {url}")
        check(d.descriptions == [page['description']], f"Duplicate/wrong descriptions {url}")
        check(d.h1 == 1, f"Expected one H1 {url}")
        check(d.canonicals == [url], f"Canonical mismatch {url}")
        if page['path'] == '/':
            home = Document((PACKAGE / page['content_file']).read_text())
            check(home.visible in d.visible, f'Homepage resource introduction is not rendered {url}')
            check(page['hero']['hero_title_1'] in d.visible, f'Homepage hero not updated {url}')
        if page['path'] != '/':
            faq = [n for s in d.scripts for n in schema_nodes(s) if n.get('@type') == 'FAQPage']
            check(len(faq) == 1, f"Rendered FAQ duplication {url}")
            check(not any('noindex' in r for r in d.robots), f'Noindex HTML robots {url}')
            check('noindex' not in response.headers.get('X-Robots-Tag', ''), f"Noindex HTTP header {url}")
            for q in faq[0]['mainEntity']:
                check(q['name'] in d.visible and Document(q['acceptedAnswer']['text']).visible in d.visible, f"Rendered FAQ mismatch {url}")
        print('LIVE PASS', url)
    total = exact = 0
    for source in sorted({r['source_url'] for r in read_csv('internal-links.csv')}):
        url = urljoin(base, urlsplit(source).path)
        d = Document(urllib.request.urlopen(url, timeout=40).read().decode('utf-8'))
        expected = [r for r in read_csv('internal-links.csv') if r['source_url'] == source]
        for row in expected:
            check(any(urlsplit(a.get('href', '')).path == urlsplit(row['target_url']).path and ' '.join(a['text'].split()) == row['anchor'] for a in d.links), f"Missing descriptive link on {url}")
        for a in d.links:
            dest = urlsplit(urljoin(url, a.get('href', '')))
            if dest.hostname == urlsplit(base).hostname and dest.path in KEYWORDS:
                total += 1
                exact += ' '.join(a['text'].replace('\u200c', ' ').split()) in KEYWORDS.values()
    check(total > 0 and exact / total <= 0.05, f'Rendered combined exact anchors {exact}/{total} exceed 5%')
    for row in read_csv('bluethesis-links.csv'):
        url = urljoin(base, urlsplit(row['source_url']).path)
        d = Document(urllib.request.urlopen(url, timeout=40).read().decode('utf-8'))
        matches = [a for a in d.links if urlsplit(a.get('href', '')).hostname in ('bluethesis.ir', 'www.bluethesis.ir') and urlsplit(a.get('href', '')).path.rstrip('/') == '/statistical-analysis']
        check(all('nofollow' in a.get('rel', '').lower().split() for a in matches), f'Outbound nofollow missing {url}')
    class NoRedirect(urllib.request.HTTPRedirectHandler):
        def redirect_request(self, req, fp, code, msg, headers, newurl): return None
    opener = urllib.request.build_opener(NoRedirect())
    for row in read_csv('redirect-map.csv'):
        source = urljoin(base, urlsplit(row['old_url']).path)
        target = urljoin(base, urlsplit(row['new_url']).path)
        try: response = opener.open(source, timeout=40)
        except HTTPError as error: response = error
        check(response.status == 301 and urljoin(source, response.headers.get('Location', '')) == target, f'Expected direct 301 {source} -> {target}')
        final = urllib.request.urlopen(target, timeout=40)
        check(final.status == 200 and final.url == target, f'Redirect target not direct 200 {target}')
    print(f'LIVE links, nofollow and redirects PASS; combined exact anchors {exact}/{total}')

if __name__ == '__main__':
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--live', metavar='BASE_URL', help='Verify owner-approved staging or production installation')
    args = parser.parse_args()
    try:
        manifest = offline()
        if args.live: live(manifest, args.live)
    except (AssertionError, OSError, ValueError) as exc:
        print('FAIL:', exc, file=sys.stderr)
        sys.exit(1)
