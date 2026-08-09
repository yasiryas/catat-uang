<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Rekomendasi
    |--------------------------------------------------------------------------
    |
    | Konfigurasi provider AI untuk fitur rekomendasi keuangan.
    | Bisa pakai model gratis OpenRouter, cukup dengan satu API key
    | yang didaftarkan di https://openrouter.ai.
    |
    */

    'enabled' => (bool) env('AI_ENABLED', false),

    'openrouter' => [
        'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
        'api_key' => env('OPENROUTER_API_KEY'),
        'model' => env('OPENROUTER_MODEL', 'deepseek/deepseek-chat-v3-0324:free'),
        'timeout' => (int) env('OPENROUTER_TIMEOUT', 60),
    ],

];
