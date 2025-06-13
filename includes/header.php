<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
$username = $isLoggedIn ? htmlspecialchars($_SESSION['username']) : '';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">LocalTrip</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Anasayfa</a>
                </li>
                <?php if ($isLoggedIn):?>
                    <li class="nav-item">
                        <a class="nav-link" href="gezilerim.php">Gezilerim</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="etkinlikler.php">Etkinlikler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="favorilerim.php">Favorilerim</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Çıkış Yap</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Giriş Yap</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Kayıt Ol</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>