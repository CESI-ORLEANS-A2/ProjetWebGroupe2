<?php

require_once('../src/modules/ControllerBase.php');

class Controller extends ControllerBase {
    public function __construct($router) {
        parent::__construct($router);
    }

    public function run() {
        $content = '';

        $database = new Database('localhost', 'ProjetWeb', 'root', 'TCqpZ4iJriGTJraT');

        $account = Account::getByType(1);

        $account = Account::getByType(2);
        $i=0;
        $users=[];
        if (!$account) {
            foreach($account as $account) {
            $users[$i]= Users::getByID($account->get('ID'));
            $i++;
            }
        return $this->render('Profil_Student.twig', array(
            'config' => $this->config,
        ));
    
    }
}
};
