<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpMqtt\Client\MqttClient;

$config = require __DIR__ . '/../config/mqtt.php';

$mqtt = new MqttClient(
    $config['broker'],
    $config['port'],
    'publisher-' . uniqid()
);

$mqtt->connect();

echo "TEST MQTT...\n";

// LCD
$mqtt->publish(
    $config['prefix'].'/'.$config['topic_lcd'],
    'TEST LCD|OK',
    0
);

// SERVO ENTRY
$mqtt->publish(
    $config['prefix'].'/'.$config['topic_entry_servo'],
    'OPEN',
    0
);

// SERVO EXIT
$mqtt->publish(
    $config['prefix'].'/'.$config['topic_exit_servo'],
    'OPEN',
    0
);

sleep(1);

$mqtt->disconnect();

echo "✔ MQTT TEST SUCCESS\n";