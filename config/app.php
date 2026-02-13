<?php
// ==================== APPLICATION CONFIGURATION ====================
// General app settings - TIDAK mengubah yang sudah ada

return [
    'app_name' => 'Wiguna Perkasa Network',
    'app_short_name' => 'WPN',
    'app_version' => '2.0.0',
    'app_description' => 'Internet Service Provider Management System',

    // Base URL
    'base_url' => '/wigunaperkasa.id',

    // Asset paths
    'assets_url' => '/wigunaperkasa.id/public/assets',

    // Upload paths
    'upload_path' => __DIR__ . '/../storage/uploads',
    'upload_url' => '/wigunaperkasa.id/storage/uploads',

    // Session settings (use existing)
    'session_name' => 'WIGUNASESS',

    // Timezone
    'timezone' => 'Asia/Jakarta',

    // Pagination
    'per_page' => 10,

    // Date format
    'date_format' => 'd/m/Y',
    'datetime_format' => 'd/m/Y H:i:s',

    // Theme
    'theme' => 'blue', // AdminLTE blue theme
];
