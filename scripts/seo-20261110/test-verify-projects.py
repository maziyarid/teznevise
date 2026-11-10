#!/usr/bin/env python3
"""Offline project-map regressions; no HTTP, WordPress or deployment actions.

Project: Teznevise WordPress Theme | Author: MAZ//ID (Maziyar)
Brand: MΛZ — https://github.com/maziyarid/M-Z
"""
from collections import Counter
from copy import deepcopy
import csv
import importlib.util
from pathlib import Path
import tempfile
import unittest

SPEC = importlib.util.spec_from_file_location('verify_projects', Path(__file__).with_name('verify-projects.py'))
PROJECTS = importlib.util.module_from_spec(SPEC)
SPEC.loader.exec_module(PROJECTS)


class ProjectVerifierContract(unittest.TestCase):
    @classmethod
    def setUpClass(cls):
        cls.inventory_path = PROJECTS.AUDIT / 'project-legacy-inventory-20261110.csv'
        cls.decisions_path = PROJECTS.AUDIT / 'project-dead-decisions-20261110.csv'
        cls.authoritative_inventory = PROJECTS.read(cls.inventory_path)
        cls.authoritative_decisions = PROJECTS.read(cls.decisions_path)
        cls.inventory_fields = list(cls.authoritative_inventory[0])
        cls.decision_fields = list(cls.authoritative_decisions[0])
        cls.authoritative_targets = PROJECTS.read(PROJECTS.AUDIT / 'project-dead-targets-20261110.csv')
        cls.target_fields = list(cls.authoritative_targets[0])

    def setUp(self):
        self.temp = tempfile.TemporaryDirectory(prefix='tezseo-project-verifier-')
        self.root = Path(self.temp.name)
        self.catalog = deepcopy(self.authoritative_inventory)
        self.decisions = deepcopy(self.authoritative_decisions)
        self.targets = deepcopy(self.authoritative_targets)

    def tearDown(self):
        self.temp.cleanup()

    def validate(self):
        inventory = self.root / 'inventory.csv'
        decisions = self.root / 'decisions.csv'
        targets = self.root / 'targets.csv'
        for path, rows, fields in ((inventory, self.catalog, self.inventory_fields),
                                   (decisions, self.decisions, self.decision_fields),
                                   (targets, self.targets, self.target_fields)):
            with path.open('w', encoding='utf-8', newline='') as stream:
                writer = csv.DictWriter(stream, fieldnames=fields)
                writer.writeheader()
                writer.writerows(rows)
        return PROJECTS.inputs(inventory, decisions, targets)

    def set_target(self, target):
        self.decisions[0]['target_url'] = target
        self.decisions[0]['expected_canonical'] = target

    def test_actual_source_backed_84_decision_map_passes(self):
        inventory, decisions, errors = PROJECTS.inputs(self.inventory_path, self.decisions_path)
        self.assertEqual(errors, [])
        self.assertEqual(len(self.authoritative_inventory), 135)
        self.assertEqual(len(inventory), 84)
        self.assertEqual(Counter(row['action'] for row in decisions), {'301': 84})
        self.assertEqual(sum(bool(row.get('unresolved_reason')) for row in decisions), 0)
        self.assertTrue(all(row.get('source_evidence', '').strip() for row in decisions))

    def test_missing_dead_url_decision_fails_exact_84_coverage(self):
        self.decisions.pop()
        errors = self.validate()[2]
        self.assertTrue(any('84 dead URLs exactly once' in error for error in errors))

    def test_duplicate_dead_url_decision_fails_coverage_and_unique_id(self):
        self.decisions[-1] = deepcopy(self.decisions[0])
        errors = self.validate()[2]
        self.assertTrue(any('84 dead URLs exactly once' in error for error in errors))
        self.assertTrue(any('Duplicate decision row ID' in error for error in errors))

    def test_duplicate_performance_inventory_url_is_rejected(self):
        self.catalog[-1] = deepcopy(self.catalog[0])
        errors = self.validate()[2]
        self.assertTrue(any('135 unique GSC performance URLs' in error for error in errors))

    def test_homepage_redirect_is_rejected(self):
        self.set_target('https://teznevise.ir/')
        errors = self.validate()[2]
        self.assertTrue(any('homepage redirect' in error for error in errors))

    def test_redirect_to_another_dead_decision_is_rejected(self):
        self.set_target(self.decisions[1]['request_url'])
        errors = self.validate()[2]
        self.assertTrue(any('chained' in error or 'chain or loop' in error for error in errors))

    def test_redirect_to_known_native_redirect_is_rejected(self):
        native = next(row['legacy_url'] for row in self.catalog if row['initial_status'] == '301')
        self.set_target(native)
        errors = self.validate()[2]
        self.assertTrue(any('Unsafe, chained or homepage redirect' in error for error in errors))

    def test_redirect_target_without_fresh_direct_evidence_is_rejected(self):
        self.set_target('https://teznevise.ir/unreviewed-target/')
        errors = self.validate()[2]
        self.assertTrue(any('target evidence' in error for error in errors))

    def test_redirected_blocked_or_noncanonical_target_evidence_is_rejected(self):
        target = self.decisions[0]['target_url']
        for field, value in (('initial_status', '301'), ('terminal_status', '404'),
                             ('final_url', 'https://teznevise.ir/another/'),
                             ('canonical_url', 'https://teznevise.ir/another/'),
                             ('robots_allowed', '0'), ('robots_meta', 'noindex'),
                             ('x_robots_tag', 'noindex')):
            with self.subTest(field=field):
                self.targets = deepcopy(self.authoritative_targets)
                next(row for row in self.targets if row['target_url'] == target)[field] = value
                errors = self.validate()[2]
                self.assertTrue(any('target evidence' in error for error in errors))

    def test_missing_or_blank_source_evidence_is_rejected(self):
        for value in ('', '   '):
            with self.subTest(source_evidence=value):
                self.decisions[0]['source_evidence'] = value
                errors = self.validate()[2]
                self.assertTrue(any('Missing WordPress/repo source evidence' in error for error in errors))

    def test_observed_decision_status_must_match_authoritative_inventory(self):
        for field in ('observed_initial_status', 'observed_final_status'):
            with self.subTest(field=field):
                self.decisions = deepcopy(self.authoritative_decisions)
                self.decisions[0][field] = '200'
                errors = self.validate()[2]
                self.assertTrue(any('Decision baseline status differs from inventory' in error for error in errors))

    def test_changed_inventory_status_invalidates_the_old_404_decision(self):
        source = self.decisions[0]['request_url']
        row = next(row for row in self.catalog if row['legacy_url'] == source)
        row['initial_status'] = row['terminal_status'] = '200'
        errors = self.validate()[2]
        self.assertTrue(any('84 initial 404 URLs' in error for error in errors))
        self.assertTrue(any('Decision baseline status differs from inventory' in error for error in errors))


if __name__ == '__main__':
    unittest.main(verbosity=2)
