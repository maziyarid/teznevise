#!/usr/bin/env python3
"""Offline GSC verifier regression cases; all HTTP responses are in-memory.

Project: Teznevise WordPress Theme | Author: MAZ//ID (Maziyar)
Brand: MΛZ — https://github.com/maziyarid/M-Z
"""
from copy import deepcopy
from email.message import Message
import importlib.util
import subprocess
import sys
import tempfile
from pathlib import Path
import unittest
from unittest.mock import patch

SPEC = importlib.util.spec_from_file_location('verify_gsc', Path(__file__).with_name('verify-gsc.py'))
GSC = importlib.util.module_from_spec(SPEC)
SPEC.loader.exec_module(GSC)
SOURCE = 'https://teznevise.ir/old/?a=%2f&b=one+two&a=2'
TARGET = 'https://teznevise.ir/thesis/'


def observation(url=SOURCE):
    return {'request_url': url, 'initial_status': 200, 'terminal_status': 200,
            'final_url': url, 'redirect_chain': [], 'is_html': True,
            'canonical_urls': [url], 'noindex': [], 'robots_meta': [], 'x_robots_tag': [],
            'fetch_error': '', 'body_truncated': False, 'robots': {'allowed': True, 'error': ''},
            'source_robots': {'allowed': True, 'error': ''}}


def inventory(url=SOURCE, row_id='gsc-0001'):
    return {'row_id': row_id, 'request_url': url, 'source_bucket': 'not_found',
            'initial_status': '404', 'terminal_status': '404', 'final_url': url,
            'original_input_json': '"original supplied sample"'}


def decision(action='restore', url=SOURCE, row_id='gsc-0001'):
    row = {key: '' for key in GSC.DECISION_COLUMNS}
    row.update(row_id=row_id, request_url=url, source_bucket='not_found', action=action,
               reason='Reviewed useful page', expected_status='200', expected_robots='allow')
    return row


class FakeResponse:
    def __init__(self, code, headers, body=b''):
        self.code = code
        self.headers = Message()
        for key, value in headers.items():
            self.headers[key] = value
        self.body = body

    def read(self, limit):
        return self.body[:limit]

    def __enter__(self):
        return self

    def __exit__(self, *args):
        return False


class VerifierContract(unittest.TestCase):
    def test_exact_percent_query_and_literal_wildcard(self):
        self.assertEqual(GSC.transport_url(SOURCE), SOURCE)
        self.assertNotEqual(GSC.compare_url(SOURCE), GSC.compare_url(SOURCE.replace('%2f', '%2F')))
        self.assertNotEqual(GSC.compare_url(TARGET + '?'), GSC.compare_url(TARGET))
        literal = 'https://teznevise.ir/a/*/entry/?p=1&p=2'
        self.assertEqual(GSC.url_error(literal), '')
        self.assertEqual(GSC.transport_url(literal), literal)
        self.assertTrue(GSC.url_error('https://teznevise.ir/a b/'))
        self.assertTrue(GSC.url_error('https://teznevise.ir/a%XY/'))
        self.assertTrue(GSC.url_error('https://other.ir/'))

    def test_single_hop_redirect_target_is_200_self_canonical_indexable(self):
        old = inventory()
        reviewed = decision('301')
        reviewed.update(target_url=TARGET, expected_status='301', expected_canonical=TARGET)
        live = observation()
        live.update(initial_status=301, final_url=TARGET, canonical_urls=[TARGET],
                    redirect_chain=[{'url': SOURCE, 'status': 301, 'target': TARGET}])
        self.assertEqual(GSC.evaluate(old, reviewed, live)['result'], 'PASS')
        for field, bad in (('terminal_status', 404), ('canonical_urls', [SOURCE]),
                           ('noindex', ['header:*:noindex'])):
            changed = deepcopy(live)
            changed[field] = bad
            self.assertEqual(GSC.evaluate(old, reviewed, changed)['result'], 'FAIL')
        live['redirect_chain'].append({'url': TARGET, 'status': 301, 'target': TARGET})
        self.assertEqual(GSC.evaluate(old, reviewed, live)['result'], 'FAIL')

    def test_410_and_private_keep_do_not_require_indexability(self):
        old = inventory()
        reviewed = decision('410')
        reviewed.update(expected_status='410', expected_robots='ignore')
        live = observation()
        live.update(initial_status=410, terminal_status=410, canonical_urls=[],
                    noindex=['meta:robots:noindex'], robots={'allowed': False, 'error': ''})
        self.assertEqual(GSC.evaluate(old, reviewed, live)['result'], 'PASS')
        reviewed = decision('keep')
        reviewed.update(expected_status='302', expected_robots='ignore', reason='Private login path')
        live.update(initial_status=302, terminal_status=200, redirect_chain=[{'status': 302, 'target': TARGET}])
        self.assertEqual(GSC.evaluate(old, reviewed, live)['result'], 'PASS')
        reviewed['reason'] = ''
        self.assertEqual(GSC.evaluate(old, reviewed, live)['result'], 'FAIL')

    def test_noindex_checks_googlebot_meta_and_scoped_http_headers(self):
        parser = GSC.Head('<meta name="Googlebot" content="noindex,follow"><meta name="robots" content="index">')
        self.assertEqual(GSC.noindex_tokens(parser.robots, []), ['meta:googlebot:noindex'])
        self.assertEqual(GSC.noindex_tokens([], ['bingbot: noindex, nofollow']), [])
        self.assertEqual(GSC.noindex_tokens([], ['googlebot: noindex', 'none']), ['header:googlebot:noindex', 'header:*:none'])
        reviewed = decision('unblock|remove_noindex')
        live = observation()
        self.assertEqual(GSC.evaluate(inventory(), reviewed, live)['result'], 'PASS')
        live['noindex'] = ['header:*:noindex']
        self.assertEqual(GSC.evaluate(inventory(), reviewed, live)['result'], 'FAIL')

    def test_robots_allow_specificity_query_and_unavailability(self):
        rules = {'terminal_status': 200, 'fetch_error': '', 'body_truncated': False,
                 'robots_text': 'User-agent: *\nDisallow: /private/\nAllow: /private/public/\nDisallow: /*?download=\n'}
        self.assertFalse(GSC.robots_decision('https://teznevise.ir/private/a/', rules)['allowed'])
        self.assertTrue(GSC.robots_decision('https://teznevise.ir/private/public/a/', rules)['allowed'])
        self.assertFalse(GSC.robots_decision('https://teznevise.ir/a/?download=1', rules)['allowed'])
        rules['terminal_status'] = 503
        self.assertIsNone(GSC.robots_decision(TARGET, rules)['allowed'])
        rules['terminal_status'] = 404
        self.assertTrue(GSC.robots_decision(TARGET, rules)['allowed'])

    def test_inventory_coverage_duplicate_conflicts_and_redirect_chains(self):
        rows = [inventory(), inventory(TARGET, 'gsc-0002')]
        one = decision('301')
        one.update(target_url=TARGET, expected_status='301')
        two = decision('301', TARGET, 'gsc-0002')
        two.update(target_url='https://teznevise.ir/proposal/', expected_status='301')
        self.assertTrue(any('chain' in e for e in GSC.check_inputs(rows, [one, two])))
        self.assertTrue(any('no decision' in e for e in GSC.check_inputs(rows, [one])))
        duplicate = inventory(SOURCE, 'gsc-0003')
        conflict = decision('410', SOURCE, 'gsc-0003')
        conflict['expected_status'] = '410'
        self.assertTrue(any('Conflicting decisions for duplicate' in e for e in GSC.check_inputs([rows[0], duplicate], [one, conflict])))
        self.assertEqual(GSC.evaluate(rows[0], None, observation())['result'], 'FAIL')

    def test_fetch_chain_preserves_original_request_and_parses_target_headers(self):
        calls = []
        responses = [FakeResponse(301, {'Location': TARGET}),
                     FakeResponse(200, {'Content-Type': 'text/html; charset=utf-8', 'X-Robots-Tag': 'googlebot: noindex'},
                                  ('<html><head><link rel="canonical" href="' + TARGET + '"></head></html>').encode())]
        class Opener:
            def open(self, request, timeout):
                calls.append(request.full_url)
                return responses.pop(0)
        with patch.object(GSC, 'build_opener', return_value=Opener()):
            result = GSC.fetch_chain(SOURCE)
        self.assertEqual(calls, [SOURCE, TARGET])
        self.assertEqual(result['request_url'], SOURCE)
        self.assertEqual(result['initial_status'], 301)
        self.assertEqual(result['terminal_status'], 200)
        self.assertEqual(result['canonical_urls'], [TARGET])
        self.assertEqual(result['noindex'], ['header:googlebot:noindex'])

    def test_duplicate_rows_share_one_fetch_but_remain_separate_reports(self):
        rows = [inventory(), inventory(SOURCE, 'gsc-0002')]
        calls = []
        def fetch(url, timeout):
            calls.append(url)
            row = observation(url)
            row['robots_text'] = 'User-agent: *\nAllow: /\n'
            return row
        with patch.object(GSC, 'fetch_chain', side_effect=fetch):
            results = GSC.observe_all(rows, 1, 1)
        self.assertEqual(calls.count(SOURCE), 1)
        self.assertEqual(calls.count('https://teznevise.ir/robots.txt'), 1)
        reports = [GSC.evaluate(r, decision(row_id=r['row_id']), results[r['request_url']]) for r in rows]
        self.assertEqual(len(reports), 2)
        self.assertEqual({r['row_id'] for r in reports}, {'gsc-0001', 'gsc-0002'})

    def test_exact_offline_unresolved_allowance_never_hides_other_errors(self):
        rows = [inventory(f'https://teznevise.ir/unresolved-{i}/', f'gsc-{i:04d}') for i in range(1, 7)]
        reviewed = []
        for row in rows:
            item = decision('keep', row['request_url'], row['row_id'])
            item['unresolved_reason'] = 'Owner must identify the original content'
            reviewed.append(item)
        errors = GSC.check_inputs(rows, reviewed)
        default = GSC.offline_result(rows, reviewed, errors)
        self.assertEqual(default['result'], 'FAIL')
        self.assertEqual(default['unresolved_decision_count'], 6)
        allowed = GSC.offline_result(rows, reviewed, errors, 6)
        self.assertEqual(allowed['result'], 'PASS_WITH_UNRESOLVED')
        self.assertEqual(len(allowed['errors']), 6)
        self.assertEqual(len(allowed['unresolved_decisions']), 6)
        self.assertEqual(allowed['blocking_errors'], [])
        mismatch = GSC.offline_result(rows, reviewed, errors, 5)
        self.assertEqual(mismatch['result'], 'FAIL')
        self.assertTrue(any('Expected exactly 5' in e for e in mismatch['blocking_errors']))
        reviewed[0]['reason'] = ''
        broken = GSC.offline_result(rows, reviewed, GSC.check_inputs(rows, reviewed), 6)
        self.assertEqual(broken['result'], 'FAIL')
        self.assertTrue(any('requires a reason' in e for e in broken['blocking_errors']))
        self.assertEqual(GSC.evaluate(rows[1], reviewed[1], observation(rows[1]['request_url']))['result'], 'FAIL')

    def test_unresolved_cli_allowance_requires_offline_check(self):
        result = subprocess.run([sys.executable, GSC.__file__, '--allow-unresolved-count', '6'],
                                capture_output=True, text=True, check=False)
        self.assertEqual(result.returncode, 2)
        self.assertIn('requires --check-inventory', result.stderr)

    def test_default_inventory_mirror_requires_byte_identical_sha256(self):
        with tempfile.TemporaryDirectory(prefix='tezseo-verifier-mirror-') as temp:
            source = Path(temp) / 'source.csv'
            mirror = Path(temp) / 'mirror.csv'
            custom = Path(temp) / 'fixture.csv'
            source.write_bytes(b'row_id,request_url\n1,https://teznevise.ir/\n')
            mirror.write_bytes(source.read_bytes())
            custom.write_bytes(source.read_bytes())
            with patch.object(GSC, 'DEFAULT_INVENTORY', source), patch.object(GSC, 'INVENTORY_MIRROR', mirror):
                state, errors = GSC.inventory_mirror_status(source)
                self.assertEqual(errors, [])
                self.assertTrue(state['inventory_mirror_required'])
                self.assertEqual(state['inventory_sha256'], state['inventory_mirror_sha256'])
                mirror.write_bytes(source.read_bytes().replace(b'\n', b'\r\n'))
                self.assertTrue(GSC.inventory_mirror_status(source)[1])
                mirror.unlink()
                self.assertTrue(GSC.inventory_mirror_status(source)[1])
                state, errors = GSC.inventory_mirror_status(custom)
                self.assertEqual(errors, [])
                self.assertFalse(state['inventory_mirror_required'])

    def test_staging_mapping_preserves_double_slashes_escapes_and_query_order(self):
        base = GSC.parse_base_url('https://Owner-Stage.example/')
        self.assertEqual(base, 'https://owner-stage.example')
        source = 'https://teznevise.ir//statistics/%2f/?a=1&a=%2F&b=one+two'
        old = inventory(source)
        old.update(canonical_url=source,
                   redirect_chain_json='[{"url":"https://teznevise.ir//old/","status":301,"target":"https://teznevise.ir//statistics/%2f/?a=1&a=%2F&b=one+two"}]')
        original = deepcopy(old)
        mapped = GSC.mapped_inventory(old, base)
        self.assertEqual(mapped['request_url'], 'https://owner-stage.example//statistics/%2f/?a=1&a=%2F&b=one+two')
        self.assertEqual(mapped['canonical_url'], mapped['request_url'])
        self.assertIn('https://owner-stage.example//old/', mapped['redirect_chain_json'])
        self.assertEqual(old, original)
        self.assertEqual(GSC.map_origin(SOURCE + '?', base), SOURCE.replace('https://teznevise.ir', base) + '?')
        self.assertEqual(GSC.map_origin('https://other.example/path/', base), 'https://other.example/path/')
        with patch.object(GSC, 'STAGING_ORIGIN', base):
            self.assertEqual(GSC.url_error(mapped['request_url']), '')
            self.assertTrue(GSC.url_error(mapped['request_url'], allow_stage=False))
            self.assertTrue(GSC.url_error('https://unconfigured-stage.example/'))

    def test_staging_expected_canonical_does_not_rewrite_production_observation(self):
        base = GSC.parse_base_url('https://stage.example')
        old = GSC.mapped_inventory(inventory(), base)
        reviewed = decision('restore')
        reviewed['expected_canonical'] = SOURCE
        reviewed = GSC.mapped_decision(reviewed, base)
        live = observation(old['request_url'])
        self.assertEqual(GSC.evaluate(old, reviewed, live)['result'], 'PASS')
        live['canonical_urls'] = [SOURCE]
        self.assertEqual(GSC.evaluate(old, reviewed, live)['result'], 'FAIL')
        self.assertEqual(live['canonical_urls'], [SOURCE])

    def test_staging_preserves_http_source_for_https_normalization_redirect(self):
        base = GSC.parse_base_url('https://stage.example')
        production_http = 'http://teznevise.ir/'
        production_https = 'https://teznevise.ir/'
        old = inventory(production_http)
        old.update(initial_status='301', terminal_status='200', final_url=production_https)
        reviewed = decision('301', production_http)
        reviewed.update(expected_status='301', target_url=production_https,
                        expected_canonical=production_https)
        old = GSC.mapped_inventory(old, base)
        reviewed = GSC.mapped_decision(reviewed, base)
        self.assertEqual(old['request_url'], 'http://stage.example/')
        self.assertEqual(old['final_url'], 'https://stage.example/')
        self.assertEqual(reviewed['target_url'], 'https://stage.example/')
        self.assertEqual(reviewed['expected_canonical'], 'https://stage.example/')
        live = observation('http://stage.example/')
        live.update(initial_status=301, final_url='https://stage.example/',
                    canonical_urls=['https://stage.example/'],
                    redirect_chain=[{'url': 'http://stage.example/', 'status': 301,
                                     'target': 'https://stage.example/'}])
        with patch.object(GSC, 'STAGING_ORIGIN', base):
            self.assertEqual(GSC.url_error(old['request_url']), '')
            self.assertEqual(GSC.url_error(old['final_url']), '')
            self.assertTrue(GSC.url_error('http://other-stage.example/'))
        self.assertEqual(GSC.evaluate(old, reviewed, live)['result'], 'PASS')

    def test_staging_rejects_credentials_nonorigins_and_offline_snapshot_modes(self):
        for unsafe in ('https://user:secret@stage.example', 'http://stage.example',
                       'https://stage.example/path/', 'https://stage.example/?x=1',
                       'https://stage.example/#fragment'):
            with self.assertRaises(ValueError):
                GSC.parse_base_url(unsafe)
        for flag in ('--snapshot-only', '--check-inventory'):
            result = subprocess.run([sys.executable, GSC.__file__, '--base-url', 'https://stage.example', flag],
                                    capture_output=True, text=True, check=False)
            self.assertEqual(result.returncode, 2)
            self.assertIn('cannot be combined', result.stderr)

    def test_snapshot_reports_drift_without_evaluating_future_decision(self):
        old = inventory()
        live = observation()
        live.update(initial_status=404, terminal_status=404, canonical_urls=[])
        reviewed = decision('restore')
        self.assertEqual(GSC.evaluate(old, reviewed, live, snapshot_only=True)['result'], 'PASS')
        self.assertEqual(GSC.evaluate(old, reviewed, live)['result'], 'FAIL')
        live['initial_status'] = 410
        self.assertEqual(GSC.evaluate(old, reviewed, live, snapshot_only=True)['result'], 'FAIL')


if __name__ == '__main__':
    unittest.main(verbosity=2)
