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
    ],
    [ // Professor_Profile
        'pattern' => '\/Professor_Profile|\/pilote',
        'controller' => '/professor_profile.php',
        'methods' => ['GET'],
    ],
    [ // User // FIXME le contrôleur n'est pas bon
        'pattern' => '\/user', 
        'controller' => '/../../modules/database/Models/User.php',
        'methods' => ['GET', 'POST'], // Adjust methods as needed
    ],
    [ // Login
        'pattern' => '\/login',
        'controller' => '/Login.php',
        'methods' => ['GET'],
    ],
    [ // Login
        'pattern' => '\/api\/login',
        'controller' => '/api/Login.php',
        'methods' => ['POST'],
    ],
    [ // Logout
        'pattern' => '\/logout',
        'controller' => '/Logout.php',
        'methods' => ['GET'],
    ],
    [ // Offer
        'pattern' => '\/offer',
        'controller' => '/Offer.php',
        'methods' => ['GET'],
    ],
    [ // API : WishList
        'pattern' => '\/api\/wishlist',
        'controller' => '/api/WishList.php',
        'methods' => ['POST'],
    ],
    [ // API : Apply
        'pattern' => '\/api\/apply',
        'controller' => '/api/Apply.php',
        'methods' => ['POST'],
    ],
    [ // API : Classes
        'pattern' => '\/api\/classes',
        'controller' => '/api/classes.php',
        'methods' => ['GET'],
    ]
];
