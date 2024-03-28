<?php

require_once('../src/modules/ControllerBase.php');

class Controller extends ControllerBase {
    public function __construct($router) {
        parent::__construct($router);
    }

    public function run() {
        return $this->render('Profil_Student.twig', array(
            'config' => $this->config,
        ));
    }
};
