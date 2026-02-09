<?php

return [
    "frontend_path" => env("THEME_BUILD_FRONTEND_PATH", "/usr/local/var/www/ercee-frontend"),
    "workdir" => env("THEME_BUILD_WORKDIR", storage_path("app/theme-builds/work")),
    "node_bin" => env("THEME_BUILD_NODE_BIN", "node"),
    "build_cmd" => env("THEME_BUILD_CMD", "npm run build"),
    "zip_ttl" => (int) env("THEME_BUILD_ZIP_TTL", 3600),
    "lock_ttl" => (int) env("THEME_BUILD_LOCK_TTL", 1800),
    "build_timeout" => (int) env("THEME_BUILD_TIMEOUT", 1200),
    "webhook_secret" => env("THEME_BUILD_WEBHOOK_SECRET"),
    "callback_timeout" => (int) env("THEME_BUILD_CALLBACK_TIMEOUT", 10),
];
