# Static → WordPress page map

**Author:** MAZ//ID (Maziyar) · **Brand:** [maziyarid/M-Z](https://github.com/maziyarid/M-Z)

| Static HTML | WP slug / URL | Template | Backend |
|-------------|---------------|----------|---------|
| `index.html` | `/` | `front-page.php` | Customizer |
| `blog.html` | `/blog/` | `home.php` / `archive.php` | Posts (Perplexity) |
| `post-sample.html` | single post | `single.php` | Post meta (Perplexity) |
| `about.html` | `/about/` | `page-about.php` | Page meta: features, timeline, policy_points |
| `team.html` | `/team/` | `page-team.php` | Page meta: team_stats, team_members |
| `tools.html` | `/tools/` | `page-tools.php` | Page meta: tools_list |
| `tool-descriptive-statistics.html` | `/tool-descriptive-statistics/` | `page-tool.php` | Page meta + content (calculator) |
| `downloads.html` | `/downloads/` | `page-downloads.php` | Page meta: downloads_list |
| `contact.html` | `/contact/` | `page-contact.php` | Customizer NAP + content |
| `inquiry.html` | `/inquiry/` | `page-contact.php` | Same template, different meta |
| `privacy.html` | `/privacy/` | `page-privacy.php` | Page meta + content |
| `service-thesis.html` | `/service-thesis/` | `page-service.php` | Page meta features/CTA |
| `service-proposal.html` | `/service-proposal/` | `page-service.php` | Page meta |
| `service-statistics.html` | `/service-statistics/` | `page-service.php` | Page meta |
| `service-simulation.html` | `/service-simulation/` | `page-service.php` | Page meta |
| `404.html` | 404 | `404.php` | — |

## Notes

- Seed via **Appearance → راه‌اندازی تزنویسه**.
- Legacy `.html` links in post content are rewritten by `teznevise_rewrite_static_links`.
- `teznevise_work/` remains as reference only; promote assets when ready. Requests under `/teznevise_work/` get `noindex`.
- Blog structured fields: handled separately (Perplexity).

— M•Z
