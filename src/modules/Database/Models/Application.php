<?php

require_once '../src/modules/Database/Model.php';

class Application extends Model {
    public function __construct($data) {
        parent::__construct(
            $data,
            array(
                'ID' => array('type' => 'int', 'readonly' => true),
                'ID_Offer' => array('type' => 'int', 'required' => true),
                'ID_Account' => array('type' => 'int', 'required' => true),
                'ID_CV' => array('type' => 'int', 'required' => true),
                'ID_MotivationLetter' => array('type' => 'int', 'required' => true),
                'Creation_Date' => array('type' => 'string', 'required' => true),
            )
        );
    }

    public function save() {
        if ($this->getID() === null) {
            $this->setID(
                Database::getInstance()->query(
                    'INSERT INTO applications (ID_Offer, ID_Account, ID_CV, ID_MotivationLetter, Creation_Date)
                    VALUES (:ID_Offer, :ID_Account, :ID_CV, :ID_MotivationLetter, :Creation_Date)',
                    array(
                        ':ID_Offer' => $this->get('ID_Offer'),
                        ':ID_Account' => $this->get('ID_Account'),
                        ':ID_CV' => $this->get('ID_CV'),
                        ':ID_MotivationLetter' => $this->get('ID_MotivationLetter'),
                        ':Creation_Date' => $this->get('Creation_Date')
                    )
                )
            );
        } else {
            Database::getInstance()->query(
                'UPDATE applications SET ID_Offer = :ID_Offer, ID_Account = :ID_Account, ID_CV = :ID_CV, ID_MotivationLetter = :ID_MotivationLetter, Creation_Date = :Creation_Date
                WHERE ID_Application = :ID',
                array(
                    ':ID' => $this->getID(),
                    ':ID_Offer' => $this->get('ID_Offer'),
                    ':ID_Account' => $this->get('ID_Account'),
                    ':ID_CV' => $this->get('ID_CV'),
                    ':ID_MotivationLetter' => $this->get('ID_MotivationLetter'),
                    ':Creation_Date' => $this->get('Creation_Date')
                )
            );
        }
    }

    public function remove(): void {
        Database::getInstance()->query(
            'DELETE FROM applications WHERE ID_Application = :ID',
            array(':ID' => $this->getID())
        );
    }

    public static function getByID(int $id): ?Application {
        $data = Database::getInstance()->fetch(
            'SELECT * FROM applications WHERE ID_Application = :ID',
            array(':ID' => $id)
        );

        if (!$data) {
            return null;
        }

        return new Application($data);
    }

    public static function getByOfferID(int $id): array {
        $data = Database::getInstance()->fetchAll(
            'SELECT * FROM applications WHERE ID_Offer = :ID',
            array(':ID' => $id)
        );

        $applications = array();
        foreach ($data as $application) {
            $applications[] = new Application($application);
        }

        return $applications;
    }

    public static function getByAccountID(int $id): array {
        $data = Database::getInstance()->fetchAll(
            'SELECT * FROM applications WHERE ID_Account = :ID',
            array(':ID' => $id)
        );

        $applications = array();
        foreach ($data as $application) {
            $applications[] = new Application($application);
        }

        return $applications;
    }

    public static function getCountByOffer(int $id): int {
        return Database::getInstance()->fetchColumn(
            'SELECT COUNT(*) FROM applications WHERE ID_Offer = :ID',
            array(':ID' => $id)
        );
    }
    
}