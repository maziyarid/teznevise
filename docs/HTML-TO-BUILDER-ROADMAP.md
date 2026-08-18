# Flexible Page Builder - HTML to WordPress Conversion Roadmap

## Overview
This roadmap outlines the systematic conversion of static HTML files from 
`teznevise_work/` into WordPress pages using the Flexible Page Builder system.

## Primary Goal
Convert 15 static HTML pages into editable WordPress pages using the builder 
meta-box, enabling non-technical users to manage content without code edits.

## Phase 1: Analysis & Planning (COMPLETE)
- Identified 15 HTML files in `teznevise_work/`
- Verified Flexible Page Builder is merged and active (v1.6.0)
- Confirmed 7 section types available
- Mobile menu issue resolved

## Files to Convert
| # | File | WordPress Template | Priority |
|---|------|-------------------|----------|
| 1 | about.html | page-about.php | High |
| 2 | contact.html | page-contact.php | High |
| 3 | downloads.html | page-downloads.php | High |
| 4 | index.html | front-page.php | High |
| 5 | inquiry.html | page-inquiry.php | High |
| 6 | privacy.html | page-privacy.php | High |
| 7 | service-proposal.html | New template | High |
| 8 | service-simulation.html | New template | High |
| 9 | service-statistics.html | New template | High |
| 10 | service-thesis.html | New template | High |
| 11 | team.html | page-team.php | High |
| 12 | tools.html | page-tools.php | High |
| 13 | tool-descriptive-statistics.html | New template | Medium |
| 14 | blog.html | archive.php | Medium |
| 15 | 404.html | 404.php | Medium |

## Phases

### Phase 2: Conversion Execution (6 days)
- Analyze HTML structure
- Create Node.js conversion scripts
- Convert files in batches
- Handle special cases

### Phase 3: Testing & Validation (3 days)
- Unit testing
- Integration testing
- Content validation

### Phase 4: Documentation (2 days)
- User guides
- Sample pages

### Phase 5: Advanced Features (5 days, Optional)
- Frontend click-to-edit
- Additional section types

## Timeline
| Phase | Duration | Dates |
|-------|----------|-------|
| Phase 1 | 1 day | Aug 18 (COMPLETE) |
| Phase 2 | 6 days | Aug 19-24 |
| Phase 3 | 3 days | Aug 25-27 |
| Phase 4 | 2 days | Aug 28-29 |
| Phase 5 | 5 days | Aug 30 - Sep 3 |

## Deliverables
1. 15 WordPress page templates using Flexible Page Builder
2. Conversion scripts (Node.js)
3. Test suite
4. User documentation

## PR Information
- Branch: feature/html-to-builder
- Related: PR #9 (Flexible Page Builder), PR #384 (Mobile menu fixes)