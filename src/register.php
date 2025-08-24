<?php

// データベース関連の共通関数を読み込む
require_once __DIR__ . '/db_functions.php';

// フォーム処理と表示のための初期化
$icecream = [
    'ice_cream_name' => '',
    'maker' => '',
    'purchase_date' => '',
    'score' => '',
    'flavor_comment' => ''
];
$errors = []; // エラーメッセージを格納する配列

// POSTリクエストの場合の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームから送信されたデータを取得
    $icecream = [
        'ice_cream_name' => $_POST['ice_cream_name'] ?? '',
        'maker' => $_POST['maker'] ?? '',
        'purchase_date' => $_POST['purchase_date'] ?? '',
        'score' => $_POST['score'] ?? '',
        'flavor_comment' => $_POST['flavor_comment'] ?? ''
    ];

    // バリデーションを実行
    $errors = validate($icecream);

    // エラーがなければデータベースに登録し、一覧ページへリダイレクト
    if (empty($errors)) {
        $link = dbConnect();
        if (create_log($link, $icecream)) {
            mysqli_close($link);
            header("Location: index.php");
            exit;
        } else {
            // DB登録失敗時のエラーメッセージを追加
            $errors['db_error'] = 'アイスの登録に失敗しました。';
        }
        mysqli_close($link); // エラーの場合も接続は閉じる
    }
    // エラーがある場合は、エラーメッセージと入力値を表示
}
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <?php require_once __DIR__ . '/_head.php'; ?>
  </head>
  <body>

    <div class="container">
      <?php require_once __DIR__ . '/_header.php'; ?>
      <main class="main_section">
        <form action="register.php" method="post">
            <?php if (!empty($errors)) : ?>
                <ul class="error-messages">
                    <?php foreach ($errors as $error_key => $error_message) : ?>
                        <li><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <div class="form-group">
                <label for="ice_cream_name">アイスの名前</label>
                <input type="text" name="ice_cream_name" id="ice_cream_name" class="form-control"
                value="<?php echo htmlspecialchars($icecream['ice_cream_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <?php if (isset($errors['ice_cream_name'])) : ?>
                    <p class="error-inline"><?php echo htmlspecialchars($errors['ice_cream_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="maker">アイスメーカー</label>
                <input type="text" name="maker" id="maker" class="form-control"
                value="<?php echo htmlspecialchars($icecream['maker'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <?php if (isset($errors['maker'])) : ?>
                    <p class="error-inline"><?php echo htmlspecialchars($errors['maker'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="purchase_date">いつ食べた？</label>
                <input type="date" name="purchase_date" id="purchase_date" class="form-control"
                value="<?php echo htmlspecialchars($icecream['purchase_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <?php if (isset($errors['purchase_date'])) : ?>
                    <p class="error-inline"><?php echo htmlspecialchars($errors['purchase_date'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="score">点数（5点満点の整数）</label>
                <input type="number" name="score" id="score" class="form-control" min="1" max="5" step="1"
                value="<?php echo htmlspecialchars($icecream['score'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <?php if (isset($errors['score'])) : ?>
                    <p class="error-inline"><?php echo htmlspecialchars($errors['score'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="flavor_comment">ひとくちメモ</label>
                <textarea name="flavor_comment" id="flavor_comment" class="form-control" rows="5"><?php echo htmlspecialchars($icecream['flavor_comment'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                <?php if (isset($errors['flavor_comment'])) : ?>
                    <p class="error-inline"><?php echo htmlspecialchars($errors['flavor_comment'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary">アイスを登録する</button>
            </div>
        </form>
      </main>
    </div>
    <?php require_once __DIR__ . '/_footer.php'; ?>
  </body>
</html>
