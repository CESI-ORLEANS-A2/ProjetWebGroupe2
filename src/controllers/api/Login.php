<?php

require_once ('../src/modules/ControllerBase.php');

require_once '../src/modules/Database/Connector.php';
require_once '../src/modules/Database/Models/Account.php';
require_once '../src/modules/Database/Models/Session.php';

class Controller extends ControllerBase
{
    private $regex = [
        'username' => '/^[a-zA-Z0-9]{3,30}$/',
        'password' => '/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,16}$/'
    ];

    public function __construct($router)
    {
        parent::__construct($router);
    }

    public function run()
    {
        try {
            // var_dump($_POST);
            // $username = $_POST['username'] ?? '';
            // $password = $_POST['password'] ?? '';
            $data = json_decode(file_get_contents('php://input'), true);
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';

            if (empty($username) || empty($password)) {
                return $this->reply('Username and password are required', 400);
            }

            if (!preg_match($this->regex['username'], $username)) {
                return $this->reply('Invalid username format', 400);
            }

            if (!preg_match($this->regex['password'], $password)) {
                return $this->reply('Invalid password format', 400);
            }

            new Database(
                $this->config->get('DB_HOST'),
                $this->config->get('DB_NAME'),
                $this->config->get('DB_USER'),
                $this->config->get('DB_PASS')
            );

            $user = Account::getByUsername($username);

            if (!$user) {
                return $this->reply('Invalid username or password', 400);
            }

            if (!$user->checkPassword($password)) {
                return $this->reply('Invalid username or password', 400);
            }

            $session = new Session([
                'UserAgent' => $_SERVER['HTTP_USER_AGENT'],
                'Geolocation' => $_SERVER['REMOTE_ADDR'],
                'ID_Account' => $user->getID()
            ]);

            $session->save();

            return $this->reply([
                'token' => $session->get('Token')
            ]);
        } catch (Exception $e) {
            return $this->reply($e->getMessage(), 500);
        }
    }
}