<?php

require_once('../src/modules/ControllerBase.php');
require_once '../src/modules/Database/Models/Account.php';
require_once '../src/modules/Database/Models/User.php';
require_once '../src/modules/Database/Connector.php';
class Controller extends ControllerBase {
    public function __construct($router) {
        parent::__construct($router);
    }

    public function run() {
        $content = '';

        $database = new Database('localhost', 'ProjetWeb', 'root', '');

       
         return $this->render('Company_Student.twig');

    }
};
