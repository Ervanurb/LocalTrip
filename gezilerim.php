<?php
session_start(); 


if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] <= 0) {
    header("Location: login.php");
    exit();
}

// Hata raporlamayı aç 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Gerekli sınıfları dahil et
require_once 'classes/Database.php';
require_once 'classes/Geziler.php'; 

// Veritabanı bağlantısını al
$database = new Database();
$db = $database->getConnection();

// Geziler sınıfından bir nesne oluştur
$geziler = new Geziler($db);
$geziler->kul_id = $_SESSION['user_id']; 

$message = ''; 
$edit_mode = false; 
$gezi_id_to_edit = null; 

// Form işlemleri 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add_or_update_gezi') {
            // Ortak değişken atamaları
            $geziler->baslik = htmlspecialchars($_POST['baslik'] ?? '', ENT_QUOTES, 'UTF-8');
            $geziler->aciklama = htmlspecialchars($_POST['aciklama'] ?? '', ENT_QUOTES, 'UTF-8');
            $geziler->konum = htmlspecialchars($_POST['konum'] ?? '', ENT_QUOTES, 'UTF-8');
            $geziler->baslangic_tarihi = htmlspecialchars($_POST['baslangic_tarihi'] ?? '', ENT_QUOTES, 'UTF-8');
            $geziler->bitis_tarihi = htmlspecialchars($_POST['bitis_tarihi'] ?? '', ENT_QUOTES, 'UTF-8');

            // Gerekli alanların boş olup olmadığını kontrol et
            if (empty($geziler->baslik) || empty($geziler->konum) || empty($geziler->baslangic_tarihi)) {
                $message = "<div class='alert alert-danger'>Başlık, Konum ve Başlangıç Tarihi zorunludur.</div>";
            } else {
                if (isset($_POST['gezi_id']) && !empty($_POST['gezi_id'])) {
                    // Geziyi güncelle
                    $geziler->id = filter_input(INPUT_POST, 'gezi_id', FILTER_SANITIZE_NUMBER_INT);
                    if ($geziler->update()) {
                        $message = "<div class='alert alert-success'>Gezi başarıyla güncellendi!</div>";
                    } else {
                        $message = "<div class='alert alert-danger'>Gezi güncellenirken bir hata oluştu.</div>";
                    }
                } else {
                    // Yeni gezi ekle
                    if ($geziler->create()) {
                        $message = "<div class='alert alert-success'>Yeni gezi başarıyla eklendi!</div>";
                        // Formu temizle
                        $geziler->baslik = '';
                        $geziler->aciklama = '';
                        $geziler->konum = '';
                        $geziler->baslangic_tarihi = '';
                        $geziler->bitis_tarihi = '';
                    } else {
                        $message = "<div class='alert alert-danger'>Gezi eklenirken bir hata oluştu.</div>";
                    }
                }
            }
        }
    }
}

// GET isteği işlemleri (Silme veya Düzenleme için veri getirme)
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'delete_gezi' && isset($_GET['id'])) {
        $geziler->id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        if ($geziler->delete()) {
            $message = "<div class='alert alert-success'>Gezi başarıyla silindi.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Gezi silinirken bir hata oluştu veya bu gezi size ait değil.</div>";
        }
        // Silme sonrası URL'den parametreleri temizle
        header("Location: gezilerim.php");
        exit();
    } elseif ($_GET['action'] == 'edit_gezi' && isset($_GET['id'])) {
        $geziler->id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        if ($geziler->readOne()) {
            // Düzenleme moduna geç
            $edit_mode = true;
            $gezi_id_to_edit = $geziler->id;
        } else {
            $message = "<div class='alert alert-danger'>Gezi bulunamadı veya size ait değil.</div>";
        }
    }
}

$result = $geziler->readByUserId($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gezilerim - LocalTrip</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Gezilerim</h2>

        <?php if (!empty($message)): ?>
            <?php echo $message; ?>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header">
                <h3><?php echo $edit_mode ? 'Geziyi Düzenle' : 'Yeni Gezi Ekle'; ?></h3>
            </div>
            <div class="card-body">
                <form action="gezilerim.php" method="POST">
                    <?php if ($edit_mode): ?>
                        <input type="hidden" name="gezi_id" value="<?php echo htmlspecialchars($gezi_id_to_edit); ?>">
                        <input type="hidden" name="action" value="add_or_update_gezi">
                    <?php else: ?>
                        <input type="hidden" name="action" value="add_or_update_gezi">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="baslik">Gezi Başlığı:</label>
                        <input type="text" class="form-control" id="baslik" name="baslik"
                               value="<?php echo htmlspecialchars($edit_mode ? $geziler->baslik : ($geziler->baslik ?? '')); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="konum">Konum:</label>
                        <input type="text" class="form-control" id="konum" name="konum"
                               value="<?php echo htmlspecialchars($edit_mode ? $geziler->konum : ($geziler->konum ?? '')); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="baslangic_tarihi">Başlangıç Tarihi:</label>
                        <input type="date" class="form-control" id="baslangic_tarihi" name="baslangic_tarihi"
                               value="<?php echo htmlspecialchars($edit_mode ? $geziler->baslangic_tarihi : ($geziler->baslangic_tarihi ?? '')); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="bitis_tarihi">Bitiş Tarihi (İsteğe Bağlı):</label>
                        <input type="date" class="form-control" id="bitis_tarihi" name="bitis_tarihi"
                               value="<?php echo htmlspecialchars($edit_mode ? $geziler->bitis_tarihi : ($geziler->bitis_tarihi ?? '')); ?>">
                    </div>
                    <div class="form-group">
                        <label for="aciklama">Açıklama:</label>
                        <textarea class="form-control" id="aciklama" name="aciklama" rows="3"><?php echo htmlspecialchars($edit_mode ? $geziler->aciklama : ($geziler->aciklama ?? '')); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo $edit_mode ? 'Geziyi Güncelle' : 'Gezi Ekle'; ?></button>
                    <?php if ($edit_mode): ?>
                        <a href="gezilerim.php" class="btn btn-secondary">İptal</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <h3 class="mt-5 mb-3">Mevcut Gezilerim</h3>
        <?php if ($result->num_rows > 0): ?>
            <div class="row">
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['baslik']); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($row['konum']); ?></h6>
                                <p class="card-text">
                                    <strong>Tarihler:</strong> <?php echo htmlspecialchars($row['baslangic_tarihi']); ?>
                                    <?php echo !empty($row['bitis_tarihi']) ? ' - ' . htmlspecialchars($row['bitis_tarihi']) : ''; ?><br>
                                    <strong>Açıklama:</strong> <?php echo htmlspecialchars($row['aciklama']); ?><br>
                                    <small class="text-muted">Oluşturulma Tarihi: <?php echo htmlspecialchars($row['olusturulma_tarihi']); ?></small>
                                </p>
                                <a href="gezilerim.php?action=edit_gezi&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">Düzenle</a>
                                <a href="gezilerim.php?action=delete_gezi&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu geziyi silmek istediğinizden emin misiniz?');">Sil</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Henüz bir gezi planınız yok. Yukarıdaki formu kullanarak yeni bir gezi ekleyebilirsiniz!</div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>