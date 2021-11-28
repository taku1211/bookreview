<?php

require('function.php');

//デバッグ開始
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「新規会員登録ページ」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();



//1.POST送信されているとき
if(!empty($_POST)){
  debug('POST送信されています。');

  //POSTの内容を変数に代入

  $name = $_POST['name'];
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass2 = $_POST['pass2'];

  //バリデーションチェック（未入力）
  validRequired($name, 'name');
  validRequired($email, 'email');
  validRequired($pass, 'pass');
  validRequired($pass2, 'pass2');

  //バリデーションチェック（ニックネーム）
  //1.最大文字数チェック
  validMaxLen($name, 'name');

  //バリデーションチェック（Email形式チェック・Email重複チェック）
  validEmail($email, 'email');
  validEmailDump($email);

  //バリデーションチェック（パスワード半角チェック・パスワード一致チェック）
  validPass($pass, 'pass');
  validMatch($pass, $pass2, 'pass2');

  //バリデーションOK
  if(empty($err_msg)){
    debug('バリデーションOK');
    $_SESSION['name'] = $name;
    $_SESSION['email'] = $email;
    $_SESSION['pass'] = $pass;
    $_SESSION['pass2'] = $pass2;

    debug('セッション情報：'.print_r($_SESSION['name'], true));

    debug('登録情報確認画面へ遷移します。');
    header("Location:registRemind.php");
  }else{
    debug('途中でエラーが発生しました。');
  }
}
 ?>

<!-- head部分-->
<?php
$siteTitle = '新規会員登録ページ';
  require('head.php');
 ?>

<!-- header部分-->
<?php
  require('header.php');
 ?>


    <div class="page-login">
      <div class="login-container">
        <h2>新規会員登録ページ</h2>
      </div>
    </div>
        <div class="login-form">
          <div class="form-container">
            <form class="form" action="" method="post">
              <p>
                <?php if(!empty($err_msg['name'])) echo $err_msg['name']; ?>
              <label for="name">ニックネーム</label> <span>※必須</span> <br>
              <input type="text" name="name" placeholder="田中太郎" value="<?php if(!empty($_POST['name'])) echo $_POST['name']; ?>">
              </p>
              <p>
                <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
              <label for="email">メールアドレス</label> <span>※必須</span> <br>
              <input type="text" name="email" placeholder="example@mail.co.jp" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
              </p>
              <p>
                <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
              <label for="pass">パスワード</label><span>※必須</span><br>
              <input type="password" name="pass" value="" placeholder="8文字以上の英数字">
              </p>
              <p>
                <?php if(!empty($err_msg['pass2'])) echo $err_msg['pass2']; ?>
              <label for="pass2">パスワード（再入力）</label><span>※必須</span><br>
              <input type="password" name="pass2" value="" placeholder="8文字以上の英数字">
              </p>
              <p>
              <input type="submit" name="submit" value="確認画面へ">
              </p>

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
