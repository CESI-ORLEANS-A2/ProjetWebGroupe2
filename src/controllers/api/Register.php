<?php

require_once('../src/modules/ControllerBase.php');

require_once '../src/modules/Database/Connector.php';
require_once '../src/modules/Database/Models/Account.php';

class Controller extends ControllerBase {
    private $regex = [ // même contraintes que pour le login
        'username' => '/^[a-zA-Z0-9]{3,30}$/',
        'password' => '/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,16}$/'
    ];

    public function __construct($router) {
        parent::__construct($router);
    }

    public function run() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';
            $firstname = $data['firstname'] ?? '';
            $name = $data['lastname'] ??'';
            $school = $data['school'] ?? '';
            $class = $data['class'] ??'';

            if (empty($username) || empty($password) || empty($firstname) || empty($name) || empty($school) || empty($class)) {
                return $this->reply('Tout les champs sont requis', 400);
            }

            if (!preg_match($this->regex['username'], $username)) {
                return $this->reply('username non valide', 400);
            }

            if (!preg_match($this->regex['password'], $password)) {
                return $this->reply('Mot de passe non valide', 400);
            }

            new Database(
                $this->config->get('DB_HOST'),
                $this->config->get('DB_NAME'),
                $this->config->get('DB_USER'),
                $this->config->get('DB_PASS')
            );

            //inscription d'un nouvel utilisateur
            $user = Account::getByUsername($username);
            if ($user) { // si l'utilisateur existe déjà
                return $this->reply("Ce nom d'utilisateur est déjà utilisé.", 400);
            }

            return $this->reply('', 200);
        } catch (Exception $e) {
            return $this->reply($e->getMessage(), 500);
        }
    }
}
