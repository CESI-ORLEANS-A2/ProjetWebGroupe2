<?php

require_once('../src/modules/ControllerBase.php');

require_once('../src/modules/Database/Models/Skill.php');
require_once('../src/modules/Database/Models/Study_Level.php');

class Controller extends ControllerBase {
    public function __construct($router) {
        parent::__construct($router);
    }

    public function run() {
        new Database(
            $this->config->get('DB_HOST'),
            $this->config->get('DB_NAME'),
            $this->config->get('DB_USER'),
            $this->config->get('DB_PASS')
        );

        $skills = Skill::getAll();
        $skills = array_map(function($skill) {
            return [
                'text' => strtolower($skill->get('Name')),
                'value' => strtolower($skill->get('Name'))
            ];
        }, $skills);

        $studyLevels = Study_Level::getAll();
        $studyLevels = array_map(function($studyLevel) {
            return [
                'text' => strtolower($studyLevel->get('Name')),
                'value' => strtolower($studyLevel->get('Name'))
            ];
        }, $studyLevels);

        return $this->render('search.twig', array(
            'skills' => $skills,
            'studyLevels' => $studyLevels
        ));
    }
};
