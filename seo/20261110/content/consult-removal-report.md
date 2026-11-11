# Wave 0 copy-removal report — PR-A only

FACT: Addendum 3 supersedes the mixed #458 content PR. PR-A starts from main 8b432f7 and contains no page bodies. No content wave is authorized. The earlier six drafts are not carried into this branch and are not approved samples.

FACT: All fetched remote heads, tags and full non-shallow style.css history were rechecked. No 1.9.35 source was found. #454 remains 4be1a44c / 1.9.31 and #452 remains 2e452a6f / 1.9.32. See version-search.json. UNKNOWN: provenance of the live 1.9.35 source and whether any uninspected server process bypasses the repository copy entry point.

FACT: The rewriting file, loader, hooks and runtime calls are entirely removed, with no feature flag or stub. The release checker now calls the repo-wide gate. Public service/request/form option labels and the default migrator labels are corrected. Footer service navigation has one exact thesis anchor and an LSI proposal anchor.

FACT: A fail-closed guard precedes rsync in the repository-owned cPanel entry point. It refuses candidates below 1.9.35, below the installed version, or with unreadable headers. The current 1.9.31 candidate is intentionally undeployable. The destination and SSH/FTPS details are unchanged; no deployment was run. Branch-specific Vercel suppression is configured at both root and react-app root to prevent another automatic preview attempt.

UNKNOWN: The current Enamad business name/activity. This run's listing request could not reach the destination: the inherited proxy refused the CONNECT tunnel with 403 (curl status 000). The earlier browser attempt also failed. The exact registered wording has NOT been inferred from the badge. The badge URL is unchanged:
https://trustseal.enamad.ir/?id=7413817&Code=HcAFYmDgGupv1YV2E6OiOkBVVihO5OpP

FACT: The source footer badge is retained exactly and repeated on About (dedicated and generic templates) and near request CTAs (service template, fallback and builder). Organization schema references the observed listing URL; the publisher ID remains a commented TODO. Rank Math or Yoast ownership suppresses the manual theme emitters, removing that theme/plugin duplication path. The rendered gate still fails if multiple owners remain. These source changes have not been deployed.

FACT: Historical CHANGELOG 1.9.10/1.9.11 called the removed mechanism Enamad-safe and warned against removal while Enamad is active. Maziyar explicitly ordered removal. Footer, About and Terms must still be reviewed against the actual listing before merge. Every manifest record says legal review: not done; none is publishable.

## Legal/business descriptions retained for Maziyar

FACT: These are retained pending actual listing verification, not silently waived by the static gate. Their findings still block the strict repository gate.

| Location | Existing wording | Reason held |
|---|---|---|
| inc/legal-pages.php:13 | 'terms'   => "<h2>موضوع قرارداد</h2><p>تزنویسه خدمات مشاوره پژوهشی، همراهی نگارش و تحلیل آماری ارائه می‌کند. اصالت علمی و مسئولیت دفاع با متقاضی است.</p><h2>محرمانگی</h2><p>فایل‌ها و هویت متقاضی محرمانه می‌ماند و بدون مجوز کتبی به شخص ثالث داده نمی‌شود.</p><h2>پرداخت</h2><p>هزینه بر اساس مرحله و حجم کار اعلام می‌شود. تزکوین اعتبار داخلی سایت است و قابل نقد شدن نیست مگر طبق سیاست بازپرداخت.</p>", | UNKNOWN: exact legal/business match; owner review required |
| footer.php:33 | <p><?php esc_html_e( 'تزنویسه همراه پژوهشی دانشجویان همه رشته‌هاست: ابزارهای آنلاین، عامل‌های هوش مصنوعی و مشاوران متخصص برای مسیر پایان‌نامه، پروپوزال، تحلیل آماری و آمادگی دفاع.', 'teznevise' ); ?></p> | UNKNOWN: exact legal/business match; owner review required |
| header.php:19 | <div class="announcement announce"><div class="announcement-inner announce-inner container announcement-wrap"><div class="announcement-items"><span class="announce-pill pill"><i class="fa-solid fa-phone" aria-hidden="true"></i><strong><?php echo esc_html( teznevise_get_contact( 'phone_display' ) ); ?></strong></span><span class="announce-pill pill announce-desktop"><i class="fa-regular fa-clock" aria-hidden="true"></i><?php echo esc_html( teznevise_get_contact( 'hours' ) ); ?></span><span class="announce-pill pill announce-desktop"><i class="fa-solid fa-lock" aria-hidden="true"></i><?php esc_html_e( 'مشاوره محرمانه و تخصصی', 'teznevise' ); ?></span></div><div class="utility-links announce-utils announce-desktop" aria-label="<?php esc_attr_e( 'لینک‌های راهنما', 'teznevise' ); ?>"><a href="<?php echo esc_url( teznevise_page_url_from_candidates( array( 'privacy-policy', 'privacy' ), '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'حریم خصوصی', 'teznevise' ); ?></a><a href="<?php echo esc_url( teznevise_page_url( 'testimonials', '/testimonials/' ) ); ?>"><?php esc_html_e( 'بازخورد مشتریان', 'teznevise' ); ?></a><a href="<?php echo esc_url( teznevise_page_url( 'inquiry', '/inquiry/' ) ); ?>"><?php esc_html_e( 'ثبت درخواست', 'teznevise' ); ?></a></div></div></div> | UNKNOWN: exact legal/business match; owner review required |
| inc/defaults.php:33 | 'hero_text'      => 'تزنویسه برای دانشجویان همه رشته‌ها ابزارهای آنلاین، عامل‌های هوش مصنوعی و مشاوران متخصص فراهم می‌کند؛ مسیر پایان‌نامه، پروپوزال، تحلیل آماری و آمادگی دفاع را شفاف و قابل اتکا می‌سازد.', | UNKNOWN: exact legal/business match; owner review required |
| inc/defaults.php:46 | 'svc1_text'  => 'مشاوره انتخاب موضوع، ساختار فصل‌ها، تحلیل آماری و آمادگی دفاع برای ارشد و دکتری — همراه ابزارهای پژوهشی.', | UNKNOWN: exact legal/business match; owner review required |
| inc/setup-pages.php:153 | 'features' => "ارائه خدمات پژوهشی استاندارد با راهنمایی تخصصی.\nمحرمانگی کامل اطلاعات و هویت پژوهشگر.\nمسیر شفاف از مشاوره تا تحویل.", | UNKNOWN: exact legal/business match; owner review required |
| inc/setup-pages.php:155 | 'timeline' => "۱۳۹۸\|شروع مشاوره تخصصی\|آغاز فعالیت با تمرکز بر پایان‌نامه و پروپوزال\n۱۴۰۰\|گسترش تحلیل آماری\|افزودن خدمات SPSS و R\n۱۴۰۲\|ابزارهای آنلاین\|راه‌اندازی ماشین‌حساب‌های پژوهشی\n۱۴۰۴\|تیم بین‌رشته‌ای\|همکاری پژوهشگران چند حوزه", | UNKNOWN: exact legal/business match; owner review required |

FACT: About history/business text in builder-defaults.json is also retained for legal review. Existing homepage body/H1 ownership is not bypassed by rewriting the whole page to make a check green. Those legacy findings remain visible. Unconfirmed historical claims and typography in archives/prototypes/React/import sources also remain blockers; PR-A does not claim a clean repository.

## Migrator reintroduction check

FACT: Replacing the three hardcoded fallback labels alone is insufficient. The importer can read inc/extracted-page-fields.json and existing post_content, so rerunning the old write path could reintroduce archived wording. The new legal manifest check refuses a non-dry-run import before either importer can write while any review is pending or missing. A PHP contract verifies the early refusal. A pre-existing dry-run cursor deletion was guarded so dry-run does not delete that option. No production migration was executed. Do not authorize a rerun merely because its fallback literals are clean; the strict source and rendered gates must also pass.

## Stored and rendered values requiring owner action

FACT: Eleven forbidden controls/anchors were found directly in the monitored content.raw values, listed in [stored-raw-offenders.csv](stored-raw-offenders.csv). The complete production output is in [rendered-production-offenders.csv.gz](rendered-production-offenders.csv.gz). Rendered output alone does not establish its database field; template/builder/menu locations require lookup before a future approved update. Current homepage/About strings, literal deletion results and natural proposals are in [homepage-and-about-final-values.md](homepage-and-about-final-values.md). No title/meta was applied to WordPress.

FACT: Representative required-route findings below retain the complete current text for the expanded lexical rule. Other typography/claim/schema findings and all sitemap routes are in the full output.

| URL | Element | Current text | Proposed text |
|---|---|---|---|
| https://teznevise.ir/ | meta[description] | انجام پروژه دانشجویی و پروژه های دانشگاهی با مسیر تخصصی برای پایان نامه، پروپوزال، تحلیل آماری، شبیه سازی و پروژه های مهندسی، همراه با مشاوره پژوهشی. | نیاز به تایید مازیار |
| https://teznevise.ir/ | meta[og:image:alt] | موسسه پژوهشی تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/ | JSON-LD$.@graph[2].caption | موسسه پژوهشی تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/ | JSON-LD$.@graph[3].description | انجام پروژه دانشجویی و پروژه های دانشگاهی با مسیر تخصصی برای پایان نامه، پروپوزال، تحلیل آماری، شبیه سازی و پروژه های مهندسی، همراه با مشاوره پژوهشی. | نیاز به تایید مازیار |
| https://teznevise.ir/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/ | a | درخواست مشاوره رایگان | ثبت درخواست |
| https://teznevise.ir/ | div[aria-label] | نمایی از مسیر مشاوره تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/ | a[aria-label] | ثبت درخواست مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/ | button | 1آیا مشاوره اولیه رایگان است؟ | نیاز به تایید مازیار |
| https://teznevise.ir/ | button | 3چطور درخواست مشاوره ثبت کنم؟ | نیاز به تایید مازیار |
| https://teznevise.ir/ | a | درخواست مشاوره رایگان | ثبت درخواست |
| https://teznevise.ir/ | button | از کجا مشاوره پایان نامه را شروع کنم؟ | نیاز به تایید مازیار |
| https://teznevise.ir/ | button[aria-label] | بستن فرم مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/ | JSON-LD$.mainEntity[0].name | آیا مشاوره اولیه رایگان است؟ | نیاز به تایید مازیار |
| https://teznevise.ir/ | JSON-LD$.mainEntity[1].acceptedAnswer.text | خیر. تزنویسه مشاوره، ساختار، روش و ابزار می دهد؛ نگارش نهایی با دانشجو است. | نیاز به تایید مازیار |
| https://teznevise.ir/ | JSON-LD$.mainEntity[2].name | چطور درخواست مشاوره ثبت کنم؟ | نیاز به تایید مازیار |
| https://teznevise.ir/about-us/ | title | درباره تزنویسه \| مشاوره پژوهشی و تحلیل داده | نیاز به تایید مازیار |
| https://teznevise.ir/about-us/ | meta[description] | با رویکرد تزنویسه در مشاوره پژوهشی، روش شناسی، تحلیل داده، بازخورد علمی و استفاده مسئولانه از ابزارهای پژوهش و هوش مصنوعی آشنا شوید. | نیاز به تایید مازیار |
| https://teznevise.ir/about-us/ | meta[og:title] | درباره تزنویسه \| مشاوره پژوهشی و تحلیل داده | نیاز به تایید مازیار |
| https://teznevise.ir/about-us/ | meta[og:description] | با رویکرد تزنویسه در مشاوره پژوهشی، روش شناسی، تحلیل داده، بازخورد علمی و استفاده مسئولانه از ابزارهای پژوهش و هوش مصنوعی آشنا شوید. | نیاز به تایید مازیار |
| https://teznevise.ir/about-us/ | meta[og:image:alt] | موسسه پژوهشی تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/about-us/ | meta[twitter:title] | درباره تزنویسه \| مشاوره پژوهشی و تحلیل داده | نیاز به تایید مازیار |
| https://teznevise.ir/about-us/ | meta[twitter:description] | با رویکرد تزنویسه در مشاوره پژوهشی، روش شناسی، تحلیل داده، بازخورد علمی و استفاده مسئولانه از ابزارهای پژوهش و هوش مصنوعی آشنا شوید. | نیاز به تایید مازیار |
| https://teznevise.ir/about-us/ | JSON-LD$.@graph[1].name | درباره تزنویسه \| مشاوره پژوهشی و تحلیل داده | نیاز به تایید مازیار |
| https://teznevise.ir/about-us/ | JSON-LD$.@graph[1].description | درباره ما تزنویسه پژوهش را به سبکی متفاوت تجربه کنید! Call Email WhatsApp ثبت سفارش آنجا که پژوهش، مرز نمی شناسد تزنویسه صرفاً یک موسسه پژوهشی نیست؛ یک اکوسیستم | نیاز به تایید مازیار |
| https://teznevise.ir/about-us/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/about-us/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/about-us/ | div[aria-label] | نمایی از مسیر مشاوره تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/about-us/ | a[aria-label] | ثبت درخواست مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/about-us/ | button | از کجا مشاوره پایان نامه را شروع کنم؟ | نیاز به تایید مازیار |
| https://teznevise.ir/about-us/ | button[aria-label] | بستن فرم مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/contact-us/ | meta[og:description] | راه های تماس با تزنویسه برای مشاوره پایان نامه، پروپوزال، تحلیل آماری و خدمات پژوهشی؛ تماس تلفنی، واتساپ، تلگرام و فرم آنلاین. | نیاز به تایید مازیار |
| https://teznevise.ir/contact-us/ | meta[og:image:alt] | موسسه پژوهشی تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/contact-us/ | meta[twitter:description] | راه های تماس با تزنویسه برای مشاوره پایان نامه، پروپوزال، تحلیل آماری و خدمات پژوهشی؛ تماس تلفنی، واتساپ، تلگرام و فرم آنلاین. | نیاز به تایید مازیار |
| https://teznevise.ir/contact-us/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/contact-us/ | div[aria-label] | نمایی از مسیر مشاوره تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/contact-us/ | a[aria-label] | ثبت درخواست مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/contact-us/ | option | مشاوره انجام پایان نامه | نیاز به تایید مازیار |
| https://teznevise.ir/contact-us/ | option | مشاوره انجام پروپوزال | نیاز به تایید مازیار |
| https://teznevise.ir/contact-us/ | button | از کجا مشاوره پایان نامه را شروع کنم؟ | نیاز به تایید مازیار |
| https://teznevise.ir/contact-us/ | button[aria-label] | بستن فرم مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/gams/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/gams/ | button | از کجا مشاوره پایان نامه را شروع کنم؟ | نیاز به تایید مازیار |
| https://teznevise.ir/gams/ | button[aria-label] | بستن فرم مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/inquiry/ | title | ثبت درخواست مشاوره پژوهشی \| تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/inquiry/ | meta[og:title] | ثبت درخواست مشاوره پژوهشی \| تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/inquiry/ | meta[og:image:alt] | موسسه پژوهشی تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/inquiry/ | meta[twitter:title] | ثبت درخواست مشاوره پژوهشی \| تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/inquiry/ | JSON-LD$.@graph[1].name | ثبت درخواست مشاوره پژوهشی \| تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/inquiry/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/inquiry/ | option | مشاوره انجام پایان نامه | نیاز به تایید مازیار |
| https://teznevise.ir/inquiry/ | option | مشاوره انجام پروپوزال | نیاز به تایید مازیار |
| https://teznevise.ir/inquiry/ | button | از کجا مشاوره پایان نامه را شروع کنم؟ | نیاز به تایید مازیار |
| https://teznevise.ir/inquiry/ | button[aria-label] | بستن فرم مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/proposal/ | meta[og:description] | مشاوره پژوهشی برای موضوع، بیان مسئله، روش تحقیق و بازبینی ساختار پروپوزال؛ بدون تضمین تصویب دانشگاه یا وعده تجاری ثابت. | نیاز به تایید مازیار |
| https://teznevise.ir/proposal/ | meta[twitter:description] | مشاوره پژوهشی برای موضوع، بیان مسئله، روش تحقیق و بازبینی ساختار پروپوزال؛ بدون تضمین تصویب دانشگاه یا وعده تجاری ثابت. | نیاز به تایید مازیار |
| https://teznevise.ir/proposal/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/proposal/ | div[aria-label] | نمایی از مسیر مشاوره تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/proposal/ | a[aria-label] | ثبت درخواست مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/proposal/ | button | از کجا مشاوره پایان نامه را شروع کنم؟ | نیاز به تایید مازیار |
| https://teznevise.ir/proposal/ | button[aria-label] | بستن فرم مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/proposal/ | JSON-LD$.name | مشاوره انجام پروپوزال | نیاز به تایید مازیار |
| https://teznevise.ir/proposal/ | JSON-LD$.serviceType | مشاوره انجام پروپوزال | نیاز به تایید مازیار |
| https://teznevise.ir/proposal/phd/ | meta[og:title] | مشاوره پروپوزال دکتری \| تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/proposal/phd/ | meta[twitter:title] | مشاوره پروپوزال دکتری \| تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/proposal/phd/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/proposal/phd/ | div[aria-label] | نمایی از مسیر مشاوره تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/proposal/phd/ | a[aria-label] | ثبت درخواست مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/proposal/phd/ | a | فرم درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/proposal/phd/ | button | از کجا مشاوره پایان نامه را شروع کنم؟ | نیاز به تایید مازیار |
| https://teznevise.ir/proposal/phd/ | button[aria-label] | بستن فرم مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/service-project/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/service-project/ | a | شروع مشاوره رایگان | ثبت درخواست |
| https://teznevise.ir/service-project/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/service-project/ | a | شروع مشاوره رایگان | ثبت درخواست |
| https://teznevise.ir/service-project/ | button | از کجا مشاوره پایان نامه را شروع کنم؟ | نیاز به تایید مازیار |
| https://teznevise.ir/service-project/ | button[aria-label] | بستن فرم مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/service-project/ | JSON-LD$.name | مشاوره پروژه دانشجویی | نیاز به تایید مازیار |
| https://teznevise.ir/service-project/ | JSON-LD$.serviceType | مشاوره پروژه دانشجویی | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/ | meta[og:description] | انجام پایان نامه کارشناسی ارشد و دکتری با مشاوره مرحله ای در انتخاب موضوع، روش تحقیق، تحلیل داده، بازبینی علمی و آمادگی دفاع؛ بدون وعده نتیجه دانشگاهی. | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/ | meta[twitter:description] | انجام پایان نامه کارشناسی ارشد و دکتری با مشاوره مرحله ای در انتخاب موضوع، روش تحقیق، تحلیل داده، بازبینی علمی و آمادگی دفاع؛ بدون وعده نتیجه دانشگاهی. | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/thesis/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/thesis/ | div[aria-label] | نمایی از مسیر مشاوره تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/ | a[aria-label] | ثبت درخواست مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/ | a | درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/thesis/ | a | راهنمای انتخاب موسسه و مشاور پژوهشی | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/ | a | فرم درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/thesis/ | a | درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/thesis/ | a | چک لیست اطلاعات موردنیاز پیش از درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/thesis/ | button | مسیر مشاوره پایان نامه چند مرحله است؟ | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/ | button | نقش دانشجو در مشاوره چیست؟ | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/ | button | چطور درخواست مشاوره ثبت کنم؟ | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/ | button[aria-label] | بستن فرم مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/ | JSON-LD$.mainEntity[0].acceptedAnswer.text | خیر. نتیجه دانشگاهی به کیفیت پژوهش، تصمیم های علمی، مقررات دانشگاه، نظر استاد و داوران و عملکرد خود پژوهشگر وابسته است. می توان کیفیت فرایند مشاوره و بازبینی را مدیریت کرد، اما نتیجه ای که در اختیار اشخاص یا نهادهای دیگر است قابل تضمین نیست. | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/ | JSON-LD$.mainEntity[3].acceptedAnswer.text | بخش عمده بررسی فایل، مشاوره و بازخورد پژوهشی می تواند آنلاین انجام شود. برای هر همکاری، کانال ارتباطی و نحوه تحویل فایل ها در شروع مشخص می شود. | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/humanities/management/ | meta[og:description] | مشاوره پایان نامه مدیریت برای انتخاب مسئله، روش تحقیق، تحلیل داده و بازبینی علمی؛ با تأکید بر تصمیم گیری پژوهشی و مرزهای اخلاق دانشگاهی. | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/humanities/management/ | meta[og:image:alt] | مشاوره پایان نامه مدیریت \| طراحی پژوهش و تحلیل | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/humanities/management/ | meta[twitter:description] | مشاوره پایان نامه مدیریت برای انتخاب مسئله، روش تحقیق، تحلیل داده و بازبینی علمی؛ با تأکید بر تصمیم گیری پژوهشی و مرزهای اخلاق دانشگاهی. | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/humanities/management/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/thesis/humanities/management/ | div[aria-label] | نمایی از مسیر مشاوره تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/humanities/management/ | a[aria-label] | ثبت درخواست مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/humanities/management/ | a | دریافت مشاوره رایگان اولیه | ثبت درخواست |
| https://teznevise.ir/thesis/humanities/management/ | a | فرم درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/thesis/humanities/management/ | button | از کجا مشاوره پایان نامه را شروع کنم؟ | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/humanities/management/ | button[aria-label] | بستن فرم مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/humanities/management/ | JSON-LD$.mainEntity[2].name | آیا می توان فقط برای تحلیل داده یا اصلاح پروپوزال مشاوره گرفت؟ | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/humanities/management/ | JSON-LD$.mainEntity[2].acceptedAnswer.text | بله؛ دامنه مشاوره می تواند روی یک مرحله مشخص مانند طراحی روش، تحلیل داده، بازبینی ساختار یا آمادگی دفاع متمرکز باشد. | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/humanities/psychology/ | meta[og:image:alt] | مشاوره پایان نامه روانشناسی \| طراحی پژوهش و تحلیل | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/humanities/psychology/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/thesis/humanities/psychology/ | div[aria-label] | نمایی از مسیر مشاوره تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/humanities/psychology/ | a[aria-label] | ثبت درخواست مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/humanities/psychology/ | a | دریافت مشاوره رایگان اولیه | ثبت درخواست |
| https://teznevise.ir/thesis/humanities/psychology/ | a | فرم درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/thesis/humanities/psychology/ | button | از کجا مشاوره پایان نامه را شروع کنم؟ | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/humanities/psychology/ | button[aria-label] | بستن فرم مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/phd/ | meta[og:title] | مشاوره رساله دکتری \| طراحی پژوهش و دفاع | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/phd/ | meta[twitter:title] | مشاوره رساله دکتری \| طراحی پژوهش و دفاع | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/phd/ | a | ثبت درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/thesis/phd/ | div[aria-label] | نمایی از مسیر مشاوره تزنویسه | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/phd/ | a[aria-label] | ثبت درخواست مشاوره | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/phd/ | a | مشاوره و طراحی پروپوزال | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/phd/ | a | فرم درخواست مشاوره | ثبت درخواست |
| https://teznevise.ir/thesis/phd/ | a | موسسه انجام پایان نامه | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/phd/ | button | از کجا مشاوره پایان نامه را شروع کنم؟ | نیاز به تایید مازیار |
| https://teznevise.ir/thesis/phd/ | button[aria-label] | بستن فرم مشاوره | نیاز به تایید مازیار |

## Evidence and held content-wave inputs

FACT: No production WordPress write was made. Twelve monitored live pages have identical before/after modified_gmt and SHA-256(content.raw); see rest-before-after.csv. This proves those observed fields are unchanged, not the absence of every possible external database change. Rendered HTML hashes are not used as a substitute.

FACT: Corrected inputs include 65 page/control/resource records, one build_order, one primary parent, planned/deferred target states, publish order, per-slug redirect decisions, held link patches, a distinct master/download canonical proposal, the Aspen/signal/bachelor evidence corrections, current tool names, source checks, and quality policy. No new proposal-by-major page is introduced. No /student-projects/ page or redirect is proposed. GAMS appears in one wave only.

FACT: Future new WordPress drafts require the placeholder [DRAFT] {slug}, explicit slug/parent/template, draft status and empty Rank Math title/meta. Resolve actual parent IDs and check all statuses for duplicates first. Existing live bodies remain repo proposals only. First obtain Maziyar's yes for a wave; then write one sample and stop for its review before writing the rest. Pairwise 5-gram overlap uses a proposed threshold below 0.12, explicitly HYPOTHESIS, with three genuinely page-specific elements and claim/source mapping. Word counts are guides, never gates.

FACT: PR #456/#457 are read-only. #456 must drop the student-projects route and its service-project redirect, apply that content to /service-project/, and drop the homepage metadata containing the forbidden wording. This PR does not amend either PR. Master publication/redirect removal and sister-site differentiation remain Maziyar's decisions.

UNKNOWN: Iran search volume, People Also Ask, backlink evidence, reviewer identities, publisher registration, price/time policy, exact Enamad activity and unobserved WordPress parent IDs. The Aspen query's HYSYS-versus-Plus attribution is not established. Existing GSC figures in the supplied pack are not described as new measurements.
