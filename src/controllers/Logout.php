<?php

require_once ('../src/modules/ControllerBase.php');

class Controller extends ControllerBase {
    public function __construct($router) {
        parent::__construct($router);
    }

    public function run() {
        // if ($this->session) {
        //     $this->session->remove();
        //     $this->render('logout.twig');
        // } else {
        //     header("Location: /login");
        //     die();
        // }
        $this->render('logout.twig');
    }
};