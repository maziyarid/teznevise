#!/usr/bin/env python3
"""Release gates for the Teznevise WordPress theme root.

Fails if the 1.9.6 patch is packaged one directory too deep, if versions
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
