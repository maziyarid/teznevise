#!/usr/bin/env python3
"""Exercise dormant installer using temporary fake WordPress roots only."""
from pathlib import Path
import subprocess
import tempfile

INSTALL = Path(__file__).with_name('install.py')

def run(operation, root, backup, *flags, success=True):
    result = subprocess.run(['python3', str(INSTALL), operation, str(root), str(backup), *flags], capture_output=True, text=True)
    assert (result.returncode == 0) is success, result.stdout + result.stderr

with tempfile.TemporaryDirectory() as temporary:
    base = Path(temporary)
    wp = base / 'wordpress'
    wp.mkdir()
    (wp / 'wp-load.php').write_text('<?php // fixture')
    backup = base / 'private'
    target = wp / 'wp-content/mu-plugins/teznevise-internal-links-20261110.php'
    run('install', wp, backup)
    assert not target.exists() and not backup.exists(), 'dry-run wrote files'
    run('install', wp, backup, '--apply')
    installed = target.read_bytes()
    assert installed == INSTALL.with_name('runtime.php').read_bytes()
    assert not (backup.stat().st_mode & 0o077)
    run('install', wp, backup, '--apply')
    run('rollback', wp, backup)
    assert target.read_bytes() == installed
    other = base / 'other'
    other.mkdir()
    (other / 'wp-load.php').write_text('<?php // fixture')
    run('install', other, backup, '--apply', success=False)
    target.write_bytes(installed + b'\n// newer')
    run('rollback', wp, backup, '--apply', success=False)
    assert target.read_bytes().endswith(b'// newer')
    target.write_bytes(installed)
    run('rollback', wp, backup, '--apply')
    assert not target.exists()
    run('rollback', wp, backup, '--apply')
    target.write_bytes(b'<?php // prior runtime')
    target.chmod(0o640)
    second = base / 'private2'
    run('install', wp, second, '--apply')
    run('rollback', wp, second, '--apply')
    assert target.read_bytes() == b'<?php // prior runtime'
    assert target.stat().st_mode & 0o777 == 0o640
print('PASS: installer dry-run, idempotence, exact restore, modes, wrong-root and newer-edit guards')
