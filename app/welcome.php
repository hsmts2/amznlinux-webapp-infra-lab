<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

echo "<div>" . htmlspecialchars($_SESSION['username']) . "さん、こんにちは！</div>";
?>
<br>
<a href="logout.php">ログアウト</a>
