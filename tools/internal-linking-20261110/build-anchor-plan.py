#!/usr/bin/env python3
"""Rebuild deterministic primary/LSI assignments from reviewed on-site phrases."""
import csv
import hashlib
import json
import math
from collections import defaultdict
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
DATA = ROOT / 'data/internal-linking-20261110'
KEYWORDS = {
    '/thesis/': 'انجام پایان نامه',
    '/thesis/master/': 'انجام پایان نامه کارشناسی ارشد',
    '/thesis/phd/': 'انجام پایان نامه دکتری',
    '/proposal/': 'انجام پروپوزال',
    '/proposal/phd/': 'انجام پروپوزال دکتری',
    '/service-project/': 'انجام پروژه های دانشجویی',
}
PRIORITY = {
    '/thesis/': [1208, 8443, 7510, 8324, 719, 760, 9440, 8119, 8175],
    '/thesis/master/': [947, 8187, 750, 8227, 8233, 8280, 9440, 8147],
    '/thesis/phd/': [1006],
    '/proposal/': [8441, 768, 8123, 8179, 8181, 719, 760],
    '/proposal/phd/': [8502],
    '/service-project/': [923, 8222, 1678],
}


def write_csv(name, fields, rows):
    with (DATA / name).open('w', newline='') as handle:
        writer = csv.DictWriter(handle, fieldnames=fields, lineterminator='\n')
        writer.writeheader()
        writer.writerows(rows)


def generic_pool(path, phrases):
    if path == '/thesis/master/':
        return [p for p in phrases if not any(t in p for t in ('دفاع', 'روش تحقیق', 'فصل های'))]
    if path == '/service-project/':
        return [p for p in phrases if not any(t in p for t in ('مهندسی', 'VHDL', 'شبیه سازی'))]
    if path == '/proposal/phd/':
        return [p for p in phrases if p != 'پروپوزال پژوهش دکتری']
    return phrases


def contextual_pool(path, phrases, source):
    topic = (source.get('title', '') + ' ' + source['url']).lower()
    pool = list(generic_pool(path, phrases))
    for phrase in phrases:
        relevant = (
            ('دفاع' in phrase and any(t in topic for t in ('defence', 'دفاع')))
            or ('روش تحقیق' in phrase and any(t in topic for t in ('method', 'research-methods', 'روش تحقیق')))
            or ('فصل های' in phrase and any(t in topic for t in ('chapter', 'فصل')))
            or ('VHDL' in phrase and 'vhdl' in topic)
            or ('مهندسی شیمی' in phrase and any(t in topic for t in ('chemical', 'شیمی')))
            or ('پروژه های مهندسی' == phrase and any(t in topic for t in ('gams', 'matrix', 'engineering', 'مهندسی')))
            or ('شبیه سازی' in phrase and any(t in topic for t in ('simulation', 'شبیه سازی')))
        )
        if relevant and phrase not in pool:
            pool.append(phrase)
    return pool


def main():
    manifest = json.loads((DATA / 'manifest.json').read_text())
    candidates = defaultdict(list)
    for row in csv.DictReader((DATA / 'lsi-map.csv').open()):
        candidates[row['keyword']].append(row['lsi phrase'])

    # One relevant primary continuation on each of these existing pages. This
    # supplies useful related-service pathways, without adding repeated links.
    by_id = {source['id']: source for source in manifest['sources']}
    phd = by_id[1053]
    phd['type'] = 'related-service'
    if not any(link['target'].endswith('/proposal/phd/') for link in phd['links']):
        phd['links'].append({'anchor': KEYWORDS['/proposal/phd/'], 'target': 'https://teznevise.ir/proposal/phd/', 'context': 'پیش از شروع رساله، سهم علمی و طرح اجرا باید روشن باشد؛ صفحه {anchor} به تنظیم این تصمیم ها در طرح دکتری می پردازد.', 'activation': 'immediate'})
    if 1678 not in by_id:
        manifest['sources'].append({'id': 1678, 'url': 'https://teznevise.ir/gams/', 'type': 'related-service', 'title': 'انجام پروژه GAMS', 'links': [{'anchor': KEYWORDS['/service-project/'], 'target': 'https://teznevise.ir/service-project/', 'context': 'برای تعریف خروجی مدل GAMS، داده های ورودی و معیار بررسی نتیجه، صفحه {anchor} مسیر برنامه ریزی و بازبینی پروژه را توضیح می دهد.', 'activation': 'immediate'}]})

    grouped = defaultdict(list)
    for source in manifest['sources']:
        for link in source['links']:
            path = link['target'].removeprefix('https://teznevise.ir')
            if path in KEYWORDS:
                grouped[path].append((source, link))

    # Reserve the smallest pools first. One source can own at most one exact
    # money anchor overall, which is stricter than merely one per target.
    allocations, used_exact_sources = {}, set()
    order = ['/thesis/phd/', '/proposal/phd/', '/service-project/', '/thesis/master/', '/thesis/', '/proposal/']
    for path in order:
        ranks = {ident: i for i, ident in enumerate(PRIORITY[path])}
        eligible = [pair for pair in grouped[path] if pair[0]['id'] not in used_exact_sources]
        eligible.sort(key=lambda pair: (ranks.get(pair[0]['id'], 999), hashlib.sha256((KEYWORDS[path] + ':' + str(pair[0]['id'])).encode()).hexdigest()))
        chosen = {source['id'] for source, _ in eligible[:math.ceil(len(grouped[path])/5)]}
        assert len(chosen) == math.ceil(len(grouped[path])/5)
        allocations[path] = chosen
        used_exact_sources.update(chosen)

    policies, ratios, primary_rows = [], [], []
    for path, keyword in KEYWORDS.items():
        pairs = grouped[path]
        number = len(pairs)
        exact_count = math.ceil(number / 5)
        assert 3 <= (number - exact_count) / exact_count <= 4
        ranks = {ident: i for i, ident in enumerate(PRIORITY[path])}
        exact_ids = allocations[path]
        phrases = candidates[keyword]
        assert 5 <= len(phrases) <= 10
        for source, link in pairs:
            pool = contextual_pool(path, phrases, source)
            role = 'exact-primary' if source['id'] in exact_ids else 'lsi'
            index = int(hashlib.sha256((str(source['id']) + ':' + keyword).encode()).hexdigest(), 16) % len(pool)
            link.update(exact_keyword=keyword, anchor_role=role, lsi_phrases=pool)
            link['anchor'] = keyword if role == 'exact-primary' else pool[index]
            for prefix in ('راهنمای', 'مراحل', 'مسیر', 'نقشه'):
                link['context'] = link['context'].replace(prefix + ' {anchor}', 'صفحه {anchor}')
            if role == 'exact-primary':
                primary_rows.append({'keyword': keyword, 'source_url': source['url'], 'target': link['target'], 'reason': 'Verified guide/hub priority' if source['id'] in ranks else 'Deterministic allocation among relevant enrolled sources'})
        policies.append({'keyword': keyword, 'target': 'https://teznevise.ir' + path, 'lsi_phrases': phrases, 'secondary_lsi_phrases': generic_pool(path, phrases), 'designated_exact_source_ids': sorted(exact_ids), 'activation': 'master-published' if path == '/thesis/master/' else 'immediate'})
        ratios.append({'keyword': keyword, 'target': 'https://teznevise.ir' + path, 'stage': 'after_separate_master_publication' if path == '/thesis/master/' else 'immediate', 'planned_primary_exact': exact_count, 'planned_primary_lsi': number-exact_count, 'lsi_per_exact': format((number-exact_count)/exact_count, '.2f'), 'scope': 'Selected primary links only; retained secondary links are additional LSI and reported separately', 'within_target_range': 'yes'})

    manifest.update(schema_version=2, anchor_policy=policies, anchor_policy_note='Exact anchors only on designated primary source pages; all other enrolled-source money links use reviewed LSI. Target profile approximately one exact to three or four LSI; do not manufacture secondary links to force a ratio.')
    (DATA / 'manifest.json').write_text(json.dumps(manifest, ensure_ascii=False, indent=2) + '\n')
    links = []
    for source in manifest['sources']:
        for link in source['links']:
            role = link.get('anchor_role', 'supporting-exact')
            action = 'ensure_primary_' + role.replace('-', '_')
            if link['activation'] == 'master-published':
                action += '_after_master_publication'
            links.append({'source_url': source['url'], 'anchor': link['anchor'], 'target': link['target'], 'action': action})
    write_csv('links.csv', ['source_url', 'anchor', 'target', 'action'], links)
    write_csv('anchor-ratios.csv', list(ratios[0]), ratios)
    write_csv('primary-exact-sources.csv', list(primary_rows[0]), primary_rows)
    print(f'Built {len(links)} selected links on {len(manifest["sources"])} enrolled sources; {sum(r["planned_primary_exact"] for r in ratios)} exact money primaries.')


if __name__ == '__main__':
    main()
