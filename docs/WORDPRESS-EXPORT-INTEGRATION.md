# WordPress Export Content Integration

**Reviewed:** 2026-08-21
**Source:** Three WordPress WXR exports supplied by the site owner and authenticated inspection of `teznevise.ir`.

## Content Inventory

| Source | Published content | Supporting media | Role in the theme |
| --- | ---: | ---: | --- |
| `WordPress.2026-08-20.xml` | 102 pages | 55 attachments | Canonical service, tool, legal, support, and informational page hierarchy. |
| `WordPress.2026-08-20(1).xml` | 1 download | 3 attachments | Download custom-content record. |
| `WordPress.2026-08-20(2).xml` | 1 case study | 1 attachment | Case-study custom-content record. |

The exports contain no `nav_menu_item` records. Existing live menu assignments therefore remain the source of truth and must not be overwritten by an import process. The live installation already contains the exported content; this change makes that content consistently visible in the revised templates rather than reimporting duplicates.

## Shared Template Contract

| Content type | Theme treatment |
| --- | --- |
| Builder-backed service and utility pages | Existing builder sections remain the primary layout. Interactive forms and calculators remain directly visible. Any accompanying classic-editor prose becomes a labelled, accessible `مشاهده بیشتر` disclosure. |
| Classic-editor informational pages | The editor body is rendered through WordPress formatting filters inside the same disclosure component, rather than appearing as an unlabelled trailing block. |
| Legacy shortcode layouts | Preserved as-is to avoid breaking established custom service layouts. |
| Blog archive and single posts | Continue to use `home.php` and `single.php`, which already render normal WordPress post content and metadata in the current design system. |
| Downloads and case studies | Continue through their existing archive and singular templates; the exports confirm that the relevant custom records are retained. |

## Navigation Findings

Live WordPress inspection found that `Main` is assigned to the Primary Menu and contains the full service and tool hierarchy. `منوی فوتر` is assigned to the Footer Menu. The Mobile Menu and Bottom Mobile Menu locations are not currently assigned.

The theme now uses the editor-managed `Main` hierarchy in the mobile drawer whenever no dedicated mobile menu exists. The compact bottom bar remains intentionally limited to five destinations, but its fallback now resolves the real content-bearing service, tool, blog, and contact slugs from the export hierarchy instead of depending on stale placeholder paths.

## Deployment Note

This change is safe to deploy without a new WXR import. After deployment, content administrators may optionally create a dedicated Mobile Menu for a more curated drawer experience; until then, the primary hierarchy is used automatically.
