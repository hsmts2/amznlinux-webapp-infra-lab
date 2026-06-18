<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $mysqli->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO tbl_users (username, password_hash) VALUES ('$username', '$password')";

    if ($mysqli->query($sql)) {
        echo " 登録が成功しました。<a href='login.php'>ログイン</a>";
    } else {
        echo " エラー：" . $mysqli->error;
    }
}
?>
<h1>ユーザー登録画面</h1>
<form method="post">
    ユーザー名： <input type="text" name="username" required><br>
    パスワード： <input type="password" name="password" required><br>
    <button type="submit">登録</button>
</form>
