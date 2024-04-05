<?php

require_once '../src/modules/Database/Model.php';

class Address extends Model {
    public function __construct($data) {
        parent::__construct(
            $data,
            array(
                'ID' => array('type' => 'int', 'readonly' => true),
                'Number' => array('type' => 'string', 'required' => true),
                'Street' => array('type' => 'string', 'required' => true),
                'City' => array('type' => 'string', 'required' => true),
                'Country' => array('type' => 'string', 'required' => true),
                'Zip' => array('type' => 'string', 'required' => true),
            )
        );
    }

    public function __toString() {
        return $this->get('Number') 
            . ' ' . $this->get('Street') 
            . ', ' . $this->get('Zip') 
            . ' ' . $this->get('City') 
            . ', ' . $this->get('Country');
    }

    public function getCityID(): int {
        return Database::getInstance()->fetch(
            'SELECT ID_City FROM addresses WHERE ID_Address = :ID',
            array(':ID' => $this->getID())
        )['ID_City'];
    }

    public function getCountryID(): int {
        return Database::getInstance()->fetch(
            'SELECT ID_Country FROM cities WHERE ID_City = :ID',
            array(':ID' => $this->getCityID())
        )['ID_Country'];
    }

    public function save(): void {
        if ($this->getID() === null) {
            $this->setID(
                Database::getInstance()->query(
                    'INSERT INTO addresses (Number, Street, ID_City)
                    VALUES (:Number, :Street, :ID_City)',
                    array(
                        ':Number' => $this->get('Number'),
                        ':Street' => $this->get('Street'),
                        ':ID_City' => $this->getCityID()
                    )
                )
            );
        } else {
            Database::getInstance()->query(
                'UPDATE addresses SET Number = :Number, Street = :Street, ID_City = :ID_City
                WHERE ID_Address = :ID',
                array(
                    ':ID' => $this->getID(),
                    ':Number' => $this->get('Number'),
                    ':Street' => $this->get('Street'),
                    ':ID_City' => $this->getCityID()
                )
            );
        }
    }

    public function remove(): void {
        if ($this->isReferenced()) {
            throw new Exception('Address is referenced by another entity');
        }

        Database::getInstance()->query(
            'DELETE FROM addresses WHERE ID_Address = :ID',
            array(':ID' => $this->getID())
        );
    }

    public function isReferenced(): bool {
        return Database::getInstance()->fetchColumn(
            'SELECT COUNT(*)
            FROM Is_located
            WHERE ID_Address = :ID',
            array(':ID' => $this->getID())
        ) > 0;
    }

    static public function getAll(): array {
        $datas = Database::getInstance()->fetch(
            'SELECT 
                addresses.ID_Address AS ID,
                addresses.Number,
                addresses.Street,
                cities.Name AS City,
                cities.ZIP AS Zip,
                countries.Name AS Country
            FROM addresses
            JOIN cities ON cities.ID_City = addresses.ID_City 
            JOIN countries ON countries.ID_Country = cities.ID_Country'
        );
        return array_map(function ($data) {
            return new Address($data);
        }, $datas);
    }

    static public function getByID(int $ID): Address {
        $data = Database::getInstance()->fetch(
            'SELECT 
                addresses.ID_Address AS ID,
                addresses.Number,
                addresses.Street,
                cities.Name AS City,
                cities.ZIP AS Zip,
                countries.Name AS Country
            FROM addresses
            JOIN cities ON cities.ID_City = addresses.ID_City 
            JOIN countries ON countries.ID_Country = cities.ID_Country
            WHERE ID_Address = :ID',
            array(':ID' => $ID)
        );
        return new Address(array(
            'ID' => $data['ID'],
            'Number' => $data['Number'],
            'Street' => $data['Street'],
            'City' => $data['City'],
            'Zip' => (string)$data['Zip'],
            'Country' => $data['Country']
        
        ));
    }

    static public function getByCity(string $City): array {
        $datas = Database::getInstance()->fetch(
            'SELECT 
                addresses.ID_Address AS ID,
                addresses.Number,
                addresses.Street,
                cities.Name AS City,
                cities.ZIP AS Zip,
                countries.Name AS Country
            FROM addresses
            JOIN cities ON cities.ID_City = addresses.ID_City 
            JOIN countries ON countries.ID_Country = cities.ID_Country
            WHERE cities.Name = :City',
            array(':City' => $City)
        );
        return array_map(function ($data) {
            return new Address($data);
        }, $datas);
    }

    static public function getByCountry(string $Country): array {
        $datas = Database::getInstance()->fetch(
            'SELECT 
                addresses.ID_Address AS ID,
                addresses.Number,
                addresses.Street,
                cities.Name AS City,
                cities.ZIP AS Zip,
                countries.Name AS Country
            FROM addresses
            JOIN cities ON cities.ID_City = addresses.ID_City 
            JOIN countries ON countries.ID_Country = cities.ID_Country
            WHERE countries.Name = :Country',
            array(':Country' => $Country)
        );
        return array_map(function ($data) {
            return new Address($data);
        }, $datas);
    }

    static public function getByZip(string $Zip): array {
        $datas = Database::getInstance()->fetch(
            'SELECT 
                addresses.ID_Address AS ID,
                addresses.Number,
                addresses.Street,
                cities.Name AS City,
                cities.ZIP AS Zip,
                countries.Name AS Country
            FROM addresses
            JOIN cities ON cities.ID_City = addresses.ID_City 
            JOIN countries ON countries.ID_Country = cities.ID_Country
            WHERE cities.ZIP = :Zip',
            array(':Zip' => $Zip)
        );
        return array_map(function ($data) {
            return new Address($data);
        }, $datas);
    }

    static public function getAllCities(): array {
        $datas = Database::getInstance()->fetch(
            'SELECT DISTINCT cities.Name AS City
            FROM addresses
            JOIN cities ON cities.ID_City = addresses.ID_City 
            JOIN countries ON countries.ID_Country = cities.ID_Country'
        );
        return array_map(function ($data) {
            return $data['City'];
        }, $datas);
    }

    static public function searchCity(string $City): array {
        $datas = Database::getInstance()->fetch(
            'SELECT DISTINCT cities.Name AS City
            FROM addresses
            JOIN cities ON cities.ID_City = addresses.ID_City 
            JOIN countries ON countries.ID_Country = cities.ID_Country
            WHERE cities.Name LIKE :City',
            array(':City' => "%$City%")
        );
        return array_map(function ($data) {
            return $data['City'];
        }, $datas);
    }

    static public function getAllCountries(): array {
        $datas = Database::getInstance()->fetch(
            'SELECT DISTINCT countries.Name AS Country
            FROM addresses
            JOIN cities ON cities.ID_City = addresses.ID_City 
            JOIN countries ON countries.ID_Country = cities.ID_Country'
        );
        return array_map(function ($data) {
            return $data['Country'];
        }, $datas);
    }

    static public function searchCountry(string $Country): array {
        $datas = Database::getInstance()->fetch(
            'SELECT DISTINCT countries.Name AS Country
            FROM addresses
            JOIN cities ON cities.ID_City = addresses.ID_City 
            JOIN countries ON countries.ID_Country = cities.ID_Country
            WHERE countries.Name LIKE :Country',
            array(':Country' => "%$Country%")
        );
        return array_map(function ($data) {
            return $data['Country'];
        }, $datas);
    }
}
