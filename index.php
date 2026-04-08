<?php
session_start();

// ================= REQUIRE =================
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/models/ParkirModel.php';
require_once __DIR__ . '/controllers/ParkirController.php';
require __DIR__ . '/vendor/autoload.php';

use PhpMqtt\Client\MqttClient;

// ================= INIT =================
$model = new ParkirModel();
$controller = new ParkirController();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// ================= HANDLE BUKA PALANG =================
if (isset($_POST['buka_palang']) && isset($_POST['id'])) {

    $id = $_POST['id'];

    // update status jadi DONE
    $controller->bukaPalang($id);

    // ================= MQTT =================
    $config = require __DIR__ . '/config/mqtt.php';

    try {

        $mqtt = new MqttClient(
            $config['broker'],
            $config['port'],
            'web-' . uniqid()
        );

        $mqtt->connect();

        $mqtt->publish(
            $config['prefix'].'/'.$config['topic_exit_servo'],
            'OPEN',
            1
        );

        $mqtt->publish(
            $config['prefix'].'/'.$config['topic_lcd'],
            'Terima Kasih|Selamat Jalan',
            1
        );

        $mqtt->loop(true);
        sleep(1);

        $mqtt->disconnect();

    } catch (Exception $e) {
        echo "MQTT ERROR: " . $e->getMessage();
    }

    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Parkir</title>
    <link rel="stylesheet" href="asset/style.css">
</head>

<body>

<div class="navbar">
    <h1>🚗 Dashboard Parkir</h1>
    <a href="logout.php">Logout</a>
</div>

<!-- ================= KENDARAAN MASUK ================= -->
<h3>Kendaraan Masuk</h3>
<table border="1" width="100%">
<tr>
    <th>ID</th>
    <th>Card</th>
    <th>Masuk</th>
    <th>Status</th>
</tr>

<?php
$data = $model->getParkirAktif()->fetchAll(PDO::FETCH_ASSOC);

if ($data) {
    foreach ($data as $row) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['card_id']}</td>
            <td>{$row['checkin_time']}</td>
            <td style='color:green; font-weight:bold;'>IN</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='4'>Kosong</td></tr>";
}
?>
</table>

<br>

<!-- ================= MENUNGGU PEMBAYARAN ================= -->
<h3>Menunggu Pembayaran</h3>
<table border="1" width="100%">
<tr>
    <th>ID</th>
    <th>Card</th>
    <th>Durasi</th>
    <th>Biaya</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>

<?php
$data = $model->getMenungguBayar()->fetchAll(PDO::FETCH_ASSOC);

if ($data) {
    foreach ($data as $row) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['card_id']}</td>
            <td>{$row['duration']} jam</td>
            <td>Rp " . number_format($row['fee'], 0, ',', '.') . "</td>
            <td style='color:orange; font-weight:bold;'>OUT</td>
            <td>
                <button onclick='bukaPalang({$row['id']})'>Buka</button>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='6'>Kosong</td></tr>";
}
?>
</table>

<br>

<!-- ================= RIWAYAT ================= -->
<h3>Riwayat</h3>
<table border="1" width="100%">
<tr>
    <th>ID</th>
    <th>Card</th>
    <th>Masuk</th>
    <th>Keluar</th>
    <th>Durasi</th>
    <th>Biaya</th>
    <th>Status</th>
</tr>

<?php
$data = $model->getHistory()->fetchAll(PDO::FETCH_ASSOC);

if ($data) {
    foreach ($data as $row) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['card_id']}</td>
            <td>{$row['checkin_time']}</td>
            <td>{$row['checkout_time']}</td>
            <td>{$row['duration']} jam</td>
            <td>Rp " . number_format($row['fee'], 0, ',', '.') . "</td>
            <td style='color:blue; font-weight:bold;'>DONE</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='7'>Kosong</td></tr>";
}
?>
</table>

<!-- ================= SCRIPT ================= -->
<script>
function bukaPalang(id) {

    if (!confirm("Buka palang kendaraan ini?")) return;

    let form = new FormData();
    form.append("id", id);
    form.append("buka_palang", "1");

    fetch("index.php", {
        method: "POST",
        body: form
    })
    .then(() => {
        alert("Palang terbuka!");
        location.reload();
    })
    .catch(err => {
        alert("Error: " + err);
    });
}

// auto refresh
setInterval(() => location.reload(), 5000);
</script>

</body>
</html>