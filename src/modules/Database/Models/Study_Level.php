<?php

require_once '../src/modules/Database/Model.php';
require_once '../src/modules/Database/ModelManager.php';

require_once '../src/modules/Database/Models/Offer.php';

class Study_Level extends Model {
    public function __construct($data) {
        parent::__construct($data, array(
            'ID' => array('type' => 'int', 'readonly' => true),
            'Name' => array('type' => 'string', 'required' => true)
        ));
    }

    public function __toString(): string {
        return $this->get('Name');
    }

    public function fromModel(Study_Level $offer) {
        $this->setID($offer->get('ID'));
        $this->set('Name', $offer->get('Name'));
    }

    public function save() {
        if (!$this->validate())
            throw new Error('Invalid data');

        $dbh = Database::getInstance();

        if (!$this->getID()) {
            $studyLevel = Study_Level::getByName($this->get('Name'));
            if ($studyLevel) {
                $this->fromModel($studyLevel);
                return;
            }
            $dbh->query(
                'INSERT INTO Study_Levels (Name) VALUES (:Name)',
                array(':Name' => $this->get('Name'))
            );

            $this->setID($dbh->lastInsertId());
        } else {
            $dbh->query(
                'UPDATE Study_Levels SET Name = :Name WHERE ID_Study_Level = :ID',
                array(':Name' => $this->get('Name'), ':ID' => $this->get('ID'))
            );
        }
    }

    public function remove() {
        if ($this->get('ID') == NULL)
            return;

        $dbh = Database::getInstance();

        if ($this->isReferenced())
            throw new Error('Referenced data');

        $dbh->query(
            'DELETE FROM Study_Levels WHERE ID_Study_Level = :ID',
            array(':ID' => $this->get('ID'))
        );
    }

    public function isReferenced(): bool {
        $dbh = Database::getInstance();

        $count = $dbh->fetchColumn(
            'SELECT count(ID_Offer) 
            FROM Is_dedicated_to 
            WHERE ID_Study_Level = :ID',
            array(':ID' => $this->get('ID'))
        );

        return $count > 0;
    }

    public static function getByID(int $ID): Study_Level {
        $data = Database::getInstance()->fetch(
            'SELECT Name FROM Study_Levels WHERE ID_Study_Level = :ID',
            array(':ID' => $ID)
        );
        return new Study_Level($data);
    }

    public static function getByName(string $Name): Study_Level | null {
        $data = Database::getInstance()->fetch(
            'SELECT 
                ID_Study_Level as ID, 
                Name
            FROM Study_Levels 
            WHERE Name = :Name',
            array(':Name' => $Name)
        );
        if ($data)
            return new Study_Level($data);
        return null;
    }

    public static function getAll(): array {
        $data = Database::getInstance()->fetchAll(
            'SELECT ID_Study_Level, Name FROM Study_Levels',
            array()
        );
        return array_map(function ($item) {
            return new Study_Level($item);
        }, $data);
    }
}

class Study_LevelManager extends ModelManager {
    public function contains(string | int $Name): bool {
        if (is_int($Name)) {
            foreach ($this->models as $model) {
                if ($model->get('ID_Study_Level') == $Name)
                    return true;
            }
        } else {
            foreach ($this->models as $model) {
                if ($model->get('Name') == $Name)
                    return true;
            }
        }
        return false;
    }

    public static function getStudyLevelsByOffer(int $offer): Study_LevelManager {
        $data = Database::getInstance()->fetchAll(
            'SELECT 
                ID_Study_Level as ID, 
                Name 
            FROM Study_Levels 
            WHERE ID_Study_Level IN (
                SELECT ID_Study_Level 
                FROM Is_dedicated_to 
                WHERE ID_Offer = :ID
            )',
            array(':ID' => $offer)
        );
        return new static(array_map(function ($item) {
            return new Study_Level($item);
        }, $data));
    }
}
