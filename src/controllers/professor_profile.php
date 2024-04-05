<?php

require_once('../src/modules/ControllerBase.php');

require_once('../src/modules/Database/Connector.php');
require_once('../src/modules/Database/Models/User.php');
require_once('../src/modules/Database/Models/Account.php');

class Controller extends ControllerBase {
    public function __construct($router) {
        parent::__construct($router);
    }

    public function run() {
        new Database(
            $this->config->get('DB_HOST'),
            $this->config->get('DB_NAME'),
            $this->config->get('DB_USER'),
            $this->config->get('DB_PASS'),
        );

        if (!isset($_REQUEST['id'])) {
            return $this->router->render404();
        }

        $pilote = Users::getByAccountID($_REQUEST['id']);

        if (!$pilote) {
            return $this->router->render404();
        }

        // $account = Account::getByID($_REQUEST['id']);

        return $this->render('professor_profile.twig', array(
            'Firstname' => $pilote->get('Firstname'),
            'Lastname' => $pilote->get('Lastname'),
        ));
    }
};
