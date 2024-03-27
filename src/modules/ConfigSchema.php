<?php

return [
    'APP_NAME' => 'string',
    'TEMPLATE_PATH' => [
        'type' => 'directory',
        'checkValidity' => true,
        'autoCreate' => true,
    ],
    'COMPONENTS_PATH' => [
        'type' => 'directory',
        'checkValidity' => true,
        'autoCreate' => true,
    ],
    'PUBLIC_PATH' => [
        'type' => 'directory',
        'checkValidity' => true,
        'autoCreate' => true,
    ],
    'TWIG_CACHE_PATH' => [
        'type' => 'directory',
        'checkValidity' => true,
        'autoCreate' => true,
    ],
    'PUBLIC_CACHE_PATH' => [
        'type' => 'directory',
        'checkValidity' => true,
        'autoCreate' => true,
    ],
    'STATIC_PATH' => [
        'type' => 'directory',
        'checkValidity' => true,
        'autoCreate' => true,
    ],
    'CONTROLLER_PATH' => [
        'type' => 'directory',
        'checkValidity' => true,
        'autoCreate' => true,
    ],
    'TWIG_ASSETS_EXTENSION' => [
        'type' => 'array',
        'schema' => [
            'path' => 'string',
            'path_chmod' => 'integer',
            'url_base_path' => 'string',
            'cache_path' => 'string',
            'cache_name' => 'string',
            'cache_lifetime' => 'integer',
            'minify' => 'integer'
        ]
    ],
    'ROUTES' => [
        'type' => 'list',
        'schema' => [
            'pattern' => 'string',
            'controller' => 'string',
            'methods' => 'array',
            'params' => [
                'type' => 'array',
                'schema' => [
                    'type' => 'string',
                    'value' => 'string'
                ],
                'optionnal' => true
            ],
        ]
    ],
    'LOGGER' => [
        'type' => 'array',
        'schema' => [
            'SHOW_ERRORS_ON_SCREEN' => 'boolean',
            'ERROR_LOG_FILE' => [
                'type' => 'file',
                'checkValidity' => false,
                'checkDirectoryValidity' => true,
                'autoCreateDirectory' => true,
                'optionnal' => true,
            ],
            'LOG_PATH' => [
                'type' => 'directory',
                'checkValidity' => true,
                'autoCreate' => true,
            ],
            'LOG_FILE' => [
                'type' => 'file',
                'checkValidity' => false,
                'checkDirectoryValidity' => true,
                'autoCreateDirectory' => true,
            ],
            'MAX_LOG_SIZE' => 'integer',
        ]
    ]
];
