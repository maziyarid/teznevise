#!/usr/bin/env python3
"""Install/restore only the dormant MU runtime; dry-run unless --apply."""
import argparse
import base64
import hashlib
import json
import os
from pathlib import Path

def digest(data):
    return hashlib.sha256(data).hexdigest()

def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('operation', choices=['install', 'rollback'])
    parser.add_argument('wordpress', type=Path)
    parser.add_argument('backup', type=Path)
    parser.add_argument('--apply', action='store_true')
    parser.add_argument('--dry-run', action='store_true')
    args = parser.parse_args()
    if args.apply and args.dry_run:
        parser.error('Choose --apply or --dry-run')
    wp = args.wordpress.resolve(strict=True)
    if not (wp / 'wp-load.php').is_file():
        raise ValueError('WordPress root must contain wp-load.php')
    if not args.backup.is_absolute() or args.backup.is_symlink():
        raise ValueError('Backup must be an absolute, non-symlink private directory')
    backup = args.backup.parent.resolve(strict=True) / args.backup.name
    if backup == wp or wp in backup.parents:
        raise ValueError('Backup must be outside the WordPress webroot')
    if backup.exists() and not backup.is_dir():
        raise ValueError('Backup path must be a directory')
    content = Path(__file__).with_name('runtime.php').read_bytes()
    mu = wp / 'wp-content' / 'mu-plugins'
    target = mu / 'teznevise-internal-links-20261110.php'
    if any(path.is_symlink() for path in (wp / 'wp-content', mu, target)):
        raise ValueError('Refusing symlink runtime paths')
    journal_file = backup / 'runtime.json'
    journal = json.loads(journal_file.read_text()) if journal_file.exists() else None
    current = target.read_bytes() if target.exists() else None
    current_hash = digest(current) if current is not None else None
    if args.operation == 'install':
        if journal:
            if journal['target'] != str(target):
                raise ValueError('Backup belongs to another WordPress root')
            if journal['installed_hash'] != digest(content):
                raise ValueError('Reviewed runtime changed since backup')
            if current_hash == journal['installed_hash']:
                print(json.dumps({'unchanged': True, 'target': str(target)}))
                return
            if current_hash != journal['before_hash']:
                raise ValueError('Newer runtime file edits; refusing overwrite')
        else:
            journal = {'target': str(target), 'before': base64.b64encode(current).decode() if current is not None else None,
                       'before_hash': current_hash, 'before_mode': target.stat().st_mode & 0o777 if current is not None else None,
                       'installed_hash': digest(content)}
        if args.apply:
            backup.mkdir(mode=0o700, exist_ok=True)
            backup.chmod(0o700)
            if not journal_file.exists():
                fd = os.open(journal_file, os.O_WRONLY | os.O_CREAT | os.O_EXCL, 0o600)
                with os.fdopen(fd, 'w') as handle:
                    json.dump(journal, handle, indent=2)
                    handle.flush()
                    os.fsync(handle.fileno())
            mu.mkdir(mode=0o755, parents=True, exist_ok=True)
            temp = target.with_suffix('.php.tzil-tmp')
            fd = os.open(temp, os.O_WRONLY | os.O_CREAT | os.O_EXCL, 0o644)
            with os.fdopen(fd, 'wb') as handle:
                handle.write(content)
                handle.flush()
                os.fsync(handle.fileno())
            temp.replace(target)
        print(json.dumps({'dry_run': not args.apply, 'target': str(target), 'active': False}))
    else:
        if not journal or journal['target'] != str(target):
            raise ValueError('Missing or wrong runtime backup')
        if current_hash == journal['before_hash']:
            print(json.dumps({'unchanged': True, 'restored': True}))
            return
        if current_hash != journal['installed_hash']:
            raise ValueError('Newer runtime file edits; refusing restore')
        if args.apply:
            if journal['before'] is None:
                target.unlink()
            else:
                temp = target.with_suffix('.php.tzil-restore')
                fd = os.open(temp, os.O_WRONLY | os.O_CREAT | os.O_EXCL, journal['before_mode'])
                with os.fdopen(fd, 'wb') as handle:
                    handle.write(base64.b64decode(journal['before']))
                    handle.flush()
                    os.fsync(handle.fileno())
                temp.chmod(journal['before_mode'])
                temp.replace(target)
        print(json.dumps({'dry_run': not args.apply, 'restored': str(target)}))

if __name__ == '__main__':
    main()
