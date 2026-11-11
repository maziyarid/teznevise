"""FACT: Offline validation of job 3 review artefacts; no write capability."""
import csv
import json
from pathlib import Path

ROOT = Path(__file__).resolve().parent
LABELS = {'FACT', 'INFERENCE', 'HYPOTHESIS', 'UNKNOWN'}


def main():
    for path in ROOT.rglob('*'):
        if path.is_file() and '__pycache__' not in path.parts:
            assert chr(0x200c) not in path.read_text(), str(path)
    data = json.loads((ROOT / 'changes.json').read_text())['changes']
    with (ROOT / 'changes.csv').open(newline='') as handle:
        assert list(csv.DictReader(handle)) == data
    assert len({row['id'] for row in data}) == len(data)
    for row in data:
        assert row['claim_label'] in LABELS
        assert row['rollback'] and row['acceptance'] and row['owner']
        assert row['status'] in {'needs-yes', 'blocked-evidence', 'owned-elsewhere', 'verified-no-change'}
    assert sum(row['id'].startswith('J3-B') for row in data) == 13
    with (ROOT / 'project-decisions.csv').open(newline='') as handle:
        projects = list(csv.DictReader(handle))
    assert len(projects) == 84
    assert len({row['source_url'] for row in projects}) == 84
    assert sum(row['proposal'] == 'restore-same-slug' for row in projects) == 15
    assert all(row['target_url'] == 'UNKNOWN' for row in projects[15:])
    assert all(row['rollback'] and row['acceptance'] for row in projects)
    evidence = json.loads((ROOT / 'live-evidence.json').read_text())
    assert len({row['url'] for row in evidence}) == len(evidence)
    assert all(row['claim_label'] in LABELS for row in evidence)
    print('FACT: validation passed; 84 project decisions, 15 restoration proposals, 13 cross-site re-point proposals; no writes.')


if __name__ == '__main__':
    main()
