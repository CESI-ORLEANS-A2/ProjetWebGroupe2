<?php

require_once '../src/modules/Database/Model.php';

class Classes extends Model {
    public function __construct(
        int $ID_Class,
        string $Name,
        int $ID_Address,
    ) {
        parent::__construct(
            array(
                'ID_Class' => $ID_Class,
                'Name' => $Name,
                'ID_Address' => $ID_Address,
            ),
            array(
                'ID_Class' => array('type' => 'int', 'readonly' => true),
                'Name' => array('type' => 'string', 'required' => true),
                'ID_Address' => array('type' => 'int', )
            )
        );
    }

    public function getID() {
        return $this->get('ID_Class');
    }

    static public function getByIDAccount(int $ID): Classes {
        $data = Database::getInstance()->fetch(
            'SELECT 
                ID_Class, 
                Name,
                ID_Address,
            FROM classes 
            WHERE ID_Class = :ID',
            array(':ID' => $ID)
        );
        return new Classes(
            $data['ID_Class'],
            $data['Name'],
            $data['ID_Address'],
        );
    }
    static public function DeleteByID(int $ID): Classes {
        $data = Database::getInstance()->fetch(
            'DELETE 
            FROM classes 
            WHERE ID_Class = :ID',
            array(':ID' => $ID)
        );
        return new Classes(
            $data['ID_Class'],
            $data['Name'],
            $data['ID_Address'],
        );
    }
    static public function AddClass(string $Name,int $ID_Address): Classes {
     $data = Database::getInstance()->fetch(
            'INSERT INTO classes (Name, ID_Address)
            VALUES (:Name, :ID_Address)',
            array(':Name' => $Name, ':ID_Address' => $ID_Address)
        );
        return new Classes(
            $data['ID_Class'],
            $data['Name'],
            $data['ID_Address'],
        );
    }
    static public function UpdateByID(int $ID, string $Name, int $ID_Address): Classes {
        $data = Database::getInstance()->fetch(
            'UPDATE classes 
            SET Name = :Name, ID_Address = :ID_Address
            WHERE ID_Class = :ID',
            array(':Name' => $Name, ':ID_Address' => $ID_Address, ':ID' => $ID)
        );
        return new Classes(
            $data['ID_Class'],
            $data['Name'],
            $data['ID_Address'],
        );
    }

    static public function getByPiloteID(int $ID): array {
        $data = Database::getInstance()->fetchAll(
            'SELECT 
                ID_Class, 
                Name,
                ID_Address
            FROM classes
            WHERE ID_Address = :ID',
            array(':ID' => $ID)
        );
        $classes = array();
        foreach ($data as $class) {
            $classes[] = new Classes(
                $class['ID_Class'],
                $class['Name'],
                $class['ID_Address'],
            );
        }
        return $classes;
    }
}
