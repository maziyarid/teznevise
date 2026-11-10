#!/usr/bin/env python3
"""Isolated installer regression tests; never opens a real WordPress installation.

Project: Teznevise WordPress Theme
Author: MAZ//ID (Maziyar)
Brand: MΛZ — https://github.com/maziyarid/M-Z
"""
from pathlib import Path
import subprocess
import sys
import tempfile
import unittest

SCRIPT = Path(__file__).with_name('runtime-files.py')
LOADER = 'teznevise-onpage-20261110.php'
MODULE = 'teznevise-onpage-20261110/seo-onpage-20261110.php'
TEMPLATE = 'teznevise-onpage-20261110/seo-onpage-20261110-template.php'


def snapshot(root):
    """Compare visible filesystem state without volatile modification timestamps."""
    return {
        str(path.relative_to(root)): (
            'directory' if path.is_dir() else 'file',
            path.stat().st_mode & 0o777,
            None if path.is_dir() else path.read_bytes(),
        )
        for path in root.rglob('*')
    }


class RuntimeFilesContract(unittest.TestCase):
    def setUp(self):
        self.temp = tempfile.TemporaryDirectory(prefix='tezseo-runtime-contract-')
        self.base = Path(self.temp.name)
        self.wp = self.base / 'web'
        (self.wp / 'wp-content').mkdir(parents=True)
        self.target = self.wp / 'wp-content/mu-plugins'
        self.backup = self.base / 'private-backup'
        # These sentinel contents must survive every operation untouched.
        (self.wp / 'wp-config.php').write_text('configuration sentinel\n')
        (self.wp / '.env').write_text('environment sentinel\n')
        self.config = {name: (self.wp / name).read_bytes() for name in ('wp-config.php', '.env')}

    def tearDown(self):
        for name, data in self.config.items():
            self.assertEqual((self.wp / name).read_bytes(), data)
        self.temp.cleanup()

    def run_installer(self, action, *options, success=True, wp=None, backup=None):
        result = subprocess.run(
            [sys.executable, str(SCRIPT), action, str(wp or self.wp),
             str(backup or self.backup), *options],
            capture_output=True, text=True, check=False,
        )
        output = result.stdout + result.stderr
        if success:
            self.assertEqual(result.returncode, 0, output)
        else:
            self.assertNotEqual(result.returncode, 0, output)
        return output

    def test_install_dry_run_creates_nothing(self):
        before = snapshot(self.base)
        output = self.run_installer('install', '--dry-run')
        self.assertIn('DRY RUN', output)
        self.assertEqual(snapshot(self.base), before)

    def test_install_repeat_and_rollback_dry_run(self):
        self.run_installer('install')
        for name in (LOADER, MODULE, TEMPLATE):
            self.assertTrue((self.target / name).is_file())
        before = snapshot(self.base)
        self.run_installer('install')
        self.assertEqual(snapshot(self.base), before)
        self.run_installer('rollback', '--dry-run')
        self.assertEqual(snapshot(self.base), before)
        self.run_installer('rollback')
        self.assertFalse(any(self.target.rglob('*.php')))
        before = snapshot(self.base)
        self.run_installer('rollback')
        self.assertEqual(snapshot(self.base), before)

    def test_restore_preexisting_bytes_and_modes(self):
        originals = {LOADER: (b'<?php // previous loader\n', 0o600),
                     MODULE: (b'<?php // previous module\n', 0o640)}
        for name, (data, mode) in originals.items():
            path = self.target / name
            path.parent.mkdir(parents=True, exist_ok=True)
            path.write_bytes(data)
            path.chmod(mode)
        self.run_installer('install')
        self.run_installer('rollback')
        for name, (data, mode) in originals.items():
            path = self.target / name
            self.assertEqual(path.read_bytes(), data)
            self.assertEqual(path.stat().st_mode & 0o777, mode)
        self.assertFalse((self.target / TEMPLATE).exists())

    def test_newer_owner_edits_refuse_install_and_rollback(self):
        self.run_installer('install')
        loader = self.target / LOADER
        loader.write_bytes(b'<?php // newer owner work\n')
        before = snapshot(self.base)
        self.assertIn('changed', self.run_installer('install', success=False))
        self.assertIn('changed', self.run_installer('rollback', success=False))
        self.assertEqual(snapshot(self.base), before)

    def test_tampered_backup_refuses_without_target_mutation(self):
        self.run_installer('install')
        original = self.backup / 'runtime-before.json'
        original.write_bytes(original.read_bytes() + b'\n')
        before = snapshot(self.target)
        self.assertIn('checksum', self.run_installer('rollback', success=False))
        self.assertIn('checksum', self.run_installer('install', success=False))
        self.assertEqual(snapshot(self.target), before)

    def test_backup_is_scoped_to_wordpress_root(self):
        self.run_installer('install')
        other = self.base / 'another-web'
        (other / 'wp-content').mkdir(parents=True)
        before = snapshot(self.base)
        self.assertIn('another root', self.run_installer('rollback', wp=other, success=False))
        self.assertEqual(snapshot(self.base), before)

    def test_unsafe_backup_and_symlink_targets_refused(self):
        unsafe = self.wp / 'backup'
        self.run_installer('install', backup=unsafe, success=False)
        self.assertFalse(unsafe.exists())
        self.target.mkdir()
        destination = self.wp / 'unrelated.php'
        destination.write_bytes(b'preserve me\n')
        (self.target / LOADER).symlink_to(destination)
        before = snapshot(self.base)
        self.assertIn('symlink', self.run_installer('install', success=False))
        self.assertEqual(snapshot(self.base), before)
        self.assertFalse(self.backup.exists())


if __name__ == '__main__':
    unittest.main(verbosity=2)
