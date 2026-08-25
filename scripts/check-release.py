#!/usr/bin/env python3
"""Release gates for the Teznevise WordPress theme root.

Fails if the 1.9.8 patch is packaged one directory too deep, if versions
drift, if require_once targets are missing, or if JSON seed files are invalid.
"""
from __future__ import annotations

import json
import os
import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
ERRORS: list[str] = []


def error(msg: str) -> None:
    ERRORS.append(msg)
    print(f"ERROR: {msg}", file=sys.stderr)


def version_from_functions() -> str:
    text = (ROOT / "functions.php").read_text(encoding="utf-8")
    match = re.search(r"define\(\s*'TEZNEVISE_VERSION',\s*'([^']+)'\s*\)", text)
    if not match:
        error("TEZNEVISE_VERSION is missing from functions.php")
        return ""
    return match.group(1)


def check_no_nested_theme() -> None:
    nested = ROOT / "teznevise" / "style.css"
    nested_fn = ROOT / "teznevise" / "functions.php"
    if nested.exists() or nested_fn.exists():
        error(
            "Nested theme package found at teznevise/. The repository root is "
            "the active WordPress theme; flatten the patch before release."
        )


def check_versions(version: str) -> None:
    style = (ROOT / "style.css").read_text(encoding="utf-8")
    if not re.search(rf"^Version:\s*{re.escape(version)}\s*$", style, re.M):
        error(f"style.css Version does not match {version}")
    readme_txt = (ROOT / "readme.txt").read_text(encoding="utf-8")
    if f"Stable tag: {version}" not in readme_txt:
        error(f"readme.txt Stable tag does not match {version}")
    readme_md = (ROOT / "README.md").read_text(encoding="utf-8")
    if f"**Version:** {version}" not in readme_md:
        error(f"README.md version does not match {version}")
    changelog = (ROOT / "CHANGELOG.md").read_text(encoding="utf-8")
    if f"## {version}" not in changelog:
        error(f"CHANGELOG.md is missing a {version} heading")


def check_requires() -> None:
    functions = (ROOT / "functions.php").read_text(encoding="utf-8")
    targets = re.findall(r"require(?:_once)?\s+TEZNEVISE_DIR\s*\.\s*'([^']+)'", functions)
    if not targets:
        error("functions.php has no TEZNEVISE_DIR require_once targets")
    for rel in targets:
        path = ROOT / rel.lstrip("/")
        if not path.is_file():
            error(f"Missing require_once target: {rel}")
    for required in (
        "inc/helpers.php",
        "inc/security.php",
        "inc/wxr-classic-content.php",
        "inc/extracted-pages.php",
        "inc/ai/class-ai-api.php",
        "inc/perf.php",
        "assets/css/critical.css",
        "assets/css/hotfix-198.css",
        "assets/css/hotfix-199.css",
        "assets/css/hotfix-200.css",
        "assets/css/hotfix-201.css",
        "assets/css/hotfix-202.css",
        "assets/css/hotfix-203.css",
        "assets/css/hotfix-204.css",
        "assets/css/hotfix-205.css",
        "assets/css/hotfix-206.css",
        "assets/css/hotfix-207.css",
        "assets/css/hotfix-208.css",
        "assets/css/hotfix-209.css",
        "assets/css/hotfix-210.css",
        "inc/ai/class-ai-knowledge.php",
        "inc/legal-copy.php",
        "inc/waitlist.php",
        "teznevise-core/teznevise-core.php",
        "footer.php",
        "header.php",
        "index.php",
        "inc/wxr-classic-content.json",
    ):
        if not (ROOT / required).is_file():
            error(f"Required runtime file missing: {required}")


def check_json() -> None:
    for rel in (
        "inc/builder-defaults.json",
        "inc/extracted-page-fields.json",
        "inc/wxr-classic-content.json",
    ):
        path = ROOT / rel
        try:
            json.loads(path.read_text(encoding="utf-8"))
        except (OSError, json.JSONDecodeError) as exc:
            error(f"{rel} is not valid JSON: {exc}")


def check_source_contracts() -> None:
    extracted = (ROOT / "inc/extracted-pages.php").read_text(encoding="utf-8")
    if "function teznevise_page_has_owned_editor_content" not in extracted:
        error("Ownership guard teznevise_page_has_owned_editor_content is missing")
    if "$seen[ $old ]" not in extracted and "$seen[$old]" not in extracted:
        error("Duplicate ID occurrence counter is missing from disclosure sanitizer")

    importer = (ROOT / "inc/wxr-classic-content.php").read_text(encoding="utf-8")
    if "teznevise_page_has_owned_editor_content" not in importer:
        error("Importer still uses the display-quality threshold as an overwrite guard")
    if "manage_options" not in importer:
        error("Importer admin trigger is not restricted to manage_options")
    if "_teznevise_classic_import_backup" not in importer:
        error("Importer does not preserve previous post_content before writing")

    ai = (ROOT / "inc/ai/class-ai-api.php").read_text(encoding="utf-8")
    if "'https'" not in ai and '"https"' not in ai:
        error("AI client does not require an HTTPS scheme")
    if "GET_LOCK" not in ai:
        error("AI quota/burst counters are not using an atomic lock")
    if "'redirection'" not in ai and '"redirection"' not in ai:
        error("AI HTTP client still follows redirects with the Authorization header")

    leads = (ROOT / "inc/frontend-compat.php").read_text(encoding="utf-8")
    if "lead', 'queued'" not in leads and 'lead", "queued"' not in leads:
        error("Lead handler does not distinguish stored vs delivered submissions")
    if "shortcode_exists( 'gravityform' )" not in leads:
        error("Gravity Forms fallback is still installed unconditionally")

    security = (ROOT / "inc/security.php").read_text(encoding="utf-8")
    if "is_author" not in security:
        error("Public author-archive enumeration is not blocked")

    faq = (ROOT / "assets/css/react-loader.css").read_text(encoding="utf-8")
    if re.search(r"\.faq-a[\s\S]{0,180}display:\s*block\s*!important", faq):
        error("FAQ answers are forced visible and will break accordion collapse")

    comments = (ROOT / "inc/ai-comments.php").read_text(encoding="utf-8")
    if "function teznevise_ai_discussion_get" not in comments:
        error("AI discussion custom-field helper is missing")
    if "function teznevise_render_ai_discussion_thread" not in comments:
        error("AI discussion thread renderer is missing")
    if "wp_trim_words" in comments and "220" in comments and "900" not in comments:
        error("AI discussion still trims the article to 220 words")

    perf = (ROOT / "inc/perf.php").read_text(encoding="utf-8")
    if "media='print'" not in perf and 'media="print"' not in perf:
        error("Runtime CSS is not loaded asynchronously")
    if "data-tz-delay" not in perf:
        error("Third-party trackers are not delayed")
    if "<noscript>" in perf:
        error("Async CSS must not concatenate a noscript stylesheet (live PSI duplicate)")

    if "function research" not in ai and "public static function research" not in ai:
        error("You.com research() helper is missing from the AI API")
    if "api.ydc-index.io" not in ai:
        error("You.com host is not allow-listed")

    builder = (ROOT / "inc/admin/builder-admin.php").read_text(encoding="utf-8")
    if "tez-builder--visual" not in builder:
        error("Visual page-builder canvas markup is missing")

    core = (ROOT / "teznevise-core" / "teznevise-core.php").read_text(encoding="utf-8")
    if "TEZNEVISE_CORE_LOADED" not in core:
        error("teznevise-core bootstrap is missing TEZNEVISE_CORE_LOADED")
    registry = (ROOT / "teznevise-core" / "inc" / "class-agent-registry.php").read_text(encoding="utf-8")
    if "function identity_lock" not in registry:
        error("Agent identity lock is missing")
    if "CRITICAL DIRECTIVE" not in registry:
        error("Identity lock does not include the mandatory identity template")
    vault = (ROOT / "teznevise-core" / "inc" / "class-key-vault.php").read_text(encoding="utf-8")
    if "AES-256-CBC" not in vault:
        error("Key vault is not using AES-256-CBC")
    router = (ROOT / "teznevise-core" / "inc" / "class-model-router.php").read_text(encoding="utf-8")
    if "function complexity" not in router:
        error("Model router complexity metric is missing")
    extracted = (ROOT / "inc" / "extracted-pages.php").read_text(encoding="utf-8")
    if "ادامه مطلب" not in extracted:
        error("Classic disclosure button is not labelled ادامه مطلب")
    if "جزئیات و توضیحات بیشتر" in extracted:
        error("Visible F1 title still remains in extracted-pages.php")
    nav = (ROOT / "inc" / "nav-walker.php").read_text(encoding="utf-8")
    if "is-mega" not in nav or "is-dropdown" not in nav:
        error("Nav walker does not distinguish mega vs dropdown items")
    chrome = (ROOT / "assets" / "js" / "chrome.js").read_text(encoding="utf-8")
    if "classList.add('js')" not in chrome and 'classList.add("js")' not in chrome:
        error("chrome.js does not mark html.js for crawler-safe disclosure")
    if "data-ai-summary" not in chrome:
        error("Interactive AI summary handler is missing from chrome.js")
    comments = (ROOT / "comments.php").read_text(encoding="utf-8")
    if "کاربران" not in comments or "هوش مصنوعی" not in comments:
        error("Dual comment tabs are missing")
    a1 = "این تب بحث ساختگی عامل‌های نام‌دار است"
    for rel, text in (
        ("comments.php", comments),
        ("inc/ai-comments.php", (ROOT / "inc" / "ai-comments.php").read_text(encoding="utf-8")),
    ):
        if a1 in text:
            error(f"A1 dummy string still present in {rel}")

    footer = (ROOT / "footer.php").read_text(encoding="utf-8")
    if "trustseal.enamad.ir/?id=7413817" not in footer:
        error("Exact Enamad trustseal HTML is missing from footer.php")
    if "مشاوره انجام پایان" not in footer:
        error("Footer services are missing مشاوره انجام phrasing")
    legal = (ROOT / "inc" / "legal-copy.php").read_text(encoding="utf-8")
    if "function teznevise_consult_copy" not in legal:
        error("Legal consulting copy helper is missing")
    waitlist = (ROOT / "inc" / "waitlist.php").read_text(encoding="utf-8")
    if "teznevise_tool_waitlist" not in waitlist:
        error("Waitlist table installer is missing")
    tezcoin = (ROOT / "inc" / "tezcoin.php").read_text(encoding="utf-8")
    if "TEZNEVISE_ENABLE_THIRD_PARTY_ANALYTICS" not in tezcoin:
        error("Third-party analytics is not protected by the explicit opt-in constant")
    if "'ga_id'           => ''" not in tezcoin or "'clarity_id'      => ''" not in tezcoin:
        error("GA/Clarity must default to disabled until explicitly configured")
    if "cdn.ywxi.net/js/1.js" in footer:
        error("Unreliable TrustedSite script must not be injected in footer.php")
    if "ساماندهی" in footer or "ثبت ملی" in footer:
        error("Placeholder Iranian seals must not remain in footer.php")
    dash = (ROOT / "page-account.php").read_text(encoding="utf-8")
    if "teznevise_auth_action" not in dash:
        error("Front-end login/register is missing from page-account.php")
    if "'overview'" not in dash and '"overview"' not in dash:
        error("Customer dashboard overview tab is missing")
    tezcoin = (ROOT / "inc" / "tezcoin.php").read_text(encoding="utf-8")
    if "function teznevise_maybe_welcome_coins" not in tezcoin:
        error("Welcome 30-coin grant is missing")
    waitlist = (ROOT / "inc" / "waitlist.php").read_text(encoding="utf-8")
    if "tz-tools-notice" not in waitlist:
        error("Tools waitlist is not the bottom notice bar")
    unis = (ROOT / "template-parts" / "universities.php").read_text(encoding="utf-8")
    if "tz-uni-card" not in unis:
        error("University row is still using the clipped uni-mark badge")
    legal = (ROOT / "inc" / "legal-copy.php").read_text(encoding="utf-8")
    if "برایتان می‌نویسیم" not in legal:
        error("Legal copy does not rewrite ghostwriting phrases")
    perf = (ROOT / "inc" / "perf.php").read_text(encoding="utf-8")
    if "hotfix-201.css" not in perf:
        error("hotfix-201.css is not in the runtime CSS concat")
    if "hotfix-202.css" not in perf:
        error("hotfix-202.css is not in the runtime CSS concat")
    if "hotfix-203.css" not in perf:
        error("hotfix-203.css is not in the runtime CSS concat")
    if "hotfix-204.css" not in perf:
        error("hotfix-204.css is not in the runtime CSS concat")
    if "hotfix-205.css" not in perf:
        error("hotfix-205.css is not in the runtime CSS concat")
    if "hotfix-206.css" not in perf:
        error("hotfix-206.css is not in the runtime CSS concat")
    if "hotfix-207.css" not in perf:
        error("hotfix-207.css is not in the runtime CSS concat")
    if "hotfix-208.css" not in perf:
        error("hotfix-208.css is not in the runtime CSS concat")
    if "hotfix-209.css" not in perf:
        error("hotfix-209.css is not in the runtime CSS concat")
    if "hotfix-210.css" not in perf:
        error("hotfix-210.css is not in the runtime CSS concat")

    seo = (ROOT / "inc" / "seo.php").read_text(encoding="utf-8")
    if "function teznevise_split_faq_blocks" not in seo:
        error("FAQ leftover splitter is missing")
    if "FAQPage" not in seo:
        error("FAQPage JSON-LD is missing")
    if "'/about'" not in seo and '"/about"' not in seo:
        error("/about/ is not redirected to about-us")
    if "function teznevise_lead_nonce_field" not in seo:
        error("Unique lead nonce helper is missing")
    if "'/order'" not in seo and '"/order"' not in seo:
        error("/order/ is not redirected to inquiry")
    if "home_url( '/blog/' )" not in seo and 'home_url( "/blog/" )' not in seo:
        error("/posts/ is not forced to /blog/")
    if "add_action( 'init', 'teznevise_alias_redirects', -999 )" not in seo:
        error("Alias redirects must run on init -999 to beat Yoast Premium")
    if "teznevise_alias_redirects();" not in seo:
        error("Alias redirects must also run at theme bootstrap for /posts/")
    if "ثبت سفارش" not in seo:
        error("Inquiry title still uses ثبت سفارش")
    aiapi = (ROOT / "inc" / "ai" / "class-ai-api.php").read_text(encoding="utf-8")
    if "function sanitize_public_reply" not in aiapi:
        error("Chat public-reply sanitizer is missing")
    extracted = (ROOT / "inc" / "extracted-pages.php").read_text(encoding="utf-8")
    if "is_front_page" not in extracted:
        error("Homepage leftover disclosure is still printed")
    if "function teznevise_strip_unverified_stat_chips" not in extracted:
        error("Unverified leftover stat-chip stripper is missing")
    about_visual = (ROOT / "template-parts" / "about-visual.php").read_text(encoding="utf-8")
    if "۹۸٪" in about_visual or "+۸۵۰۰" in about_visual:
        error("About visual still hardcodes invented counts")
    hotfix209 = (ROOT / "assets" / "css" / "hotfix-209.css").read_text(encoding="utf-8")
    if "faq-q__text" not in hotfix209 or "display: block !important" not in hotfix209:
        error("hotfix-209 does not unhide FAQ question text against hotfix-196 last-child rule")
    legacy = (ROOT / "inc" / "legacy-wpcode.php").read_text(encoding="utf-8")
    if '<h1 class="tzdl-single-title"' in legacy or "<h1 class='tzdl-single-title'" in legacy:
        error("Download single leftover still emits a second H1")
    if re.search(r"<h1>\s*<\?php echo esc_html\(\s*\$term->name", legacy):
        error("Download category leftover still emits a second H1")

    skills_cfg = (ROOT / "teznevise-core" / "config" / "skills.php").read_text(encoding="utf-8")
    for skill_agent in ("teznevise", "christina", "ada", "professor", "parantez", "elara", "cyrus", "mira"):
        if f"'{skill_agent}'" not in skills_cfg:
            error(f"Skills catalog missing agent {skill_agent}")
    blog = (ROOT / "inc" / "blog.php").read_text(encoding="utf-8")
    if "function teznevise_render_ai_overview" not in blog:
        error("Auto AI overview renderer is missing")
    if "بازبینی انسانی" not in blog:
        error("Human-review label is missing from overview helper")
    if "teznevise_mark_overview_human_review" not in blog:
        error("Human-review marker on overview edit is missing")
    if "teznevise_overview_meta_updated" not in blog:
        error("Overview meta-update hook for human review is missing")
    debate = (ROOT / "teznevise-core" / "inc" / "class-debate-orchestrator.php").read_text(encoding="utf-8")
    if "function enqueue_published" not in debate:
        error("Existing-post overview/debate backfill is missing")
    if "store_overview" not in debate:
        error("Overview store does not preserve human review")
    if "discussion_has_items" not in debate:
        error("Debate orchestrator does not detect empty discussions")
    if "1.9.21" not in debate:
        error("Backfill version bump 1.9.21 is missing")
    single = (ROOT / "single.php").read_text(encoding="utf-8")
    if "teznevise_render_ai_overview" not in single:
        error("Single post does not auto-render the AI overview")
    if "teznevise_builder_render_sections" in single:
        error("single.php must not dump page-builder sections under posts")
    comments_php = (ROOT / "comments.php").read_text(encoding="utf-8")
    if "/account/" not in comments_php:
        error("Comment login CTA is not the front-end /account/ page")
    dash = (ROOT / "page-account.php").read_text(encoding="utf-8")
    if "view=lost" not in dash and "'lost'" not in dash:
        error("Account lost-password view is missing")
    core = (ROOT / "teznevise-core" / "teznevise-core.php").read_text(encoding="utf-8")
    if "config/skills.php" not in core:
        error("skills.php is not loaded from teznevise-core")
    if "teznevise_core_backfill_tick" not in core:
        error("Backfill cron hook is not registered")

    agents_cfg = (ROOT / "teznevise-core" / "config" / "agents.php").read_text(encoding="utf-8")
    for agent_id in ("teznevise", "christina", "ada", "professor", "parantez", "elara", "cyrus", "mira"):
        if f"'{agent_id}'" not in agents_cfg and f'"{agent_id}"' not in agents_cfg:
            error(f"Named agent {agent_id} missing from roster config")
        skill = ROOT / "teznevise-core" / "skills" / f"{agent_id}.md"
        if not skill.is_file():
            error(f"SKILL.md missing for {agent_id}")
        mark = ROOT / "assets" / "img" / "agents" / f"{agent_id}.svg"
        if not mark.is_file():
            error(f"Agent SVG missing: {agent_id}")
    debate = (ROOT / "teznevise-core" / "inc" / "class-debate-orchestrator.php").read_text(encoding="utf-8")
    if "visualizer" not in debate or "synthesis" not in debate:
        error("Debate orchestrator is missing visualizer/synthesis steps")
    if "teznevise_core_debate_sequence" not in (ROOT / "teznevise-core" / "config" / "agents.php").read_text(encoding="utf-8"):
        error("Debate sequence helper is missing")
    seq = (ROOT / "teznevise-core" / "config" / "agents.php").read_text(encoding="utf-8")
    if "'ada', 'professor', 'parantez', 'elara', 'cyrus', 'mira'" not in seq:
        error("Debate sequence is not Ada→Professor→Parantez→Elara→Cyrus→Mira")
    hub = (ROOT / "teznevise-core" / "inc" / "class-admin-hub.php").read_text(encoding="utf-8")
    if "teznevise-hub" not in hub:
        error("Admin hub menu slug is missing")
    nav = (ROOT / "assets" / "js" / "nav-dropdown.js").read_text(encoding="utf-8")
    if "scheduleOpen" not in nav or "openDelay" not in nav:
        error("Mega-menu hover-intent open delay is missing")
    tezcoin = (ROOT / "inc" / "tezcoin.php").read_text(encoding="utf-8")
    if "ساماندهی" in tezcoin or "samandehi_url" in tezcoin:
        error("Samandehi leftover must not remain in tezcoin admin")
    footer = (ROOT / "footer.php").read_text(encoding="utf-8")
    if "cdn.ywxi.net/js/1.js" in footer:
        error("TrustedSite DNS-dependent script still exists in footer")
    chat = (ROOT / "js" / "ai" / "chat.js").read_text(encoding="utf-8")
    if "typewrite" not in chat:
        error("Chat composer is missing typewriter streaming")
    chat_php = (ROOT / "inc" / "ai" / "class-ai-chat.php").read_text(encoding="utf-8")
    if "data-agent-pick" not in chat_php:
        error("Chat composer is missing the named-agent picker")
    if "data-ai-model" not in chat_php:
        error("Chat composer is missing the compact LLM selector")
    comments = (ROOT / "inc" / "ai-comments.php").read_text(encoding="utf-8")
    if "ava-method" in comments and "teznevise_core_agent_roster" not in comments:
        error("AI comment defaults still use آوا/پارسا/نیکا without roster mapping")

    settings = (ROOT / "inc" / "ai" / "class-ai-settings.php").read_text(encoding="utf-8")
    if "displayed_model_name" not in settings:
        error("Agent form is missing the handwritten displayed_model_name field")
    meta = (ROOT / "teznevise-core" / "inc" / "class-meta-boxes.php").read_text(encoding="utf-8")
    if "tz-skill-md-pick" not in meta:
        error("Per-post SKILL.md media button is missing")
    if "teznevise_api_key_agent" not in meta:
        error("Per-agent post API key fields are missing")
    faq207 = (ROOT / "assets" / "css" / "hotfix-207.css").read_text(encoding="utf-8")
    if "faq-item.open" not in faq207:
        error("hotfix-207 does not restore FAQ accordion collapse")
    builder = (ROOT / "inc" / "class-teznevise-builder.php").read_text(encoding="utf-8")
    if "'unique'" not in builder and '"unique"' not in builder:
        error("Builder renderer does not skip duplicate section types")
    compat = (ROOT / "inc" / "frontend-compat.php").read_text(encoding="utf-8")
    if "function teznevise_hero_inquiry_allowed" not in compat:
        error("Hero inquiry allow-list helper is missing")
    for mark in (
        "assets/img/universities/tehran.svg",
        "assets/img/universities/amirkabir.svg",
        "assets/img/universities/sbu.svg",
        "assets/img/universities/sharif.webp",
        "assets/img/universities/tmu.webp",
        "assets/img/universities/iust.webp",
        "assets/img/universities/isfahan.webp",
        "assets/img/universities/shiraz.webp",
    ):
        if not (ROOT / mark).is_file():
            error(f"University mark missing: {mark}")

    knowledge = (ROOT / "inc" / "ai" / "class-ai-knowledge.php").read_text(encoding="utf-8")
    if "CREATE TABLE" not in knowledge or "ft_chunk" not in knowledge:
        error("Knowledge corpus CREATE TABLE / FULLTEXT is missing")
    if "teznevise_ai_corpus_v" not in knowledge or "1.9.22" not in knowledge:
        error("Corpus backfill version 1.9.22 is missing")
    if "prompt_pack" not in knowledge:
        error("Knowledge prompt_pack() is missing")
    api = (ROOT / "inc" / "ai" / "class-ai-api.php").read_text(encoding="utf-8")
    if "/chat/rate" not in api:
        error("REST /chat/rate is missing")
    if "prompt_pack" not in api:
        error("Chat system prompt does not inject site knowledge")
    chat_js = (ROOT / "js" / "ai" / "chat.js").read_text(encoding="utf-8")
    if "det.open = true" not in chat_js or "فرآیند پاسخ" not in chat_js:
        error("Live response process must open immediately with a visible status label")
    if "chat/rate" not in chat_js:
        error("Chat client does not post ratings")
    live = (ROOT / "template-parts" / "live-chat.php").read_text(encoding="utf-8")
    if 'data-thinking="0"' not in live:
        error("Live chat process preference is not off by default")
    if 'data-collaboration-mode="single"' not in live:
        error("Live chat default is not single-agent")
    hub = (ROOT / "teznevise-core" / "inc" / "class-admin-hub.php").read_text(encoding="utf-8")
    if "reindex_corpus" not in hub:
        error("Admin hub is missing corpus reindex")
    hotfix208 = (ROOT / "assets" / "css" / "hotfix-208.css").read_text(encoding="utf-8")
    if "tz-livechat" not in hotfix208:
        error("hotfix-208 does not cover live chat chrome")
    css_ai = (ROOT / "css" / "teznevise-ai.css").read_text(encoding="utf-8")
    if ".tz-ai-chat.tz-gpt" not in css_ai:
        error("teznevise-ai.css lost the ChatGPT composer chrome")
    if ".tz-gpt-hints" not in css_ai:
        error("teznevise-ai.css is missing hint-chip styles")


def check_whitespace() -> None:
    for path in ROOT.rglob("*"):
        if not path.is_file():
            continue
        rel = path.relative_to(ROOT).as_posix()
        if rel.startswith(".git/") or "/node_modules/" in rel or rel.startswith("teznevise_work/"):
            continue
        if path.suffix not in {".php", ".css", ".js", ".md", ".yml", ".json", ".py", ".txt"}:
            continue
        try:
            text = path.read_text(encoding="utf-8")
        except (OSError, UnicodeDecodeError):
            continue
        marker_left = "<" * 7
        marker_right = ">" * 7
        if marker_left in text or marker_right in text:
            error(f"Conflict marker in {rel}")


def main() -> int:
    os.chdir(ROOT)
    check_no_nested_theme()
    version = version_from_functions()
    if version:
        check_versions(version)
    check_requires()
    check_json()
    check_source_contracts()
    check_whitespace()
    if ERRORS:
        print(f"{len(ERRORS)} release check(s) failed", file=sys.stderr)
        return 1
    print("release checks passed")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
