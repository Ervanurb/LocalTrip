<?php
session_start();

// Hata raporlamayı aç (Geliştirme aşamasında faydalıdır)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Gerekli sınıfları dahil et
require_once 'classes/Database.php';
require_once 'classes/Etkinlikler.php'; 
require_once 'classes/Favoriler.php';   

// Veritabanı bağlantısını al
$database = new Database();
$db = $database->getConnection();

// Etkinlikler ve Favoriler sınıfından nesneler oluştur
$etkinlikler = new Etkinlikler($db);
$favoriler = new Favoriler($db);

$message = ''; 

// Favorilere ekleme/kaldırma işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0) { 
        $favoriler->kul_id = $_SESSION['user_id'];
        $favoriler->etkinlik_id = filter_input(INPUT_POST, 'etkinlik_id', FILTER_SANITIZE_NUMBER_INT);

        if ($_POST['action'] == 'add_favorite') {
            if ($favoriler->addFavorite()) {
                $message = "<div class='alert alert-success'>Etkinlik favorilerinize eklendi!</div>";
            } else {
                $message = "<div class='alert alert-info'>Bu etkinlik zaten favorilerinizde veya bir hata oluştu.</div>";
            }
        } elseif ($_POST['action'] == 'remove_favorite') {
            if ($favoriler->removeFavorite()) {
                $message = "<div class='alert alert-warning'>Etkinlik favorilerinizden kaldırıldı.</div>";
            } else {
                $message = "<div class='alert alert-danger'>Etkinlik favorilerinizden kaldırılırken bir hata oluştu.</div>";
            }
        }
    } else {
        $message = "<div class='alert alert-danger'>Favori eklemek/kaldırmak için lütfen giriş yapın.</div>";
    }
}

// Tüm etkinlikleri oku
$etkinlikler_result = $etkinlikler->readAll();

// Kullanıcının favori etkinlik ID'lerini al (buton durumunu belirlemek için)
$user_favorites = [];
if (isset($_SESSION['user_id'])) {
    $favori_result = $favoriler->readFavoritesByUserId($_SESSION['user_id']);
    while ($row = $favori_result->fetch_assoc()) {
        $user_favorites[$row['etkinlik_id']] = true; // Favori ID'sini anahtar olarak kaydet
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etkinlikler - LocalTrip</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Etkinlikler </h2>

        <?php if (!empty($message)): ?>
            <?php echo $message; ?>
        <?php endif; ?>

        <?php if ($etkinlikler_result->num_rows > 0): ?>
            <div class="row">
                <?php while($row = $etkinlikler_result->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['baslik']); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($row['konum']); ?></h6>
                                <p class="card-text">
                                    <strong>Tarih:</strong> <?php echo htmlspecialchars($row['tarih']); ?><br>
                                    <?php if (!empty($row['baslangic_saati'])): ?>
                                        <strong>Saat:</strong> <?php echo htmlspecialchars(substr($row['baslangic_saati'], 0, 5)); ?>
                                        <?php echo !empty($row['bitis_saati']) ? ' - ' . htmlspecialchars(substr($row['bitis_saati'], 0, 5)) : ''; ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($row['kategori'])): ?>
                                        <strong>Kategori:</strong> <?php echo htmlspecialchars($row['kategori']); ?><br>
                                    <?php endif; ?>
                                    <strong>Detay:</strong> <?php echo htmlspecialchars($row['detay']); ?>
                                </p>
                                <div class="mt-auto">
                                    <?php if (isset($_SESSION['user_id'])): // Sadece giriş yapmış kullanıcılar için favori butonlarını göster ?>
                                        <form action="etkinlikler.php" method="POST" class="d-inline">
                                            <input type="hidden" name="etkinlik_id" value="<?php echo $row['id']; ?>">
                                            <?php if (isset($user_favorites[$row['id']])): ?>
                                                <button type="submit" name="action" value="remove_favorite" class="btn btn-sm btn-outline-warning">
                                                    Favorilerden Kaldır
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" name="action" value="add_favorite" class="btn btn-sm btn-success">
                                                    Favorilere Ekle
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    <?php else: ?>
                                        <a href="login.php" class="btn btn-sm btn-info">Giriş Yap (Favorilere Ekle)</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Henüz görüntülenecek bir etkinlik bulunamadı.</div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
</body>
</html>