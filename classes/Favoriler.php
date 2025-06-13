<?php

class Favoriler {
    private $conn;
    private $table_name = "favoriler"; 

    public $id; // Favori kaydının kendi ID'si
    public $kul_id; // Favoriyi ekleyen kullanıcının ID'si
    public $etkinlik_id; // Favoriye eklenen etkinliğin ID'si
    public $favori_eklenme_tarihi; // Burası güncellendi!

    public function __construct($db) {
        $this->conn = $db;
    }

    
    public function addFavorite() {
        $query_check = "SELECT id FROM " . $this->table_name . " WHERE kul_id = ? AND etkinlik_id = ? LIMIT 0,1";
        $stmt_check = $this->conn->prepare($query_check);
        $stmt_check->bind_param("ii", $this->kul_id, $this->etkinlik_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            return false;
        }

        $query_insert = "INSERT INTO " . $this->table_name . "
                        SET
                            kul_id = ?,
                            etkinlik_id = ?";

        $stmt_insert = $this->conn->prepare($query_insert);
        $stmt_insert->bind_param("ii", $this->kul_id, $this->etkinlik_id);

        if ($stmt_insert->execute()) {
            return true; 
        }
        return false; 
    }

    public function readFavoritesByUserId($user_id) {
        $query = "SELECT
                    f.id as favori_id,
                    f.kul_id,
                    f.etkinlik_id,
                    f.favori_eklenme_tarihi, 
                    e.baslik,
                    e.detay,
                    e.konum,
                    e.tarih,
                    e.baslangic_saati,
                    e.bitis_saati,
                    e.kategori
                FROM
                    " . $this->table_name . " f
                LEFT JOIN
                    etkinlikler e
                ON
                    f.etkinlik_id = e.id
                WHERE
                    f.kul_id = ?
                ORDER BY
                    f.favori_eklenme_tarihi DESC"; 

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        return $stmt->get_result(); 
    }

    public function removeFavorite() {
        $query = "DELETE FROM " . $this->table_name . " WHERE kul_id = ? AND etkinlik_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $this->kul_id, $this->etkinlik_id);

        if ($stmt->execute()) {
            return $stmt->affected_rows > 0;
        }
        return false; 
    }
}
?>