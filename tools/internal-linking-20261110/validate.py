#!/usr/bin/env python3
"""Offline data contracts; never connects to WordPress."""
import csv
import json
from collections import Counter
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
for source in manifest['sources']:
    assert source['id'] not in seen
    seen.add(source['id'])
    assert source['url'].startswith('https://teznevise.ir/')
    if source['type'] in ('tool', 'download', 'download-hub'):
        assert 1 <= len(source['links']) <= 2
        assert any(link['activation'] == 'immediate' for link in source['links'])
    targets = set()
    for link in source['links']:
        assert link['target'] == 'https://teznevise.ir' + owners[link['anchor']]
        assert link['target'] not in targets
        targets.add(link['target'])
        assert link['context'].count('{anchor}') == 1
        assert link['activation'] == ('master-published' if link['target'].endswith('/thesis/master/') else 'immediate')
        assert not any('0' <= c <= '9' for c in link['context']), link['context']
expected = {1208: ['/thesis/','/service-project/'], 8443: ['/thesis/','/service-simulation/'],7510:['/thesis/'],8324:['/thesis/'],8441:['/proposal/'],8502:['/proposal/phd/'],8429:['/thesis/chapter-four/'],8401:['/service-article/']}
by_id = {s['id']: s for s in manifest['sources']}
for ident, paths in expected.items():
    assert set('https://teznevise.ir'+p for p in paths) <= {l['target'] for l in by_id[ident]['links']}
phd_proposal = next(s for s in manifest['sources'] if s['url'] == 'https://teznevise.ir/proposal/phd/')
assert any(l['target'] == 'https://teznevise.ir/thesis/phd/' for l in phd_proposal['links'])
assert manifest['master']['parent_id'] == 947
assert manifest['master']['path'] == '/thesis/master/'
assert len(manifest['blue_links']) == 13
assert len({x['source_id'] for x in manifest['blue_links']}) == 12
inventory = list(csv.DictReader((DATA / 'inventory.csv').open()))
assert len(inventory) == 180
assert Counter(x['decision'] for x in inventory) == {'include':165, 'exclude_redirect_alias':15}
assert {int(x['id']) for x in inventory if x['decision']=='include'} <= seen
csv_links = list(csv.DictReader((DATA / 'links.csv').open()))
assert len(csv_links) == sum(len(s['links']) for s in manifest['sources'])
for directory in (DATA, ROOT / 'tools/internal-linking-20261110'):
    for file in directory.rglob('*'):
        if file.is_file() and '__pycache__' not in str(file):
            assert bytes([0xe2, 0x80, 0x8c]) not in file.read_bytes(), file
print('PASS: inventory, sole owners, exact anchors, gates, verified map, CSV parity, Persian text')
