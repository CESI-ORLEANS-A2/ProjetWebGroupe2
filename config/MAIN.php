<?php

return [
    'APP_NAME' => 'projet_web',

    // Path
    'TEMPLATE_PATH' => __DIR__ . '/../src/views/',
    'COMPONENTS_PATH' => __DIR__ . '/../src/components/',
    'PUBLIC_PATH' => __DIR__ . '/../public/..',
    'TWIG_CACHE_PATH' => __DIR__ . '/../cache/twig/',
    'PUBLIC_CACHE_PATH' => __DIR__ . '/../cache/public/',
    'STATIC_PATH' => __DIR__ . '/../src/static/',
    'CONTROLLER_PATH' => __DIR__ . '/../src/controllers/',

    'TWIG_ASSETS_EXTENSION' => include __DIR__ . '/TWIG_ASSETS_EXTENSION.php',
    'ROUTES' => include __DIR__ . '/ROUTES.php',
    'LOGGER' => include __DIR__ . '/LOGGER.php',

    'HEADERS' => [
        'Access-Control-Allow-Origin' => 'http://projet-web.fr',
        'Access-Control-Allow-Methods' => 'GET, POST',
        'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
        'Access-Control-Allow-Credentials' => 'true',
    ]
];
