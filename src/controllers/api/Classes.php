<?php

require_once('../src/modules/ControllerBase.php');

require_once '../src/modules/Database/Connector.php';
require_once '../src/modules/Database/Models/Class.php';

class Controller extends ControllerBase {
    public function __construct($router) {
        parent::__construct($router);
    }

    public function run() {
        try {
            if (!isset($_REQUEST['pilote'])) {
                return $this->reply('Missing pilote', 400);
            }

            $limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 10;
            $offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;

            new Database(
                $this->config->get('DB_HOST'),
                $this->config->get('DB_NAME'),
                $this->config->get('DB_USER'),
                $this->config->get('DB_PASS'),
            );

            $classes = Classes::getByPiloteID((int)$_REQUEST['pilote']);

            if (empty($classes)) {
                return $this->reply([], 200);
            }

            $data = array_slice($classes, $offset, $limit);

            foreach ($data as $key => $class) {
                $data[$key] = [
                    'Name' => $class->get('Name'),
                ];
            }

            return $this->reply([
                'total_count' => count($classes),
                'count' => count($data),
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return $this->reply($e->getMessage(), 500);
        }
    }
}
