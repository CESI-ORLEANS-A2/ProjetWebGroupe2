<?php

require_once '../src/modules/Database/Model.php';

require_once '../src/modules/Database/Models/Study_Level.php';
require_once '../src/modules/Database/Models/Skill.php';
require_once '../src/modules/Database/Models/Location.php';
require_once '../src/modules/Database/Models/Company.php';

class Offer extends Model {
    private Study_LevelManager | null $studyLevels = null;
    private SkillManager | null $skills = null;

    public function __construct($data) {
        parent::__construct($data, array(
            'ID' => array('type' => 'int', 'readonly' => true),
            'Title' => array('type' => 'string', 'require' => true),
            'Pay' => array('type' => 'string', 'required' => true),
            'Start_Date' => array('type' => 'datetime', 'required' => true),
            'End_Date' => array('type' => 'datetime', 'required' => true),
            'Places' => array('type' => 'int', 'required' => true),
            'Description' => array('type' => 'string', 'required' => true),
            'Creation_Date' => array('type' => 'datetime', 'readonly' => true),
            'Duration' => array('type' => 'int', 'required' => true),
            'ID_Location' => array('type' => 'int', 'required' => true),
            'ID_Company' => array('type' => 'int', 'required' => true),
        ));
    }

    public function save() {
        if (!$this->validate())
            throw new Error('Invalid data');

        $dbh = Database::getInstance();

        $data =
            array(
                'Pay' => $this->get('Pay'),
                'Start_Date' => $this->get('Start_Date'),
                'End_Date' => $this->get('End_Date'),
                'Places' => $this->get('Places'),
                'Description' => $this->get('Description'),
                'ID_Location' => $this->get('ID_Location'),
                'ID_Company' => $this->get('ID_Company')
            );


        if ($this->isUpdated('Duration')) {
            $duration = $dbh->fetch(
                'SELECT ID_Duration FROM Durations WHERE Duration = :Duration',
                array(':Duration' => $this->get('Duration'))
            );
            if ($duration == null) {
                $dbh->insert(
                    'INSERT INTO Durations (Duration) VALUES (:Duration)',
                    array(':Duration' => $this->get('Duration'))
                );
                $duration = $dbh->lastInsertId();
            }

            $data['ID_Duration'] = $duration['ID_Duration'];
        }

        if ($this->studyLevels) {
            foreach ($this->studyLevels as $studyLevel) {
                if (!$studyLevel->getID()) {
                    $studyLevel->save();
                    $dbh->insert(
                        'Is_dedicated_to',
                        array(
                            'ID_Offer' => $this->getID(),
                            'ID_Study_Level' => $studyLevel->getID()
                        )
                    );
                } else
                    $studyLevel->save();
            }
        }

        if ($this->getID() == NULL) {
            $dbh->insert(
                'INSERT INTO offers (
                    Pay, 
                    Start_Date, 
                    End_Date, 
                    Places, 
                    Description, 
                    ID_Duration, 
                    ID_Location, 
                    ID_Company
                ) VALUES (
                    :Pay, 
                    :Start_Date, 
                    :End_Date, 
                    :Places, 
                    :Description, 
                    :ID_Duration, 
                    :ID_Location, 
                    :ID_Company
                )',
                $data
            );
            $this->set('ID', $dbh->lastInsertId());
        } else {
            $dbh->update(
                'offers',
                $data,
                'ID_Offer = :ID',
                array(':ID' => $this->get('ID'))
            );
        }
    }

    public function getStudyLevels() {
        if (isset($this->studyLevels) && $this->studyLevels != null)
            return $this->studyLevels;

        if (!$this->get('ID'))
            return $this->studyLevels = new Study_LevelManager();

        return $this->studyLevels = Study_LevelManager::getStudyLevelsByOffer($this->get('ID'));
    }

    public function addStudyLevel(string ...$studyLevels) {
        if (!$this->studyLevels)
            $this->getStudyLevels();

        foreach ($studyLevels as $studyLevel) {
            $this->studyLevels->append(
                new Study_Level(array('Name' => $studyLevel))
            );
        }
    }

    public function removeStudyLevel(string ...$studyLevels) {
        if (!$this->studyLevels)
            $this->getStudyLevels();

        foreach ($studyLevels as $studyLevel) {
            $i = $this->studyLevels->find('Name', $studyLevel);
            if ($i != null)
                $this->studyLevels->remove($i);
        }
    }

    public function clearStudyLevels() {
        if (!$this->studyLevels)
            $this->getStudyLevels();

        $this->studyLevels->clear();
    }

    public function getSkills() {
        if ($this->skills)
            return $this->skills;

        return $this->skills = SkillManager::getByOffer($this->get('ID'));
    }

    public function addSkill(string ...$skill) {
        if (!$this->skills)
            $this->skills = new SkillManager();

        foreach ($skill as $s) {
            $this->skills->append(new Skill(array('Name' => $s)));
        }
    }

    public function removeSkill(string ...$skill) {
        if (!$this->skills)
            $this->skills = new SkillManager();

        foreach ($skill as $s) {
            $i = $this->skills->find('Name', $s);
            if ($i != null)
                $this->skills->remove($i);
        }
    }

    public function getLocation() {
        return Location::getById($this->get('ID_Location'));
    }

    public function getCompany() {
        return Company::getById($this->get('ID_Company'));
    }

    public function getAge($format = '%a jours') {
        return $this->get('Start_Date')->diff(new DateTime())->format($format);
    }

    public function getDuration($format = '%a jours') {
        return $this->get('Start_Date')->diff($this->get('End_Date'))->format($format);
    }

    public function getInterestedCount() {
        return Database::getInstance()->fetchColumn(
            'SELECT COUNT(*) 
            FROM Is_of_interest_to
            WHERE ID_Offer = :ID',
            array(':ID' => $this->getID())
        );
    }

    public function addInterest($accountId) {
        Database::getInstance()->insert(
            'Is_of_interest_to',
            array(
                'ID_Account' => $accountId,
                'ID_Offer' => $this->getID()
            )
        );
    }

    public function removeInterest($accountId) {
        Database::getInstance()->delete(
            'Is_of_interest_to',
            'ID_Account = :ID_Account AND ID_Offer = :ID_Offer',
            array(
                ':ID_Account' => $accountId,
                ':ID_Offer' => $this->getID()
            )
        );
    }

    public function isInterested($accountId) {
        return Database::getInstance()->fetchColumn(
            'SELECT COUNT(*) 
            FROM Is_of_interest_to
            WHERE ID_Account = :ID_Account AND ID_Offer = :ID_Offer',
            array(
                ':ID_Account' => $accountId,
                ':ID_Offer' => $this->getID()
            )
        ) > 0;
    }

    public function addApplication($accountId) {
        $dbh = Database::getInstance();

        $dbh->insert(
            'MotivationLetters',
            array(
                'FileName' => ''
            )
        );
        $MotivationLetterID = $dbh->lastInsertId();

        $dbh->insert(
            'CVs',
            array(
                'FileName' => ''
            )
        );
        $CV_ID = $dbh->lastInsertId();

        $dbh->insert(
            'Applications',
            array(
                'ID_Account' => $accountId,
                'ID_Offer' => $this->getID(),
                'Creation_Date' => date('Y-m-d H:i:s'),
                'ID_MotivationLetter' => $MotivationLetterID,
                'ID_CV' => $CV_ID
            )
        );
    }

    public function removeApplication($accountId) {
        $dbh = Database::getInstance();

        ['ID_MotivationLetter' => $MotivationLetterID, 'ID_CV' => $CV_ID] = $dbh->fetch(
            'SELECT ID_MotivationLetter, ID_CV
            FROM Applications
            WHERE ID_Account = :ID_Account AND ID_Offer = :ID_Offer',
            array(
                ':ID_Account' => $accountId,
                ':ID_Offer' => $this->getID()
            )
        );

        $dbh->delete(
            'Applications',
            'ID_Account = :ID_Account AND ID_Offer = :ID_Offer',
            array(
                ':ID_Account' => $accountId,
                ':ID_Offer' => $this->getID()
            )
        );

        $dbh->delete(
            'MotivationLetters',
            'ID_MotivationLetter = :ID_MotivationLetter',
            array(
                ':ID_MotivationLetter' => $MotivationLetterID
            )
        );

        $dbh->delete(
            'CVs',
            'ID_CV = :ID_CV',
            array(
                ':ID_CV' => $CV_ID
            )
        );
    }

    public function isApplied($accountId) {
        return Database::getInstance()->fetchColumn(
            'SELECT COUNT(*) 
            FROM Applications
            WHERE ID_Account = :ID_Account AND ID_Offer = :ID_Offer',
            array(
                ':ID_Account' => $accountId,
                ':ID_Offer' => $this->getID()
            )
        ) > 0;
    }

    public static function getById($id) {
        $data = Database::getInstance()->fetch(
            'SELECT
                ID_Offer as ID,
                Title,
                Pay,
                Start_Date,
                End_Date,
                Places,
                Description,
                Creation_Date,
                Durations.Duration as Duration,
                ID_Location,
                ID_Company
            FROM offers 
            JOIN Durations ON offers.ID_Duration = Durations.ID_Duration
            WHERE ID_Offer = :ID',
            array(':ID' => $id)
        );
        return Offer::fromData($data);
    }

    public static function getAll(int $limit): array {
        $data = Database::getInstance()->fetchAll(
            'SELECT
                ID_Offer as ID,
                Title,
                Pay,
                Start_Date,
                End_Date,
                Places,
                Description,
                Creation_Date,
                Durations.Duration as Duration,
                ID_Location,
                ID_Company
            FROM offers 
            JOIN Durations ON offers.ID_Duration = Durations.ID_Duration
            LIMIT ' . $limit,
        );
        return array_filter(array_map(function ($item) {
            return Offer::fromData($item);
        }, $data), function ($item) {
            return $item != null;
        });
    }

    public static function getByCompany($id) {
        $data = Database::getInstance()->fetchAll(
            'SELECT
                ID_Offer as ID,
                Title,
                Pay,
                Start_Date,
                End_Date,
                Places,
                Description,
                Creation_Date,
                Durations.Duration as Duration,
                ID_Location,
                ID_Company
            FROM offers 
            JOIN Durations ON offers.ID_Duration = Durations.ID_Duration
            WHERE ID_Company = :ID',
            array(':ID' => $id)
        );
        return array_map(function ($item) {
            Offer::fromData($item);
        }, $data);
    }

    public static function getByLocation($id) {
        $data = Database::getInstance()->fetchAll(
            'SELECT
                ID_Offer as ID,
                Title,
                Pay,
                Start_Date,
                End_Date,
                Places,
                Description,
                Creation_Date,
                Durations.Duration as Duration,
                ID_Location,
                ID_Company
            FROM offers 
            JOIN Durations ON offers.ID_Duration = Durations.ID_Duration
            WHERE ID_Location = :ID',
            array(':ID' => $id)
        );
        return array_map(function ($item) {
            Offer::fromData($item);
        }, $data);
    }

    public static function getByDuration($id) {
        $data = Database::getInstance()->fetchAll(
            'SELECT
                ID_Offer as ID,
                Title,
                Pay,
                Start_Date,
                End_Date,
                Places,
                Description,
                Creation_Date,
                Durations.Duration as Duration,
                ID_Location,
                ID_Company
            FROM offers 
            JOIN Durations ON offers.ID_Duration = Durations.ID_Duration
            WHERE offers.ID_Duration = :ID',
            array(':ID' => $id)
        );
        return array_map(function ($item) {
            Offer::fromData($item);
        }, $data);
    }

    public static function getByDate($date) {
        $data = Database::getInstance()->fetchAll(
            'SELECT
                ID_Offer as ID,
                Title,
                Pay,
                Start_Date,
                End_Date,
                Places,
                Description,
                Creation_Date,
                Durations.Duration as Duration,
                ID_Location,
                ID_Company
            FROM offers 
            JOIN Durations ON offers.ID_Duration = Durations.ID_Duration
            WHERE Start_Date <= :Date AND End_Date >= :Date',
            array(':Date' => $date)
        );
        return array_map(function ($item) {
            Offer::fromData($item);
        }, $data);
    }

    public static function getByStartDate($date) {
        $data = Database::getInstance()->fetchAll(
            'SELECT
                ID_Offer as ID,
                Title,
                Pay,
                Start_Date,
                End_Date,
                Places,
                Description,
                Creation_Date,
                Durations.Duration as Duration,
                ID_Location,
                ID_Company
            FROM offers 
            JOIN Durations ON offers.ID_Duration = Durations.ID_Duration
            WHERE Start_Date <= :Date',
            array(':Date' => $date)
        );
        return array_map(function ($item) {
            Offer::fromData($item);
        }, $data);
    }

    public static function getByDescription($description) {
        $data = Database::getInstance()->fetchAll(
            'SELECT
                ID_Offer as ID,
                Title,
                Pay,
                Start_Date,
                End_Date,
                Places,
                Description,
                Creation_Date,
                Durations.Duration as Duration,
                ID_Location,
                ID_Company
            FROM offers 
            JOIN Durations ON offers.ID_Duration = Durations.ID_Duration
            WHERE INSTR(Description, :Description) > 0',
            array(':Description' => $description)
        );
        return array_map(function ($item) {
            Offer::fromData($item);
        }, $data);
    }

    public static function getByPay($pay) {
        $data = Database::getInstance()->fetchAll(
            'SELECT
                ID_Offer as ID,
                Title,
                Pay,
                Start_Date,
                End_Date,
                Places,
                Description,
                Creation_Date,
                Durations.Duration as Duration,
                ID_Location,
                ID_Company
            FROM offers 
            JOIN Durations ON offers.ID_Duration = Durations.ID_Duration
            WHERE Pay >= :Pay',
            array(':Pay' => $pay)
        );
        return array_map(function ($item) {
            Offer::fromData($item);
        }, $data);
    }

    private static function fromData($data) {
        return new Offer(array(
            'ID' => $data['ID'],
            'Title' => $data['Title'] ?? '',
            'Pay' => $data['Pay'],
            'Start_Date' => new DateTime($data['Start_Date']),
            'End_Date' => new DateTime($data['End_Date']),
            'Places' => $data['Places'],
            'Description' => $data['Description'],
            'Creation_Date' => new DateTime($data['Creation_Date']),
            'Duration' => $data['Duration'],
            'ID_Location' => $data['ID_Location'],
            'ID_Company' => $data['ID_Company']
        ));
    }

    public static function getRandom(int $count): array {
        $data = Database::getInstance()->fetchAll(
            'SELECT
                ID_Offer as ID,
                Title,
                Pay,
                Start_Date,
                End_Date,
                Places,
                Description,
                Creation_Date,
                Durations.Duration as Duration,
                ID_Location,
                ID_Company
            FROM offers 
            JOIN Durations ON offers.ID_Duration = Durations.ID_Duration
            ORDER BY RAND()
            LIMIT ' . $count
        );
        return array_map(function ($item) {
            return Offer::fromData($item);
        }, $data);
    }

    public static function getWishList(int $accountId): array {
        $data = Database::getInstance()->fetchAll(
            'SELECT
                ID_Offer as ID,
                Title,
                Pay,
                Start_Date,
                End_Date,
                Places,
                Description,
                Creation_Date,
                Durations.Duration as Duration,
                ID_Location,
                ID_Company
            FROM offers 
            JOIN Durations ON offers.ID_Duration = Durations.ID_Duration
            JOIN Is_of_interest_to ON offers.ID_Offer = Is_of_interest_to.ID_Offer
            WHERE Is_of_interest_to.ID_Account = :ID',
            array(':ID' => $accountId)
        );
        return array_map(function ($item) {
            return Offer::fromData($item);
        }, $data);
    }
}
