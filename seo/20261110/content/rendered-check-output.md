# Wave 0 rendered and static check output

FACT: Production is report-only and cannot fail CI. Repo and local/staging tiers are blocking. These are red results, not a passing acceptance claim.

| Tier | Coverage | Findings | Result |
|---|---:|---:|---|
| repo-static | 464 | 13573 | FAIL |
| local-rendered | 22 | 349 | FAIL |
| production-rendered | 404 | 1942 | REPORT ONLY — findings |
| legal-gate | 0 | 65 | FAIL |

FACT: The local database is synthetic and has no production content. Its local permalink option was set for routing; no page content was created. The server gate crawled its existing sitemap and required routes; missing routes and redirects remain failures, not simulated successes.

| Required local URL | HTTP | Findings |
|---|---:|---:|
| http://127.0.0.1:8089/ | 200 | 56 |
| http://127.0.0.1:8089/about-us/ | 404 | 1 |
| http://127.0.0.1:8089/contact-us/ | 404 | 1 |
| http://127.0.0.1:8089/gams/ | 404 | 1 |
| http://127.0.0.1:8089/inquiry/ | 404 | 1 |
| http://127.0.0.1:8089/proposal/ | 404 | 1 |
| http://127.0.0.1:8089/proposal/phd/ | 404 | 1 |
| http://127.0.0.1:8089/service-project/ | 200 | 36 |
| http://127.0.0.1:8089/thesis/ | 404 | 1 |
| http://127.0.0.1:8089/thesis/humanities/management/ | 404 | 1 |
| http://127.0.0.1:8089/thesis/humanities/psychology/ | 404 | 1 |
| http://127.0.0.1:8089/thesis/phd/ | 404 | 1 |

FACT: Browser checks also ran on the twelve required local URLs after JavaScript, with POST requests blocked. Production browser navigation failed on certificate trust for all twelve routes and is marked **not run**; certificate verification was not disabled. The production sitemap check uses WordPress server-rendered HTML and did run.

FACT: Full outputs print every finding and preserve current offending element text. Download [production JSON](production-rendered.json.gz), [all production elements CSV](rendered-production-offenders.csv.gz), [local JSON](local-rendered.json.gz), [static JSON](repo-static.json.gz), and [browser results](browser-dom-check.json.gz). These are gzip files.

FACT: Twenty-one copy-check tests, six version-guard tests, three PHP contracts and changed-file PHP syntax checks pass. The full gates remain red. Missing FAQPage is never reported as a defect; optional schema mismatch, mixed schema sources, forbidden review schema and main-region exact-anchor overuse are checked.

FACT: Existing main release metadata is inconsistent: README.md and readme.txt disagree with 1.9.31. This PR does not bump a version to hide that.
