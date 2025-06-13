<?php
class User {
    private $conn;
    private $table_name = "kullanicilar";

    public $id;
    public $kullanici_adi;
    public $ad;
    public $email;
    public $sifre_hash;
    public $olusturulma_tarihi;

    // Yapıcı metotta Database sınıfının bağlantı nesnesini alıyoruz
    public function __construct($db) {
        $this->conn = $db;
    }


    public function register() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    kullanici_adi = ?,
                    ad = ?,
                    email = ?,
                    sifre_hash = ?";

        $stmt = $this->conn->prepare($query);
        $this->kullanici_adi = htmlspecialchars(strip_tags($this->kullanici_adi));
        $this->ad = htmlspecialchars(strip_tags($this->ad));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bind_param("ssss", $this->kullanici_adi, $this->ad, $this->email, $this->sifre_hash);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function findByUsername() {
        $query = "SELECT id, kullanici_adi, ad, email, sifre_hash FROM " . $this->table_name . " WHERE kullanici_adi = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $this->kullanici_adi = htmlspecialchars(strip_tags($this->kullanici_adi));
        $stmt->bind_param("s", $this->kullanici_adi);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->id = $row['id'];
            $this->ad = $row['ad'];
            $this->email = $row['email'];
            $this->sifre_hash = $row['sifre_hash'];
            return true;
        }
        return false;
    }

    public function findByEmail() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return true;
        }
        return false;
    }
}
?>