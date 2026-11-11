# Copy removal review — draft only

FACT: Requested branch starts at main 8b432f7. All remote branch tips were inspected. PR #454 is 4be1a44c (1.9.31); #452 is 2e452a6f (1.9.32). No 1.9.35 style header was found in fetched refs or their style.css history. UNKNOWN: provenance of deployed 1.9.35; reconcile with Maziyar before merge.

FACT: PR #456 is now 313d180c, rather than the supplied f53b21ed. Both manifests were retained locally for read-only comparison. PR #457 is 34be496e. Neither PR was edited.

FACT: Enamad badge link observed in live homepage HTML:
https://trustseal.enamad.ir/?id=7413817&Code=HcAFYmDgGupv1YV2E6OiOkBVVihO5OpP

UNKNOWN: Exact registered business name and activity. HTTP fetch returned 403; WPVibe headless fetch timed out and its direct fallback returned 530. No activity wording can be inferred from those failures. Existing badge markup and link are preserved. Legal and business-description prose is held pending this verification.

## Legal/business-description lines held

- inc/legal-pages.php: Terms opening paragraph, including «خدمات مشاوره پژوهشی».
- inc/defaults.php: svc1_text description, including «مشاوره انتخاب موضوع».
- inc/setup-pages.php: About features and history wording.
- inc/builder-defaults.json: About history and explanatory business text.
- header.php: business-description notice «مشاوره محرمانه و تخصصی».
- front-page.php: existing homepage body remains untouched.

FACT: Removal of the runtime rewrite was explicitly ordered. Historical CHANGELOG 1.9.10/1.9.11 framed it as Enamad-safe and warned against removal while Enamad is active. Footer, About and Terms require review against actual Enamad conditions before merge. Removal does not establish compliance.

## Stored content

Rendered observations will be appended by the verification script. Rendered text alone does not prove a database location: confirm the owning stored field before applying any future approved changes. No existing WordPress content is edited by this work.

## PR #456 amendment

FACT: Homepage description in its manifest includes «توضیح فرایند مشاوره». DROP that proposed homepage metadata before merge. Do not introduce /student-projects/ or its redirect: apply its draft content to /service-project/. Preserve /thesis/master/ publication and redirect decisions for Maziyar.

## Rendered baseline offenders (FACT)

| URL | Element | Current text | Proposed text |
|---|---|---|---|
| https://teznevise.ir/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/ | a | درخواست مشاوره رایگان | ثبت درخواست |
| https://teznevise.ir/ | div[aria-label] | نمایی از مسیر مشاوره تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/ | a[aria-label] | ثبت درخواست مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/ | a | درخواست مشاوره رایگان | ثبت درخواست |
| https://teznevise.ir/ | button[aria-label] | بستن فرم مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/proposal/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/proposal/ | div[aria-label] | نمایی از مسیر مشاوره تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/proposal/ | a[aria-label] | ثبت درخواست مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/proposal/ | button[aria-label] | بستن فرم مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/proposal/phd/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/proposal/phd/ | div[aria-label] | نمایی از مسیر مشاوره تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/proposal/phd/ | a[aria-label] | ثبت درخواست مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/proposal/phd/ | a | فرم درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/proposal/phd/ | button[aria-label] | بستن فرم مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/service-project/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/service-project/ | a | شروع مشاوره رایگان | ثبت درخواست |
| https://teznevise.ir/service-project/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/service-project/ | a | شروع مشاوره رایگان | ثبت درخواست |
| https://teznevise.ir/service-project/ | button[aria-label] | بستن فرم مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/thesis/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/thesis/ | div[aria-label] | نمایی از مسیر مشاوره تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/ | a[aria-label] | ثبت درخواست مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/ | a | درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/thesis/ | a | فرم درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/thesis/ | a | درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/thesis/ | a | چک لیست اطلاعات موردنیاز پیش از درخواست مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/ | button[aria-label] | بستن فرم مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/humanities/management/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/thesis/humanities/management/ | div[aria-label] | نمایی از مسیر مشاوره تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/humanities/management/ | a[aria-label] | ثبت درخواست مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/humanities/management/ | a | دریافت مشاوره رایگان اولیه | ثبت درخواست |
| https://teznevise.ir/thesis/humanities/management/ | a | فرم درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/thesis/humanities/management/ | button[aria-label] | بستن فرم مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/humanities/psychology/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/thesis/humanities/psychology/ | div[aria-label] | نمایی از مسیر مشاوره تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/humanities/psychology/ | a[aria-label] | ثبت درخواست مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/humanities/psychology/ | a | دریافت مشاوره رایگان اولیه | ثبت درخواست |
| https://teznevise.ir/thesis/humanities/psychology/ | a | فرم درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/thesis/humanities/psychology/ | button[aria-label] | بستن فرم مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/phd/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/thesis/phd/ | div[aria-label] | نمایی از مسیر مشاوره تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/phd/ | a[aria-label] | ثبت درخواست مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/phd/ | a | مشاوره و طراحی پروپوزال | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/phd/ | a | فرم درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/thesis/phd/ | button[aria-label] | بستن فرم مشاوره | نیاز به تایید مازیار |
