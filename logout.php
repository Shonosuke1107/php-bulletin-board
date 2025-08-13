<?php
session_start();
$_SESSION = array();
session_destroy();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>ログアウト</title>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f5f5f5;
    text-align: center;
    padding-top: 50px;
}
.message-box {
    display: inline-block;
    background-color: #fff;
    border: 2px solid #4CAF50;
    border-radius: 10px;
    padding: 30px 50px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
.message-box h2 {
    color: #4CAF50;
    margin-bottom: 20px;
}
.button {
    display: inline-block;
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
}
.button:hover {
    background-color: #45a049;
}
</style>
</head>
<body>

<div class="message-box">
    <h2>ログアウトしました</h2>
    <a href="login.php" class="button">ログイン画面へ戻る</a>
</div>

</body>
</html>