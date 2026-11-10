#!/usr/bin/env python3
"""Install/restore only the reviewed standalone runtime; never theme/config files."""
import argparse
import base64
import fcntl
import hashlib
import json
import os
from pathlib import Path
import tempfile

ROOT = Path(__file__).resolve().parents[2]
FILES = {
    'teznevise-onpage-20261110/seo-onpage-20261110.php': ROOT / 'inc/seo-onpage-20261110.php',
    'teznevise-onpage-20261110/seo-onpage-20261110-template.php': ROOT / 'inc/seo-onpage-20261110-template.php',
    'teznevise-onpage-20261110/seo-gsc-20261110.php': ROOT / 'inc/seo-gsc-20261110.php',
}
LOADER = b"<?php\n/** Project: Teznevise | Author: MAZ//ID (Maziyar) | @package Teznevise */\nrequire_once __DIR__ . '/teznevise-onpage-20261110/seo-onpage-20261110.php';\n"

def digest(data): return hashlib.sha256(data).hexdigest()

def atomic(path, data, mode=0o600):
    path.parent.mkdir(parents=True, exist_ok=True)
    fd, tmp = tempfile.mkstemp(prefix='.seo-', dir=path.parent)
    try:
        with os.fdopen(fd, 'wb') as f:
            f.write(data)
            f.flush()
            os.fsync(f.fileno())
        os.chmod(tmp, mode)
        os.replace(tmp, path)
    finally:
        if os.path.exists(tmp): os.unlink(tmp)

def main():
    p = argparse.ArgumentParser(description=__doc__)
    p.add_argument('action', choices=['install', 'rollback'])
    p.add_argument('wordpress_root', type=Path)
    p.add_argument('private_backup', type=Path)
    p.add_argument('--dry-run', action='store_true')
    a = p.parse_args()
    assert a.wordpress_root.is_absolute() and a.private_backup.is_absolute(), 'Absolute paths required'
    wp = a.wordpress_root.resolve(strict=True)
    backup = a.private_backup.resolve()
    assert not backup.is_relative_to(wp), 'Backup must be outside web root'
    assert not a.private_backup.is_symlink(), 'Backup symlink refused'
    assert (wp / 'wp-content').is_dir(), 'Expected existing WordPress wp-content'
    if backup.exists(): assert backup.stat().st_mode & 0o077 == 0, 'Backup requires mode 0700'
    target = wp / 'wp-content/mu-plugins'
    desired = {name: source.read_bytes() for name, source in FILES.items()}
    desired['teznevise-onpage-20261110.php'] = LOADER
    def current(name):
        path = target / name
        assert not path.is_symlink(), 'Runtime symlink refused'
        assert path.resolve().is_relative_to(wp), 'Runtime target escapes WordPress root'
        return path.read_bytes() if path.exists() else None
    saved_file = backup / 'runtime-before.json'
    checksum = backup / 'runtime-before.sha256'
    if a.action == 'rollback' or saved_file.exists():
        data = saved_file.read_bytes()
        assert digest(data) == checksum.read_text().strip(), 'Runtime backup checksum mismatch'
        saved = json.loads(data)
        assert saved['wordpress_root'] == str(wp), 'Backup belongs to another root'
    else:
        saved = {'wordpress_root': str(wp), 'files': {name: {'before': base64.b64encode(current(name)).decode() if current(name) is not None else None, 'before_mode': (target / name).stat().st_mode & 0o777 if current(name) is not None else None, 'installed_sha256': digest(data)} for name, data in desired.items()}}
    if a.action == 'install':
        for name, data in desired.items():
            record = saved['files'][name]
            before = base64.b64decode(record['before']) if record['before'] is not None else None
            assert digest(data) == record['installed_sha256'], 'Package revision changed; use a fresh backup directory'
            assert current(name) in (before, data), 'Runtime file changed; refusing overwrite'
    else:
        for name, record in saved['files'].items():
            before = base64.b64decode(record['before']) if record['before'] is not None else None
            data = current(name)
            assert data == before or (data is not None and digest(data) == record['installed_sha256']), 'Runtime file changed; refusing rollback overwrite'
    if a.dry_run:
        print('DRY RUN:', a.action, len(desired), 'runtime files; no writes')
        return
    backup.mkdir(mode=0o700, parents=False, exist_ok=True)
    with (backup / 'lock').open('a') as lock:
        os.chmod(backup / 'lock', 0o600)
        fcntl.flock(lock, fcntl.LOCK_EX | fcntl.LOCK_NB)
        if not saved_file.exists():
            data = (json.dumps(saved, indent=2) + '\n').encode()
            atomic(saved_file, data)
            atomic(checksum, (digest(data) + '\n').encode())
        # Module and template precede loader on install; loader is removed/restored first on rollback.
        names = list(desired) if a.action == 'install' else list(reversed(desired))
        for name in names:
            path = target / name
            if a.action == 'install': atomic(path, desired[name], 0o644)
            else:
                value = saved['files'][name]['before']
                if value is None: path.unlink(missing_ok=True)
                else: atomic(path, base64.b64decode(value), saved['files'][name]['before_mode'])
    print(a.action.upper(), 'complete: runtime files only; no WordPress content changes')

if __name__ == '__main__':
    try: main()
    except (AssertionError, OSError, ValueError) as e: raise SystemExit(str(e))
