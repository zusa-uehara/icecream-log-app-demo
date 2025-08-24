<?php

// データベース関連の共通関数を読み込む
require_once __DIR__ . '/db_functions.php';


// データベースに接続
$db_link = dbConnect();

$sql = "SELECT id, ice_cream_name, maker, purchase_date, score, flavor_comment FROM ice_cream_logs ORDER BY purchase_date DESC";

// クエリの実行
$result = mysqli_query($db_link, $sql);

// クエリ実行エラーの確認
if ($result === false) {
    // クエリの実行に失敗した場合のエラー処理
    error_log('SQL Error: ' . mysqli_error($db_link));

    // ユーザーへのエラー表示
    echo '<p>データベースからのデータ取得中にエラーが発生しました。</p>';

    $ice_cream_logs = [];
} else {
    // クエリが成功した場合、結果を全て連想配列の配列として取得
    $ice_cream_logs = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // 結果セット（mysqli_result オブジェクト）を解放
    mysqli_free_result($result);
}

// データベース接続を閉じる
mysqli_close($db_link);

?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <?php require_once __DIR__ . '/_head.php'; ?>
  </head>
  <body>
    <div class="container">
      <?php require_once __DIR__ . '/_header.php'; ?>

      <div class="solid-button-area">
        <a href='register.php'>アイス追加！</a>
      </div>
      <main>
        <?php if (!empty($ice_cream_logs) && is_array($ice_cream_logs)) : ?>
          <?php foreach ($ice_cream_logs as $ice_cream_log) : ?>
            <?php
              // 日付文字列（例: 'YYYY-MM-DD'）を取得
              $date_str = $ice_cream_log['purchase_date'] ?? '';
              // 日付をタイムスタンプに変換
              $timestamp = $date_str ? strtotime($date_str) : false;

              // 表示用に年、月日、曜日を整形
              $year = $timestamp ? date('Y', $timestamp) : 'YYYY';
              $month_day = $timestamp ? date('n.j', $timestamp) : 'M.D';
              $day_of_week = $timestamp ? date('D', $timestamp) : '---';

              // 英語の曜日略称を日本語に変換
              $day_of_week_jp = [
                  'Sun' => '日', 'Mon' => '月', 'Tue' => '火', 'Wed' => '水',
                  'Thu' => '木', 'Fri' => '金', 'Sat' => '土'
              ];
              $day_of_week_abbr = $day_of_week_jp[$day_of_week] ?? '？'; // 日本語の曜日略称
            ?>
            <section class="icecream-entry">
              <div class="entry-date">
                <div class="entry-year">
                  <?php echo htmlspecialchars($year, ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <div class="entry-month-day">
                  <?php echo htmlspecialchars($month_day, ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <div class="entry-day-of-week">
                  (<?php echo htmlspecialchars($day_of_week_abbr, ENT_QUOTES, 'UTF-8'); ?>)
                </div>
              </div>
              <div class="entry-content">
                <div class="entry-details">
                  <h2><?php echo htmlspecialchars($ice_cream_log['ice_cream_name'] ?? '名前なし', ENT_QUOTES, 'UTF-8'); ?></h2>
                  <div class="maker-score">
                    <p class="maker">メーカー: <?php echo htmlspecialchars($ice_cream_log['maker'] ?? '不明', ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="score">点数: <?php echo htmlspecialchars($ice_cream_log['score'] ?? '-', ENT_QUOTES, 'UTF-8'); ?> / 5</p>
                  </div>
                </div>
                <div class="entry-comment">
                  <h3>ひとくちメモ:</h3>
                  <p><?php echo nl2br(htmlspecialchars($ice_cream_log['flavor_comment'] ?? 'メモなし', ENT_QUOTES, 'UTF-8')); ?></p>
                </div>
              </div>
            </section>
          <?php endforeach; ?>
        <?php else : ?>
            <p class="no-logs-message">まだアイスの記録がありません。</p>
            <p class="no-logs-message">上の「アイス追加！」ボタンから最初の記録を登録してみましょう。</p>
        <?php endif; ?>
      </main>
    </div>
    <?php require_once __DIR__ . '/_footer.php'; ?>
  </body>
</html>
