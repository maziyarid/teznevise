#!/usr/bin/env python3
"""Verify the exact 84 dead project URLs and their source-backed decisions, read-only."""
import argparse
from collections import Counter
import csv
import importlib.util
import json
from pathlib import Path
from urllib.parse import urlsplit

ROOT = Path(__file__).resolve().parents[2]
AUDIT = ROOT / 'seo/20261110/audit'
SPEC = importlib.util.spec_from_file_location('project_gsc_verifier', Path(__file__).with_name('verify-gsc.py'))
GSC = importlib.util.module_from_spec(SPEC)
SPEC.loader.exec_module(GSC)


def read(path):
    with path.open(encoding='utf-8', newline='') as file: return list(csv.DictReader(file))


def inputs(inventory_path, decisions_path, targets_path=None):
    catalog = read(inventory_path)
    decisions = read(decisions_path)
    targets = read(targets_path or AUDIT / 'project-dead-targets-20261110.csv')
    target_proofs = {row['target_url']: row for row in targets}
    by_url = {row['legacy_url']: row for row in catalog}
    errors = []
    if len(target_proofs) != len(targets): errors.append('Duplicate direct-target evidence URL')
    if len(catalog) != 135 or len(by_url) != 135: errors.append('Expected exactly 135 unique GSC performance URLs')
    dead = {url for url, row in by_url.items() if row['initial_status'] == '404'}
    if len(dead) != 84: errors.append('Expected exactly 84 initial 404 URLs in the performance cohort')
    if len(decisions) != 84 or {row['request_url'] for row in decisions} != dead:
        errors.append('Decision map must cover each of the 84 dead URLs exactly once')
    if len({row['row_id'] for row in decisions}) != len(decisions): errors.append('Duplicate decision row ID')
    inventory = []
    for decision in decisions:
        url = decision['request_url']
        observed = by_url.get(url)
        if not observed:
            errors.append('Decision outside authoritative inventory: ' + url)
            continue
        if urlsplit(url).path.startswith('/project/') is False or GSC.url_error(url, allow_stage=False): errors.append('Unsafe project URL: ' + url)
        if decision['action'] not in ('301', 'restore'): errors.append('Unsupported project decision: ' + url)
        if not decision['reason'] or decision.get('unresolved_reason'): errors.append('Missing rationale or unresolved project decision: ' + url)
        if decision['action'] == '301':
            target = decision['target_url']
            known_target = by_url.get(target)
            known_nonterminal = known_target and (known_target['initial_status'] != '200' or known_target['terminal_status'] != '200' or known_target['final_url'] != target)
            if GSC.url_error(target, allow_stage=False) or urlsplit(target).path == '/' or target in dead or known_nonterminal:
                errors.append('Unsafe, chained or homepage redirect: ' + url)
            proof = target_proofs.get(target)
            if (not proof or proof.get('initial_status') != '200' or proof.get('terminal_status') != '200'
                    or proof.get('final_url') != target or proof.get('canonical_url') != target
                    or proof.get('robots_allowed', '').lower() not in ('1', 'true')
                    or 'noindex' in (proof.get('robots_meta', '') + ' ' + proof.get('x_robots_tag', '')).lower()
                    or proof.get('fetch_error')):
                errors.append('Missing or invalid direct-200 self-canonical indexable target evidence: ' + url)
        if not decision.get('source_evidence', '').strip(): errors.append('Missing WordPress/repo source evidence: ' + url)
        inventory.append({
            'row_id': decision['row_id'], 'source_bucket': decision['source_bucket'], 'request_url': url,
            'original_input_json': json.dumps(url, ensure_ascii=True), 'initial_status': observed['initial_status'],
            'terminal_status': observed['terminal_status'], 'final_url': observed['final_url'], 'redirect_chain_json': '[]',
            'canonical_url': observed['canonical_url'], 'robots_meta': observed['robots_meta'], 'x_robots_tag': observed['x_robots_tag'],
            'robots_allowed': observed['robots_allowed'], 'fetch_error': observed['fetch_error'],
        })
    # The common validator checks canonical expectations, URL safety and intended graph loops.
    errors.extend(GSC.check_inputs(inventory, decisions))
    return inventory, decisions, list(dict.fromkeys(errors))


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--inventory', type=Path, default=AUDIT / 'project-legacy-inventory-20261110.csv')
    parser.add_argument('--decisions', type=Path, default=AUDIT / 'project-dead-decisions-20261110.csv')
    parser.add_argument('--check-map', action='store_true', help='Offline exact coverage and no-chain checks')
    parser.add_argument('--snapshot-only', action='store_true', help='Re-fetch the pre-apply baseline, without claiming new redirects are live')
    parser.add_argument('--base-url', help='Explicit owner staging HTTPS origin')
    parser.add_argument('--workers', type=int, default=4)
    parser.add_argument('--output-prefix', type=Path, default=ROOT / 'work/seo-20261110/project-verification')
    args = parser.parse_args()
    if not 1 <= args.workers <= 8: parser.error('Use 1 to 8 workers')
    if args.base_url and (args.snapshot_only or args.check_map): parser.error('Staging origin is for live acceptance only')
    inventory, decisions, errors = inputs(args.inventory, args.decisions)
    counts = Counter(row['action'] for row in decisions)
    if errors or args.check_map:
        result = {'result': 'FAIL' if errors else 'PASS', 'dead_urls': len(inventory), 'restored': counts['restore'], 'redirected': counts['301'], 'unresolved': sum(bool(r.get('unresolved_reason')) for r in decisions), 'errors': errors}
        print(json.dumps(result, ensure_ascii=True, indent=2))
        return int(bool(errors))
    origin = GSC.parse_base_url(args.base_url) if args.base_url else None
    GSC.STAGING_ORIGIN = origin
    tested_inventory = [GSC.mapped_inventory(row, origin) for row in inventory] if origin else inventory
    tested_decisions = [GSC.mapped_decision(row, origin) for row in decisions] if origin else decisions
    observations = GSC.observe_all(tested_inventory, args.workers, 25)
    by_id = {row['row_id']: row for row in tested_decisions}
    rows = []
    for original, tested in zip(inventory, tested_inventory):
        row = GSC.evaluate(tested, by_id[tested['row_id']], observations[tested['request_url']], snapshot_only=args.snapshot_only)
        row['request_url'] = original['request_url']
        row['tested_url'] = tested['request_url']
        rows.append(row)
    passed = sum(row['result'] == 'PASS' for row in rows)
    report = {'fetched_utc': GSC.utc_now(), 'mode': 'snapshot-only' if args.snapshot_only else 'post-apply-expectations', 'base_url': origin,
              'summary': {'result': 'PASS' if passed == len(rows) else 'FAIL', 'dead_urls': len(rows), 'pass': passed, 'fail': len(rows) - passed, 'restored': counts['restore'], 'redirected': counts['301'], 'unresolved': sum(bool(r.get('unresolved_reason')) for r in decisions)},
              'rows': rows}
    GSC.write_reports(args.output_prefix, report)
    print(json.dumps({**report['summary'], 'mode': report['mode'], 'json_report': str(args.output_prefix.with_suffix('.json')), 'csv_report': str(args.output_prefix.with_suffix('.csv'))}, ensure_ascii=True, indent=2))
    return int(passed != len(rows))


if __name__ == '__main__': raise SystemExit(main())
