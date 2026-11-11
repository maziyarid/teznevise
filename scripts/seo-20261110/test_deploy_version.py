import importlib.util
from pathlib import Path
import tempfile
import unittest
s = importlib.util.spec_from_file_location('guard', Path(__file__).with_name('check_deploy_version.py'))
v = importlib.util.module_from_spec(s)
s.loader.exec_module(v)

class DeployGuard(unittest.TestCase):
    def exercise(self, candidate, installed, allowed):
        with tempfile.TemporaryDirectory() as td:
            a, b = Path(td)/'candidate.css', Path(td)/'installed.css'
            a.write_text('Version: '+candidate+'\n')
            if installed is not None: b.write_text('Version: '+installed+'\n')
            if allowed: self.assertEqual(v.check(a,b), tuple(map(int,candidate.split('.'))))
            else:
                with self.assertRaises((ValueError,OSError)): v.check(a,b)
    def test_floor(self): self.exercise('1.9.34','1.9.20',False)
    def test_live_downgrade(self): self.exercise('1.9.35','1.9.36',False)
    def test_equal(self): self.exercise('1.9.35','1.9.35',True)
    def test_upgrade(self): self.exercise('1.9.36','1.9.35',True)
    def test_missing_installed(self): self.exercise('1.9.36',None,False)
    def test_malformed(self): self.exercise('unknown','1.9.35',False)

if __name__ == '__main__': unittest.main()
