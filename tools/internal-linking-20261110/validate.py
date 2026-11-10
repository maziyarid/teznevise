#!/usr/bin/env python3
"""Offline data contracts; never connects to WordPress."""
import csv
import json
from collections import Counter
from html.parser import HTMLParser
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
DATA = ROOT / 'data/internal-linking-20261110'
manifest = json.loads((DATA / 'manifest.json').read_text())
owners = {
    'انجام پایان نامه': '/thesis/',
    'انجام پایان نامه کارشناسی ارشد': '/thesis/master/',
    'انجام پایان نامه دکتری': '/thesis/phd/',
    'انجام پروپوزال': '/proposal/',
    'انجام پروپوزال دکتری': '/proposal/phd/',
    'انجام پروژه های دانشجویی': '/service-project/',
    'انجام فصل چهارم پایان نامه': '/thesis/chapter-four/',
    'انجام مقاله': '/service-article/',
    'انجام شبیه سازی': '/service-simulation/',
}
seen = set()
lsi_map = list(csv.DictReader((DATA / 'lsi-map.csv').open()))
assert list(lsi_map[0]) == ['keyword', 'lsi phrase', 'source', 'GSC impressions if known']
lsi_by_keyword = {}
for row in lsi_map:
    lsi_by_keyword.setdefault(row['keyword'], []).append(row['lsi phrase'])
    assert 'https://teznevise.ir/' in row['source']
    assert row['GSC impressions if known'] == ''  # No query-level export found.
    assert row['lsi phrase'] != row['keyword']
    assert 2 <= len(row['lsi phrase'].split()) <= 6
    assert row['lsi phrase'] != 'اینجا'
    if 'پایان نامه' in row['keyword']:
        assert 'مشاوره' not in row['lsi phrase']
assert len(lsi_by_keyword) == 6
assert all(5 <= len(set(phrases)) == len(phrases) <= 10 for phrases in lsi_by_keyword.values())
policies = {p['target']: p for p in manifest['anchor_policy']}
assert len(policies) == 6
for target, policy in policies.items():
    assert target == 'https://teznevise.ir' + owners[policy['keyword']]
    assert policy['lsi_phrases'] == lsi_by_keyword[policy['keyword']]
    assert set(policy['secondary_lsi_phrases']) <= set(policy['lsi_phrases'])
    assert policy['secondary_lsi_phrases']
    assert len(policy['designated_exact_source_ids']) == len(set(policy['designated_exact_source_ids']))
actual_ratios = {}
for source in manifest['sources']:
    assert source['id'] not in seen
    seen.add(source['id'])
    assert source['url'].startswith('https://teznevise.ir/')
    if source['type'] in ('tool', 'download', 'download-hub'):
        assert 1 <= len(source['links']) <= 2
        assert any(link['activation'] == 'immediate' for link in source['links'])
    targets = set()
    exact_money_count = 0
    for link in source['links']:
        if link['target'] in policies:
            policy = policies[link['target']]
            assert link['exact_keyword'] == policy['keyword']
            designated = source['id'] in policy['designated_exact_source_ids']
            assert link['anchor_role'] == ('exact-primary' if designated else 'lsi')
            assert link['anchor'] == policy['keyword'] if designated else link['anchor'] in policy['lsi_phrases']
            assert set(link['lsi_phrases']) <= set(policy['lsi_phrases'])
            exact_money_count += designated
            counts = actual_ratios.setdefault(link['target'], Counter())
            counts[link['anchor_role']] += 1
        else:
            assert link['target'] == 'https://teznevise.ir' + owners[link['anchor']]
        assert link['target'] not in targets
        targets.add(link['target'])
        assert link['context'].count('{anchor}') == 1
        assert link['activation'] == ('master-published' if link['target'].endswith('/thesis/master/') else 'immediate')
        assert not any('0' <= c <= '9' for c in link['context']), link['context']
    assert exact_money_count <= 1, source['url']
for target, counts in actual_ratios.items():
    assert counts['exact-primary'] == len(policies[target]['designated_exact_source_ids'])
    assert 3 <= counts['lsi'] / counts['exact-primary'] <= 4
ratio_rows = list(csv.DictReader((DATA / 'anchor-ratios.csv').open()))
assert len(ratio_rows) == 6
for row in ratio_rows:
    counts = actual_ratios[row['target']]
    assert int(row['planned_primary_exact']) == counts['exact-primary']
    assert int(row['planned_primary_lsi']) == counts['lsi']
expected = {1208: ['/thesis/','/service-project/'], 8443: ['/thesis/','/service-simulation/'],7510:['/thesis/'],8324:['/thesis/'],8441:['/proposal/'],8502:['/proposal/phd/'],8429:['/thesis/chapter-four/'],8401:['/service-article/']}
by_id = {s['id']: s for s in manifest['sources']}
for ident, paths in expected.items():
    assert set('https://teznevise.ir'+p for p in paths) <= {l['target'] for l in by_id[ident]['links']}
phd_proposal = next(s for s in manifest['sources'] if s['url'] == 'https://teznevise.ir/proposal/phd/')
assert any(l['target'] == 'https://teznevise.ir/thesis/phd/' for l in phd_proposal['links'])
assert manifest['master']['parent_id'] == 947
assert manifest['master']['path'] == '/thesis/master/'
class DraftAnchors(HTMLParser):
    def __init__(self):
        super().__init__()
        self.current = None
        self.links = []
    def handle_starttag(self, tag, attrs):
        if tag == 'a':
            self.current = [dict(attrs)['href'], '']
    def handle_data(self, text):
        if self.current is not None:
            self.current[1] += text
    def handle_endtag(self, tag):
        if tag == 'a' and self.current is not None:
            self.links.append(self.current)
            self.current = None
draft = DraftAnchors()
draft.feed((DATA / manifest['master']['content_file']).read_text())
assert len(draft.links) == 3
for target, label in draft.links:
    assert label in policies[target]['lsi_phrases'], 'Draft outgoing links are non-designated LSI.'
assert len(manifest['blue_links']) == 13
assert len({x['source_id'] for x in manifest['blue_links']}) == 12
inventory = list(csv.DictReader((DATA / 'inventory.csv').open()))
assert len(inventory) == 180
assert Counter(x['decision'] for x in inventory) == {'include':165, 'exclude_redirect_alias':15}
assert {int(x['id']) for x in inventory if x['decision']=='include'} <= seen
csv_links = list(csv.DictReader((DATA / 'links.csv').open()))
assert len(csv_links) == sum(len(s['links']) for s in manifest['sources'])
assert [(r['source_url'], r['anchor'], r['target']) for r in csv_links] == [(s['url'], l['anchor'], l['target']) for s in manifest['sources'] for l in s['links']]
for directory in (DATA, ROOT / 'tools/internal-linking-20261110'):
    for file in directory.rglob('*'):
        if file.is_file() and '__pycache__' not in str(file):
            assert bytes([0xe2, 0x80, 0x8c]) not in file.read_bytes(), file
print('PASS: inventory, sole owners, reviewed LSI evidence, designated exact sources, ratios, gates, verified map, CSV parity, Persian text')
