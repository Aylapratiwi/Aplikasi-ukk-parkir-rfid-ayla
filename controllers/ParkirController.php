<?php
require_once __DIR__ . '/../models/ParkirModel.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpMqtt\Client\MqttClient;

class ParkirController {

    private $model;

    public function __construct() {
        $this->model = new ParkirModel();
    }

    public function bukaPalang($id) {
        if (!$id) {
            echo "ID tidak ditemukan";
            return;
        }

        $this->model->markDone($id);

        $server = 'broker.hivemq.com';
        $port = 1883;
        $clientId = 'web_' . rand();

        $mqtt = new MqttClient($server, $port, $clientId);
        $mqtt->connect();

        $mqtt->publish('parking/ayla/exit/servo', 'OPEN', 0);

        $mqtt->disconnect();

        header("Location: ../index.php");
        exit;
    }
}
?>