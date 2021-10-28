<?php

//共通変数・関数を読み込み
require('function.php');

//デバッグ開始
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「パスワード変更用Email送信ページ」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証は不要（ログインできない人が使うページなので）

//POST送信された場合
if(!empty($_POST)){

  debug('POST送信があります。');
  debug('POST送信の中身：'.print_r($_POST,true));

  //POST情報を変数に代入
  $email = $_POST['email'];
  //バリデーションチェック（未入力チェック＋Email形式チェック＋最大文字数チェック）
  validRequired($email, 'email');
  validEmail($email, 'email');
  validMaxLen($email, 'email');

  if(empty($err_msg)){
    debug('バリデーションＯＫです。');

    //例外処理
    try {
      //DB接続
      $dbh = dbConnect();
      //SQL文作成
      $sql = 'SELECT id FROM users WHERE email = :email AND delete_flg = 0';
      $data = array(':email' => $email);
      //SQL文実行
      $stmt = queryPost($dbh, $sql, $data);
      //SQL文の検索結果の値を取得
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      if($stmt && array_shift($result)){
        debug('一致するEmailを持つユーザーがいます。：'.print_r($result['id'], true));
        $_SESSION['msg-success'] = SUC03;
        $_SESSION['email'] = $email;

        //パスワード更新画面へ
        header("Location:passRenew.php");
      }else{
        debug('一致するユーザーがいません。');
        $err_msg['email'] =　MSG18;
      }
    } catch (\Exception $e) {
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG07;
    }

  }
}
debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
 ?>


<!-- head部分-->
<?php
$siteTitle = 'パスワード再送信ページ';
  require('head.php');
 ?>

<!-- header部分-->
<?php
  require('header.php');
 ?>


    <div class="page-login">
      <div class="login-container">
        <h2>パスワード変更画面</h2>
      </div>
    </div>
        <div class="login-form">
          <div class="form-container">
            <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
            <form class="form" action="" method="post">
              <p>登録したメールアドレスを入力してください。</p>
              <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
              <p>
              <label for="email">メールアドレス</label><br>
              <input type="text" name="email" placeholder="example@mail.co.jp" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
              </p>
              <p>
              <input type="submit" name="submit" value="次へ進む">
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
