#!/usr/bin/env python3
"""Refuse unreadable headers, downgrades and candidates below the deployed floor."""
import argparse
from pathlib import Path
import re
import sys

FLOOR = (1, 9, 35)

def version(path):
    text = Path(path).read_text(encoding='utf-8')
    match = re.search(r'^Version:\s*(\d+)\.(\d+)\.(\d+)\s*$', text, re.M)
    if not match:
        raise ValueError(f'No unambiguous numeric theme header: {path}')
    return tuple(map(int, match.groups()))

def check(candidate, installed):
    proposed = version(candidate)
    current = version(installed)
    if proposed < max(FLOOR, current):
        raise ValueError(f'Refusing theme {proposed}: required floor {FLOOR}, installed {current}')
    return proposed

def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--candidate', type=Path, required=True)
    ap.add_argument('--installed', type=Path, required=True)
    args = ap.parse_args()
    try:
        result = check(args.candidate, args.installed)
    except (OSError, ValueError) as exc:
        print(f'DEPLOY REFUSED: {exc}', file=sys.stderr)
        return 1
    print(f'Version guard passed: {result}')
    return 0

if __name__ == '__main__':
    sys.exit(main())
