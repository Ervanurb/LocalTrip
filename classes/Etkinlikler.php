<?php
// localtrip/classes/Etkinlikler.php

class Etkinlikler {
    private $conn;
    private $table_name = "etkinlikler"; // Veritabanı tablosunun adı

    // Tablonun sütunlarına karşılık gelen özellikler
    public $id;
    public $baslik;
    public $detay;
    public $konum;
    public $tarih;
    public $baslangic_saati;
    public $bitis_saati;
    public $kategori;
    public $olusturulma_tarihi;

    // Yapıcı metot: Veritabanı bağlantı nesnesini alır
    public function __construct($db) {
        $this->conn = $db;
    }

    // Tüm etkinlikleri listeleme metodu (READ işlemi)
    public function readAll() {
        // SQL sorgusu
        $query = "SELECT
                    id, baslik, detay, konum, tarih, baslangic_saati, bitis_saati, kategori, olusturulma_tarihi
                FROM
                    " . $this->table_name . "
                ORDER BY
                    tarih ASC, baslangic_saati ASC"; // Tarihe ve saate göre sırala

        // Sorguyu hazırla
        $stmt = $this->conn->prepare($query);
        // Sorguyu çalıştır
        $stmt->execute();

        return $stmt->get_result(); // Sonuç kümesini döndür
    }

    public function readOne() {
        $query = "SELECT
                    id, baslik, detay, konum, tarih, baslangic_saati, bitis_saati, kategori, olusturulma_tarihi
                FROM
                    " . $this->table_name . "
                WHERE
                    id = ?
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            $this->id = $row['id'];
            $this->baslik = $row['baslik'];
            $this->detay = $row['detay'];
            $this->konum = $row['konum'];
            $this->tarih = $row['tarih'];
            $this->baslangic_saati = $row['baslangic_saati'];
            $this->bitis_saati = $row['bitis_saati'];
            $this->kategori = $row['kategori'];
            $this->olusturulma_tarihi = $row['olusturulma_tarihi'];
            return true;
        }
        return false;
    }

    // Etkinlik ekleme metodu (Yönetici paneli için)
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    baslik = ?, detay = ?, konum = ?, tarih = ?, baslangic_saati = ?, bitis_saati = ?, kategori = ?";

        $stmt = $this->conn->prepare($query);

        $this->baslik = htmlspecialchars(strip_tags($this->baslik));
        $this->detay = htmlspecialchars(strip_tags($this->detay));
        $this->konum = htmlspecialchars(strip_tags($this->konum));
        $this->tarih = htmlspecialchars(strip_tags($this->tarih));
        $this->baslangic_saati = htmlspecialchars(strip_tags($this->baslangic_saati));
        $this->bitis_saati = htmlspecialchars(strip_tags($this->bitis_saati));
        $this->kategori = htmlspecialchars(strip_tags($this->kategori));

        $stmt->bind_param("sssssss",
            $this->baslik,
            $this->detay,
            $this->konum,
            $this->tarih,
            $this->baslangic_saati,
            $this->bitis_saati,
            $this->kategori
        );

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Etkinlik güncelleme metodu (Yönetici paneli için)
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    baslik = ?, detay = ?, konum = ?, tarih = ?, baslangic_saati = ?, bitis_saati = ?, kategori = ?
                WHERE
                    id = ?";

        $stmt = $this->conn->prepare($query);

        $this->baslik = htmlspecialchars(strip_tags($this->baslik));
        $this->detay = htmlspecialchars(strip_tags($this->detay));
        $this->konum = htmlspecialchars(strip_tags($this->konum));
        $this->tarih = htmlspecialchars(strip_tags($this->tarih));
        $this->baslangic_saati = htmlspecialchars(strip_tags($this->baslangic_saati));
        $this->bitis_saati = htmlspecialchars(strip_tags($this->bitis_saati));
        $this->kategori = htmlspecialchars(strip_tags($this->kategori));

        $stmt->bind_param("sssssssi",
            $this->baslik,
            $this->detay,
            $this->konum,
            $this->tarih,
            $this->baslangic_saati,
            $this->bitis_saati,
            $this->kategori,
            $this->id
        );

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Etkinlik silme metodu (Yönetici paneli için)
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>