<?php

// 環境変数を読み込むためのライブラリを自動で読み込み
require_once __DIR__ . '/vendor/autoload.php';

/**
 * データベースに接続し、リンクを返します。
 * 接続に失敗した場合はエラーログを記録し、スクリプトを終了します。
 * @return mysqli データベース接続リンク
 */
function dbConnect(){
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $dbHost = $_ENV['DB_HOST'];
    $dbUsername = $_ENV['DB_USERNAME'];
    $dbPassword = $_ENV['DB_PASSWORD'];
    $dbDatabese = $_ENV['DB_DATABASE'];

    $link = mysqli_connect($dbHost,$dbUsername,$dbPassword,$dbDatabese);

    if(!$link) {
        error_log('Error: データベース接続に失敗しました - ' . mysqli_connect_error());
        exit('データベース接続エラーが発生しました。しばらくしてから再度お試しください。');
    }
    mysqli_set_charset($link, "utf8mb4");
    return $link;
}

/**
 * アイスクリームのログをデータベースに登録します。
 * SQLインジェクション対策のためプリペアドステートメントを使用します。
 * @param mysqli $link データベース接続リンク
 * @param array $ice_cream_log 登録するアイスクリームのデータ
 * @return bool 登録が成功した場合はtrue、失敗した場合はfalse
 */
function create_log($link, $ice_cream_log)
{
    $sql = "INSERT INTO ice_cream_logs (ice_cream_name, maker, purchase_date, score, flavor_comment) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        error_log('Error: SQLプリペアに失敗しました - ' . mysqli_error($link));
        return false;
    }

    // パラメータをバインド: s=string, i=integer, d=double
    // scoreは数値なので 'd' (double) または 'i' (integer)
    mysqli_stmt_bind_param($stmt, "sssds",
        $ice_cream_log['ice_cream_name'],
        $ice_cream_log['maker'],
        $ice_cream_log['purchase_date'],
        $ice_cream_log['score'],
        $ice_cream_log['flavor_comment']
    );

    $result = mysqli_stmt_execute($stmt);

    if (!$result){
        error_log('Error: アイスの登録に失敗しました - ' . mysqli_stmt_error($stmt));
    }

    mysqli_stmt_close($stmt);
    return $result;
}

/**
 * アイスクリームのログデータをバリデーションします。
 * @param array $ice_cream_log バリデーションするデータ
 * @return array エラーメッセージの配列。エラーがない場合は空の配列。
 */
function validate($ice_cream_log)
{
    $errors = [];

    if (!mb_strlen($ice_cream_log['ice_cream_name'])) {
        $errors['ice_cream_name'] = 'アイスクリーム名を入力してください';
    } elseif (mb_strlen($ice_cream_log['ice_cream_name']) > 100) {
        $errors['ice_cream_name'] = 'アイスクリーム名は100文字以内で入力してください';
    }

    if (!mb_strlen($ice_cream_log['maker'])) {
        $errors['maker'] = 'メーカー名を入力してください';
    } elseif (mb_strlen($ice_cream_log['maker']) > 100) {
        $errors['maker'] = 'メーカー名は100文字以内で入力してください';
    }

    if (!strlen($ice_cream_log['purchase_date'])) {
        $errors['purchase_date'] = '購入日を入力してください';
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $ice_cream_log['purchase_date'])) {
        $errors['purchase_date'] = '購入日は「YYYY-MM-DD」形式で入力してください';
    }

    $score = (int)$ice_cream_log['score'];
    if ($score < 1 || $score > 5) {
        $errors['score'] = '点数は1〜5の整数を入力してください';
    }

    if (!mb_strlen($ice_cream_log['flavor_comment'])) {
        $errors['flavor_comment'] = '感想を入力してください';
    } elseif (mb_strlen($ice_cream_log['flavor_comment']) > 1000) {
        $errors['flavor_comment'] = '感想は1,000文字以内で入力してください';
    }

    return $errors;
}
