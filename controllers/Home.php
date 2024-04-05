<?php

require_once('../src/modules/ControllerBase.php');

require_once '../src/modules/Database/Connector.php';
require_once '../src/modules/Database/Models/Offer.php';

class Controller extends ControllerBase {
    public function __construct($router) {
        parent::__construct($router);
    }

    public function run() {
        $content = "<div>";

        $database = new Database(
            $this->config->get('DB_HOST'),
            $this->config->get('DB_NAME'),
            $this->config->get('DB_USER'),
            $this->config->get('DB_PASS')
        );

        $offer = Offer::getByID(2);

        $content .= 'Title : ' . $offer->get('Title') . "</br>";
        $content .= 'Description : ' . $offer->get('Description') . '</br>';

        $content .= 'Study Levels : ' . $offer->getStudyLevels() . '</br>';

        $offer->addStudyLevel('Test');

        $content .= 'Study Levels : ' . $offer->getStudyLevels() . '</br>';

        $offer->removeStudyLevel('Test');

        $content .= 'Study Levels : ' . $offer->getStudyLevels() . '</br>';

        $offer->addStudyLevel('Test3');
        $offer->save();

        $content .= 'Study Levels : ' . $offer->getStudyLevels() . '</br>';

        $content .= 'Skills : ' . $offer->getSkills() . '</br>';

        $offer->addSkill('Test');

        $content .= 'Skills : ' . $offer->getSkills() . '</br>';

        return $this->render('home.twig', array(
            'content' => $content . "</div>"
        ));
    }
};
