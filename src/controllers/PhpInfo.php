<?php

require_once('../src/modules/controller.php');

class PhpInfo extends Controller {
    public function __construct($router)
    {
        parent::__construct($router);
    }

    public function run() {
        return phpinfo();
    }
};