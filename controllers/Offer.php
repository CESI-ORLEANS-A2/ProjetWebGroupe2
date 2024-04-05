<?php

require_once('../src/modules/ControllerBase.php');

require_once('../src/modules/Database/Connector.php');
require_once('../src/modules/Database/Models/Offer.php');
require_once('../src/modules/Database/Models/Application.php');

class Controller extends ControllerBase {
    public function __construct($router) {
        parent::__construct($router);
    }

    public function run() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->router->render404();
        }

        new Database(
            $this->config->get('DB_HOST'),
            $this->config->get('DB_NAME'),
            $this->config->get('DB_USER'),
            $this->config->get('DB_PASS'),
        );

        $offer = Offer::getById($id);

        if (!$offer) {
            $this->router->render404();
        }

        $applicationsCount = Application::getCountByOffer($offer->getID());

        $company = $offer->getCompany();

        $headquarters = $company->getHeadquarters();

        $activities = implode(', ', array_map(function ($activity) {
            return $activity['Name'];
        }, $company->getActivities()));

        $others = Offer::getRandom(5);

        $this->render('offer', [
            'offer' => $offer,
            'applicationsCount' => $applicationsCount,
            'company' => $company,
            'headquarters' => $headquarters,
            'activities' => $activities,
            'others' => $others
        ]);
    }
};
