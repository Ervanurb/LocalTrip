<?php
session_start(); 

// Kullanıcı giriş yapmamışsa, giriş sayfasına yönlendir
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
require_once 'classes/Favoriler.php'; 

// Veritabanı bağlantısını al
$database = new Database();
$db = $database->getConnection();

// Favoriler sınıfından nesne oluştur
$favoriler = new Favoriler($db);
$favoriler->kul_id = $_SESSION['user_id']; 

$message = ''; 

// Favorilerden kaldırma işlemi (sadece bu sayfadan)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'remove_favorite') {
    $favoriler->etkinlik_id = filter_input(INPUT_POST, 'etkinlik_id', FILTER_SANITIZE_NUMBER_INT);

    if ($favoriler->removeFavorite()) {
        $message = "<div class='alert alert-success'>Etkinlik favorilerinizden başarıyla kaldırıldı.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Etkinlik favorilerinizden kaldırılırken bir hata oluştu.</div>";
    }
}

// Kullanıcının tüm favori etkinliklerini oku
$favori_etkinlikler_result = $favoriler->readFavoritesByUserId($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorilerim - LocalTrip</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
   
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Favori Etkinliklerim</h2>

        <?php if (!empty($message)): ?>
            <?php echo $message; ?>
        <?php endif; ?>

        <?php if ($favori_etkinlikler_result->num_rows > 0): ?>
            <div class="row">
                <?php while($row = $favori_etkinlikler_result->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['baslik']); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($row['konum']); ?></h6>
                                <p class="card-text">
                                    <strong>Tarih:</strong> <?php echo htmlspecialchars($row['tarih']); ?><br>
                                    <?php if (!empty($row['kategori'])): ?>
                                        <strong>Kategori:</strong> <?php echo htmlspecialchars($row['kategori']); ?><br>
                                    <?php endif; ?>
                                    <strong>Detay:</strong> <?php echo htmlspecialchars($row['detay']); ?>
                                </p>
                                <div class="mt-auto">
                                    <form action="favorilerim.php" method="POST" class="d-inline">
                                        <input type="hidden" name="etkinlik_id" value="<?php echo $row['etkinlik_id']; ?>">
                                        <button type="submit" name="action" value="remove_favorite" class="btn btn-sm btn-danger" onclick="return confirm('Bu etkinliği favorilerinizden kaldırmak istediğinizden emin misiniz?');">
                                            Favorilerden Kaldır
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Henüz favori etkinliğiniz bulunmamaktadır. <a href="etkinlikler.php">Etkinlikleri Keşfet</a>.</div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
</body>
</html>