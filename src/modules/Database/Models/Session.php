<?php

require_once '../src/modules/Database/Model.php';

// CREATE TABLE Sessions(
// 	ID_Session INT NOT NULL AUTO_INCREMENT,
// 	Token VARCHAR (255) NOT NULL,
// 	Creation_Date DATETIME NOT NULL,
// 	Update_Date DATETIME NOT NULL,
//     UserAgent VARCHAR (255) NOT NULL,
//     Geolocation VARCHAR (50) NOT NULL,
//     ID_Account INT NOT NULL,
// 	PRIMARY KEY (ID_Session),
//     FOREIGN KEY(ID_Account) REFERENCES Accounts(ID_Account)
// );

class Session extends Model
{
    public function __construct($data)
    {
        parent::__construct(
            array(
                'ID' => $data['ID'] ?? null,
                'Token' => $data['Token'] ?? bin2hex(random_bytes(32)),
                'Creation_Date' => $data['Creation_Date'] ?? new Datetime(),
                'Update_Date' => $data['Update_Date'] ?? new Datetime(),
                'UserAgent' => $data['UserAgent'] ?? '',
                'Geolocation' => $data['Geolocation'] ?? '',
                'ID_Account' => $data['ID_Account']
            ),
            array(
                'ID' => array('type' => 'int', 'readonly' => true),
                'Token' => array('type' => 'string', 'required' => true),
                'Creation_Date' => array('type' => 'datetime', 'readonly' => true),
                'Update_Date' => array('type' => 'datetime', 'readonly' => true),
                'UserAgent' => array('type' => 'string', 'required' => true),
                'Geolocation' => array('type' => 'string', 'required' => true),
                'ID_Account' => array('type' => 'int', 'required' => true)
            )
        );
    }

    public function __toString(): string
    {
        return $this->get('Token');
    }

    public function fromModel(Session $session)
    {
        $this->setID($session->get('ID'));
        $this->set('Token', $session->get('Token'));
        $this->set('Creation_Date', $session->get('Creation_Date'));
        $this->set('Update_Date', $session->get('Update_Date'));
        $this->set('UserAgent', $session->get('UserAgent'));
        $this->set('Geolocation', $session->get('Geolocation'));
        $this->set('ID_Account', $session->get('ID_Account'));
    }

    public function save()
    {
        if (!$this->validate())
            throw new Error('Invalid data');

        $dbh = Database::getInstance();

        if (!$this->getID()) {
            $session = Session::getByToken($this->get('Token'));
            if ($session) {
                $this->fromModel($session);
                return;
            }
            $dbh->query(
                'INSERT INTO Sessions (
                    Token, 
                    Creation_Date, 
                    Update_Date, 
                    UserAgent, 
                    Geolocation, 
                    ID_Account
                ) VALUES (
                    :Token, 
                    :Creation_Date, 
                    :Update_Date, 
                    :UserAgent, 
                    :Geolocation, 
                    :ID_Account
                )',
                array(
                    ':Token' => $this->get('Token'),
                    ':Creation_Date' => $this->get('Creation_Date'),
                    ':Update_Date' => $this->get('Update_Date'),
                    ':UserAgent' => $this->get('UserAgent'),
                    ':Geolocation' => $this->get('Geolocation'),
                    ':ID_Account' => $this->get('ID_Account')
                )
            );

            $this->setID($dbh->lastInsertId());
        } else {
            $dbh->query(
                'UPDATE Sessions 
                SET 
                    Token = :Token, 
                    Creation_Date = :Creation_Date,
                    Update_Date = :Update_Date,
                    UserAgent = :UserAgent, 
                    Geolocation = :Geolocation, 
                    ID_Account = :ID_Account 
                WHERE ID_Session = :ID',
                array(
                    ':Token' => $this->get('Token'),
                    ':Creation_Date' => $this->get('Creation_Date'),
                    ':Update_Date' => $this->get('Update_Date'),
                    ':UserAgent' => $this->get('UserAgent'),
                    ':Geolocation' => $this->get('Geolocation'),
                    ':ID_Account' => $this->get('ID_Account'),
                    ':ID' => $this->get('ID')
                )
            );
        }
    }

    public function remove()
    {
        if ($this->get('ID') == NULL)
            return;

        $dbh = Database::getInstance();

        $dbh->query(
            'DELETE FROM Sessions WHERE ID_Session = :ID',
            array(':ID' => $this->get('ID'))
        );
    }

    static public function getByToken(string $Token): ?Session
    {
        $data = Database::getInstance()->fetch(
            'SELECT ID_Session, Token, Creation_Date, Update_Date, UserAgent, Geolocation, ID_Account 
            FROM Sessions 
            WHERE Token = :Token',
            array(':Token' => $Token)
        );

        if (!$data)
            return null;

        return new Session(
            array(
                'ID' => $data['ID_Session'],
                'Token' => $data['Token'],
                'Creation_Date' => new Datetime($data['Creation_Date']),
                'Update_Date' => new Datetime($data['Update_Date']),
                'UserAgent' => $data['UserAgent'],
                'Geolocation' => $data['Geolocation'],
                'ID_Account' => $data['ID_Account']
            )
        );
    }

    static public function getByID(int $ID): ?Session
    {
        $data = Database::getInstance()->fetch(
            'SELECT Token, Creation_Date, Update_Date, UserAgent, Geolocation, ID_Account 
            FROM Sessions 
            WHERE ID_Session = :ID',
            array(':ID' => $ID)
        );

        if (!$data)
            return null;

        return new Session(
            array(
                'ID' => $ID,
                'Token' => $data['Token'],
                'Creation_Date' => new Datetime($data['Creation_Date']),
                'Update_Date' => new Datetime($data['Update_Date']),
                'UserAgent' => $data['UserAgent'],
                'Geolocation' => $data['Geolocation'],
                'ID_Account' => $data['ID_Account']
            )
        );
    }

    static public function getAll(): array
    {
        $data = Database::getInstance()->fetchAll(
            'SELECT ID_Session, Token, Creation_Date, Update_Date, UserAgent, Geolocation, ID_Account 
            FROM Sessions',
            array()
        );

        $sessions = array();
        foreach ($data as $session) {
            $sessions[] = new Session(
                array(
                    'ID' => $session['ID_Session'],
                    'Token' => $session['Token'],
                    'Creation_Date' => new Datetime($session['Creation_Date']),
                    'Update_Date' => new Datetime($session['Update_Date']),
                    'UserAgent' => $session['UserAgent'],
                    'Geolocation' => $session['Geolocation'],
                    'ID_Account' => $session['ID_Account']
                )
            );
        }

        return $sessions;
    }

    static public function getByAccount(int $ID_Account): array
    {
        $data = Database::getInstance()->fetchAll(
            'SELECT ID_Session, Token, Creation_Date, Update_Date, UserAgent, Geolocation, ID_Account 
            FROM Sessions 
            WHERE ID_Account = :ID_Account',
            array(':ID_Account' => $ID_Account)
        );

        $sessions = array();
        foreach ($data as $session) {
            $sessions[] = new Session(
                array(
                    'ID' => $session['ID_Session'],
                    'Token' => $session['Token'],
                    'Creation_Date' => new Datetime($session['Creation_Date']),
                    'Update_Date' => new Datetime($session['Update_Date']),
                    'UserAgent' => $session['UserAgent'],
                    'Geolocation' => $session['Geolocation'],
                    'ID_Account' => $session['ID_Account']
                )
            );
        }

        return $sessions;
    }

    static public function deleteByAccount(int $ID_Account): void
    {
        $dbh = Database::getInstance();

        $dbh->query(
            'DELETE FROM Sessions WHERE ID_Account = :ID_Account',
            array(':ID_Account' => $ID_Account)
        );
    }

    static public function deleteByToken(string $Token): void
    {
        $dbh = Database::getInstance();

        $dbh->query(
            'DELETE FROM Sessions WHERE Token = :Token',
            array(':Token' => $Token)
        );
    }

    static public function deleteExpired(): void
    {
        $dbh = Database::getInstance();

        $dbh->query(
            'DELETE FROM Sessions WHERE Update_Date < DATE_SUB(NOW(), INTERVAL 1 DAY)',
            array()
        );
    }

    static public function deleteAll(): void
    {
        $dbh = Database::getInstance();

        $dbh->query(
            'DELETE FROM Sessions',
            array()
        );
    }

    static public function count(): int
    {
        return Database::getInstance()->fetchColumn(
            'SELECT COUNT(*) 
            FROM Sessions',
            array()
        );
    }
}
