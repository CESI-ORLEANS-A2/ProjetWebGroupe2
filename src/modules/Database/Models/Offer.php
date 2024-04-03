<?php

require_once '../src/modules/Database/Model.php';

require_once '../src/modules/Database/Models/Study_Level.php';
require_once '../src/modules/Database/Models/Skill.php';

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
}
