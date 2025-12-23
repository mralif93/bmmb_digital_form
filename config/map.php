<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MAP SSO Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for SSO integration with MAP (Muamalat Application Platform)
    |
    */

    // Shared secret for SSO token verification
    // IMPORTANT: This must match the secret used in MAP's eform_redirect function
    // For local development, we use a known shared secret
    'sso_secret' => env('MAP_SSO_SECRET', 'map-eform-sso-shared-secret-2024'),

    // SSO token expiry in seconds
    'token_expiry' => env('MAP_SSO_TOKEN_EXPIRY', 60),

    // MAP redirect URL (used when sending users back to MAP)
    'redirect_url' => env('MAP_REDIRECT_URL', 'http://127.0.0.1:8000/redirect/eform/'),

    // MAP base URL
    'base_url' => env('MAP_BASE_URL', 'http://127.0.0.1:8000'),

    // MAP API verify endpoint (for production use)
    'verify_url' => env('MAP_VERIFY_URL', 'http://127.0.0.1:8000/api/eform/verify/'),

    // MAP login page URL (for logout redirect)
    'login_url' => env('MAP_LOGIN_URL', 'http://127.0.0.1:8000/pengurusan/login/'),

    // MAP logout URL (for federated logout - logging out of eForm also logs out of MAP)
    'logout_url' => env('MAP_LOGOUT_URL', 'http://127.0.0.1:8000/pengurusan/logout/'),

    // MAP database path for sync operations
    // Local development default: ../FinancingApp/FinancingApp_Backend/FinancingApp/db.sqlite3
    // Production: Set via MAP_DATABASE_PATH in .env
    'database_path' => env('MAP_DATABASE_PATH', base_path('../FinancingApp/FinancingApp_Backend/FinancingApp/db.sqlite3')),
];
