<?php

return [
    [
        'pattern' => '\/|\/home', // Home
        'controller' => 'Home',
        'method' => 'GET'
    ],
    [
        'pattern' => '\/|\/search', // Search
        'controller' => 'Search',
        'method' => 'GET'
    ],
    [
        'pattern' => '\/|\/phpinfo', // PhpInfo
        'controller' => 'PhpInfo',
        'method' => 'GET'
    ]
];
