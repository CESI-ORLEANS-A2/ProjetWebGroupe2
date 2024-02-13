<?php

require_once('../src/modules/controller.php');

class Search extends Controller {
    public function __construct($router)
    {
        parent::__construct($router);
    }

    public function run() {
        return $this->render('search.twig', array(
            'config' => $this->config,
        ));
    }
};