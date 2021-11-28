<?php
//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「');
debug('Ajaxページ」');
debug('「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

if(isset($_POST['productId']) && isset($_SESSION['user_id']) && isLogin()){
  debug('POST送信があります。');
  $p_id = $_POST['productId'];
  $u_id = $_SESSION['user_id'];
  debug('商品ID:'.$p_id);

  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = 'SELECT * FROM favorite WHERE product_id = :p_id AND user_id = :u_id';
    $data = array(':u_id' => $u_id, ':p_id' => $p_id);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    $resultCount = $stmt->rowCount();
    debug('取得したレコードの列数:'.$resultCount);
    //レコードが1件でもある場合
    if(!empty($resultCount)){
      //レコードを削除する
      $sql = 'DELETE FROM favorite WHERE product_id = :p_id AND user_id = :u_id';
      $data = array(':u_id'=> $u_id, ':p_id'=> $p_id);
      //クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
      debug('お気に入りから削除しました。');
      $_SESSION['msg-success'] = SUC06;
    }else{
      $sql = 'INSERT INTO favorite (product_id, user_id, create_date) VALUES (:p_id, :u_id, :date)';
      //レコードを挿入する
      $data = array(':p_id'=>$p_id, ':u_id'=>$u_id, ':date'=> date('Y-m-d H:i:s'));
      //クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
      debug('お気に入りに登録しました。');
      $_SESSION['msg-success'] = SUC05;
    }
  } catch (\Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}
debug('Ajax処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
 ?>
