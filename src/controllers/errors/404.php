<?php

require_once('./modules/Controller.php');

class Error404 extends Controller {
    public function __construct($router) {
        parent::__construct($router);
    }

    public function run() {
        header("HTTP/1.0 404 Not Found");
        return $this->twig->render('errors/404.twig', array(
            'config' => $this->config,
        ));
    }
};
