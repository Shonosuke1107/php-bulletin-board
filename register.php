<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>新規登録画面</title>
  <style>
    body {
      font-family: sans-serif;
      background-color: #f4f8fb;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .register-box {
      background-color: #fff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      width: 400px;
      text-align: center;
    }

    .register-box h2 {
      margin-bottom: 20px;
      color: #333;
    }

    input[type="text"],
    input[type="password"] {
      width: 90%;
      padding: 10px;
      margin-bottom: 20px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 8px;
    }

    input[type="submit"] {
      padding: 10px 20px;
      font-size: 16px;
      background-color: #28a745;
      border: none;
      color: white;
      border-radius: 8px;
      cursor: pointer;
    }

    input[type="submit"]:hover {
      background-color: #218838;
    }

    .message {
      margin-top: 20px;
      font-size: 16px;
    }

    .error {
      color: red;
    }

    .success {
      color: green;
    }

    .back-button {
      margin-top: 30px;
    }

    .back-button a {
      text-decoration: none;
    }

    .back-button button {
      padding: 8px 16px;
      font-size: 14px;
      border: none;
      background-color: #007bff;
      color: white;
      border-radius: 6px;
      cursor: pointer;
    }

    .back-button button:hover {
      background-color: #0056b3;
    }
  </style>
</head>

<body>
  <div class="register-box">
    <h2>新規登録</h2>
    <form action="" method="post">
      <input type="text" name="name" placeholder="ユーザー名"><br>
      <input type="password" name="password" placeholder="パスワード"><br>
      <input type="submit" name="submit" value="登録">
    </form>

    <div class="message">
      <?php
        $dsn = 'mysql:dbname=データベース名;host=localhost';
        $user = 'ユーザー名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        $success = false;
        
        if (!empty($_POST["name"]) && !empty($_POST["password"])) {
            $name = $_POST["name"];
            $password = $_POST["password"];

            $sql = "SELECT * FROM users WHERE name = :username";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $name, PDO::PARAM_STR);
            $stmt->execute();
            $existingUser = $stmt->fetch();

            if ($existingUser) {
                echo '<span class="error">このユーザー名はすでに使われています。</span>';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (name, password) VALUES (:name, :password)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
                $stmt->execute();
                echo '<span class="success">登録が完了しました！</span>';
                $success = true;
                
                $sql = "SELECT * FROM users WHERE name = :username";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':username', $name, PDO::PARAM_STR);
                $stmt->execute();
                $new_user = $stmt->fetch();
                
                $_SESSION["user_id"] = $new_user["id"];
                $_SESSION["name"] = $new_user["name"];
                
            }
        } elseif (!empty($_POST["name"])) {
            echo '<span class="error">登録するパスワードを入力してください。</span>';
        } elseif (!empty($_POST["password"])) {
            echo '<span class="error">登録する名前を入力してください。</span>';
        }
      ?>
    </div>
    
    <?php if ($success){ ?>
      <div class="back-button">
      <a href="photo_diary.php"><button type="button">マイページへ</button></a>
    </div>
    <?php }; ?>

    <!-- ログイン画面に戻るボタン -->
    <div class="back-button">
      <a href="login.php"><button type="button">ログイン画面に戻る</button></a>
    </div>

  </div>
</body>
</html>
