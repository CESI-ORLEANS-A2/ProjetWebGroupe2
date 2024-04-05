<?php

require_once '../src/modules/Database/Model.php';


class Users extends Model {
    public function __construct(
        int $ID,
        string $Firstname,
        string $Lastname,
        int $Account,
        int $Type,
        string $Classe,
        string $City
    ) {
        parent::__construct(
            array(
                'ID' => $ID,
                'Firstname' => $Firstname,
                'Lastname' => $Lastname,
                'Account' => $Account,
                'Type' => $Type,
                'Classe'=>$Classe,
                'City'=>$City
            ),
            array(
                'ID' => array('type' => 'int', 'readonly' => true),
                'Firstname' => array('type' => 'string', 'required' => true),
                'Lastname' => array('type' => 'string', 'required' => true),
                'Account' => array('type' => 'int' ),
                'Type' =>array('type' => 'int'),
                'Classe'=>array('type'=> 'string'),
                'City'=>array('type'=> 'string'))
        );
    }
    static public function getByID(int $ID): Users | null {
        $data = Database::getInstance()->fetch(
            'SELECT 
                ID_User, 
                Firstname,
                Lastname,  
                Users.ID_Account,
                accounts.ID_Type as Type,
                Classes.Name as class,
                Cities.Name as city
            FROM users 
            JOIN Accounts on Users.ID_Account = accounts.ID_Account
            JOIN Classes on Accounts.ID_Class = Classes.ID_Class
            JOIN AccountTypes ON accounts.ID_Type = AccountTypes.ID_Type
            JOIN Live_in ON accounts.ID_Account = Live_in.ID_Account
            JOIN Cities ON Live_in.ID_City = Cities.ID_City
            WHERE Users.ID_Account = :ID',
            array(':ID' => $ID)
        );
        if(!$data){
            return null;
        }
        return new Users(
            $data['ID_User'],
            $data['Firstname'],
            $data['Lastname'],
            $data['ID_Account'],
            $data['Type'],
            $data['class'],
            $data['city']
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
}
?>