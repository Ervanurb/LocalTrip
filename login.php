<?php
session_start();

// Kullanıcı zaten giriş yapmışsa, ana sayfaya yönlendir
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0) {
    header("Location: gezilerim.php"); 
    exit();
}

require_once 'classes/Database.php';
require_once 'classes/User.php';

// Veritabanı bağlantısını al
$database = new Database();
$db = $database->getConnection();


$user = new User($db);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user->kullanici_adi = filter_input(INPUT_POST, 'kullanici_adi', FILTER_SANITIZE_STRING);
    $password = $_POST['sifre']; 

    
    if (empty($user->kullanici_adi) || empty($password)) {
        $message = "<div class='alert alert-danger'>Lütfen tüm alanları doldurun.</div>";
    } else {
        // Kullanıcı adıyla kullanıcıyı bulmaya çalış
        if ($user->findByUsername()) {
            // Şifreyi doğrula
            if (password_verify($password, $user->sifre_hash)) {
                // Giriş başarılı! Oturumu başlat
                $_SESSION['user_id'] = $user->id;
                $_SESSION['username'] = $user->kullanici_adi;
                $_SESSION['ad'] = $user->ad; 

                
                header("Location: gezilerim.php");
                exit();
            } else {
                $message = "<div class='alert alert-danger'>Hatalı kullanıcı adı veya şifre.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Hatalı kullanıcı adı veya şifre.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - LocalTrip</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
     </head>
<body>
    <?php include 'includes/header.php';  ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2>Giriş Yap</h2>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($message)): ?>
                            <?php echo $message; ?>
                        <?php endif; ?>
                        <form action="login.php" method="POST">
                            <div class="form-group">
                                <label for="kullanici_adi">Kullanıcı Adı:</label>
                                <input type="text" class="form-control" id="kullanici_adi" name="kullanici_adi" required>
                            </div>
                            <div class="form-group">
                                <label for="sifre">Şifre:</label>
                                <input type="password" class="form-control" id="sifre" name="sifre" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Giriş Yap</button>
                        </form>
                        <p class="mt-3 text-center">Hesabınız yok mu? <a href="register.php">Şimdi Kayıt Ol</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
     </body>
</html>