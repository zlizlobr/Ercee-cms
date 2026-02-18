#!/usr/bin/env python3
"""Gate 2 checks: implementation scope localization and cross-module guardrails."""

from __future__ import annotations

import argparse
import os
import subprocess
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]

CODE_EXTENSIONS = {".php", ".js", ".ts", ".vue", ".blade.php"}
IMPLEMENTATION_TOP_LEVEL_DIRS = {
    "app",
    "bootstrap",
    "config",
    "database",
    "lang",
    "modules",
    "public",
    "resources",
    "routes",
    "scripts",
    "tests",
}
IMPLEMENTATION_ROOT_FILES = {
    "composer.json",
    "composer.lock",
    "package.json",
    "package-lock.json",
    "phpunit.xml",
    "phpstan.neon",
    "vite.config.js",
}
FORBIDDEN_PATTERNS = (
    "->get(\"http://\"",
    "->get('http://",
    "->post(\"http://\"",
    "->post('http://",
    "file_get_contents('http://",
    "file_get_contents(\"http://\"",
)


def git_ref_exists(ref: str) -> bool:
    result = subprocess.run(
        ["git", "rev-parse", "--verify", "--quiet", ref],
        capture_output=True,
        text=True,
        cwd=ROOT,
    )
    return result.returncode == 0


def detect_diff_range(base_ref: str) -> str:
    candidates: list[str] = []

    # Explicit CLI input wins if available.
    if base_ref:
        candidates.append(base_ref)

    # PR base branch from GitHub Actions env.
    github_base_ref = os.getenv("GITHUB_BASE_REF", "").strip()
    if github_base_ref:
        candidates.extend([f"origin/{github_base_ref}", github_base_ref])

    # Common defaults.
    candidates.extend(["origin/main", "main", "origin/develop", "develop"])

    for ref in candidates:
        if git_ref_exists(ref):
            return f"{ref}...HEAD"

    # Fallback for shallow/single-branch checkouts.
    if git_ref_exists("HEAD~1"):
        return "HEAD~1..HEAD"

    # Last resort: inspect current commit only.
    return "HEAD"


def changed_files(diff_range: str) -> list[Path]:
    if diff_range == "HEAD":
        cmd = ["git", "show", "--name-only", "--pretty=format:", "HEAD"]
    else:
        cmd = ["git", "diff", "--name-only", diff_range]

    result = subprocess.run(cmd, capture_output=True, text=True, cwd=ROOT)
    if result.returncode != 0:
        raise RuntimeError(result.stderr.strip() or "git diff failed")

    files: list[Path] = []
    for line in result.stdout.splitlines():
        line = line.strip()
        if not line:
            continue
        files.append(ROOT / line)
    return files


def is_code_file(path: Path) -> bool:
    if path.suffix in CODE_EXTENSIONS:
        return True
    return path.name.endswith(".blade.php")


def check_scope_localization(files: list[Path], max_roots: int) -> list[str]:
    roots = set()
    for path in files:
        rel = path.relative_to(ROOT)
        parts = rel.parts
        if not parts:
            continue
        if len(parts) == 1:
            if parts[0] in IMPLEMENTATION_ROOT_FILES:
                roots.add("root-config")
            continue
        if parts[0] in IMPLEMENTATION_TOP_LEVEL_DIRS:
            roots.add(parts[0])

    if len(roots) > max_roots:
        return [
            f"Implementation is spread across too many top-level areas ({len(roots)} > {max_roots}): {', '.join(sorted(roots))}"
        ]
    return []


def check_cross_module_guardrails(files: list[Path]) -> list[str]:
    errors: list[str] = []

    for path in files:
        if not path.exists() or not is_code_file(path):
            continue
        rel = path.relative_to(ROOT)
        text = path.read_text(encoding="utf-8", errors="ignore")

        if "modules/" in str(rel):
            for pattern in FORBIDDEN_PATTERNS:
                if pattern in text:
                    errors.append(f"{rel}: forbidden raw HTTP pattern '{pattern}'")

    return errors


def check_implementace_evidence() -> list[str]:
    readme = ROOT / "artifacts" / "gates" / "README.md"
    if not readme.exists():
        return ["Missing artifacts/gates/README.md"]
    return []


def main() -> int:
    parser = argparse.ArgumentParser(description="Enforce Gate 2 implementace criteria")
    parser.add_argument("--base-ref", default="origin/main")
    parser.add_argument("--max-top-level-roots", type=int, default=6)
    args = parser.parse_args()

    diff_range = detect_diff_range(args.base_ref)
    print(f"INFO: using diff range '{diff_range}'")

    try:
        files = changed_files(diff_range)
    except RuntimeError as exc:
        print(f"ERROR: unable to determine changed files: {exc}")
        return 1

    if not files:
        print("OK: no changed files detected")
        return 0

    errors: list[str] = []
    errors.extend(check_scope_localization(files, args.max_top_level_roots))
    errors.extend(check_cross_module_guardrails(files))
    errors.extend(check_implementace_evidence())

    if errors:
        print("Gate 2 implementation criteria failed:\n")
        for err in errors:
            print(f"- {err}")
        return 1

    print("OK: Gate 2 implementation criteria passed")
    return 0


if __name__ == "__main__":
    sys.exit(main())
