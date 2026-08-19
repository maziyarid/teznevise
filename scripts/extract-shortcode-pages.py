#!/usr/bin/env python3
"""Extract published PAGE content (HTML + WPCode shortcodes) into theme custom fields.

Never invents copy. Never reads or writes post_type=post. Preserves slugs.

Usage:
  python3 scripts/extract-shortcode-pages.py
  python3 scripts/extract-shortcode-pages.py --check
"""
from __future__ import annotations

import argparse
import csv
import json
import re
import sys
from collections import Counter, defaultdict
from html import unescape
from pathlib import Path

from bs4 import BeautifulSoup, NavigableString, Tag

ROOT = Path(__file__).resolve().parents[1]
CSV_PATH = ROOT / "docs" / "sep_posts.csv"
DEFAULTS_PATH = ROOT / "inc" / "builder-defaults.json"
OUT_PATH = ROOT / "inc" / "extracted-page-fields.json"

PLACEHOLDER_RE = re.compile(r"PLACEHOLDER|replace with real copy", re.I)
SHORTCODE_ONLY_RE = re.compile(r"^\s*\[[a-zA-Z][a-zA-Z0-9_-]*(?:\s[^\]]*)?\]\s*$")
ADD_SHORTCODE_RE = re.compile(r"add_shortcode\(\s*['\"]([a-zA-Z0-9_-]+)['\"]")
PAGE_SHORTCODE_RE = re.compile(r"\[([a-zA-Z][a-zA-Z0-9_-]*)")
PHP_HTML_RE = re.compile(r"\?>([\s\S]*?)(?:<\?php|$)")
STYLE_RE = re.compile(r"<style\b[^>]*>[\s\S]*?</style>", re.I)
SCRIPT_RE = re.compile(r"<script\b[^>]*>[\s\S]*?</script>", re.I)

# Original production slugs → theme PHP templates. Do not invent new slugs.
TEMPLATE_BY_SLUG = {
    "home": "front-page.php",
    "about-us": "page-about.php",
    "our-team": "page-team.php",
    "contact-us": "page-contact.php",
    "inquiry": "page-contact.php",
    "privacy-policy": "page-privacy.php",
    "cookie-policy": "page-privacy.php",
    "downloads": "page-downloads.php",
    "online-calculation-tools": "page-tools.php",
    "blog": "",
}

SERVICE_SLUGS = {
    "thesis",
    "proposal",
    "gams",
    "price-calculator",
    "case-studies",
}

CALCULATOR_PARENT = "online-calculation-tools"

ICON_HINTS = [
    (r"پایان.?نامه|thesis|graduation", "fa-solid fa-graduation-cap"),
    (r"پروپوزال|proposal", "fa-solid fa-file-circle-check"),
    (r"آمار|آماری|spss|آزمون", "fa-solid fa-chart-line"),
    (r"شبیه", "fa-solid fa-flask"),
    (r"محاسب|ابزار|calculator", "fa-solid fa-calculator"),
    (r"تیم|پژوهشگر", "fa-solid fa-user-group"),
    (r"تماس|ارتباط", "fa-solid fa-phone"),
    (r"سفارش|inquiry", "fa-solid fa-pen-to-square"),
    (r"حریم|سیاست|قوانین|کوکی", "fa-solid fa-shield-halved"),
    (r"دانلود", "fa-solid fa-download"),
    (r"افتخار|سابقه", "fa-solid fa-trophy"),
    (r"داستان", "fa-solid fa-book-open"),
    (r"شغل|همکاری|careers|join", "fa-solid fa-briefcase"),
]


def clean_text(value: str | None, limit: int = 0) -> str:
    if not value:
        return ""
    text = unescape(str(value))
    text = re.sub(r"\[[^\]]*\]", " ", text)  # drop leftover shortcodes inside text
    text = re.sub(r"<[^>]+>", " ", text)
    text = re.sub(r"\s+", " ", text).strip(" \t\r\n-–—|")
    if PLACEHOLDER_RE.search(text):
        return ""
    if limit and len(text) > limit:
        text = text[: limit - 1].rsplit(" ", 1)[0] + "…"
    return text


def first_icon(html: str) -> str:
    m = re.search(r'class=["\']([^"\']*fa-[^"\']+)["\']', html or "")
    if not m:
        return ""
    classes = [c for c in m.group(1).split() if c.startswith("fa")]
    return " ".join(classes)[:100]


def guess_icon(title: str) -> str:
    blob = title or ""
    for pattern, icon in ICON_HINTS:
        if re.search(pattern, blob, re.I):
            return icon
    return ""


def extract_html_from_php(code: str) -> str:
    if not code:
        return ""
    if code.count("[PLACEHOLDER") >= 2 or "replace with real copy" in code.lower():
        # Homepage WPCode was reconstructed; do not treat placeholders as content.
        return ""
    parts = [m.group(1) for m in PHP_HTML_RE.finditer(code)]
    html = "\n".join(parts) if parts else code
    html = STYLE_RE.sub("", html)
    html = SCRIPT_RE.sub("", html)
    html = re.sub(r"<\?php[\s\S]*?\?>", " ", html)
    html = re.sub(r"<\?[\s\S]*?\?>", " ", html)
    return html


def soup_of(html: str) -> BeautifulSoup:
    return BeautifulSoup(html or "", "html.parser")


def el_text(el: Tag | None, limit: int = 0) -> str:
    if el is None:
        return ""
    return clean_text(el.get_text(" ", strip=True), limit=limit)


def page_path(page: dict, by_id: dict[str, dict]) -> str:
    slug = page.get("post_name") or ""
    if not slug:
        return ""
    parts = [slug]
    pid = page.get("post_parent") or "0"
    seen: set[str] = set()
    while pid and pid not in ("0", "") and pid not in seen:
        seen.add(pid)
        parent = by_id.get(pid)
        if not parent:
            break
        parent_slug = parent.get("post_name") or ""
        if parent_slug:
            parts.append(parent_slug)
        pid = parent.get("post_parent") or "0"
    return "/".join(reversed(parts))


def template_for(page: dict, path: str, parent_slug: str) -> str:
    slug = page.get("post_name") or ""
    if slug in TEMPLATE_BY_SLUG:
        return TEMPLATE_BY_SLUG[slug]
    if slug.endswith("-calculator") or slug == "price-calculator":
        return "page-tool.php"
    if parent_slug == CALCULATOR_PARENT:
        return "page-tool.php"
    if slug in SERVICE_SLUGS or parent_slug in ("thesis", "proposal", "humanities"):
        return "page-service.php"
    if path.startswith("thesis/") or path.startswith("proposal/"):
        return "page-service.php"
    return "page.php"


def looks_like_app_ui(soup: BeautifulSoup, slug: str = "") -> bool:
    slug = slug or ""
    if "calculator" in slug or slug in {"price-calculator"}:
        return True
    if soup.select(".tz-calc, .tz-calculator, [data-calculator], .calc-wrap, .tz-descriptive"):
        return True
    return False


def collect_items_from_cards(container: Tag, limit: int = 24) -> list[dict]:
    items = []
    card_sel = re.compile(
        r"(card|item|step|col|box|member|tool|svc|feature|challenge|stat|faq|-w|-sc|-st|-sa)\b",
        re.I,
    )
    cards = [
        el
        for el in container.find_all(["article", "div", "li", "a"])
        if any(card_sel.search(c or "") for c in el.get("class", []))
    ]
    if not cards:
        cards = container.find_all(["li", "article"])
    seen = set()
    for card in cards:
        # Skip nested cards already counted via parent
        if card.parent in cards:
            continue
        title_el = card.find(["h2", "h3", "h4", "strong", "b"])
        title = el_text(title_el, 120)
        if not title:
            title = clean_text(card.get("aria-label") or "", 120)
        text = ""
        for p in card.find_all("p"):
            text = el_text(p, 400)
            if text and text != title:
                break
        if not text:
            raw = el_text(card, 400)
            if title and raw.startswith(title):
                raw = raw[len(title) :].strip()
            text = clean_text(raw, 280)
        href = ""
        link = card if card.name == "a" else card.find("a", href=True)
        if link and link.get("href"):
            href = link.get("href").strip()
            if href.startswith("javascript:"):
                href = ""
        icon = first_icon(str(card))
        key = (title, text[:80], href)
        if not title or key in seen:
            continue
        if PLACEHOLDER_RE.search(title) or PLACEHOLDER_RE.search(text):
            continue
        seen.add(key)
        item = {"title": title, "text": text}
        if icon:
            item["icon"] = icon
        if href and not href.startswith("#"):
            item["url"] = href
        items.append(item)
        if len(items) >= limit:
            break
    return items


def section(
    stype: str,
    *,
    title: str = "",
    text: str = "",
    eyebrow: str = "",
    items: list | None = None,
    cta_text: str = "",
    cta_url: str = "",
    columns: str = "3",
    background: str = "default",
) -> dict | None:
    if PLACEHOLDER_RE.search(title or "") or PLACEHOLDER_RE.search(text or ""):
        return None
    if not title and not text and not items:
        return None
    data = {
        "type": stype,
        "enabled": True,
        "eyebrow": eyebrow,
        "title": title,
        "text": text,
        "columns": columns,
        "background": background,
        "items": items or [],
    }
    if stype in ("hero", "cta_band"):
        data["cta_text"] = cta_text
        data["cta_url"] = cta_url
    if stype == "cta_band":
        data.pop("eyebrow", None)
        data.pop("columns", None)
        data.pop("items", None)
        data["items"] = []
    return data


def classify_heading(title: str) -> str:
    t = title or ""
    if re.search(r"چالش|عواقب|ضعیف|مشکل", t):
        return "challenges"
    if re.search(r"مراحل|قدم|فرآیند|فرایند|چگونه", t):
        return "process_steps"
    if re.search(r"خدمت|خدمات|رشته|نرم.?افزار|ابزار|مقطع|روش", t):
        return "service_cards"
    if re.search(r"سوالات متداول|پرسش", t):
        return "feature_list"
    if re.search(r"ثبت|شروع|بنویسید|سفارش|مشاوره رایگان|آماده", t):
        return "cta_band"
    return "feature_list"


def parse_hero(soup: BeautifulSoup, fallback_title: str) -> tuple[dict, Tag | None]:
    h1 = soup.find("h1")
    title = el_text(h1, 160) or fallback_title
    eyebrow = ""
    lead = ""
    cta_text = ""
    cta_url = ""
    items: list[dict] = []

    start = h1 or soup
    # eyebrow / badge just before or inside hero
    badge = None
    if h1:
        badge = h1.find_previous(
            lambda t: isinstance(t, Tag)
            and t.name in ("span", "div", "p")
            and any("badge" in (c or "").lower() or "eyebrow" in (c or "").lower() for c in t.get("class", []))
        )
    if badge:
        eyebrow = el_text(badge, 80)
    if not eyebrow:
        for el in soup.find_all(["span", "div"], class_=True)[:12]:
            cls = " ".join(el.get("class", [])).lower()
            if "badge" in cls or "eyebrow" in cls:
                eyebrow = el_text(el, 80)
                if eyebrow:
                    break

    paras = []
    node = h1.find_next_sibling() if h1 else None
    hops = 0
    cursor = h1
    while cursor and hops < 12:
        hops += 1
        cursor = cursor.find_next()
        if not isinstance(cursor, Tag):
            continue
        if cursor.name in ("h2", "h3"):
            break
        if cursor.name == "p":
            t = el_text(cursor, 400)
            if t and t not in paras:
                paras.append(t)
            if len(paras) >= 2:
                break
        if cursor.name == "a" and not cta_text:
            label = el_text(cursor, 60)
            href = (cursor.get("href") or "").strip()
            if label and href and href not in ("#", "javascript:void(0)"):
                cta_text, cta_url = label, href

    lead = paras[0] if paras else ""

    # trust / stats near hero
    stats = soup.select(".tz-stat-item, .tz-stat, .trust-item, [data-count]")
    for st in stats[:6]:
        num = st.get("data-count") or ""
        suffix = st.get("data-suffix") or ""
        label = ""
        lab_el = st.find(class_=re.compile(r"label"))
        if lab_el:
            label = el_text(lab_el, 80)
        if not label:
            label = el_text(st, 80)
        if num and label:
            title_i = f"{num}{suffix} {label}".strip()
        else:
            title_i = label
        title_i = clean_text(title_i, 80)
        if title_i:
            items.append({"title": title_i, "icon": "fa-solid fa-check"})

    sec = section(
        "hero",
        eyebrow=eyebrow,
        title=title,
        text=lead,
        items=items,
        cta_text=cta_text,
        cta_url=cta_url,
    )
    return sec or section("hero", title=fallback_title, text=lead), h1


def parse_faq(container: Tag) -> list[dict]:
    items = []
    faq_blocks = container.select(".tzpr-faq, .tzt-faq, .tz-faq, [class*='faq']")
    for block in faq_blocks:
        qel = block.select_one(".tzpr-q, .tzt-q, .tz-q, button, summary, h3, h4")
        ael = block.select_one(".tzpr-a, .tzt-a, .tz-a, .answer, p")
        q, a = el_text(qel, 200), el_text(ael, 600)
        if q and a and a.startswith(q):
            a = a[len(q) :].strip()
        if q:
            items.append({"title": q, "text": a, "icon": "fa-solid fa-circle-question"})
    if items:
        return items[:16]
    dts = container.find_all("dt")
    if dts:
        for dt in dts:
            dd = dt.find_next_sibling("dd")
            q, a = el_text(dt, 160), el_text(dd, 500)
            if q:
                items.append({"title": q, "text": a, "icon": "fa-solid fa-circle-question"})
        return items
    details = container.find_all("details")
    if details:
        for d in details:
            q = el_text(d.find("summary"), 160)
            a = el_text(d, 500)
            if q and a.startswith(q):
                a = a[len(q) :].strip()
            if q:
                items.append({"title": q, "text": a, "icon": "fa-solid fa-circle-question"})
        return items
    questions = container.find_all(["h3", "h4", "strong"])
    for qel in questions:
        q = el_text(qel, 160)
        if not q or not re.search(r"[؟?]", q):
            # still accept FAQ-looking short headings
            if not q or len(q) > 140:
                continue
        nxt = qel.find_next_sibling()
        a = el_text(nxt, 500) if nxt else ""
        if q:
            items.append({"title": q, "text": a, "icon": "fa-solid fa-circle-question"})
    return items[:16]


def iter_heading_chunks(soup: BeautifulSoup):
    """Yield (h2, chunk_tag) without dropping cards that live outside the heading wrapper."""
    used = set()
    for sec in soup.find_all("section"):
        h2 = sec.find("h2")
        if h2 is None:
            continue
        used.add(id(h2))
        yield h2, sec
    for h2 in soup.find_all("h2"):
        if id(h2) in used:
            continue
        # Climb to a block that also contains the following grid.
        parent = h2.parent
        chunk_root = parent
        if parent and parent.parent and parent.parent.name not in ("body", "[document]", "html"):
            # If parent is a heading-only wrap (hd / head / title), use the grandparent.
            cls = " ".join(parent.get("class", [])).lower()
            if re.search(r"(hd|head|title|intro)$", cls) or parent.name in ("header",):
                chunk_root = parent.parent
        yield h2, chunk_root


def group_by_h2(soup: BeautifulSoup, skip_hero_h1: Tag | None) -> list[dict]:
    sections: list[dict] = []
    for h2, chunk in iter_heading_chunks(soup):
        title = el_text(h2, 160)
        if not title:
            continue
        if chunk is None:
            continue
        if looks_like_app_ui(chunk) and not chunk.find("h3"):
            # Calculator widget under a heading — keep heading text only.
            text = el_text(chunk.find("p"), 400)
            sec = section("feature_list", title=title, text=text, items=[])
            if sec:
                sections.append(sec)
            continue

        stype = classify_heading(title)
        text_bits = [el_text(p, 400) for p in chunk.find_all("p")]
        text_bits = [t for t in text_bits if t]
        desc = chunk.find("p", class_=re.compile(r"desc|lead|sub"))
        lead = el_text(desc, 400) if desc else " ".join(text_bits[:2])[:500]

        if stype == "cta_band":
            a = chunk.find("a", href=True)
            cta_text = el_text(a, 60) if a else "ثبت درخواست"
            cta_url = (a.get("href") if a else "/inquiry/") or "/inquiry/"
            sec = section("cta_band", title=title, text=lead, cta_text=cta_text, cta_url=cta_url, background="soft")
            if sec:
                sections.append(sec)
            continue

        if stype == "feature_list" and re.search(r"سوالات متداول|پرسش", title):
            items = parse_faq(chunk)
            sec = section("feature_list", title=title, text=lead, items=items, columns="2")
            if sec:
                sections.append(sec)
            continue

        items = collect_items_from_cards(chunk)
        if not items:
            # h3 blocks as items
            for h3 in chunk.find_all("h3"):
                it_title = el_text(h3, 120)
                nxt = h3.find_next_sibling()
                it_text = el_text(nxt, 400) if nxt else ""
                if it_title:
                    items.append({"title": it_title, "text": it_text})
        if not items:
            lis = [el_text(li, 200) for li in chunk.find_all("li")]
            lis = [t for t in lis if t]
            items = [{"title": t, "icon": "fa-solid fa-check"} for t in lis[:12]]

        if not items:
            full = el_text(chunk, 900)
            if title and full.startswith(title):
                full = full[len(title) :].strip()
            if not lead:
                lead = clean_text(full, 700)

        if stype == "process_steps":
            for i, it in enumerate(items, 1):
                it.setdefault("icon", "fa-solid fa-circle-check")
                if not it.get("text") and it.get("title"):
                    pass
            sec = section("process_steps", title=title, text=lead, items=items, background="soft")
        elif stype == "challenges":
            sec = section("challenges", title=title, text=lead, items=items, columns="3")
        elif stype == "service_cards":
            sec = section("service_cards", title=title, text=lead, items=items, columns="3")
        else:
            sec = section("feature_list", title=title, text=lead, items=items, columns="3")
        if sec:
            sections.append(sec)
    return sections


def parse_team_meta(soup: BeautifulSoup) -> dict:
    meta: dict[str, str] = {}
    stats_lines = []
    for st in soup.select(".tz-stat-item"):
        num = st.find(class_=re.compile(r"tz-stat-number"))
        lab = st.find(class_=re.compile(r"tz-stat-label"))
        n = ""
        if num:
            n = num.get("data-count") or el_text(num)
            suf = num.get("data-suffix") or ""
            n = f"{n}{suf}"
        label = el_text(lab)
        if n and label:
            stats_lines.append(f"{n}|{label}")
    if stats_lines:
        meta["team_stats"] = "\n".join(stats_lines)

    members = []
    for card in soup.select(".tz-team-card"):
        name = el_text(card.find(class_=re.compile(r"tz-card-title")))
        role = el_text(card.find(class_=re.compile(r"tz-card-subtitle")))
        field = el_text(card.find(class_=re.compile(r"tz-card-code")))
        bio_bits = []
        specs = card.select(".tz-spec-row")
        for row in specs[:4]:
            bio_bits.append(el_text(row, 160))
        if not name:
            name = el_text(card.find(["h3", "h4"]))
        if name:
            members.append("|".join([name, role, field, "؛ ".join(b for b in bio_bits if b)]))
    if members:
        meta["team_members"] = "\n".join(members)
    return meta


def parse_tools_list(soup: BeautifulSoup) -> str:
    lines = []
    for a in soup.find_all("a", href=True):
        href = a.get("href") or ""
        if not re.search(r"calculator|calculation|tool", href, re.I) and "/online" not in href:
            # still allow internal calc slugs
            if not re.search(r"-(calculator|size|alpha|validity|power|icc|kr20)", href, re.I):
                continue
        title = el_text(a, 80) or clean_text(a.get("title") or "", 80)
        parent = a.find_parent(["article", "div", "li"])
        desc = ""
        if parent:
            p = parent.find("p")
            desc = el_text(p, 160)
        icon = first_icon(str(parent or a))
        if title:
            lines.append("|".join([title, href, desc, icon]))
    # unique by href
    seen = set()
    out = []
    for line in lines:
        href = line.split("|")[1] if "|" in line else line
        if href in seen:
            continue
        seen.add(href)
        out.append(line)
    return "\n".join(out[:40])


def parse_timeline(soup: BeautifulSoup) -> str:
    lines = []
    for el in soup.select(".tz-timeline-item, .timeline-item, .tz-year"):
        raw = el_text(el, 240)
        if not raw:
            continue
        # year | title | text
        m = re.match(r"(۱[۳۴][۰-۹]{2}|13[0-9]{2}|14[0-9]{2})\s*[:\-–]?\s*(.*)", raw)
        if m:
            rest = m.group(2)
            if "—" in rest:
                a, b = rest.split("—", 1)
                lines.append(f"{m.group(1)}|{a.strip()}|{b.strip()}")
            else:
                lines.append(f"{m.group(1)}|{rest}|")
        elif "|" not in raw:
            lines.append(raw.replace(" – ", "|").replace(" - ", "|"))
    return "\n".join(lines[:12])


def html_to_builder(html: str, page_title: str, slug: str) -> tuple[list[dict], dict]:
    soup = soup_of(html)
    for t in soup(["style", "script", "noscript", "svg"]):
        t.decompose()
    meta: dict[str, str] = {}
    sections: list[dict] = []

    app_ui = looks_like_app_ui(soup, slug)
    hero, h1 = parse_hero(soup, page_title)
    if hero:
        sections.append(hero)
        if hero.get("eyebrow"):
            meta["eyebrow"] = hero["eyebrow"]
        if hero.get("text"):
            meta["subtitle"] = hero["text"]
        if hero.get("cta_text"):
            meta["cta_text"] = hero["cta_text"]
        if hero.get("cta_url"):
            meta["cta_url"] = hero["cta_url"]

    icon = first_icon(html) or guess_icon(page_title + " " + slug)
    if icon:
        meta["service_icon"] = icon

    if slug in ("our-team", "team"):
        meta.update(parse_team_meta(soup))
    if slug in ("online-calculation-tools", "tools"):
        tools = parse_tools_list(soup)
        if tools:
            meta["tools_list"] = tools
    if slug in ("about-us", "our-story"):
        tl = parse_timeline(soup)
        if tl:
            meta["timeline"] = tl

    if app_ui:
        # Calculators: hero + any FAQ/heading copy, skip the widget itself.
        for h2 in soup.find_all("h2"):
            title = el_text(h2, 160)
            if re.search(r"سوالات|راهنما|چگونه|توضیح", title or ""):
                chunk_nodes = []
                for sib in h2.next_siblings:
                    if isinstance(sib, Tag) and sib.name == "h2":
                        break
                    chunk_nodes.append(sib)
                chunk = soup_of("<div>" + "".join(str(n) for n in chunk_nodes) + "</div>").div
                items = parse_faq(chunk) if chunk else []
                if not items and chunk:
                    items = [{"title": el_text(li, 200), "icon": "fa-solid fa-check"} for li in chunk.find_all("li") if el_text(li)]
                sec = section("feature_list", title=title, items=items, columns="2")
                if sec:
                    sections.append(sec)
        features = []
        for li in soup.find_all("li"):
            t = el_text(li, 160)
            if t and len(t) < 140:
                features.append(t)
            if len(features) >= 8:
                break
        if features:
            meta["features"] = "\n".join(features)
        return sections, meta

    rest = group_by_h2(soup, h1)
    sections.extend(rest)

    if not rest:
        # unstructured HTML: turn h2-less paragraphs / lists into feature_list
        items = collect_items_from_cards(soup)
        if not items:
            lis = [el_text(li, 220) for li in soup.find_all("li")]
            items = [{"title": t, "icon": "fa-solid fa-check"} for t in lis if t][:16]
        if not items:
            ps = [el_text(p, 400) for p in soup.find_all("p")]
            ps = [p for p in ps if p and p != (hero or {}).get("text")]
            items = [{"title": p[:80], "text": p} for p in ps[:10]]
        if items:
            sec = section("feature_list", title="", text="", items=items)
            if sec:
                sections.append(sec)

    # trailing CTA if none
    if not any(s.get("type") == "cta_band" for s in sections):
        a = None
        for cand in soup.find_all("a", href=True):
            label = el_text(cand, 40)
            href = cand.get("href") or ""
            if re.search(r"سفارش|مشاوره|درخواست|تماس", label) and href:
                a = cand
                break
        if a:
            sec = section(
                "cta_band",
                title=el_text(a, 80),
                text="",
                cta_text=el_text(a, 60),
                cta_url=a.get("href"),
                background="soft",
            )
            if sec:
                sections.append(sec)

    # features meta from first feature_list items
    for s in sections:
        if s.get("type") == "feature_list" and s.get("items") and "features" not in meta:
            lines = []
            for it in s["items"][:8]:
                line = it.get("title") or ""
                if it.get("text"):
                    line = f"{line} — {it['text']}" if line else it["text"]
                if line:
                    lines.append(line[:200])
            if lines:
                meta["features"] = "\n".join(lines)
            break

    return sections, meta


def load_dump() -> tuple[list[dict], list[dict]]:
    csv.field_size_limit(50_000_000)
    pages: list[dict] = []
    wpcode: list[dict] = []
    with CSV_PATH.open(newline="", encoding="utf-8") as fh:
        for row in csv.DictReader(fh):
            ptype = row.get("post_type")
            if ptype == "page":
                pages.append(row)
            elif ptype == "wpcode":
                wpcode.append(row)
    return pages, wpcode


def shortcode_map(wpcode: list[dict]) -> dict[str, str]:
    mapping: dict[str, str] = {}
    for row in wpcode:
        html = extract_html_from_php(row.get("post_content") or "")
        if not html or len(clean_text(html)) < 40:
            continue
        names = ADD_SHORTCODE_RE.findall(row.get("post_content") or "")
        for name in names:
            # Prefer longer HTML if a shortcode appears twice (publish over trash)
            prev = mapping.get(name, "")
            if len(html) > len(prev):
                mapping[name] = html
    return mapping


def page_source_html(page: dict, sc_html: dict[str, str]) -> tuple[str, str]:
    content = page.get("post_content") or ""
    stripped = content.strip()
    if stripped and not SHORTCODE_ONLY_RE.match(stripped) and len(clean_text(stripped)) >= 80:
        return content, "page_html"
    codes = PAGE_SHORTCODE_RE.findall(content)
    for code in codes:
        if code in sc_html and sc_html[code]:
            return sc_html[code], f"shortcode:{code}"
    return "", "empty"


def index_defaults_for_home() -> tuple[list[dict], dict]:
    """Home WPCode is placeholder-only; use the site's own redesign defaults."""
    if not DEFAULTS_PATH.exists():
        return [], {}
    doc = json.loads(DEFAULTS_PATH.read_text(encoding="utf-8"))
    entry = (doc.get("pages") or {}).get("index") or {}
    sections = entry.get("sections") or []
    meta = {
        "subtitle": entry.get("excerpt") or "",
        "eyebrow": "تزنویسه",
    }
    return sections, meta


def build_document() -> dict:
    pages, wpcode = load_dump()
    by_id = {p["ID"]: p for p in pages}
    sc_html = shortcode_map(wpcode)
    published = [p for p in pages if p.get("post_status") == "publish" and (p.get("post_name") or "")]

    out_pages: dict[str, dict] = {}
    stats = Counter()

    for page in published:
        path = page_path(page, by_id)
        slug = page.get("post_name") or ""
        parent = by_id.get(page.get("post_parent") or "")
        parent_slug = (parent or {}).get("post_name") or ""
        title = page.get("post_title") or slug
        html, source = page_source_html(page, sc_html)

        if slug == "home" and (not html or source == "empty"):
            sections, meta = index_defaults_for_home()
            source = "builder-defaults:index"
        elif not html:
            # Still record identity so migrator can assign template / skip cleanly.
            sections, meta = [], {}
            stats["empty"] += 1
        else:
            sections, meta = html_to_builder(html, title, slug)
            stats[source.split(":")[0]] += 1

        # Drop empty section shells
        sections = [s for s in sections if s and (s.get("title") or s.get("text") or s.get("items"))]

        if not meta.get("cta_text") and sections:
            hero = next((s for s in sections if s.get("type") == "hero"), None)
            if hero and hero.get("cta_text"):
                meta["cta_text"] = hero["cta_text"]
                meta["cta_url"] = hero.get("cta_url") or ""

        if "cta_text" not in meta and slug not in ("blog",):
            # only if a CTA already exists in source sections
            cta = next((s for s in sections if s.get("type") == "cta_band"), None)
            if cta:
                meta.setdefault("cta_text", cta.get("cta_text") or "")
                meta.setdefault("cta_url", cta.get("cta_url") or "")

        entry = {
            "id": int(page["ID"]),
            "slug": slug,
            "path": path,
            "url": f"/{path}/" if path else "/",
            "title": title,
            "excerpt": clean_text(page.get("post_excerpt") or "", 240),
            "status": page.get("post_status"),
            "parent": int(page.get("post_parent") or 0),
            "parent_slug": parent_slug,
            "menu_order": int(page.get("menu_order") or 0),
            "guid": page.get("guid") or "",
            "template": template_for(page, path, parent_slug),
            "source": source,
            "shortcodes": sorted(set(PAGE_SHORTCODE_RE.findall(page.get("post_content") or ""))),
            "builder": bool(sections),
            "meta": {k: v for k, v in meta.items() if v},
            "sections": sections,
        }
        out_pages[path] = entry

    return {
        "meta": {
            "generated_from": "docs/sep_posts.csv",
            "policy": "pages-only; preserve slugs and post metadata; no invented copy",
            "published_pages": len(published),
            "with_sections": sum(1 for p in out_pages.values() if p["sections"]),
            "with_meta": sum(1 for p in out_pages.values() if p["meta"]),
            "sources": dict(stats),
            "skipped_post_types": ["post"],
        },
        "pages": out_pages,
    }


def validate(doc: dict) -> list[str]:
    errors = []
    pages = doc.get("pages") or {}
    if len(pages) < 70:
        errors.append(f"expected ~84 published pages, got {len(pages)}")
    # collisions on path
    if "proposal/phd" not in pages or "thesis/phd" not in pages:
        errors.append("nested phd paths missing (proposal/phd and thesis/phd)")
    # must not invent placeholder copy
    blob = json.dumps(pages, ensure_ascii=False)
    if "PLACEHOLDER" in blob:
        errors.append("PLACEHOLDER copy leaked into extracted fields")
    # never include posts
    for p in pages.values():
        if p.get("slug") in ("amoozesh-fasl-avval-payanname",):
            errors.append("sample post leaked into pages")
        if not p.get("slug"):
            errors.append("empty slug")
        if p.get("status") != "publish":
            errors.append(f"non-publish page {p.get('slug')}")
    # original slugs present
    for slug in ("about-us", "contact-us", "thesis", "proposal", "our-team", "inquiry", "privacy-policy"):
        if not any(p.get("slug") == slug for p in pages.values()):
            errors.append(f"missing original slug {slug}")
    # new seed slugs may exist empty — that's ok, but they must keep their slug
    thesis = pages.get("thesis")
    if thesis and not thesis.get("sections"):
        errors.append("thesis hub has no extracted sections")
    return errors


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--check", action="store_true")
    args = parser.parse_args()
    doc = build_document()
    errors = validate(doc)
    OUT_PATH.write_text(json.dumps(doc, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
    print(
        f"Wrote {OUT_PATH.relative_to(ROOT)} — "
        f"{doc['meta']['published_pages']} pages, "
        f"{doc['meta']['with_sections']} with sections, "
        f"{doc['meta']['with_meta']} with meta"
    )
    # compact table
    for path, p in sorted(doc["pages"].items(), key=lambda kv: kv[0]):
        print(
            f"  {path:42} sections={len(p['sections']):2} meta={len(p['meta']):2} "
            f"src={p['source'][:28]:28} tpl={p['template']}"
        )
    if errors:
        print("\nValidation failed:")
        for e in errors:
            print(" -", e)
        return 1
    print("\nValidation passed.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
