# PR: Flexible Page Builder with Custom Sections

## Status: Planning Phase - Awaiting Approval

**Priority:** High  
**Related:** Menu fixes, Theme redesign, Content management  
**Author:** MAZ//ID (Maziyar)  
**Date:** August 18, 2026

---

## Overview

This PR introduces a flexible page builder system that allows:
- Add unlimited items to any section (software, challenges, services, etc.)
- Duplicate items with one click
- Remove individual items or entire sections
- Reorder items via drag-and-drop
- Upload SVGs or choose Font Awesome icons
- Upload images for visual process panels
- Frontend visual editing (click-to-edit)
- Backend form-based editing (for power users)

---

## Files to Create/Modify

| File | Action | Purpose |
|------|--------|---------|
| inc/class-teznevise-builder.php | CREATE | Core builder class |
| inc/admin/builder-admin.php | CREATE | Admin UI |
| inc/admin/builder-assets.php | CREATE | Asset registration |
| inc/defaults.php | MODIFY | Add default values |
| functions.php | MODIFY | Register admin pages |
| page-service-thesis.php | MODIFY | Use builder system |
| page-service-statistics.php | MODIFY | Use builder system |
| page-about.php | MODIFY | Use builder system |
| page-contact.php | MODIFY | Use builder system |
| page-downloads.php | MODIFY | Use builder system |
| page-tools.php | MODIFY | Use builder system |
| page-team.php | MODIFY | Use builder system |
| page-privacy.php | MODIFY | Use builder system |
| single.php | MODIFY | Use builder system |
| assets/css/builder-admin.css | CREATE | Admin styling |
| assets/js/builder-admin.js | CREATE | Admin functionality |
| assets/css/builder-frontend.css | CREATE | Frontend styling |
| assets/js/builder-frontend.js | CREATE | Frontend functionality |

---

## Technical Architecture

### Data Storage
All section data stored as JSON in post meta with prefix _teznevise_:

Example:
section_title: Software Catalog
section_subtitle: Description here
categories: [ { title: Category 1, items: [ { icon_type: fontawesome, icon: fa-solid fa-chart-area, name: Software Name, url: /link/ } ] } ]

### Section Types
- software_catalog: Grid of software/tools with icons
- challenges: Grid of challenge cards with icons
- process_steps: Step-by-step process with visual panels
- service_cards: Grid of service cards
- hero: Page hero section
- cta_band: Call-to-action band

---

## Core Features

### Feature 1: Repeatable Items
- Add unlimited items
- Duplicate with one click
- Remove individual items
- Reorder via drag-and-drop

### Feature 2: Section Management
- Add new sections to any page
- Remove entire sections
- Reorder sections

### Feature 3: Icon Picker
- Choose from Font Awesome icons (dropdown)
- OR upload custom SVG icons
- Preview before saving

### Feature 4: Uploadable Images
- Upload images for process panels
- Standard WordPress media uploader
- Alt text support

### Feature 5: Frontend Visual Editor
- Click-to-edit on frontend for admins
- Live preview
- AJAX save (no page reload)

---

## Implementation Steps

### Phase 1: Core Infrastructure (2 days)
1. Create inc/class-teznevise-builder.php
2. Create inc/admin/builder-admin.php
3. Create inc/admin/builder-assets.php
4. Modify functions.php
5. Create assets/css/builder-admin.css
6. Create assets/js/builder-admin.js

### Phase 2: Section Types (2 days)
1. Implement Software Catalog
2. Implement Challenges Grid
3. Implement Process Steps
4. Implement Service Cards
5. Implement Hero
6. Implement CTA Band

### Phase 3: Template Updates (3 days)
Update all page-*.php templates and single.php

### Phase 4: Frontend Editor (2 days)
1. Create frontend editor overlay
2. Implement click-to-edit
3. Implement AJAX save
4. Add admin bar edit buttons

### Phase 5: Testing & Documentation (1 day)
Test all features and write documentation

**Total Estimated Time:** 10 days

---

## Next Steps

1. Review this PR documentation
2. Request changes if needed
3. Approve to begin implementation
4. I will start with Phase 1 (Core Infrastructure)

---

Questions? Please leave comments on this PR.

Ready to implement? Just approve and I will begin!