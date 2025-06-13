<?php
class Database {
    private $host = "localhost";
    private $db_name = "dbstorage22360859060";
    private $username = "dbusr22360859060";
    private $password = "PUBNEEQGPAPH";
    public $conn;

    // Veritabanı bağlantısını sağlayan metod
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);

            // Bağlantı hatası kontrolü
            if ($this->conn->connect_error) {
                throw new Exception("Veritabanı bağlantısı başarısız: " . $this->conn->connect_error);
            }

            $this->conn->set_charset("utf8mb4");

        } catch (Exception $e) {
            // Hata durumunda uygulamayı durdur
            die($e->getMessage());
        }

        return $this->conn;
    }
}
?>