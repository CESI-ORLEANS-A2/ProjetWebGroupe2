<?php

require_once('../src/modules/ControllerBase.php');

require_once ('../src/modules/Database/Connector.php');
require_once ('../src/modules/Database/Models/Account.php');
require_once ('../src/modules/Database/Models/Users.php');

class Controller extends ControllerBase {
    public function __construct($router) {
        parent::__construct($router);
    }

    public function run() {
        $content = '';

        if (!isset($_GET['id'])){
            return $this->router->render404();
        }

        $database = new Database('localhost', 'ProjetWeb', 'root', 'toor');

       
        $account = Users::getByID($_GET['id']);

        if (!$account || $account->get('Type') != 1) {

            return $this->router->render404();
            }
        
        
        return $this->render('Profil_Student.twig', array(
            'lastName'=> $account->get('Lastname'),
            'firstName'=> $account->get('Firstname'),
            'centre'=> $account->get('Classe'),
            'classe'=> $account->get('City')
        ));
    
    }
};


