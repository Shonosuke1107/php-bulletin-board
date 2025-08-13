
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ログイン画面</title>
  <style>
    /*  画面全体の背景や中央配置などの装飾 */
    body {
      font-family: Arial, sans-serif; /* 全体のフォント */
      background-color: #f5f5f5; /* 背景色 */
      display: flex;
      justify-content: center; /* 横中央 */
      align-items: center;     /* 縦中央 */
      height: 100vh;           /* 画面の高さいっぱい */
    }

    /*ログインフォームを囲う箱のデザイン */
    .login-box {
      background-color: white;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      width: 300px;
    }

    /*フォーム内のテキストボックスやパスワード欄 */
    .login-box input[type="text"],
    .login-box input[type="password"] {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    /*ログインボタンのスタイル */
    .login-box input[type="submit"] {
      width: 100%;
      background-color: #4CAF50;
      color: white;
      border: none;
      padding: 10px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }

    /*ログインボタンにマウスを乗せた時の色変化 */
    .login-box input[type="submit"]:hover {
      background-color: #45a049;
    }

    /*新規登録ボタンの外側divの中央寄せと余白 */
    .register-button {
      text-align: center;
      margin-top: 15px;
    }

    /*新規登録ボタン自体のスタイル */
    .register-button button {
      padding: 10px 20px;
      font-size: 16px;
      background-color: #2196F3;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    /*新規登録ボタンのホバー時 */
    .register-button button:hover {
      background-color: #0b7dda;
    }
    
    .error-message {
      color: red;
      text-align: center;
      margin-top: 10px;
    }
  </style>
</head>

<body>
    
    <?php
     //データベース設定
    $dsn = 'mysql:dbname=データベース名;host=localhost';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    $message = "";
    
    if (!empty($_POST["name"]) && !empty($_POST["password"])) {
        $name = $_POST["name"];
        $password = $_POST["password"];
        
        // 入力された名前のユーザーを検索
        $sql = "SELECT * FROM users WHERE name = :name";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user["password"])) {
        // セッションに保存して掲示板へ
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["name"] = $user["name"];
        header("Location: photo_diary.php");
        exit; // ここで処理終了
        }else{ 
        $message = "ユーザー名またはパスワードが正しくありません。";
       }    
    }elseif(!empty($_POST["name"])){
        $message = "パスワードを入力してください";
    }elseif(!empty($_POST["password"])){
        $message = "名前を入力してください";
    }
    ?>
    <!--ログインフォーム本体を囲う箱 -->
    <div class="login-box">
    <h2 style="text-align: center;">ログイン</h2>

    <form method="post">
      <input type="text" name="name" placeholder="ユーザー名"><br>
      <input type="password" name="password" placeholder="パスワード"><br>
      <input type="submit" value="ログイン">
    </form>
    
    <?php if (!empty($message)){ ?>
      <div class="error-message"><?php echo $message; ?></div>
    <?php }; ?>

    <div class="register-button">
      <a href="register.php">
        <button type="button">新規登録</button>
      </a>
    </div>
    </div>
  
</body>
</html>
