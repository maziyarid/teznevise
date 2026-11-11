import importlib.util
import unittest
from pathlib import Path
s = importlib.util.spec_from_file_location('verify', Path(__file__).with_name('verify_content.py'))
v = importlib.util.module_from_spec(s)
s.loader.exec_module(v)

class RenderedContract(unittest.TestCase):
    def test_nested_text_and_accessible_names(self):
        rows = v.rendered_errors('https://teznevise.ir/thesis/', '<a href="/inquiry/"><span>درخواست</span> مشاوره</a><button aria-label="مشاوره"></button>')
        self.assertEqual([r['element'] for r in rows], ['a', 'button[aria-label]'])
        self.assertEqual(rows[0]['current_text'], 'درخواست مشاوره')
    def test_scope_of_head_metadata(self):
        html = '<title>مشاوره</title><h1>مشاوره</h1><meta name="description" content="مشاوره">'
        self.assertEqual(len(v.rendered_errors('https://teznevise.ir/thesis/phd/', html)), 3)
        self.assertEqual(v.rendered_errors('https://teznevise.ir/about-us/', html), [])
    def test_plain_text_is_not_an_anchor(self):
        self.assertEqual(v.rendered_errors('https://teznevise.ir/thesis/', '<p>مشاوره</p><a href="/inquiry/">ثبت درخواست</a>'), [])
    def test_invalid_schema_and_unpublished_link_fail(self):
        errors = v.body_errors('/thesis/', '# متن\n## پرسش ها\n[پژوهش](/thesis/master/)\n```json\n{invalid}\n```', 1)
        self.assertTrue(any('unpublished' in e for e in errors))
        self.assertTrue(any('invalid JSON' in e for e in errors))

if __name__ == '__main__': unittest.main()
