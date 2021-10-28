<?php
//共通変数・関数を読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('退会ページ「「「「「「「「「「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//POST送信されていた場合（退会ボタンを押した場合）
if(!empty($_POST)){
  $delete_flg = 1;

  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //SQL文を作成
    $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :u_id';
    $sql2 = 'UPDATE book_post SET delete_flg = 1 WHERE user_id = :u_id';
    $sql3 = 'UPDATE favorite SET delete_flg = 1 WHERE user_id = :u_id';
    $data = array(':u_id' => $_SESSION['user_id']);
    //SQL文実行
    $stmt1 = queryPost($dbh, $sql1, $data);
    $stmt2 = queryPost($dbh, $sql2, $data);
    $stmt3 = queryPost($dbh, $sql3, $data);

    //クエリ成功の場合
    if($stmt1 && $stmt2 && $stmt3){
      //セッションを削除する
      session_destroy();
      //セッション削除後のセッションを確認
      debug('セッション削除後のセッション情報：'.print_r($_SESSION, true));
      //トップページへ遷移
      $_SESSION['msg-success'] = SUC09;
      header("Location:index.php");
    }else{
      //クエリ失敗の場合
      debug('クエリ（退会処理）に失敗しました。');
      $err_msg['common'] = MSG07;
    }
  } catch (\Exception $e) {
    error_log('エラーが発生しました。：'.$e->getMessage());
    $err_msg['common'] = MSG07;
  }
debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
}


debug('画面表示処理終了＜＜＜＜＜＜＜＜＜＜＜＜＜＜＜＜＜');
 ?>

<!-- head部分-->
<?php
$siteTitle = '退会ページ';
  require('head.php');
 ?>

<!-- header部分-->
<?php
  require('header.php');
 ?>

    <div class="page-login">
      <div class="login-container">
        <h2>退会確認ページ</h2>
      </div>
    </div>
        <div class="login-form">
          <div class="form-container">
            <form class="form form_re" action="" method="post">
              <p>退会すると会員情報はすべて削除されます。<br>
                　本当に退会しますか？
               </p>
              <div class="button-area2">
                <button type="button" name="button"><a class="button-link" href="mypage.php">退会しない</a></button>
                <input type="submit" name="submit" value="退会する">
              </div>
            </form>
            <?php
              require('js-to-top-button.php');
             ?>

          </div>
        </div>

        <!-- footer部分-->
        <?php
        require('footer.php');
         ?>
