<?php

require_once '../src/modules/Database/Model.php';

class Account extends Model {

    public function __construct(
        int $ID,
        DateTime $Creation_Date,
        string $Username,
        string $Password,
        string $Type,
        int $Class
    ) {
        parent::__construct(
            array(
                'ID' => $ID,
                'Creation_Date' => $Creation_Date,
                'Username' => $Username,
                'Password' => $Password,
                'Type' => $Type,
                'Class' => $Class
            ),
            array(
                'ID' => array('type' => 'int', 'readonly' => true),
                'Creation_Date' => array('type' => 'datetime', 'required' => true, 'readonly' => true),
                'Username' => array('type' => 'string', 'required' => true),
                'Password' => array('type' => 'string', 'required' => true),
                'Type' => array('type' => 'string', 'required' => true),
                'Class' => array('type' => 'int')
            )
        );
    }

    public function checkPassword(string $Password): bool {
        return $Password === $this->get('Password');
        // return password_verify($Password, $this->get('Password'));
    }

    static public function getByID(int $ID): Account|null {
        $data = Database::getInstance()->fetch(
            'SELECT 
                accounts.ID_Account, 
                accounts.Creation_Date,
                accounts.Username, 
                accounts.Password, 
                AccountTypes.Name AS Type, 
                ID_Class as Class
            FROM accounts 
            JOIN AccountTypes ON accounts.ID_Type = AccountTypes.ID_Type
            WHERE accounts.ID_Account = :ID',
            array(':ID' => $ID)
        );
        if (!$data) return null;
        return new Account(
            $data['ID_Account'],
            new DateTime($data['Creation_Date']),
            $data['Username'],
            $data['Password'],
            $data['Type'],
            $data['Class']
        );
    }

    static public function getByUsername(string $Username): Account|null {
        $data = Database::getInstance()->fetch(
            'SELECT 
                accounts.ID_Account, 
                accounts.Creation_Date,
                accounts.Username, 
                accounts.Password, 
                AccountTypes.Name AS Type, 
                ID_Class as Class
            FROM accounts
            JOIN AccountTypes ON accounts.ID_Type = AccountTypes.ID_Type
            WHERE accounts.Username = :Username',
            array(':Username' => $Username)
        );

        if (!$data) return null;
        return new Account(
            $data['ID_Account'],
            new DateTime($data['Creation_Date']),
            $data['Username'],
            $data['Password'],
            $data['Type'],
            $data['Class']
        );
    }

    static public function getAll(int $limit = 1000): array {
        $data = Database::getInstance()->fetchAll(
            'SELECT 
                accounts.ID_Account as ID, 
                accounts.Creation_Date,
                accounts.Username, 
                accounts.Password, 
                AccountTypes.Name AS Type, 
                ID_Class as Class
            FROM accounts
            JOIN AccountTypes ON accounts.ID_Type = AccountTypes.ID_Type
            LIMIT :limit',
            array(':limit' => $limit)
        );
        $accounts = [];
        foreach ($data as $account) {
            $accounts[] = new Account(
                $account['ID'],
                $account['Creation_Date'],
                $account['Username'],
                $account['Password'],
                $account['Type'],
                $account['Class']
            );
        }
        return $accounts;
    }


    static public function getByType(int $Type): array {
        $datas = Database::getInstance()->fetch(
            'SELECT 
                accounts.ID_Account, 
                accounts.Creation_Date,
                accounts.Username, 
                accounts.Password, 
                AccountTypes.Name AS Type, 
                ID_Class as Class
            FROM accounts 
            JOIN AccountTypes ON accounts.ID_Type = AccountTypes.ID_Type
            WHERE accounts.ID_Type = :Type',
            array(':Type' => $Type)
        );
        $accounts = [];
        if (is_array($datas) && isset($datas[0]) && is_array($datas[0])) {
            foreach ($datas as $data) {
                $accounts[] = new Account(
                    $data['ID_Account'],
                    new DateTime($data['Creation_Date']),
                    $data['Username'],
                    $data['Password'],
                    $data['Type'],
                    $data['Class']
                );
            }
        } else if (is_array($datas)) {
            $accounts[] = new Account(
                $datas['ID_Account'],
                new DateTime($datas['Creation_Date']),
                $datas['Username'],
                $datas['Password'],
                $datas['Type'],
                $datas['Class']
            );
        }

        return $accounts;
    }


    function save() {
        if (!$this->validate())
            throw new Exception("Invalid data");

        $dbh = Database::getInstance();

        $classID = $this->get('Class');
        $typeID = null;

        if ($this->isDefined('Class')) {
            $classID = $dbh->fetchColumn(
                'SELECT ID_Class 
                    FROM Classes
                    WHERE ID_Class = :ID',
                array(':ID' => $this->get('Class'))
            );
            if (!$classID)
                throw new Exception("Invalid class");
        }

        if ($this->isDefined('City')) {
            $cityID = $dbh->fetchColumn(
                'SELECT ID_City 
                    FROM Cities
                    WHERE ID_City = :ID',
                array(':ID' => $this->get('City'))
            );
            if (!$cityID)
                throw new Exception("Invalid city");
        }

        $typeID = $dbh->fetchColumn(
            'SELECT ID_Type 
                    FROM AccountTypes
                    WHERE Name = :Type',
            array(':Type' => $this->get('Type'))
        );
        if (!$typeID)
            throw new Exception("Invalid type");


        $query = '';

        if ($this->getID() == null) {
            $query =
                'INSERT INTO accounts (
                    Creation_Date, 
                    Username, 
                    Password, 
                    ID_Type' .
                ($classID ? ', ID_Class' : '') .
                ($cityID ? ', ID_City' : '') .
                ') VALUES (
                    :Creation_Date, 
                    :Username, 
                    :Password, 
                    :ID_Type,' .
                ($classID ? ' :ID_Class' : '') .
                ($cityID ? ' :ID_City' : '') .
                ')';
        } else {
            $query =
                'UPDATE accounts SET 
                Creation_Date = :Creation_Date, 
                Username = :Username, 
                Password = :Password, 
                ID_Type = :ID_Type' .
                ($classID ? ', ID_Class = :ID_Class' : '') .
                ($cityID ? ', ID_City = :ID_City' : '') .
                ' WHERE ID_Account = :ID';
        }

        $params = array(
            ':Creation_Date' => $this->get('Creation_Date'),
            ':Username' => $this->get('Username'),
            ':Password' => $this->get('Password'),
            ':ID_Type' => $typeID,
        );

        if ($classID)
            $params[':ID_Class'] = $classID;

        if ($cityID)
            $params[':ID_City'] = $cityID;

        if ($this->getID() != null)
            $params[':ID'] = $this->getID();

        $dbh->query(
            $query,
            $params
        );
    }

    public static function fromArray(array $data): Account {
        return new Account(
            $data['ID'],
            $data['Creation_Date'],
            $data['Username'],
            $data['Password'],
            $data['Type'],
            $data['Class']
        );
    }
}
