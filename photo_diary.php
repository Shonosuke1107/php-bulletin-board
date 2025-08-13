<?php
session_start();
//ログインしていない場合
if (empty($_SESSION["user_id"])) {
    // HTMLを直接出力
    echo '<p style="color:red;font-weight:bold;">ログインしてください。</p>';
    echo '<a href="login.php">
            <button style="padding:8px 16px;font-size:16px;">ログイン画面へ</button>
          </a>';
    exit; // ここで処理終了（掲示板の中身は表示しない）
}
$edit_photo = "";
$edit_comment = "";
$edit_id = "";
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>フォト日記</title>
  <style>
    body {
      font-family: "Segoe UI", sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
    }

    h1 {
      text-align: center;
      font-size: 42px;
      color: #2c3e50;
      margin: 40px 0 10px;
    }

    .welcome {
      text-align: center;
      font-size: 18px;
      color: #666;
      margin-bottom: 30px;
    }

    .form-container {
      background-color: #ffffff;
      max-width: 600px;
      margin: 0 auto 40px;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      text-align: center;
    }

    .form-container input[type="file"],
    .form-container input[type="text"] {
      font-size: 16px;
      padding: 12px;
      margin: 10px 0;
      width: 100%;
      border: 1px solid #ccc;
      border-radius: 6px;
      box-sizing: border-box;
    }

    .form-container input[type="submit"] {
      background-color: #3498db;
      color: white;
      border: none;
      padding: 12px 24px;
      font-size: 16px;
      border-radius: 6px;
      cursor: pointer;
      margin-top: 15px;
    }

    .form-container input[type="submit"]:hover {
      background-color: #2980b9;
    }

    .entry-card {
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      max-width: 600px;
      margin: 20px auto;
      padding: 20px;
    }

    .entry-meta {
      font-size: 14px;
      color: #888;
      margin-bottom: 10px;
    }

    .entry-photo img {
      width: 100%;
      max-width: 300px;
      height: auto;
      display: block;
      margin: 10px auto;
      border-radius: 6px;
      border: 1px solid #ddd;
    }

    .entry-comment {
      font-size: 18px;
      color: #333;
      margin-top: 10px;
      text-align: center;
      white-space: pre-wrap;
    }

    form[method="post"] {
      display: inline-block;
      margin: 5px;
    }

    input[type="submit"][name="delete"] {
      background-color: #e74c3c;
    }

    input[type="submit"][name="delete"]:hover {
      background-color: #c0392b;
    }

    input[type="submit"][name="edit"] {
      background-color: #f39c12;
    }

    input[type="submit"][name="edit"]:hover {
      background-color: #d68910;
    }
    
</style>

</head>
<body>

  <h1>今日の気持ちと写真を、1枚の日記に。</h1>
  
  <div style="text-align: right; padding: 10px 20px;">
  <form action="logout.php" method="post" style="display:inline;">
    <input type="submit" value="ログアウト" style="background-color: #555; color: white; padding: 6px 14px; border-radius: 5px; border: none; cursor: pointer;">
  </form>
  </div>

  <div class="welcome">
    <?php echo "ようこそ " . ($_SESSION["name"]) . " さん"; ?>
  </div>

<?php
// DB接続
$dsn = 'mysql:dbname=データベース名;host=localhost';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//消去処理
if (isset($_POST["delete"])) {
    $delete_id = $_POST["delete_id"];
    $sql = 'delete from photo_diary where id = :id';
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
      $stmt->execute();
}

//編集押したときの処理
if (isset($_POST["edit"])) {
    $edit_id = $_POST["edit_id"];
    $sql = "SELECT * FROM photo_diary WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $edit_id, PDO::PARAM_INT);
    $stmt->execute();
    $edit_row = $stmt->fetch();

    if ($edit_row) {
        $edit_photo = ($edit_row['photo']);
        $edit_comment = htmlspecialchars($edit_row['comment']);
    }
}
//ここは編集する処理
if (!empty($_FILES["photo"]["name"]) && !empty($_POST["comment"]) && !empty($_POST["post_edit_id"])){
    $photo = basename($_FILES["photo"]["name"]);
    $date = date("Y-m-d H:i:s");
    $id = $_POST["post_edit_id"];  // 変更する投稿番号
    $new_comment = $_POST["comment"];  // 変更したい名前
    
    if (!file_exists("uploads")) mkdir("uploads");

    if (move_uploaded_file($_FILES["photo"]["tmp_name"], "uploads/" . $photo)) {
    $sql = 'UPDATE photo_diary SET comment=:comment, date =:date, photo =:photo, is_edited = 1 WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    
    $stmt->bindParam(':comment', $new_comment, PDO::PARAM_STR);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    $stmt->bindParam(':photo', $_FILES["photo"]["name"], PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    }else{
        echo "<div class='welcome' style='color:red;'>画像をアップロードできませんでした。</div>";
    }
}
//新規投稿処理
elseif(!empty($_FILES["photo"]["name"]) && !empty($_POST["comment"])) {
    $photo = basename($_FILES["photo"]["name"]);
    $comment = $_POST["comment"];
    $date = date("Y-m-d H:i:s");

    if (!file_exists("uploads")) mkdir("uploads");

    if (move_uploaded_file($_FILES["photo"]["tmp_name"], "uploads/" . $photo)) {
        $sql = "INSERT INTO photo_diary (user_id, user_name, comment, photo, date) VALUES (:user_id, :user_name, :comment, :photo, :date)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $_SESSION["user_id"], PDO::PARAM_STR);
        $stmt->bindParam(':user_name', $_SESSION["name"], PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':photo', $photo, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->execute();
        echo "「<div class='welcome'>" . htmlspecialchars($comment) . "」を受け付けました！</div>";
    } else {
        echo "<div class='welcome' style='color:red;'>画像をアップロードできませんでした。</div>";
    }
}elseif (isset($_POST["submit"])) {
    echo "<div class='welcome' style='color:red;'>写真またはコメントが入力されていません。</div>";
}

?>
<div class="form-container">
    <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="post_edit_id" value= "<?php echo $edit_id; ?>">
      <input type="file" name="photo" accept="image/*" value="<?php echo $edit_photo; ?>" required><br>
      <input type="text" name="comment" placeholder="本日の一言" value="<?php echo $edit_comment; ?>" required><br>
      <input type="submit" name="submit" value="投稿">
    </form>
</div>
<?php
// 表示処理
$sql = 'SELECT * FROM photo_diary ORDER BY id DESC';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();

foreach ($results as $row) {
    echo '<div class="entry-card">';
    echo '<div class="entry-meta"><strong style="color: #000;">' . $row['user_name'] . ' </strong>｜ ' . $row['date'] ;
    if (!empty($row['is_edited']) && $row['is_edited'] == 1) {
    echo '（編集済み）</div>';
    }else{
    echo '</div>';
    }

    if ($row['user_id'] == $_SESSION["user_id"]): ?>
      <!-- 削除ボタンのフォーム -->
    <form method="post" style="display: inline;">
  　<input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
    <input type="submit" name="delete" value="削除" style="background-color: red; color: white; padding: 5px 10px; border-radius: 5px; border: none;">
    </form>

    <!-- 編集ボタンのフォーム -->
    <form method="post" style="display: inline;">
    <input type="hidden" name="edit_id" value="<?= $row['id'] ?>">
    <input type="submit" name="edit" value="編集" style="background-color: fff3cd; color: white; padding: 5px 10px; border-radius: 5px; border: none; margin-left: 10px;">
    </form>
    <?php endif;
    echo '<div class="entry-photo"><img src="uploads/' . htmlspecialchars($row['photo']) . '" alt="投稿画像"></div>';
    echo '<div class="entry-comment">' . htmlspecialchars($row['comment']) . '</div>';
    echo '</div>';
}
    echo '</div>';
?>

</body>
</html>