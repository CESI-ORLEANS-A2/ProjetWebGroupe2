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

            $isInWishList = $offer->isInterested($this->connectedAccount->getID());

            if ($isInWishList) {
                $offer->removeInterest($this->connectedAccount->getID());
                return $this->reply(['isInWishList' => false], 200);
            }

            $offer->addInterest($this->connectedAccount->getID());
            return $this->reply(['isInWishList' => true], 200);
        } catch (Exception $e) {
            return $this->reply($e->getMessage(), 500);
        }
    }
}
