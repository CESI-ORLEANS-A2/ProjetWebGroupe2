<?php

require_once '../src/modules/Database/Model.php';

require_once '../src/modules/Database/Models/Address.php';

class Location extends Model {
    public function __construct($data) {
        parent::__construct(
            $data,
            array(
                'ID' => array('type' => 'int', 'readonly' => true),
                'Description' => array('type' => 'string', 'required' => true),
                'ID_Company' => array('type' => 'int', 'required' => true),
                'IsHeadquarters' => array('type' => 'bool', 'required' => true),
            )
        );
    }

    public function save() {
        if ($this->getID() === null) {
            $this->setID(
                Database::getInstance()->query(
                    'INSERT INTO locations (Description, ID_Company, IsHeadquarters)
                    VALUES (:Description, :ID_Company, :IsHeadquarters)',
                    array(
                        ':Description' => $this->get('Description'),
                        ':ID_Company' => $this->get('ID_Company'),
                        ':IsHeadquarters' => $this->get('IsHeadquarters')
                    )
                )
            );
        } else {
            Database::getInstance()->query(
                'UPDATE locations SET Description = :Description, ID_Company = :ID_Company, IsHeadquarters = :IsHeadquarters
                WHERE ID_Location = :ID',
                array(
                    ':ID' => $this->getID(),
                    ':Description' => $this->get('Description'),
                    ':ID_Company' => $this->get('ID_Company'),
                    ':IsHeadquarters' => $this->get('IsHeadquarters')
                )
            );
        }
    }

    public function remove(): void {
        if ($this->isReferenced()) {
            throw new Exception('Location is referenced by another entity');
        }
        
        Database::getInstance()->query(
            'DELETE FROM locations WHERE ID_Location = :ID',
            array(':ID' => $this->getID())
        );
    }

    public function isReferenced(): bool {
        return Database::getInstance()->fetchColumn(
            'SELECT COUNT(*)
            FROM Is_located
            WHERE ID_Location = :ID',
            array(':ID' => $this->getID())
        ) > 0;
    }

    public function getAddresses(): array {
        $datas = Database::getInstance()->fetchAll(
            'SELECT ID_Address as ID
            FROM Is_located
            WHERE ID_Location = :ID',
            array(':ID' => $this->getID())
        );
        return array_map(function ($data) {
            return Address::getByID($data['ID']);
        }, $datas);
    }

    public function getAddress(): Address {
        $data = Database::getInstance()->fetch(
            'SELECT ID_Address as ID
            FROM Is_located
            WHERE ID_Location = :ID',
            array(':ID' => $this->getID())
        );
        return Address::getByID($data['ID']);
    }

    public function addAddress(Address $address): void {
        Database::getInstance()->query(
            'INSERT INTO Is_located (ID_Location, ID_Address)
            VALUES (:ID_Location, :ID_Address)',
            array(
                ':ID_Location' => $this->getID(),
                ':ID_Address' => $address->getID()
            )
        );
    }

    public static function getByID(int $ID): Location {
        $data = Database::getInstance()->fetch(
            'SELECT 
                ID_Location, 
                Description,
                ID_Company,
                IsHeadquarters
            FROM locations 
            WHERE ID_Location = :ID',
            array(':ID' => $ID)
        );
        return new Location(
            array(
                'ID' => $data['ID_Location'],
                'Description' => $data['Description'],
                'ID_Company' => $data['ID_Company'],
                'IsHeadquarters' => $data['IsHeadquarters'] === 1
            )
        );
    }

    public static function getByIDCompany(int $ID): array {
        $datas = Database::getInstance()->fetchAll(
            'SELECT ID_Location as ID
            FROM locations
            WHERE ID_Company = :ID',
            array(':ID' => $ID)
        );
        return array_map(function ($data) {
            return Location::getByID($data['ID']);
        }, $datas);
    }
}
