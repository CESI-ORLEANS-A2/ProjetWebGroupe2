<?php

require_once '../src/modules/Database/Model.php';
require_once '../src/modules/Database/ModelManager.php';

class Skill extends Model {
    public function __construct($data) {
        parent::__construct(
            array(
                'ID' => $data['ID'] ?? null,
                'Name' => $data['Name']
            ),
            array(
                'ID' => array('type' => 'int', 'readonly' => true),
                'Name' => array('type' => 'string', 'required' => true)
            )
        );
    }

    public function __toString(): string {
        return $this->get('Name');
    }

    public function fromModel(Skill $skill) {
        $this->setID($skill->get('ID'));
        $this->set('Name', $skill->get('Name'));
    }

    public function save() {
        if (!$this->validate())
            throw new Error('Invalid data');

        $dbh = Database::getInstance();

        if (!$this->getID()) {
            $skill = Skill::getByName($this->get('Name'));
            if ($skill) {
                $this->fromModel($skill);
                return;
            }
            $dbh->query(
                'INSERT INTO Skills (Name) VALUES (:Name)',
                array(':Name' => $this->get('Name'))
            );

            $this->set('ID', $dbh->lastInsertId());
        } else {
            $dbh->query(
                'UPDATE Skills SET Name = :Name WHERE ID_Skill = :ID',
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
            'DELETE FROM Skills WHERE ID_Skill = :ID',
            array(':ID' => $this->get('ID'))
        );
    }

    public function isReferenced(): bool {
        $dbh = Database::getInstance();

        $data = $dbh->fetch(
            'SELECT COUNT(*)
            FROM Has 
            JOIN Requires
            WHERE Requires.ID_Skill = :ID OR Has.ID_Skill = :ID',
            array(':ID' => $this->get('ID'))
        );

        return $data['Count'] > 0;
    }

    static public function getByName(string $Name): ?Skill {
        $data = Database::getInstance()->fetch(
            'SELECT ID_Skill, Name FROM Skills WHERE Name = :Name',
            array(':Name' => $Name)
        );

        if (!$data)
            return null;

        return new Skill(
            array(
                'ID' => $data['ID_Skill'],
                'Name' => $data['Name']
            )
        );
    }

    static public function getByID(int $ID): ?Skill {
        $data = Database::getInstance()->fetch(
            'SELECT Name FROM Skills WHERE ID_Skill = :ID',
            array(':ID' => $ID)
        );

        if (!$data)
            return null;

        return new Skill(
            array(
                'ID' => $ID,
                'Name' => $data['Name']
            )
        );
    }

    static public function getAll(): array {
        $data = Database::getInstance()->fetchAll(
            'SELECT ID_Skill, Name FROM Skills',
            array()
        );

        $skills = array();
        foreach ($data as $skill) {
            $skills[] = new Skill(
                array(
                    'ID' => $skill['ID_Skill'],
                    'Name' => $skill['Name']
                )
            );
        }

        return $skills;
    }
}

class SkillManager extends ModelManager {

    public function contains(string | int $Name): bool {
        if (is_int($Name)) {
            foreach ($this->models as $model) {
                if ($model->get('ID') == $Name)
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

    public static function getByOffer(int $ID_Offer): SkillManager {
        $data = Database::getInstance()->fetchAll(
            'SELECT 
                Skills.ID_Skill, 
                Name
            FROM Skills
            JOIN Requires ON Skills.ID_Skill = Requires.ID_Skill
            WHERE ID_Offer = :ID',
            array(':ID' => $ID_Offer)
        );

        $manager = new SkillManager();
        foreach ($data as $skill) {
            $manager->append(new Skill(
                array(
                    'ID' => $skill['ID_Skill'],
                    'Name' => $skill['Name']
                )
            ));
        }

        return $manager;
    }
}
