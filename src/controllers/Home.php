<?php

require_once('../src/modules/controller.php');

class Home extends Controller {
    public function __construct($router)
    {
        parent::__construct($router);
    }

    public function run() {
        return $this->render('home.twig', array(
            'config' => $this->config,
        ));
    }
};