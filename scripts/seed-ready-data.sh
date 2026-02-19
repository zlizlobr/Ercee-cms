#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
MODULES_REPO="${MODULES_REPO:-/usr/local/var/www/ercee-modules}"
VERIFY_ONLY=0
STRICT_JSON_PATTERN=0

if [[ "${1:-}" == "--verify-only" ]]; then
    VERIFY_ONLY=1
fi

if [[ "${1:-}" == "--strict-json-pattern" ]] || [[ "${2:-}" == "--strict-json-pattern" ]]; then
    STRICT_JSON_PATTERN=1
fi

declare -a SEEDER_ENTRIES=(
    "Database\\Seeders\\RolesAndPermissionsSeeder|$ROOT_DIR/database/seeders/RolesAndPermissionsSeeder.php"
    "Database\\Seeders\\AdminUserSeeder|$ROOT_DIR/database/seeders/AdminUserSeeder.php"
    "Database\\Seeders\\NavigationSeeder|$ROOT_DIR/database/seeders/NavigationSeeder.php"
    "Database\\Seeders\\ProductsSeeder|$ROOT_DIR/database/seeders/ProductsSeeder.php"
    "Database\\Seeders\\TemplatePagesSeeder|$ROOT_DIR/database/seeders/TemplatePagesSeeder.php"
    "Database\\Seeders\\HomePageSeeder|$ROOT_DIR/database/seeders/HomePageSeeder.php"
    "Modules\\Forms\\Database\\Seeders\\FormsSeeder|$MODULES_REPO/ercee-module-forms/database/seeders/FormsSeeder.php"
    "Modules\\Analytics\\Database\\Seeders\\AnalyticsProvidersSeeder|$MODULES_REPO/ercee-module-analytics/database/seeders/AnalyticsProvidersSeeder.php"
)

declare -a NON_JSON_PATTERN=()
declare -a MISSING_FILES=()

print_header() {
    echo
    echo "== $1 =="
}

check_json_pattern() {
    local class_name="$1"
    local file_path="$2"

    if [[ ! -f "$file_path" ]]; then
        MISSING_FILES+=("$class_name ($file_path)")
        return
    fi

    if rg -q "readSeedJson\\(" "$file_path"; then
        echo "[OK] $class_name uses ReadsJsonSeedData::readSeedJson()"
        return
    fi

    if rg -q "json_decode\\(" "$file_path" && rg -q "seed-data|\\.json" "$file_path"; then
        echo "[OK] $class_name reads JSON directly"
        return
    fi

    NON_JSON_PATTERN+=("$class_name ($file_path)")
    echo "[WARN] $class_name does not match JSON seeder pattern"
}

run_seeders() {
    local class_name="$1"
    local file_path="$2"

    if [[ ! -f "$file_path" ]]; then
        echo "[SKIP] $class_name (missing file: $file_path)"
        return
    fi

    echo "[RUN] php artisan db:seed --class=\"$class_name\" --no-interaction"
    (cd "$ROOT_DIR" && php artisan db:seed --class="$class_name" --no-interaction)
}

print_header "Seeder JSON pattern check"
for entry in "${SEEDER_ENTRIES[@]}"; do
    class_name="${entry%%|*}"
    file_path="${entry#*|}"
    check_json_pattern "$class_name" "$file_path"
done

if (( ${#MISSING_FILES[@]} > 0 )); then
    print_header "Missing seeder files"
    for item in "${MISSING_FILES[@]}"; do
        echo "- $item"
    done
fi

if (( ${#NON_JSON_PATTERN[@]} > 0 )); then
    print_header "Seeders not using JSON pattern"
    for item in "${NON_JSON_PATTERN[@]}"; do
        echo "- $item"
    done

    if (( STRICT_JSON_PATTERN == 1 )); then
        echo
        echo "Strict JSON pattern mode enabled. Exiting with error."
        exit 1
    fi
fi

if (( VERIFY_ONLY == 1 )); then
    print_header "Verify only mode"
    echo "Pattern check complete. No seeders were executed."
    exit 0
fi

print_header "Running seeders"
for entry in "${SEEDER_ENTRIES[@]}"; do
    class_name="${entry%%|*}"
    file_path="${entry#*|}"
    run_seeders "$class_name" "$file_path"
done

print_header "Done"
echo "Database seeding finished."
