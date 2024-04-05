<?php

require_once ('../src/modules/ControllerBase.php');

class Controller extends ControllerBase
{
    public function __construct($router)
    {
        parent::__construct($router);
    }

    public function run()
    {
        if ($this->connectedAccount) {
            header("Location: /");
            die();
        }
        return $this->render('login.twig', array());
    }
}
;
