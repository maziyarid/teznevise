#!/usr/bin/env node
/**
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 * Brand: MΛZ — https://github.com/maziyarid/M-Z
 *
 * Analyze teznevise_work/*.html against inc/builder-defaults.json.
 *
 * Usage:
 *   node scripts/html-to-builder.mjs            # print inventory + mapping
 *   node scripts/html-to-builder.mjs --check    # exit 1 if inventory/schema fails
 */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const htmlDir = path.join(root, 'teznevise_work');
const defaultsPath = path.join(root, 'inc', 'builder-defaults.json');

const SECTION_TYPES = [
  'hero',
  'software_catalog',
  'challenges',
  'service_cards',
  'feature_list',
  'process_steps',
  'cta_band',
];

const REQUIRED_TEMPLATE = {
  'about.html': 'page-about.php',
  'contact.html': 'page-contact.php',
  'downloads.html': 'page-downloads.php',
  'index.html': 'front-page.php',
  'inquiry.html': 'page-contact.php',
  'privacy.html': 'page-privacy.php',
  'service-proposal.html': 'page-service.php',
  'service-simulation.html': 'page-service.php',
  'service-statistics.html': 'page-service.php',
  'service-thesis.html': 'page-service.php',
  'team.html': 'page-team.php',
  'tools.html': 'page-tools.php',
  'tool-descriptive-statistics.html': 'page-tool.php',
  'post-sample.html': 'single.php',
  'blog.html': 'home.php',
  '404.html': '404.php',
};

export function listHtmlFiles() {
  return fs
    .readdirSync(htmlDir)
    .filter((name) => name.endsWith('.html'))
    .sort();
}

export function loadDefaults() {
  return JSON.parse(fs.readFileSync(defaultsPath, 'utf8'));
}

function isHttpish(url) {
  return /^(https?:)?\/\//i.test(url) || url.startsWith('/') || url.startsWith('#') || url === '';
}

export function validateDefaults(defaults, htmlFiles) {
  const errors = [];
  const pages = defaults.pages || {};
  const excluded = defaults.excluded || {};
  const meta = defaults.meta || {};

  if (htmlFiles.length !== 16) {
    errors.push(`expected 16 HTML files, found ${htmlFiles.length}: ${htmlFiles.join(', ')}`);
  }
  if (meta.html_inventory !== 16) {
    errors.push(`meta.html_inventory is ${meta.html_inventory}, expected 16`);
  }
  if (Object.keys(pages).length !== 14) {
    errors.push(`expected 14 singular builder sources, found ${Object.keys(pages).length}`);
  }
  if (Object.keys(excluded).length !== 2) {
    errors.push(`expected 2 excluded sources, found ${Object.keys(excluded).length}`);
  }
  if (!pages['post-sample']) {
    errors.push('post-sample.html is not mapped in pages');
  }
  if (!excluded.blog || excluded.blog.builder !== false) {
    errors.push('blog.html must be excluded from the builder');
  }
  if (!excluded['404'] || excluded['404'].builder !== false) {
    errors.push('404.html must be excluded from the builder');
  }

  const mappedSources = [
    ...Object.values(pages).map((page) => page.source),
    ...Object.values(excluded).map((page) => page.source),
  ].sort();
  const missing = htmlFiles.filter((name) => !mappedSources.includes(name));
  const extra = mappedSources.filter((name) => !htmlFiles.includes(name));
  if (missing.length) {
    errors.push(`defaults omit HTML files: ${missing.join(', ')}`);
  }
  if (extra.length) {
    errors.push(`defaults reference missing HTML: ${extra.join(', ')}`);
  }

  for (const [source, template] of Object.entries(REQUIRED_TEMPLATE)) {
    const entry =
      Object.values(pages).find((page) => page.source === source) ||
      Object.values(excluded).find((page) => page.source === source);
    if (!entry) {
      errors.push(`no mapping for ${source}`);
      continue;
    }
    if (entry.template !== template) {
      errors.push(`${source} should map to ${template}, got ${entry.template}`);
    }
  }

  for (const [key, page] of Object.entries(pages)) {
    if (!page.builder) {
      errors.push(`${key} is in pages but builder=false`);
    }
    if (!Array.isArray(page.sections) || page.sections.length === 0) {
      errors.push(`${key} has no sections`);
      continue;
    }
    if (!page.url || !page.url.startsWith('/')) {
      errors.push(`${key} is missing a root-relative url (SEO slug preservation)`);
    }
    page.sections.forEach((section, index) => {
      if (!SECTION_TYPES.includes(section.type)) {
        errors.push(`${key}[${index}] unknown type ${section.type}`);
      }
      if (section.enabled !== true) {
        errors.push(`${key}[${index}] should be enabled`);
      }
      const items = Array.isArray(section.items) ? section.items : [];
      items.forEach((item, itemIndex) => {
        if (item.url && !isHttpish(item.url)) {
          errors.push(`${key}[${index}].items[${itemIndex}] has unsafe url ${item.url}`);
        }
      });
      if (section.cta_url && !isHttpish(section.cta_url)) {
        errors.push(`${key}[${index}] has unsafe cta_url ${section.cta_url}`);
      }
    });
  }

  return errors;
}

export function analyzeHtml(fileName) {
  const html = fs.readFileSync(path.join(htmlDir, fileName), 'utf8');
  const main = html.match(/<main[^>]*>([\s\S]*?)<\/main>/i);
  const body = main ? main[1] : html;
  const sections = [...body.matchAll(/<section\b([^>]*)>([\s\S]*?)<\/section>/gi)];
  const title = (html.match(/<title>([\s\S]*?)<\/title>/i) || [, ''])[1].replace(/\s+/g, ' ').trim();
  return {
    file: fileName,
    title,
    sectionCount: sections.length,
  };
}

function printReport(defaults, htmlFiles) {
  console.log('HTML inventory:', htmlFiles.length);
  htmlFiles.forEach((name) => {
    const info = analyzeHtml(name);
    console.log(`  ${name.padEnd(38)} sections=${String(info.sectionCount).padStart(2)}  ${info.title}`);
  });
  console.log('\nBuilder mappings:');
  for (const [key, page] of Object.entries(defaults.pages)) {
    console.log(
      `  ${page.source.padEnd(38)} → ${page.template.padEnd(20)} ${page.sections.length} sections  ${page.url}`,
    );
  }
  console.log('\nExcluded from builder:');
  for (const page of Object.values(defaults.excluded)) {
    console.log(`  ${page.source.padEnd(38)} → ${page.template}  (${page.reason.split('.')[0]})`);
  }
}

const isMain = process.argv[1] && path.resolve(process.argv[1]) === fileURLToPath(import.meta.url);
if (isMain) {
  const defaults = loadDefaults();
  const htmlFiles = listHtmlFiles();
  const errors = validateDefaults(defaults, htmlFiles);
  if (!process.argv.includes('--quiet')) {
    printReport(defaults, htmlFiles);
  }
  if (errors.length) {
    console.error('\nValidation failed:');
    errors.forEach((error) => console.error(' -', error));
    process.exit(1);
  }
  console.log('\nValidation passed: 16 HTML files, 14 builder sources, 2 documented exclusions.');
  if (process.argv.includes('--check')) {
    process.exit(0);
  }
}
