<?php

require_once('./modules/ControllerBase.php');

class Controller extends ControllerBase {
    public function __construct($router) {
        parent::__construct($router);
    }

    public function run() {
        header("HTTP/1.0 404 Not Found");
        return $this->render('errors/404.twig', array(
        ));
    }
};
