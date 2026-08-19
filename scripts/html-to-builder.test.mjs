#!/usr/bin/env node
/**
 * Conversion inventory + schema tests (no WordPress runtime required).
 */

import assert from 'node:assert/strict';
import { listHtmlFiles, loadDefaults, validateDefaults, analyzeHtml } from './html-to-builder.mjs';

const htmlFiles = listHtmlFiles();
const defaults = loadDefaults();
const errors = validateDefaults(defaults, htmlFiles);

assert.equal(htmlFiles.length, 16, 'teznevise_work/ must contain 16 top-level HTML pages');
assert.ok(htmlFiles.includes('post-sample.html'), 'post-sample.html must be in the inventory');
assert.ok(htmlFiles.includes('blog.html'));
assert.ok(htmlFiles.includes('404.html'));
assert.equal(errors.length, 0, errors.join('\n'));

assert.equal(Object.keys(defaults.pages).length, 14);
assert.equal(defaults.pages['post-sample'].template, 'single.php');
assert.equal(defaults.pages.inquiry.template, 'page-contact.php');
assert.equal(defaults.pages['service-thesis'].template, 'page-service.php');
assert.equal(defaults.excluded.blog.template, 'home.php');
assert.equal(defaults.excluded['404'].template, '404.php');

for (const page of Object.values(defaults.pages)) {
  assert.match(page.url, /^\//, `${page.source} must keep a root-relative permalink`);
  assert.ok(page.sections.length > 0, `${page.source} must have at least one section`);
}

const about = analyzeHtml('about.html');
assert.ok(about.sectionCount >= 5, 'about.html should still have the mapped story/mission/timeline sections');

console.log('html-to-builder tests passed');
