<?php

return [
    [ // Home
        'pattern' => '\/|\/home', // Regular expression pattern, match / or /home
        'controller' => '/Home.php', // Relative to CONTROLLER_PATH
        'methods' => ['GET'] // Allowed methods
    ],
    [ // Search
        'pattern' => '\/search', 
        'controller' => '/Search.php',
        'methods' => ['GET']
    ],
    [ // PhpInfo
        'pattern' => '\/phpinfo', 
        'controller' => '/PhpInfo.php',
        'methods' => ['GET'],
        'environment' => 'development' // Only available in development
    ],
    [ // API : Search
        'pattern' => '\/api\/search',
        'controller' => '/api/Search.php',
        'methods' => ['GET']
    ]
];
