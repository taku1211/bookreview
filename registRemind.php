<?php

//共通変数・関数を読み込み
require('function.php');

//デバッグ開始
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「登録情報確認ページ」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//セッション情報の表示
debug('セッション情報：'.print_r($_SESSION, true));

//POST送信されていた場合
//セッションに入れていたユーザー情報を変数に代入

if(!empty($_POST)){

  $name = $_POST['name_re'];
  $email = $_POST['email_re'];
  $pass = $_POST['pass_re'];

  debug('POST送信の中身：'.print_r($_POST,true));

  //例外処理
  try {
    //データベースへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = "INSERT INTO users(username, email, password, login_time, create_date) VALUES(:username, :email, :pass, :login_time, :create_date)";
    $data = array(':username'=> $name, ':email'=> $email, ':pass'=> password_hash($pass, PASSWORD_DEFAULT), ':login_time'=> date('Y-m-d H:i:s'),':create_date'=> date('Y-m-d H:i:s'));
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      debug('クエリに成功しました。会員登録完了');
      debug("ログイン認証を更新します");
      //ログイン有効期限を設定（一時間）
      $sesLimit = 60*60;
      //最終ログイン時間を現在の時間にする
      $_SESSION['login_time'] = time();
      $_SESSION['login_limit'] = $sesLimit;
      //ユーザーIDを格納する
      $_SESSION['user_id'] = $dbh->lastInsertId();

      debug('セッション変数の中身：'.print_r($_SESSION,true));
      debug('ログインページへ遷移します。');
      $_SESSION['msg-success'] = SUC11;
      header("Location:mypage.php");
    }else{
      debug('クエリに失敗しました。');
      $err_msg['common'] = MSG07;
    }
  } catch (\Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');


 ?>



<!-- head部分-->
<?php
$siteTitle = '登録情報確認ページ';
  require('head.php');
 ?>

<!-- header部分-->
<?php
  require('header.php');
 ?>

    <div class="page-login">
      <div class="login-container">
        <h2>登録情報確認</h2>
      </div>
    </div>
        <div class="login-form">
          <div class="form-container">
            <form class="form form_re" action="" method="post">
              <p>
              <label for="name">ニックネーム</label> <span>※必須</span> <br>
              <input type="text" name="name_re" placeholder="田中太郎" value="<?php if(!empty($_SESSION['name'])) echo $_SESSION['name'] ?>">
              <?php if(!empty($_SESSION['name'])) echo $_SESSION['name']; ?>
            </p><br>
              <p>
              <label for="email">メールアドレス</label> <span>※必須</span> <br>
              <input type="text" name="email_re" placeholder="example@mail.co.jp" value="<?php if(!empty($_SESSION['email'])) echo $_SESSION['email'] ?>">
              <?php if(!empty($_SESSION['email'])) echo $_SESSION['email']; ?>
            </p><br>
              <p>
              <label for="password">パスワード</label><span>※必須</span><br>
              <input type="password" name="pass_re" placeholder="8文字以上の英数字" value="<?php if(!empty($_SESSION['pass'])) echo $_SESSION['pass'] ?>">
              ●●●●●●●●（※セキュリティ保持のため表示していません。）
            </p><br>
              <div class="button-area2">
                <button type="button" name="button" onclick="locattion.href='userRegist.php'">入力画面へ戻る</button>
                <input type="submit" name="submit" value="登録する">
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
