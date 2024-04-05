<?php

require_once '../src/modules/Database/Model.php';

const schema = array(
    'ID' => array('type' => 'int', 'readonly' => true),
    'Firstname' => array('type' => 'string', 'required' => true),
    'Lastname' => array('type' => 'string', 'required' => true),
    'Account' => array('type' => 'int', )
);


class Users extends Model {
    public function __construct(
        int $ID,
        string $Firstname,
        string $Lastname,
        int $Account
    ) {
        parent::__construct(
            array(
                'ID' => $ID,
                'Firstname' => $Firstname,
                'Lastname' => $Lastname,
                'Account' => $Account
            ),
            schema
        );
    }
    static public function getByID(int $ID): Users {
        $data = Database::getInstance()->fetch(
            'SELECT 
                ID_User, 
                Firstname,
                Lastname,  
                ID_Account
            FROM users 
            WHERE ID_Account = :ID',
            array(':ID' => $ID)
        );
       if($data)
        return new Users(
            $data['ID_User'],
            $data['Firstname'],
            $data['Lastname'],
            $data['ID_Account']
        );
        
    }
    static public function DeleteByID(int $ID): Users {
        $data = Database::getInstance()->fetch(
            'DELETE 
            FROM users 
            WHERE ID_Account = :ID',
            array(':ID' => $ID)
        );
        return new Users(
            $data['ID_Users'],
            $data['Firstname'],
            $data['Lastname'],
            $data['Account']
        );
    }
    static public function AddUser(string $Firstname, string $Lastname, int $Account): Users {
        $data = Database::getInstance()->fetch(
            'INSERT INTO users (Firstname, Lastname, Account) VALUES (:Firstname, :Lastname, :Account)',
            array(':Firstname' => $Firstname, ':Lastname' => $Lastname, ':Account' => $Account)
        );
        return new Users(
            $data['ID_Users'],
            $data['Firstname'],
            $data['Lastname'],
            $data['Account']
        );
    }
    static public function UpdateByID(int $ID): Users {
        $data = Database::getInstance()->fetch(
            'UPDATE 
            FROM users 
            WHERE ID_Account = :ID',
            array(':ID' => $ID)
        );
        return new Users(
            $data['ID_Users'],
            $data['Firstname'],
            $data['Lastname'],
            $data['Account']
        );
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    // Call AddUser function with data received from client-side
    $result = Users::AddUser($data['Firstname'], $data['Lastname'], $data['Account']);
    // Return response to client-side
    echo json_encode($result);}
?>
