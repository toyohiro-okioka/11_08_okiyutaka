<?php

// 送信データのチェック
// var_dump($_GET);
// exit();
session_start();
// 関数ファイルの読み込み
include("functions.php");
check_session_id();

// 送信データ受け取り
$id = $_GET["id"];

// DB接続
$pdo = connect_to_db();

// ファイルパスを取得
$sql = 'SELECT image FROM todo_table WHERE id=:id';

// SQL準備&実行
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

if ($status == false) {
  // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
  $error = $stmt->errorInfo();
  echo json_encode(["error_msg" => "{$error[2]}"]);
  exit();
} else {
  $record = $stmt->fetch(PDO::FETCH_ASSOC);
}

$get_dir = __FILE__;
$get_dir_path =  dirname($get_dir);
$deletefiles = $get_dir_path . "/" . $record["image"];

// DELETE文を作成&実行
$sql = "DELETE FROM todo_table WHERE id=:id";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

// データ登録処理後
if ($status == false) {
  // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
  $error = $stmt->errorInfo();
  echo json_encode(["error_msg" => "{$error[2]}"]);
  exit();
} else {

  // ファイル削除
  if (file_exists($deletefiles)) {
    unlink($deletefiles);
  }

  // 正常にSQLが実行された場合は一覧ページファイルに移動し，一覧ページの処理を実行する
  header("Location:todo_read.php");
  exit();
}
