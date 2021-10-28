<?php

require('function.php');

//デバッグ開始
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「ログインページ」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証実施
require('auth.php');

//POST送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります。');

//送信情報を変数に代入
$email = $_POST['email'];
$pass = $_POST['pass'];
$pass_save = (!empty($_POST['pass_save'])) ? true : false;

debug('POST送信の中身：'.print_r($_POST,true));

//バリデーションチェック
//1.未入力チェック
validRequired($email, 'email');
validRequired($pass, 'pass');

//2.Email形式チェック・Email最大文字数チェック
validEmail($email, 'email');
validMaxLen($email, 'email');

//3.パスワード形式チェック
validPass($pass,'pass');

  if(empty($err_msg)){
    debug('バリデーションOKです。');

    //例外処理
    try {
      //データベースへ接続
      $dbh = dbConnect();
      //SQL文作成
      $sql = 'SELECT password, id FROM users WHERE email = :email AND delete_flg = 0';
      $data = array(':email' => $email);
      //SQL文実行
      $stmt = queryPost($dbh, $sql, $data);

      //取得した値を展開、出力
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      debug('クエリ結果の中身：'.print_r($result, true));

      if(!empty($result) && password_verify($pass, $result['password'])){
        debug('パスワードが一致しました。');

        //最終ログイン時間を現在の時刻に設定
        $_SESSION['login_time'] = time();
        //ログイン保持にチェックがあるかどうか
        if($pass_save){
          //ログイン保持にチェックがある場合
          //ログインリミットを30日に設定
          debug('ログイン保持にチェックがあります。');
          $_SESSION['login_limit'] = 60*60*24*30;
        }else{
          //ログイン保持にチェックがない場合
          //ログインリミットを1時間に設定
          debug('ログイン保持にチェックがありません。');
          $_SESSION['login_limit'] = 60*60;
        }

        //ログインIDをセッションに格納
        $_SESSION['user_id'] = $result['id'];
        debug('セッション変数の中身：'.print_r($_SESSION, true));

        //マイページへ遷移
        debug('マイページへ遷移します。');
        $_SESSION['msg-success'] = SUC07;
        header("Location:mypage.php");
        exit;
      }else{
        debug('Emailもしくはパスワードが違います。');
        $err_msg['common'] = MSG09;
      }
    }catch(\Exception $e){
      err_log('エラー発生：'. $e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
 ?>

<!-- head部分-->
<?php
$siteTitle = 'ログインページ';
  require('head.php');
 ?>

<!-- header部分-->
<?php
  require('header.php');
 ?>
 <!-- JSエリアメッセージ-->
 <?php
 require('js-msg.php');
  ?>


    <div class="page-login">
      <div class="login-container">
        <h2>ログイン画面</h2>
      </div>
    </div>
        <div class="login-form">
          <div class="form-container">
            <div class="area-msg">
              <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
            </div>
            <form class="form" action="" method="post">
              <p>
              <label for="email" class="<?php if(!empty($err_msg['email'])) echo 'err' ?>">メールアドレス<br>
              <input type="text" name="email" placeholder="example@mail.co.jp" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
              </label>
              </p>
              <div class="area-msg">
                <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
              </div>

              <p>
              <label for="password" class="<?php if(!empty($err_msg['pass'])) echo 'err' ?>">パスワード<br>
              <input type="password" name="pass" value="" placeholder="8文字以上の英数字">
              </label>
              </p>
              <div class="area-msg">
                <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
              </div>
              <p>
              <input type="checkbox" name="pass_save">ログイン状態を保持する
              </p>
              <div class="pass-remind">
                <a href="passRemindSend.php">パスワードを忘れた方はこちら</a>
              </div>
              <p>
              <input type="submit" name="submit" value="ログイン">
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
