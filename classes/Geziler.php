<?php

class Geziler {
    private $conn;
    private $table_name = "geziler"; 

    public $id;
    public $kul_id; 
    public $baslik;
    public $aciklama;
    public $konum;
    public $baslangic_tarihi;
    public $bitis_tarihi;
    public $olusturulma_tarihi;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    kul_id = ?,
                    baslik = ?,
                    aciklama = ?,
                    konum = ?,
                    baslangic_tarihi = ?,
                    bitis_tarihi = ?";

        // Sorguyu hazırla
        $stmt = $this->conn->prepare($query);

       
        $this->baslik = htmlspecialchars(strip_tags($this->baslik));
        $this->aciklama = htmlspecialchars(strip_tags($this->aciklama));
        $this->konum = htmlspecialchars(strip_tags($this->konum));

        // Parametreleri bağla (i: integer, s: string)
        $stmt->bind_param("isssss",
            $this->kul_id,
            $this->baslik,
            $this->aciklama,
            $this->konum,
            $this->baslangic_tarihi,
            $this->bitis_tarihi
        );

        // Sorguyu çalıştır
        if ($stmt->execute()) {
            return true; 
        }
        return false; 
    }

    public function readByUserId($user_id) {
        $query = "SELECT
                    id, kul_id, baslik, aciklama, konum, baslangic_tarihi, bitis_tarihi, olusturulma_tarihi
                FROM
                    " . $this->table_name . "
                WHERE
                    kul_id = ?
                ORDER BY
                    olusturulma_tarihi DESC"; 

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        return $stmt->get_result(); 
    }

    public function readOne() {
        $query = "SELECT
                    id, kul_id, baslik, aciklama, konum, baslangic_tarihi, bitis_tarihi, olusturulma_tarihi
                FROM
                    " . $this->table_name . "
                WHERE
                    id = ? AND kul_id = ?
                LIMIT 0,1"; 

    
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $this->id, $this->kul_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc(); 

        // Eğer bir kayıt bulunduysa, sınıf özelliklerine ata
        if ($row) {
            $this->id = $row['id'];
            $this->kul_id = $row['kul_id'];
            $this->baslik = $row['baslik'];
            $this->aciklama = $row['aciklama'];
            $this->konum = $row['konum'];
            $this->baslangic_tarihi = $row['baslangic_tarihi'];
            $this->bitis_tarihi = $row['bitis_tarihi'];
            $this->olusturulma_tarihi = $row['olusturulma_tarihi'];
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    baslik = ?,
                    aciklama = ?,
                    konum = ?,
                    baslangic_tarihi = ?,
                    bitis_tarihi = ?
                WHERE
                    id = ? AND kul_id = ?";

        // Sorguyu hazırla
        $stmt = $this->conn->prepare($query);

    
        $this->baslik = htmlspecialchars(strip_tags($this->baslik));
        $this->aciklama = htmlspecialchars(strip_tags($this->aciklama));
        $this->konum = htmlspecialchars(strip_tags($this->konum));

        // Parametreleri bağla (s: string, i: integer)
        $stmt->bind_param("sssssii",
            $this->baslik,
            $this->aciklama,
            $this->konum,
            $this->baslangic_tarihi,
            $this->bitis_tarihi,
            $this->id,
            $this->kul_id
        );

        // Sorguyu çalıştır
        if ($stmt->execute()) {
            return true; 
        }

        return false; 
    }

    
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ? AND kul_id = ?";

        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $this->id, $this->kul_id);

        
        if ($stmt->execute()) {
            return true; 
        }

       
        return false; 
    }
}
?>