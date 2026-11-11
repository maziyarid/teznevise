#!/usr/bin/env python3
"""Validate consistency of the held wave inputs; never creates content."""
import csv
import json
from pathlib import Path

root=Path(__file__).resolve().parents[2]/'seo/20261110/content'
m=json.loads((root/'manifest.json').read_text())
rows=m['pages'];paths={p['path'] for p in rows}
assert len(paths)==len(rows), 'Duplicate paths'
assert sorted(p['build_order'] for p in rows)==list(range(1,len(rows)+1)), 'Conflicting build ordering'
assert all(p['legal_review']['status']=='not done' and not p['publishable'] for p in rows)
assert all(p['content_file'] is None and not p['future_wordpress_draft']['write_allowed_now'] for p in rows)
assert not (root/'pages').exists(), 'PR-A must not include page drafts'
gams=[p for p in rows if p['id']=='P-GAMS'];assert len(gams)==1 and gams[0]['wave']==3
for page in rows:
    assert page['primary_parent']!=page['secondary_parent'], 'Duplicate parent'
    assert page['future_wordpress_draft']['status']=='draft'
    assert page['future_wordpress_draft']['rank_math_title']==page['future_wordpress_draft']['rank_math_description']==''
graph=list(csv.DictReader((root/'link-graph.csv').open()));budget={}
for edge in graph:
    assert edge['source'] in paths and edge['region']=='main'
    assert edge['target_state'] in ('live200','planned','deferred')
    if edge['anchor_type']=='exact':
        assert edge['target']==edge['primary_parent'], 'Only the primary parent gets an exact money anchor'
        budget[edge['source']]=budget.get(edge['source'],0)+1
    if edge['target']=='/thesis/master/':assert edge['target_state']=='deferred'
assert all(v<=1 for v in budget.values())
pack={p['id']:p for p in (json.loads(l) for l in (root/'data-pack-corrected.jsonl').read_text().splitlines())}
assert '56 impr' not in pack['P-ELEC']['input']['شاهد']
assert '51 impr' not in pack['P-SILVACO']['input']['شاهد']
assert '23 impr' not in pack['P-BACHELOR']['input']['شاهد']
print(f'PASS: {len(rows)} held input records; {len(graph)} proposed main-content links; zero page bodies or authorized writes')
