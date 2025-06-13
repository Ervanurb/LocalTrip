<?php
session_start();


require_once 'classes/Database.php';
require_once 'classes/User.php';

$database = new Database();
$db = $database->getConnection();


$user = new User($db);

$message = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user->kullanici_adi = filter_input(INPUT_POST, 'kullanici_adi', FILTER_SANITIZE_STRING);
    $user->ad = filter_input(INPUT_POST, 'ad', FILTER_SANITIZE_STRING);
    $user->email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['sifre']; 

    // Alanların boş olup olmadığını kontrol et
    if (empty($user->kullanici_adi) || empty($user->ad) || empty($user->email) || empty($password)) {
        $message = "Lütfen tüm alanları doldurun.";
    } elseif (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
        $message = "Geçersiz e-posta formatı.";
    } elseif ($user->findByUsername()) { 
        $message = "Bu kullanıcı adı zaten alınmış.";
    } elseif ($user->findByEmail()) { 
        $message = "Bu e-posta adresi zaten kayıtlı.";
    } else {
        // Şifreyi hash'le
        $user->sifre_hash = password_hash($password, PASSWORD_DEFAULT);

        // Kullanıcıyı kaydet
        if ($user->register()) {
            $message = "<div class='alert alert-success'>Kayıt başarıyla oluşturuldu! Şimdi <a href='login.php'>giriş yapabilirsiniz</a>.</div>";
            // Başarılı kayıttan sonra form alanlarını temizle
            $user->kullanici_adi = '';
            $user->ad = '';
            $user->email = '';
        } else {
            $message = "<div class='alert alert-danger'>Kayıt oluşturulurken bir hata oluştu. Lütfen tekrar deneyin.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol - LocalTrip</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2>Kayıt Ol</h2>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($message)): ?>
                            <?php echo $message; ?>
                        <?php endif; ?>
                        <form action="register.php" method="POST">
                            <div class="form-group">
                                <label for="kullanici_adi">Kullanıcı Adı:</label>
                                <input type="text" class="form-control" id="kullanici_adi" name="kullanici_adi" value="<?php echo htmlspecialchars($user->kullanici_adi ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="ad">Adınız:</label>
                                <input type="text" class="form-control" id="ad" name="ad" value="<?php echo htmlspecialchars($user->ad ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">E-posta:</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user->email ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="sifre">Şifre:</label>
                                <input type="password" class="form-control" id="sifre" name="sifre" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Kayıt Ol</button>
                        </form>
                        <p class="mt-3 text-center">Zaten hesabınız var mı? <a href="login.php">Giriş Yap</a></p>
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