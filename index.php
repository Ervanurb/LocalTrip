<?php
// Oturumu her PHP sayfasının en başında başlatın
session_start();

// Gerekli sınıfları dahil et 
require_once 'classes/Database.php'; 

if (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0) {
    header("Location: gezilerim.php"); 
    exit();
}
// Oturum açmamışsa veya ilk kez geliyorsa, giriş/kayıt sayfasına yönlendir veya tanıtım yap
else {
    // header("Location: login.php"); // Doğrudan giriş sayfasına yönlendirmek isterseniz
    // exit();
}


//HTML
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ana Sayfa - LocalTrip</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
     </head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-5">
        <div class="jumbotron text-center">
            <h1 class="display-4">LocalTrip'e Hoş Geldiniz!</h1>
            <p class="lead">Yerel etkinlikleri keşfedin ve seyahat planlarınızı kolayca oluşturun.</p>
            <hr class="my-4">
            <p>Başlamak için hemen bir hesap oluşturun veya giriş yapın.</p>
            <a class="btn btn-primary btn-lg" href="register.php" role="button">Kayıt Ol</a>
            <a class="btn btn-secondary btn-lg" href="login.php" role="button">Giriş Yap</a>
        </div>

        <div class="row text-center mt-5">
            <div class="col-md-4">
                <h3>Etkinlikleri Keşfet</h3>
                <p>Çevrenizdeki en popüler ve ilgi çekici yerel etkinlikleri bulun.</p>
            </div>
            <div class="col-md-4">
                <h3>Geziler Planla</h3>
                <p>Kendi kişisel gezi rotalarınızı oluşturun ve düzenleyin.</p>
            </div>
            <div class="col-md-4">
                <h3>Favorilerini Kaydet</h3>
                <p>Beğendiğiniz etkinlikleri ve gezileri favorilerinize ekleyin.</p>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</html>