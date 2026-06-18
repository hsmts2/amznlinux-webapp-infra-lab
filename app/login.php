<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $mysqli->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM tbl_users WHERE username = '$username'";
    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['username'] = $username;
            header('Location: welcome.php');
            exit;
        } else {
            echo " パスワードが間違っています。";
        }
    } else {
        echo " ユーザーが見つかりません。";
    }
}
?>

<form method="post">
    ユーザー名： <input type="text" name="username" required><br>
    パスワード： <input type="password" name="password" required><br>
    <button type="submit">ログイン</button>
</form>
