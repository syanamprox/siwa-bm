<?php

/**
 * AI Intelligence config — port dari d360_demo_businessplus.
 * HTTP API Anthropic-compatible (z.ai proxy) via INTEL_* env.
 */

return [
    'model' => env('INTEL_MODEL', 'haiku'),
    'timeout' => (int) env('INTEL_TIMEOUT', 120),
    'model_haiku'  => env('INTEL_MODEL_HAIKU', 'glm-4.7'),
    'model_sonnet' => env('INTEL_MODEL_SONNET', 'glm-5.2'),
    'model_opus'   => env('INTEL_MODEL_OPUS', 'glm-5.2'),
    'auth_token' => env('INTEL_AUTH_TOKEN', ''),
    'api_key'    => env('INTEL_API_KEY', ''),
    'base_url'   => env('INTEL_BASE_URL', 'https://api.z.ai/api/anthropic'),
];
