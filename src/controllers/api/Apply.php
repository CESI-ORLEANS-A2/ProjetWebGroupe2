<?php

require_once('../src/modules/ControllerBase.php');

require_once '../src/modules/Database/Connector.php';
require_once '../src/modules/Database/Models/Account.php';
require_once '../src/modules/Database/Models/Session.php';
require_once '../src/modules/Database/Models/Offer.php';

class Controller extends ControllerBase {
    public function __construct($router) {
        parent::__construct($router);
    }

    public function run() {
        try {
            if (!$this->connectedAccount) {
                return $this->reply('Unauthorized', 401);
            }

            if (!isset($_REQUEST['offer'])) {
                return $this->reply('Missing offer', 400);
            }

            new Database(
                $this->config->get('DB_HOST'),
                $this->config->get('DB_NAME'),
                $this->config->get('DB_USER'),
                $this->config->get('DB_PASS'),
            );

            $offerId = $_REQUEST['offer'];
            $offer = Offer::getById($offerId);

            if (!$offer) {
                return $this->reply('Offer not found', 404);
            }

            $isApplied = $offer->isApplied($this->connectedAccount->getID());

            if ($isApplied) {
                $offer->removeApplication($this->connectedAccount->getID());
                return $this->reply(['isApplied' => false], 200);
            }

            $offer->addApplication($this->connectedAccount->getID());
            return $this->reply(['isApplied' => true], 200);

        } catch (Exception $e) {
            return $this->reply($e->getMessage(), 500);
        }
    }
}
