import importlib.util
import json
from pathlib import Path
import tempfile
import unittest
from unittest.mock import patch
spec=importlib.util.spec_from_file_location('gate',Path(__file__).with_name('verify_content.py'))
v=importlib.util.module_from_spec(spec);spec.loader.exec_module(v)
BAD=''.join(map(chr,[0x645,0x634,0x627,0x648,0x631,0x647]))

class CopyGate(unittest.TestCase):
    def test_all_visible_controls(self):
        html=''.join('<'+t+'>'+BAD+'</'+t+'>' for t in ['a','button','label','option','title','h1'])
        p=v.rendered('http://test/',html)
        self.assertEqual(len([e for e in p.errors if e['rule']=='lexicon']),6)
    def test_attributes(self):
        html='<input value="'+BAD+'" placeholder="'+BAD+'"><img alt="'+BAD+'"><div title="'+BAD+'" aria-label="'+BAD+'"></div>'
        self.assertEqual(len(v.rendered('http://test/',html).errors),5)
    def test_social_meta(self):
        html='<meta property="og:title" content="'+BAD+'"><meta name="twitter:description" content="'+BAD+'">'
        self.assertEqual(len(v.rendered('http://test/',html).errors),2)
    def test_escaped_json_and_breadcrumb(self):
        text=json.dumps({'@type':'BreadcrumbList','itemListElement':[{'name':BAD}]},ensure_ascii=True)
        p=v.rendered('http://test/','<script type="application/ld+json">'+text+'</script>')
        self.assertTrue(any(e['rule']=='lexicon' and 'JSON-LD' in e['location'] for e in p.errors))
    def test_json_default_is_decoded(self):
        with tempfile.TemporaryDirectory() as td:
            root=Path(td);(root/'builder-defaults.json').write_text(json.dumps({'title':BAD},ensure_ascii=True))
            with patch.object(v,'ROOT',root),patch.object(v.subprocess,'check_output',return_value=b'builder-defaults.json\0'):
                result=v.repo_scan()
            self.assertEqual(len(result['errors']),1)
    def test_typography(self):
        for code in [0x200c,0x064a,0x0643,0x0660,0x0669]:
            self.assertTrue(any(e['rule']=='typography' for e in v.findings(chr(code),'test')))
        self.assertEqual(v.findings('MATLAB R2026a https://example.org/2026','test'),[])
    def test_optional_faq(self):
        self.assertEqual(v.rendered('http://test/','<h1>Guide</h1>').errors,[])
    def test_faq_text_must_match(self):
        faq={'@type':'FAQPage','mainEntity':[{'name':'Question?','acceptedAnswer':{'text':'Answer.'}}]}
        script='<script type="application/ld+json">'+json.dumps(faq)+'</script>'
        self.assertEqual(v.rendered('http://test/','<button>Question?</button><p>Answer.</p>'+script).errors,[])
        self.assertTrue(any(e['rule']=='faq-schema-mismatch' for e in v.rendered('http://test/',script).errors))
    def test_one_schema_owner(self):
        html='<script class="rank-math-schema" type="application/ld+json">{}</script><script type="application/ld+json">{}</script>'
        self.assertTrue(any(e['rule']=='mixed-schema-sources' for e in v.rendered('http://test/',html).errors))
    def test_claim_families_and_units(self):
        for value in ['\u0636\u0645\u0627\u0646\u062a\u06cc', '\u062a\u0636\u0645\u06cc\u0646\u06cc', '\u0631\u062a\u0628\u0647 \u06cc\u06a9', '\u06f1\u06f0\u06f0 \u062f\u0631\u0635\u062f', '\u06f2 \u0631\u0648\u0632', '\u06f5\u06f0 \u062a\u0648\u0645\u0627\u0646']:
            self.assertTrue(any(e['rule']=='claim' for e in v.findings(value,'test')),value)
    def test_english_family(self):
        self.assertTrue(v.WORD.search('con'+'sultancy'))
    def test_sitemap_does_not_crawl_image_locations(self):
        xml='<urlset xmlns:image="http://example.org/image"><url><loc>https://test/page/</loc><image:image><image:loc>https://test/image.png</image:loc></image:image></url></urlset>'
        with patch.object(v,'fetch',return_value=(200,xml,None)):
            urls,errors,sources=v.sitemap_urls('https://test')
        self.assertEqual(urls,{'https://test/page/'})
    def test_footer_is_outside_anchor_budget(self):
        label='\u0627\u0646\u062c\u0627\u0645 \u067e\u0627\u06cc\u0627\u0646 \u0646\u0627\u0645\u0647'
        a='<a href="/thesis/">'+label+'</a>'
        p=v.rendered('http://test/','<main>'+a+'</main><footer>'+a+'</footer>')
        self.assertFalse(any(e['rule']=='exact-anchor-budget' for e in p.errors))
        p=v.rendered('http://test/','<main>'+a+a+'</main>')
        self.assertTrue(any(e['rule']=='exact-anchor-budget' for e in p.errors))
    def test_draft_pages_are_not_in_notes_allowlist(self):
        with tempfile.TemporaryDirectory() as td:
            root=Path(td);p=root/'seo/20261110/content/pages/test.md';p.parent.mkdir(parents=True);p.write_text(BAD)
            with patch.object(v,'ROOT',root),patch.object(v.subprocess,'check_output',return_value=b'seo/20261110/content/pages/test.md\0'):
                self.assertEqual(len(v.repo_scan()['errors']),1)
    def test_compiled_translation_original_and_translation(self):
        import struct
        original=['',BAD];translated=['Content-Type: text/plain; charset=UTF-8\n','Clean']
        originals=[x.encode() for x in original];translations=[x.encode() for x in translated]
        offset=60;ot=[];tt=[];payload=b''
        for value in originals:
            ot.append((len(value),offset+len(payload)));payload+=value+b'\0'
        for value in translations:
            tt.append((len(value),offset+len(payload)));payload+=value+b'\0'
        binary=struct.pack('<7I',0x950412de,0,2,28,44,0,0)+b''.join(struct.pack('<2I',*x) for x in ot+tt)+payload
        with tempfile.TemporaryDirectory() as td:
            root=Path(td);(root/'languages').mkdir();(root/'languages/fa.mo').write_bytes(binary)
            with patch.object(v,'ROOT',root),patch.object(v.subprocess,'check_output',return_value=b'languages/fa.mo\0'):
                self.assertTrue(any(e['rule']=='lexicon' for e in v.repo_scan()['errors']))
    def test_invalid_translation_cannot_be_silently_skipped(self):
        with tempfile.TemporaryDirectory() as td:
            root=Path(td);(root/'languages').mkdir();(root/'languages/fa.po').write_bytes(b'\xff\xfe\0')
            with patch.object(v,'ROOT',root),patch.object(v.subprocess,'check_output',return_value=b'languages/fa.po\0'):
                self.assertTrue(any(e['rule']=='decode-error' for e in v.repo_scan()['errors']))
    def test_plain_breadcrumb_current_name(self):
        self.assertTrue(v.rendered('http://test/','<span class="breadcrumb-current">'+BAD+'</span>').errors)
    def test_json_keys_and_mime_parameters(self):
        html='<script type="application/ld+json; charset=utf-8">'+json.dumps({BAD:'value'})+'</script>'
        self.assertTrue(v.rendered('http://test/',html).errors)
    def test_inline_formatting_does_not_invent_spaces(self):
        faq={'@type':'FAQPage','mainEntity':[{'name':'Question?','acceptedAnswer':{'text':'Answer.'}}]}
        html='<button>Q<em>uestion</em>?</button><p>An<strong>swer.</strong></p><script type="application/ld+json">'+json.dumps(faq)+'</script>'
        self.assertEqual(v.rendered('http://test/',html).errors,[])
    def test_ratings_are_rejected(self):
        self.assertTrue(any(e['rule']=='prohibited-rating-schema' for e in v.rendered('http://test/','<script type="application/ld+json">{"@type":"AggregateRating"}</script>').errors))
    def test_legal_review_owner(self):
        base={'path':'/test/','legal_review':{'status':'done','reviewed_by':'someone','reviewed_at':'2026-11-11','approval_reference':'test'}}
        self.assertTrue(v.legal_gate({'pages':[base]}))
        base['legal_review']['reviewed_by']='Maziyar'
        self.assertFalse(v.legal_gate({'pages':[base]}))

if __name__=='__main__':unittest.main()
