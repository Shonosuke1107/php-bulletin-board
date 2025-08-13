<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>掲示板リセット</title>
  </head>

  <body>
   
    <?php
     //データベース設定
    $dsn = 'mysql:dbname=データベース名;host=localhost';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
     //掲示板テーブルが存在していた場合の消去
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    $sql = 'DROP TABLE IF EXISTS photo_diary';
    $stmt = $pdo->query($sql);

    //掲示板テーブルの作成
    $sql = "CREATE TABLE IF NOT EXISTS photo_diary"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "user_id INT,"
        . "user_name TEXT,"
        . "comment TEXT,"
        . "photo VARCHAR(255),"
        . "date DATETIME"
        . ");";
 
    $stmt = $pdo->query($sql);
  
    $sql = "ALTER TABLE photo_diary ADD COLUMN is_edited TINYINT(1) DEFAULT 0";
    $stmt = $pdo->query($sql);
    
    //ユーザーテーブルの作成
    $sql = "CREATE TABLE IF NOT EXISTS users"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name TEXT,"
        . "password TEXT"
        . ");";
 
    $stmt = $pdo->query($sql);


    ?>
  </body>
</html>