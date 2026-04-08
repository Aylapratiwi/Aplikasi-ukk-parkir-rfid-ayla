<?php
require_once __DIR__ . '/../config/koneksi.php';

class ParkirModel {

    private $conn;
    private $table = "tbl_parkir";
    private $tarif_per_jam = 2000;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function getParkirAktif() {
        $stmt = $this->conn->prepare("
            SELECT * FROM {$this->table}
            WHERE status='IN'
            ORDER BY checkin_time DESC
        ");
        $stmt->execute();
        return $stmt;
    }

    public function getMenungguBayar() {
    $stmt = $this->conn->prepare("
        SELECT * FROM {$this->table}
        WHERE status='OUT'
        ORDER BY id DESC
    ");
    $stmt->execute();
    return $stmt;
}

    public function getHistory() {
        $stmt = $this->conn->prepare("
            SELECT * FROM {$this->table}
            WHERE status='DONE'
            ORDER BY id DESC LIMIT 50
        ");
        $stmt->execute();
        return $stmt;
    }

    public function getParkirById($id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM {$this->table}
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByCardAndStatus($card_id, $status) {
        $stmt = $this->conn->prepare("
            SELECT * FROM {$this->table}
            WHERE card_id = ? AND status = ?
            ORDER BY id DESC LIMIT 1
        ");
        $stmt->execute([$card_id, $status]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkIn($card_id) {
        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table} (card_id, checkin_time, status)
            VALUES (?, NOW(), 'IN')
        ");
        return $stmt->execute([$card_id]);
    }

    public function checkOut($id) {

    $data = $this->getParkirById($id);
    if (!$data) return false;

    $stmt = $this->conn->prepare("
        UPDATE {$this->table} SET
            checkout_time = NOW(),
            duration = CEIL(TIMESTAMPDIFF(SECOND, checkin_time, NOW()) / 3600),
            fee = CEIL(TIMESTAMPDIFF(SECOND, checkin_time, NOW()) / 3600) * ?,
            status = 'OUT'
        WHERE id = ?
    ");

    return $stmt->execute([$this->tarif_per_jam, $id]);
}

    public function markDone($id) {
        $stmt = $this->conn->prepare("
            UPDATE {$this->table}
            SET status='DONE'
            WHERE id=?
        ");
        return $stmt->execute([$id]);
    }
}
?>