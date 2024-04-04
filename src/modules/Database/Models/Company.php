<?php

require_once '../src/modules/Database/Model.php';

class Company extends Model {
    private array|null $activities = null;

    public function __construct($data) {
        parent::__construct(
            $data,
            array(
                'ID' => array('type' => 'int', 'readonly' => true),
                'Name' => array('type' => 'string', 'required' => true),
            )
        );
    }

    public function getActivities() {
        if ($this->activities === null) {
            $this->activities = Database::getInstance()->fetchAll(
                'SELECT 
                    Activities.ID_Activity,
                    Activities.Name
                FROM Activities 
                JOIN Operates_in ON Activities.ID_Activity = Operates_in.ID_Activity
                WHERE Operates_in.ID_Company = :ID',
                array(':ID' => $this->getID())
            );
        }
        return $this->activities;
    }

    public function removeActivity(int $ID_Activity): void {
        Database::getInstance()->query(
            'DELETE FROM Operates_in 
            WHERE ID_Company = :ID_Company 
            AND Activities.Name = :ID_Activity',
            array(':ID_Company' => $this->getID(), ':ID_Activity' => $ID_Activity)
        );
        $this->activities = null;
    }

    public function addActivity(int $ID_Activity): void {
        Database::getInstance()->query(
            'INSERT INTO Operates_in (ID_Company, ID_Activity)
            VALUES (:ID_Company, :ID_Activity)',
            array(':ID_Company' => $this->getID(), ':ID_Activity' => $ID_Activity)
        );
        $this->activities = null;
    }

    public function addNewActivity(string $Name): void {
        $dbh = Database::getInstance();

        $dbh->query(
            'INSERT INTO Activities (Name)
            VALUES (:Name)',
            array(':Name' => $Name)
        );
        $ID_Activity = $dbh->lastInsertID();

        $this->addActivity($ID_Activity);
    }

    public function getLocations() {
        $datas = Database::getInstance()->fetchAll(
            'SELECT 
                Locations.ID_Location as ID,
                Locations.Description,
                Locations.IsHeadquarters,
                Locations.ID_Company
            FROM Locations 
            JOIN Companies ON Locations.ID_Company = Companies.ID_Company
            WHERE Companies.ID_Company = :ID',
            array(':ID' => $this->getID())
        );

        return array_map(
            function ($data) {
                return new Location($data);
            },
            $datas
        );
    }

    public function getHeadquarters() {
        $data = Database::getInstance()->fetch(
            'SELECT 
                Locations.ID_Location as ID,
                Locations.Description,
                Locations.IsHeadquarters,
                Locations.ID_Company
            FROM Locations 
            JOIN Companies ON Locations.ID_Company = Companies.ID_Company
            WHERE Companies.ID_Company = :ID
                AND Locations.IsHeadquarters = 1',
            array(':ID' => $this->getID())
        );

        if (!$data) {
            return null;
        }

        return new Location(array(
            'ID' => $data['ID'],
            'Description' => $data['Description'],
            'IsHeadquarters' => !!$data['IsHeadquarters'],
            'ID_Company' => $data['ID_Company']
        ));
    }

    public static function getById(int $id): ?Company {
        $data = Database::getInstance()->fetch(
            'SELECT 
                ID_Company as ID,
                Name
            FROM Companies 
            WHERE ID_Company = :ID',
            array(':ID' => $id)
        );

        if (!$data) {
            return null;
        }

        return new Company($data);
    }
}
