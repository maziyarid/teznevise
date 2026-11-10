#!/usr/bin/env python3
"""Read-only exact-URL verification of the reviewed Search Console decisions.

Project: Teznevise WordPress Theme
Author: MAZ//ID (Maziyar)
Brand: MΛZ — https://github.com/maziyarid/M-Z

GET requests only. No WordPress commands, credentials, deploys or URL discovery.
Percent escapes and query ordering are preserved; a literal * is never expanded.
"""
import argparse
from concurrent.futures import ThreadPoolExecutor
import csv
from datetime import datetime, timezone
import hashlib
from html.parser import HTMLParser
import json
from pathlib import Path
import re
import sys
from urllib.error import HTTPError, URLError
from urllib.parse import quote, urljoin, urlsplit, urlunsplit
from urllib.request import Request, build_opener, HTTPRedirectHandler

ROOT = Path(__file__).resolve().parents[2]
AUDIT = ROOT / 'seo/20261110/audit'
DEFAULT_INVENTORY = AUDIT / 'gsc-indexing-20261110.csv'
INVENTORY_MIRROR = ROOT / 'audit/gsc-indexing-20261110.csv'
AGENT = 'Teznevise-Owner-GSC-Verification/20261110 (read-only; github.com/maziyarid/teznevise)'
HOSTS = {'teznevise.ir', 'www.teznevise.ir'}
STAGING_ORIGIN = None
ACTIONS = {'301', '410', 'restore', 'unblock', 'remove_noindex', 'keep'}
INVENTORY_COLUMNS = {'row_id', 'source_bucket', 'request_url', 'initial_status', 'terminal_status', 'final_url'}
DECISION_COLUMNS = {'row_id', 'request_url', 'source_bucket', 'action', 'target_url', 'batch', 'reason',
                    'observed_initial_status', 'observed_final_status', 'expected_status',
                    'expected_canonical', 'expected_robots', 'post_id', 'content_file',
                    'implementation', 'unresolved_reason'}


def utc_now():
    return datetime.now(timezone.utc).isoformat()


def read_csv(path, required):
    with path.open(encoding='utf-8-sig', newline='') as stream:
        reader = csv.DictReader(stream)
        missing = required - set(reader.fieldnames or [])
        if missing:
            raise ValueError(f'{path}: missing columns {sorted(missing)}')
        return list(reader)


def inventory_mirror_status(path):
    required = path.resolve() == DEFAULT_INVENTORY.resolve()
    state = {'inventory_mirror_required': required,
             'inventory_sha256': hashlib.sha256(path.read_bytes()).hexdigest(),
             'inventory_mirror_sha256': None}
    errors = []
    if required:
        if not INVENTORY_MIRROR.is_file():
            errors.append('Default inventory mirror is missing: ' + str(INVENTORY_MIRROR))
        else:
            state['inventory_mirror_sha256'] = hashlib.sha256(INVENTORY_MIRROR.read_bytes()).hexdigest()
            if state['inventory_sha256'] != state['inventory_mirror_sha256']:
                errors.append('Default inventory and root audit mirror differ byte-for-byte (SHA256 mismatch)')
    return state, errors


def offline_result(inventory, decisions, errors, allow_unresolved_count=None):
    unresolved = [{'row_id': r['row_id'], 'request_url': r['request_url'],
                   'reason': r['unresolved_reason']} for r in decisions
                  if r.get('unresolved_reason', '').strip()]
    unresolved_errors = {'Unresolved decision: ' + r['row_id'] + ': ' + r['reason']
                         for r in unresolved}
    all_errors = list(errors)
    blocking = list(errors)
    allowance_matches = allow_unresolved_count is not None and len(unresolved) == allow_unresolved_count
    if allowance_matches:
        # The narrow allowance cannot hide missing coverage, bad redirects or any other defect.
        blocking = [error for error in errors if error not in unresolved_errors]
    elif allow_unresolved_count is not None:
        mismatch = f'Expected exactly {allow_unresolved_count} unresolved decision rows; found {len(unresolved)}'
        all_errors.append(mismatch)
        blocking.append(mismatch)
    result = 'FAIL' if blocking else 'PASS_WITH_UNRESOLVED' if unresolved else 'PASS'
    return {'result': result, 'supplied_rows': len(inventory), 'decision_rows': len(decisions),
            'unique_exact_urls': len({r['request_url'] for r in inventory}),
            'allowed_unresolved_count': allow_unresolved_count,
            'unresolved_decision_count': len(unresolved), 'unresolved_decisions': unresolved,
            'errors': all_errors, 'blocking_errors': blocking}


def action_set(row):
    return {part.strip() for part in row.get('action', '').split('|') if part.strip()}


def url_error(url, allow_stage=True):
    """Validate without stripping, decoding, sorting queries or rewriting input."""
    if not url or url != url.strip() or any(c.isspace() or ord(c) < 32 or ord(c) == 127 for c in url):
        return 'Empty URL or literal whitespace/control character in URL'
    try:
        parts = urlsplit(url)
        if parts.username is not None or parts.password is not None:
            return 'Credential-bearing URLs are refused'
        production = parts.scheme in ('http', 'https') and parts.hostname in HOSTS and parts.port is None
        stage = allow_stage and STAGING_ORIGIN and parts.scheme in ('http', 'https') and parts.netloc.lower() == urlsplit(STAGING_ORIGIN).netloc
        if not production and not stage:
            return 'Expected a production URL or the explicitly configured staging origin'
        if parts.fragment:
            return 'Fragment supplied: HTTP cannot verify a fragment as a separate URL'
        if re.search(r'%(?![0-9a-fA-F]{2})', url):
            return 'Malformed percent escape; URL was not repaired or expanded'
    except ValueError as exc:
        return str(exc)
    return ''


def parse_base_url(value):
    if not value or value != value.strip() or any(c.isspace() or ord(c) < 32 for c in value):
        raise ValueError('--base-url requires a credentialless HTTPS origin')
    try:
        parts = urlsplit(value)
        parts.port  # Reject malformed or out-of-range ports.
    except ValueError as exc:
        raise ValueError('--base-url: ' + str(exc)) from exc
    if (parts.scheme != 'https' or not parts.hostname or parts.username is not None or
            parts.password is not None or parts.path not in ('', '/') or '?' in value or '#' in value):
        raise ValueError('--base-url requires a credentialless HTTPS origin without path, query or fragment')
    if any(ord(c) > 127 for c in parts.netloc):
        raise ValueError('--base-url hostname must use its ASCII/IDNA spelling')
    return 'https://' + parts.netloc.lower()


def map_origin(url, base_url):
    """Replace only a production scheme/authority; preserve the exact remaining bytes."""
    if not url or not base_url:
        return url
    match = re.match(r'^([A-Za-z][A-Za-z0-9+.-]*://[^/?#]+)(.*)$', url)
    if not match:
        return url
    parts = urlsplit(url)
    if parts.scheme in ('http', 'https') and parts.hostname in HOSTS and parts.port is None and parts.username is None:
        origin = ('http://' + urlsplit(base_url).netloc) if parts.scheme == 'http' else base_url
        return origin + match[2]
    return url


def mapped_inventory(row, base_url):
    mapped = dict(row)
    for key in ('request_url', 'final_url'):
        mapped[key] = map_origin(row.get(key, ''), base_url)
    if 'canonical_url' in row:
        mapped['canonical_url'] = ' | '.join(map_origin(url, base_url) for url in row['canonical_url'].split(' | '))
    if row.get('link_canonical_header'):
        mapped['link_canonical_header'] = re.sub(r'<([^>]+)>', lambda m: '<' + map_origin(m[1], base_url) + '>', row['link_canonical_header'])
    if row.get('redirect_chain_json'):
        chain = json.loads(row['redirect_chain_json'])
        mapped['redirect_chain_json'] = json.dumps([{key: map_origin(value, base_url) if key in ('url', 'target', 'location') else value for key, value in hop.items()} for hop in chain], ensure_ascii=True)
    return mapped


def mapped_decision(row, base_url):
    mapped = dict(row)
    for key in ('request_url', 'target_url', 'expected_canonical'):
        mapped[key] = map_origin(row.get(key, ''), base_url)
    return mapped


def transport_url(url):
    """Encode only non-ASCII transport characters; retain existing escapes and query."""
    return quote(url, safe="%:/?#[]@!$&'()*+,;=-._~")


def compare_url(url):
    """Case-insensitive scheme/host only; path, escapes and query remain exact."""
    p = urlsplit(transport_url(url))
    result = urlunsplit((p.scheme.lower(), p.netloc.lower(), p.path or '/', p.query, p.fragment))
    if '?' in url.split('#', 1)[0] and not p.query:
        result += '?'
    return result


class NoRedirect(HTTPRedirectHandler):
    def redirect_request(self, req, fp, code, msg, headers, newurl):
        return None


class Head(HTMLParser):
    def __init__(self, source):
        super().__init__(convert_charrefs=True)
        self.canonicals = []
        self.robots = []
        self.title = []
        self.in_title = False
        self.feed(source)

    def handle_starttag(self, tag, attrs):
        a = {k.lower(): v for k, v in attrs}
        if tag == 'link' and 'canonical' in (a.get('rel') or '').lower().split():
            self.canonicals.append(a.get('href') or '')
        if tag == 'meta' and (a.get('name') or '').lower() in ('robots', 'googlebot'):
            self.robots.append({'name': a['name'].lower(), 'content': a.get('content') or ''})
        if tag == 'title':
            self.in_title = True

    def handle_endtag(self, tag):
        if tag == 'title':
            self.in_title = False

    def handle_data(self, text):
        if self.in_title:
            self.title.append(text)


def header_canonicals(values):
    result = []
    for value in values:
        for match in re.finditer(r'<([^>]+)>\s*([^<>]*)', value):
            rel = re.search(r'\brel\s*=\s*(?:"([^"]*)"|\x27([^\x27]*)\x27|([^;,\s]+))', match[2], re.I)
            if rel and 'canonical' in next(x for x in rel.groups() if x is not None).lower().split():
                result.append(match[1])
    return result


def noindex_tokens(meta, headers):
    found = []
    for row in meta:
        for token in re.split(r'[\s,;]+', row['content'].lower()):
            if token in ('noindex', 'none'):
                found.append('meta:' + row['name'] + ':' + token)
    for value in headers:
        scope = ''
        for segment in value.lower().split(','):
            segment = segment.strip()
            scoped = re.match(r'^([a-z0-9_-]+)\s*:\s*(.*)$', segment)
            if scoped and scoped[1] not in ('unavailable_after',):
                scope, segment = scoped[1], scoped[2]
            if scope and scope not in ('googlebot', '*'):
                continue
            for token in re.split(r'[\s;]+', segment):
                if token in ('noindex', 'none'):
                    found.append('header:' + (scope or '*') + ':' + token)
    return found


def fetch_chain(url, timeout=25, max_hops=10, body_limit=2_000_000):
    row = {'request_url': url, 'transport_url': transport_url(url), 'fetched_utc': utc_now(),
           'initial_status': None, 'terminal_status': None, 'final_url': url,
           'redirect_chain': [], 'content_type': '', 'is_html': False,
           'canonical_urls': [], 'robots_meta': [], 'x_robots_tag': [],
           'noindex': [], 'title': '', 'fetch_error': '', 'body_truncated': False,
           'literal_wildcard': '*' in url, 'body_sha256': '', 'robots_text': ''}
    invalid = url_error(url)
    if invalid:
        row['fetch_error'] = invalid
        return row
    opener = build_opener(NoRedirect())
    current = transport_url(url)
    seen = set()
    try:
        for hop in range(max_hops + 1):
            if current in seen:
                raise ValueError('Redirect loop')
            seen.add(current)
            if url_error(current):
                raise ValueError('Redirect target refused: ' + url_error(current))
            request = Request(current, headers={'User-Agent': AGENT, 'Accept': 'text/html,text/plain,*/*;q=0.5', 'Accept-Encoding': 'identity'}, method='GET')
            try:
                response = opener.open(request, timeout=timeout)
            except HTTPError as exc:
                response = exc
            with response:
                status = response.code
                if row['initial_status'] is None:
                    row['initial_status'] = status
                row['terminal_status'] = status
                row['final_url'] = current
                if status in (301, 302, 303, 307, 308) and response.headers.get('Location'):
                    location = response.headers['Location']
                    target = transport_url(urljoin(current, location))
                    row['redirect_chain'].append({'url': current, 'status': status, 'location': location, 'target': target})
                    if hop == max_hops:
                        raise ValueError('Redirect limit exceeded')
                    current = target
                    continue
                data = response.read(body_limit + 1)
                row['body_truncated'] = len(data) > body_limit
                data = data[:body_limit]
                row['body_sha256'] = hashlib.sha256(data).hexdigest()
                row['content_type'] = response.headers.get('Content-Type', '')
                row['x_robots_tag'] = response.headers.get_all('X-Robots-Tag') or []
                row['is_html'] = 'html' in row['content_type'].lower() or bool(re.search(br'<(?:html|head)\b', data[:1024], re.I))
                charset = response.headers.get_content_charset() or 'utf-8'
                try:
                    text = data.decode(charset, errors='replace')
                except LookupError:
                    text = data.decode('utf-8', errors='replace')
                canonicals = header_canonicals(response.headers.get_all('Link') or [])
                if row['is_html']:
                    head = Head(text)
                    canonicals.extend(head.canonicals)
                    row['robots_meta'] = head.robots
                    row['title'] = ''.join(head.title).strip()
                row['canonical_urls'] = [transport_url(urljoin(current, c)) for c in canonicals]
                row['noindex'] = noindex_tokens(row['robots_meta'], row['x_robots_tag'])
                if urlsplit(current).path == '/robots.txt':
                    row['robots_text'] = text
                break
    except (HTTPError, URLError, TimeoutError, OSError, ValueError) as exc:
        row['fetch_error'] = type(exc).__name__ + ': ' + str(exc)
    return row


def robots_octets(value):
    """Robots matching: decode unreserved escapes only; retain reserved octets."""
    value = transport_url(value)
    unreserved = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~'
    return re.sub(r'%([0-9a-fA-F]{2})', lambda m: chr(int(m[1], 16)) if chr(int(m[1], 16)) in unreserved else '%' + m[1].upper(), value)


def robots_rules(text, agent='Googlebot'):
    groups = []
    agents = []
    rules = []
    started_rules = False
    for number, original in enumerate(text.splitlines(), 1):
        line = original.split('#', 1)[0].strip()
        if ':' not in line:
            continue
        key, value = (s.strip() for s in line.split(':', 1))
        key = key.lower()
        if key == 'user-agent':
            if started_rules:
                groups.append((agents, rules))
                agents, rules, started_rules = [], [], False
            agents.append(value.lower())
        elif key in ('allow', 'disallow') and agents:
            started_rules = True
            if value:
                rules.append({'directive': key, 'pattern': value, 'line': number})
    if agents:
        groups.append((agents, rules))
    candidates = []
    for names, entries in groups:
        scores = [0 if name == '*' else len(name) for name in names if name == '*' or name in agent.lower()]
        if scores:
            candidates.append((max(scores), entries))
    if not candidates:
        return []
    best = max(score for score, _ in candidates)
    return [r for score, entries in candidates if score == best for r in entries]


def robots_decision(url, observation):
    """Return None for unavailable robots, never silently treat failures as allow."""
    status = observation['terminal_status']
    if observation['fetch_error']:
        return {'allowed': None, 'rule': '', 'line': '', 'error': observation['fetch_error']}
    if status is not None and 400 <= status < 500 and status != 429:
        return {'allowed': True, 'rule': '', 'line': '', 'error': '', 'reason': 'robots.txt HTTP client error; no applicable restrictions'}
    if status != 200 or observation['body_truncated']:
        return {'allowed': None, 'rule': '', 'line': '', 'error': 'robots.txt unavailable, redirected unsuccessfully or truncated'}
    p = urlsplit(transport_url(url))
    path = robots_octets((p.path or '/') + (('?' + p.query) if p.query else ''))
    matches = []
    for rule in robots_rules(observation['robots_text']):
        pattern = robots_octets(rule['pattern'])
        end = pattern.endswith('$')
        pattern = pattern[:-1] if end else pattern
        regex = '^' + '.*'.join(re.escape(piece) for piece in pattern.split('*')) + ('$' if end else '')
        if re.search(regex, path):
            # Percent-encoded octets each contribute one specificity unit.
            length = len(re.sub(r'%[0-9A-F]{2}', 'x', pattern.replace('*', '')))
            matches.append((length, rule['directive'] == 'allow', rule))
    if not matches:
        return {'allowed': True, 'rule': '', 'line': '', 'error': '', 'reason': 'No matching disallow rule'}
    _, allowed, rule = max(matches, key=lambda x: (x[0], x[1]))
    return {'allowed': allowed, 'rule': rule['directive'] + ': ' + rule['pattern'], 'line': rule['line'], 'error': ''}


def check_inputs(inventory, decisions):
    errors = []
    inventory_by_id = {}
    decision_by_id = {}
    for row in inventory:
        key = row['row_id']
        if not key or key in inventory_by_id:
            errors.append('Empty or duplicate inventory row_id: ' + key)
        inventory_by_id[key] = row
        if url_error(row.get('request_url', ''), allow_stage=False):
            errors.append('Inventory request_url is not a valid exact production URL: ' + key)
    for row in decisions:
        key = row['row_id']
        if not key or key in decision_by_id:
            errors.append('Empty or duplicate decision row_id: ' + key)
        decision_by_id[key] = row
        if key not in inventory_by_id:
            errors.append('Decision without supplied inventory row: ' + key)
            continue
        if row['request_url'] != inventory_by_id[key]['request_url']:
            errors.append('Decision URL differs from exact inventory request_url: ' + key)
        if row['source_bucket'] != inventory_by_id[key]['source_bucket']:
            errors.append('Decision source_bucket differs from supplied row: ' + key)
        for decision_key, inventory_key in (('observed_initial_status', 'initial_status'), ('observed_final_status', 'terminal_status')):
            if row.get(decision_key) and status_number(row[decision_key]) != status_number(inventory_by_id[key].get(inventory_key)):
                errors.append('Decision baseline status differs from inventory: ' + key + ':' + decision_key)
        actions = action_set(row)
        if not actions or not actions <= ACTIONS:
            errors.append('Missing or unsupported action: ' + key)
        if len(actions & {'301', '410', 'restore', 'keep'}) > 1:
            errors.append('Conflicting terminal actions: ' + key)
        if not row.get('reason', '').strip():
            errors.append('Decision requires a reason: ' + key)
        if not row.get('expected_status', '').isdigit() or not 100 <= int(row['expected_status']) <= 599:
            errors.append('Decision requires an HTTP expected_status from 100 through 599: ' + key)
        if row.get('expected_robots', '') not in ('allow', 'disallow', 'unchanged', 'ignore', ''):
            errors.append('Unsupported expected_robots: ' + key)
        if row.get('expected_canonical') and url_error(row['expected_canonical'], allow_stage=False):
            errors.append('Invalid explicit canonical URL: ' + key)
        if '301' in actions:
            target = row.get('target_url', '')
            if url_error(target, allow_stage=False):
                errors.append('Missing or invalid exact redirect target: ' + key)
            if target == row['request_url']:
                errors.append('Self redirect: ' + key)
        if row.get('unresolved_reason', '').strip():
            errors.append('Unresolved decision: ' + key + ': ' + row['unresolved_reason'])
    for key in inventory_by_id.keys() - decision_by_id.keys():
        errors.append('Supplied URL row has no decision: ' + key)
    duplicate_rules = {}
    for row in decisions:
        signature = tuple(row.get(key, '') for key in ('action', 'target_url', 'expected_status', 'expected_canonical', 'expected_robots'))
        if row['request_url'] in duplicate_rules and duplicate_rules[row['request_url']] != signature:
            errors.append('Conflicting decisions for duplicate exact URL: ' + row['row_id'])
        duplicate_rules[row['request_url']] = signature
    redirect_sources = {r['request_url'] for r in decisions if '301' in action_set(r)}
    for row in decisions:
        if '301' in action_set(row) and row['target_url'] in redirect_sources:
            errors.append('Decision map creates a redirect chain or loop: ' + row['row_id'])
    return errors


def add_check(checks, name, expected, actual, passed):
    checks.append({'name': name, 'expected': expected, 'actual': actual, 'result': 'PASS' if passed else 'FAIL'})


def status_number(value):
    try:
        return int(value)
    except (TypeError, ValueError):
        return None


def expect_indexable(checks, observation, canonical, label='target'):
    add_check(checks, label + '_terminal_status', 200, observation['terminal_status'], observation['terminal_status'] == 200)
    robot = observation.get('robots', {})
    add_check(checks, label + '_robots', 'allow', robot.get('allowed'), robot.get('allowed') is True)
    add_check(checks, label + '_noindex', [], observation['noindex'], not observation['noindex'])
    add_check(checks, label + '_complete_fetch', 'no error or truncation', observation['fetch_error'] or observation['body_truncated'], not observation['fetch_error'] and not observation['body_truncated'])
    if observation['is_html'] or canonical:
        expected = canonical or observation['final_url']
        actual = observation['canonical_urls']
        add_check(checks, label + '_canonical', [expected], actual, len(actual) == 1 and compare_url(actual[0]) == compare_url(expected))


def evaluate(inventory, decision, observation, snapshot_only=False):
    checks = []
    unresolved = []
    if observation['fetch_error']:
        unresolved.append(observation['fetch_error'])
    add_check(checks, 'request_fetch', 'successful observation', observation['fetch_error'], not observation['fetch_error'])
    baseline = {k: inventory.get(k, '') for k in ('initial_status', 'terminal_status', 'final_url', 'canonical_url', 'robots_allowed', 'robots_matched_rule', 'fetched_utc', 'robots_meta', 'x_robots_tag', 'redirect_chain_json', 'link_canonical_header')}
    drift = []
    for key in ('initial_status', 'terminal_status'):
        old = status_number(inventory.get(key))
        if old is not None and old != observation[key]:
            drift.append({'field': key, 'before': old, 'now': observation[key]})
    if inventory.get('final_url') and inventory['final_url'] != observation['final_url']:
        drift.append({'field': 'final_url', 'before': inventory['final_url'], 'now': observation['final_url']})
    if snapshot_only:
        for key in ('initial_status', 'terminal_status'):
            old = status_number(inventory.get(key))
            add_check(checks, 'snapshot_' + key, old, observation[key], old is not None and old == observation[key])
        old = inventory.get('final_url', '')
        add_check(checks, 'snapshot_final_url', old, observation['final_url'], bool(old) and old == observation['final_url'])
        if 'redirect_chain_json' in inventory:
            original_chain = json.loads(inventory['redirect_chain_json'] or '[]')
            expected_chain = [{'status': r['status'], 'target': r['target']} for r in original_chain]
            actual_chain = [{'status': r['status'], 'target': r['target']} for r in observation['redirect_chain']]
            add_check(checks, 'snapshot_redirect_chain', expected_chain, actual_chain, expected_chain == actual_chain)
        if 'canonical_url' in inventory:
            expected_canonical = [url for url in inventory['canonical_url'].split(' | ') if url]
            expected_canonical = header_canonicals((inventory.get('link_canonical_header') or '').split(' | ')) + expected_canonical
            add_check(checks, 'snapshot_canonicals', expected_canonical, observation['canonical_urls'], expected_canonical == observation['canonical_urls'])
        if inventory.get('robots_allowed', '').lower() in ('true', 'false'):
            expected_allowed = inventory['robots_allowed'].lower() == 'true'
            actual_allowed = observation.get('source_robots', observation.get('robots', {})).get('allowed')
            add_check(checks, 'snapshot_robots_allowed', expected_allowed, actual_allowed, expected_allowed is actual_allowed)
        if 'robots_meta' in inventory or 'x_robots_tag' in inventory:
            original_noindex = noindex_tokens([{'name': 'robots', 'content': inventory.get('robots_meta', '')}], inventory.get('x_robots_tag', '').split(' | '))
            add_check(checks, 'snapshot_noindex_present', bool(original_noindex), bool(observation['noindex']), bool(original_noindex) == bool(observation['noindex']))
    elif not decision:
        unresolved.append('No reviewed decision for supplied URL row')
        add_check(checks, 'reviewed_decision', 'exact matching row_id and URL', None, False)
    else:
        if decision.get('unresolved_reason', '').strip():
            unresolved.append(decision['unresolved_reason'])
        actions = action_set(decision)
        add_check(checks, 'decision_reason', 'nonempty reason', decision.get('reason', ''), bool(decision.get('reason', '').strip()))
        add_check(checks, 'decision_url', inventory['request_url'], decision['request_url'], inventory['request_url'] == decision['request_url'])
        add_check(checks, 'known_action', sorted(ACTIONS), sorted(actions), bool(actions) and actions <= ACTIONS)
        add_check(checks, 'compatible_terminal_actions', 'at most one terminal action', sorted(actions & {'301', '410', 'restore', 'keep'}), len(actions & {'301', '410', 'restore', 'keep'}) <= 1)
        expected_status = status_number(decision.get('expected_status'))
        add_check(checks, 'source_status', expected_status, observation['initial_status'], expected_status is not None and expected_status == observation['initial_status'])
        robot = observation.get('source_robots', observation.get('robots', {}))
        expected_robots = decision.get('expected_robots', '')
        if expected_robots in ('allow', 'disallow'):
            expected = expected_robots == 'allow'
            add_check(checks, 'source_robots', expected_robots, robot.get('allowed'), robot.get('allowed') is expected)
        elif expected_robots == 'unchanged':
            previous = inventory.get('robots_allowed', '').lower()
            expected = True if previous in ('true', '1', 'yes', 'allow', 'allowed') else False if previous in ('false', '0', 'no', 'disallow', 'blocked') else None
            add_check(checks, 'source_robots_unchanged', expected, robot.get('allowed'), expected is not None and robot.get('allowed') is expected)
        if '301' in actions:
            chain = observation['redirect_chain']
            target = decision.get('target_url', '')
            add_check(checks, 'single_301_hop', 'one 301', [r['status'] for r in chain], len(chain) == 1 and chain[0]['status'] == 301)
            actual = chain[0]['target'] if chain else ''
            add_check(checks, 'reviewed_redirect_target', target, actual, bool(target) and compare_url(target) == compare_url(actual) if actual else False)
            add_check(checks, 'final_target', target, observation['final_url'], bool(target) and compare_url(target) == compare_url(observation['final_url']))
            expect_indexable(checks, observation, decision.get('expected_canonical') or (target if observation['is_html'] else ''), 'target')
        if '410' in actions:
            add_check(checks, 'direct_410', '410 without redirect', {'status': observation['terminal_status'], 'hops': len(observation['redirect_chain'])}, observation['initial_status'] == 410 and observation['terminal_status'] == 410 and not observation['redirect_chain'])
        if 'restore' in actions:
            add_check(checks, 'restored_direct', '200 without redirect', {'status': observation['initial_status'], 'hops': len(observation['redirect_chain'])}, observation['initial_status'] == 200 and not observation['redirect_chain'])
            expect_indexable(checks, observation, decision.get('expected_canonical') or (inventory['request_url'] if observation['is_html'] else ''), 'restored')
        if 'unblock' in actions:
            add_check(checks, 'robots_unblocked', 'allow', robot.get('allowed'), robot.get('allowed') is True)
        if 'remove_noindex' in actions:
            add_check(checks, 'meta_and_header_noindex_removed', [], observation['noindex'], not observation['noindex'])
            add_check(checks, 'noindex_response_complete', 'complete terminal 200', {'status': observation['terminal_status'], 'truncated': observation['body_truncated']}, observation['terminal_status'] == 200 and not observation['fetch_error'] and not observation['body_truncated'])
        # Explicit canonical expectations also apply to unblock/noindex/keep decisions.
        if decision.get('expected_canonical') and not (actions & {'301', 'restore'}):
            expected = decision['expected_canonical']
            actual = observation['canonical_urls']
            add_check(checks, 'explicit_canonical', [expected], actual, len(actual) == 1 and compare_url(actual[0]) == compare_url(expected))
    robot_error = observation.get('robots', {}).get('error') or observation.get('source_robots', {}).get('error')
    if robot_error:
        # Robots uncertainty matters for an action that expects robot visibility.
        if decision and (action_set(decision) & {'301', 'restore', 'unblock'} or decision.get('expected_robots') in ('allow', 'disallow', 'unchanged')):
            unresolved.append(robot_error)
    if snapshot_only:
        drift = [{'field': c['name'], 'before': c['expected'], 'now': c['actual']} for c in checks if c['name'].startswith('snapshot_') and c['result'] == 'FAIL']
    passed = not unresolved and all(c['result'] == 'PASS' for c in checks)
    return {'row_id': inventory['row_id'], 'source_bucket': inventory.get('source_bucket', ''),
            'request_url': inventory['request_url'], 'tested_url': inventory['request_url'], 'original_input_json': inventory.get('original_input_json', ''), 'action': decision.get('action', '') if decision else '',
            'result': 'PASS' if passed else 'FAIL', 'unresolved': unresolved,
            'reason': decision.get('reason', '') if decision else '', 'baseline': baseline,
            'checks': checks, 'drift': drift, 'observed': {k: v for k, v in observation.items() if k != 'robots_text'}}


def observe_all(inventory, workers, timeout):
    urls = list(dict.fromkeys(r['request_url'] for r in inventory))
    with ThreadPoolExecutor(max_workers=workers) as pool:
        observations = dict(zip(urls, pool.map(lambda url: fetch_chain(url, timeout=timeout), urls)))
    robot_urls = set()
    for url, row in observations.items():
        for candidate in (url, row['final_url']):
            if not url_error(candidate):
                p = urlsplit(candidate)
                robot_urls.add(p.scheme + '://' + p.netloc + '/robots.txt')
    with ThreadPoolExecutor(max_workers=workers) as pool:
        robots = dict(zip(sorted(robot_urls), pool.map(lambda url: fetch_chain(url, timeout=timeout), sorted(robot_urls))))
    for url, row in observations.items():
        for key, candidate in (('source_robots', url), ('robots', row['final_url'])):
            if url_error(candidate):
                row[key] = {'allowed': None, 'error': url_error(candidate), 'rule': '', 'line': ''}
            else:
                p = urlsplit(candidate)
                row[key] = robots_decision(candidate, robots[p.scheme + '://' + p.netloc + '/robots.txt'])
    return observations


def write_reports(prefix, report):
    prefix.parent.mkdir(parents=True, exist_ok=True)
    prefix.with_suffix('.json').write_text(json.dumps(report, ensure_ascii=True, indent=2) + '\n', encoding='utf-8')
    fields = ['row_id', 'source_bucket', 'request_url', 'tested_url', 'action', 'result', 'unresolved', 'initial_status', 'terminal_status', 'final_url', 'failed_checks_json', 'checks_json', 'snapshot_drift_json', 'source_robots_json', 'robots_json', 'canonical_urls_json', 'noindex_json', 'fetch_error']
    with prefix.with_suffix('.csv').open('w', encoding='utf-8', newline='') as stream:
        writer = csv.DictWriter(stream, fieldnames=fields)
        writer.writeheader()
        for row in report['rows']:
            observed = row['observed']
            data = {key: row[key] for key in ('row_id', 'source_bucket', 'request_url', 'tested_url', 'action', 'result')}
            data.update({key: observed[key] for key in ('initial_status', 'terminal_status', 'final_url', 'fetch_error')})
            data.update(unresolved=json.dumps(row['unresolved'], ensure_ascii=True), failed_checks_json=json.dumps([c for c in row['checks'] if c['result'] == 'FAIL'], ensure_ascii=True), checks_json=json.dumps(row['checks'], ensure_ascii=True), snapshot_drift_json=json.dumps(row['drift'], ensure_ascii=True), source_robots_json=json.dumps(observed.get('source_robots', {}), ensure_ascii=True), robots_json=json.dumps(observed.get('robots', {}), ensure_ascii=True), canonical_urls_json=json.dumps(observed['canonical_urls'], ensure_ascii=True), noindex_json=json.dumps(observed['noindex'], ensure_ascii=True))
            writer.writerow({key: str(value).replace('\u200c', '\\u200c') if value is not None else '' for key, value in data.items()})


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--inventory', type=Path, default=DEFAULT_INVENTORY)
    parser.add_argument('--decisions', type=Path, default=AUDIT / 'gsc-decisions-20261110.csv')
    parser.add_argument('--output-prefix', type=Path, default=ROOT / 'work/seo-20261110/gsc-verification')
    parser.add_argument('--base-url', metavar='HTTPS_ORIGIN', help='Explicit owner staging origin; map expected/input origins in memory, preserving the production CSV')
    parser.add_argument('--snapshot-only', action='store_true', help='Check Phase1 snapshot drift rather than future decision outcomes')
    parser.add_argument('--check-inventory', action='store_true', help='Offline schema, coverage, rationale and no-chain checks; no requests')
    parser.add_argument('--allow-unresolved-count', type=int, default=None, metavar='N', help='Offline-only exact reviewed unresolved count; reasons remain visible and all other errors still fail')
    parser.add_argument('--workers', type=int, default=6)
    parser.add_argument('--timeout', type=int, default=25)
    args = parser.parse_args()
    global STAGING_ORIGIN
    stage_origin = None
    if args.base_url:
        if args.snapshot_only or args.check_inventory:
            parser.error('--base-url cannot be combined with --snapshot-only or --check-inventory')
        try:
            stage_origin = parse_base_url(args.base_url)
        except ValueError as exc:
            parser.error(str(exc))
    if not 1 <= args.workers <= 12 or not 1 <= args.timeout <= 120:
        parser.error('Use 1 to 12 workers and a 1 to 120 second timeout')
    if args.allow_unresolved_count is not None and (not args.check_inventory or args.snapshot_only or args.allow_unresolved_count < 0):
        parser.error('--allow-unresolved-count requires --check-inventory alone and a nonnegative exact count')
    mirror, mirror_errors = inventory_mirror_status(args.inventory)
    inventory = read_csv(args.inventory, INVENTORY_COLUMNS)
    decisions = read_csv(args.decisions, DECISION_COLUMNS) if args.decisions.exists() or not args.snapshot_only else []
    errors = check_inputs(inventory, decisions) + mirror_errors
    if args.snapshot_only:
        # A Phase1 recheck can run before Phase2 decisions exist.
        errors = [e for e in errors if e.startswith('Empty or duplicate inventory row_id:') or e in mirror_errors]
    if args.check_inventory or mirror_errors:
        report = {**offline_result(inventory, decisions, errors, args.allow_unresolved_count), **mirror}
        print(json.dumps(report, ensure_ascii=True, indent=2))
        return 1 if report['result'] == 'FAIL' else 0
    # Production coverage, URL safety and mirror integrity are evaluated before mapping.
    # Unresolved owner decisions remain visible failures; they do not prevent read-only checks.
    structural_errors = [e for e in errors if not e.startswith('Unresolved decision: ')]
    if stage_origin and structural_errors:
        print(json.dumps({**offline_result(inventory, decisions, errors), **mirror}, ensure_ascii=True, indent=2))
        return 1
    STAGING_ORIGIN = stage_origin
    tested_inventory = [mapped_inventory(row, stage_origin) for row in inventory] if stage_origin else inventory
    tested_decisions = [mapped_decision(row, stage_origin) for row in decisions] if stage_origin else decisions
    observations = observe_all(tested_inventory, args.workers, args.timeout)
    by_id = {row['row_id']: row for row in tested_decisions}
    rows = []
    for original, tested in zip(inventory, tested_inventory):
        result = evaluate(tested, by_id.get(tested['row_id']), observations[tested['request_url']], args.snapshot_only)
        result['request_url'] = original['request_url']
        result['tested_url'] = tested['request_url']
        rows.append(result)
    report = {'checked_utc': utc_now(), 'mode': 'snapshot-only' if args.snapshot_only else 'post-apply-expectations',
              'user_agent': AGENT, 'base_url': stage_origin, 'requests': 'Read-only GET, exact queries and escapes; no wildcard expansion',
              'input_errors': errors, 'inventory_integrity': mirror, 'summary': {'supplied_rows': len(inventory), 'decision_rows': len(decisions),
              'unique_exact_urls': len(observations), 'duplicate_rows': len(inventory) - len(observations),
              'pass': sum(r['result'] == 'PASS' for r in rows), 'fail': sum(r['result'] == 'FAIL' for r in rows),
              'unresolved_rows': sum(bool(r['unresolved']) for r in rows), 'snapshot_drift_rows': sum(bool(r['drift']) for r in rows)}, 'rows': rows}
    write_reports(args.output_prefix, report)
    print(json.dumps({**report['summary'], 'input_errors': len(errors), 'mode': report['mode'], 'base_url': stage_origin, 'json_report': str(args.output_prefix.with_suffix('.json')), 'csv_report': str(args.output_prefix.with_suffix('.csv'))}, indent=2))
    return 1 if errors or report['summary']['fail'] else 0


if __name__ == '__main__':
    try:
        sys.exit(main())
    except (OSError, ValueError, KeyError) as exc:
        print('FAIL:', exc, file=sys.stderr)
        sys.exit(2)
