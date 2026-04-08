<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/koneksi.php';

use PhpMqtt\Client\MqttClient;

$config = require __DIR__ . '/../config/mqtt.php';

// ================= MQTT CONNECT =================
$mqtt = new MqttClient(
    $config['broker'],
    $config['port'],
    $config['client_id']
);

// 🔥 pakai keep alive biar ga putus
$mqtt->connect(null, true, 60);

$topicEntry = $config['prefix'].'/'.$config['topic_rfid_entry'];
$topicExit  = $config['prefix'].'/'.$config['topic_rfid_exit'];

echo "============================\n";
echo "Listening ENTRY: $topicEntry\n";
echo "Listening EXIT : $topicExit\n";
echo "============================\n";

// ================= ENTRY =================
$mqtt->subscribe($topicEntry, function ($topic, $message) use ($mqtt, $conn, $config) {

    $cardId = trim($message);
    echo "\n[ENTRY] RFID: $cardId\n";

    try {

        // 🔥 LOCK TABLE (biar ga tabrakan request cepat)
        $conn->beginTransaction();

        // 🔥 CEK APAKAH MASIH ADA STATUS IN
        $cek = $conn->prepare("
            SELECT id FROM tbl_parkir
            WHERE card_id = ? AND status = 'IN'
            LIMIT 1 FOR UPDATE
        ");
        $cek->execute([$cardId]);
        $data = $cek->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            echo "❌ GAGAL: Kartu masih aktif (belum keluar)\n";

            $conn->rollBack();

            // OLED tampilkan error
            $mqtt->publish(
                $config['prefix'].'/'.$config['topic_lcd'],
                'Masih Parkir!|Tidak Bisa Masuk',
                0
            );

            return;
        }

        // ✅ INSERT
        $stmt = $conn->prepare("
            INSERT INTO tbl_parkir (card_id, checkin_time, status)
            VALUES (?, NOW(), 'IN')
        ");
        $stmt->execute([$cardId]);

        $conn->commit();

        echo "✔ BERHASIL MASUK\n";

        // OLED
        $mqtt->publish(
            $config['prefix'].'/'.$config['topic_lcd'],
            'Selamat Datang|Silakan Masuk',
            0
        );

        // SERVO ENTRY
        $mqtt->publish(
            $config['prefix'].'/'.$config['topic_entry_servo'],
            'OPEN',
            0
        );

    } catch (Exception $e) {

        $conn->rollBack();

        echo "❌ ERROR ENTRY: " . $e->getMessage() . "\n";
    }

}, 0);
// ================= EXIT =================
$mqtt->subscribe($topicExit, function ($topic, $message) use ($mqtt, $conn, $config) {

    $cardId = trim($message);
    echo "\n[EXIT] RFID: $cardId\n";

    try {

        // ambil data terakhir yg masih IN
        $cek = $conn->prepare("
            SELECT id, checkin_time FROM tbl_parkir
            WHERE card_id = ? AND status = 'IN'
            ORDER BY id DESC LIMIT 1
        ");
        $cek->execute([$cardId]);
        $data = $cek->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            echo "❌ Data tidak ditemukan!\n";
            return;
        }

        // ================= HITUNG DURASI =================
        $duration_jam = ceil(
            (time() - strtotime($data['checkin_time'])) / 3600
        );

        if ($duration_jam <= 0) {
            $duration_jam = 1;
        }

        $total = $duration_jam * 2000;

        // ================= UPDATE DB =================
        $stmt = $conn->prepare("
            UPDATE tbl_parkir SET
                checkout_time = NOW(),
                duration = ?,
                fee = ?,
                status = 'OUT'
            WHERE id = ?
        ");
        $stmt->execute([$duration_jam, $total, $data['id']]);

        echo "✔ DURASI: {$duration_jam} jam\n";
        echo "✔ TOTAL: {$total}\n";

        // OLED tampilkan total
        $mqtt->publish(
            $config['prefix'].'/'.$config['topic_lcd'],
            'Total: Rp '.$total.'|Silakan Bayar',
            1
        );

        echo "✔ MQTT EXIT TERKIRIM\n";

    } catch (Exception $e) {
        echo "❌ ERROR EXIT: " . $e->getMessage() . "\n";
    }

}, 0);


// ================= LOOP =================
echo "🚀 MQTT Listener Running...\n";

try {
    $mqtt->loop(true);
} catch (Exception $e) {
    echo "❌ MQTT LOOP ERROR: " . $e->getMessage() . "\n";
}