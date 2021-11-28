<?php
//共通変数・関数を読み込み
require('function.php');

//デバッグ開始
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「パスワード変更ページ」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証は不要（ログインできない人が使うページなので）

//POST送信された場合
if(!empty($_POST)){
  debug('POST送信があります。');

  //POST送信の中身を変数に代入
  $pass = $_POST['pass'];
  $pass2 = $_POST['pass2'];

  //セッションに保存しておいたemailアドレスを変数に代入
  $email = $_SESSION['email'];

  //バリデーションチェック（パスワード形式チェック＋一致しているかのチェック）
  validPass($pass, 'pass');
  validMatch($pass, $pass2, 'pass');

  if(empty($err_msg)){
    debug('バリデーションOKです。');

    //例外処理
    try {
      //DBへ接続
      $dbh = dbConnect();
      //SQL文作成
      $sql = 'UPDATE users SET password = :pass WHERE email = :email AND delete_flg = 0';
      $data = array(':pass' => password_hash($pass, PASSWORD_DEFAULT), ':email' => $email);
      //SQL文実行
      $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
        debug('クエリ成功。パスワードを更新しました。');
        //ログインぺー＾字へ遷移
        $_SESSION['msg-success'] = SUC01;
        header("Location:login.php");
      }else{
        debug('クエリに失敗しました。');
        $err_msg['common'] = MSG07;
      }

    } catch (\Exception $e) {
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG07;

    }

  }
}
debug('画面表示処理終了＜＜＜＜＜＜＜＜＜＜＜＜＜＜＜＜');
 ?>


<!-- head部分-->
<?php
$siteTitle = 'パスワード再登録ページ';
  require('head.php');
 ?>

<!-- header部分-->
<?php
  require('header.php');
 ?>


    <div class="page-login">
      <div class="login-container">
        <h2>パスワード再登録画面</h2>
      </div>
    </div>
        <div class="login-form">
          <div class="form-container">
            <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
            <form class="form" action="" method="post">
              <p>新しいパスワードを入力してください。</p>
              <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
              <p>
              <label for="pass">新しいパスワード</label><span>※必須</span><br>
              <input type="password" name="pass" value="" placeholder="8文字以上の英数字">
              </p>
              <p>
              <label for="pass2">新しいパスワード（再入力）</label><span>※必須</span><br>
              <input type="password" name="pass2" value="" placeholder="8文字以上の英数字">
              </p>
              <p>
              <input type="submit" name="submit" value="パスワード再登録">
              </p>

            </form>
          </div>
          <?php
            require('js-to-top-button.php');
           ?>

        </div>
        <!-- footer部分-->
        <?php
        require('footer.php');
         ?>
