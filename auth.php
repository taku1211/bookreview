<?php

//自動ログイン認証・自動ログアウト

//ログインしている場合
debug('ログイン認証開始');
debug('セッション情報：'.print_r($_SESSION, true));
if(!empty($_SESSION['login_time'])){
  if($_SESSION['login_time'] + $_SESSION['login_limit'] > time()){
    debug('ログイン有効期限内です。');
    $_SESSION['login_time'] = time();
    if(basename($_SERVER['PHP_SELF']) === 'login.php'){
      debug('ログインページをスキップしてマイページへ遷移します。');
      header("Location:index.php");
    }
  }else{
    debug('ログイン有効期限外です。');
    //セッションを破棄する
    session_destroy();
    debug('ログインページへ遷移します。');
    header("Location:login.php");
  }

}else{
  debug('未ログインユーザーです。');
  if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
    header("Location:login.php");
  }
}
 ?>
