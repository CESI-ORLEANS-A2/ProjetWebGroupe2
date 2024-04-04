<?php

require_once('../src/modules/ControllerBase.php');

require_once ('../src/modules/Database/Connector.php');
require_once ('../src/modules/Database/Models/Account.php');


class Controller extends ControllerBase {
    public function __construct($router) {
        parent::__construct($router);
    }

    public function run() {
        $content = '';

        $database = new Database($this->config->get('DB_HOST'), $this->config->get('DB_NAME'), $this->config->get('DB_USER'), $this->config->get('DB_PASS'));

        $account = Account::getByID(1);

        $content .= 'Account: ' . $account->get('Username');

        $account->set('Username', 'test');

        $content .= ' => ' . $account->get('Username');

        $account->save();

        return $this->render('home.twig', array(
            'content' => $content
        ));
    }
};
