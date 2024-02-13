<?php

return [
    // Public assets cache directory
    'path' => __DIR__ . '/../cache/public',

    // Public cache directory permissions (octal)
    // You need to prefix mode with a zero (0)
    // Use -1 to disable chmod
    'path_chmod' => 0750,

    // The public url base path
    'url_base_path' => 'http://static.projet-web.fr/cache/public/',

    // Internal cache settings
    //
    // The main cache directory
    // Use '' (empty string) to disable the internal cache
    'cache_path' => '',

    // Used as the subdirectory of the cache_path directory,
    // where cache items will be stored
    'cache_name' => '',

    // The lifetime (in seconds) for cache items
    // With a value 0 causing items to be stored indefinitely
    'cache_lifetime' => 0,

    // Enable JavaScript and CSS compression
    // 1 = on, 0 = off
    'minify' => 1
];
