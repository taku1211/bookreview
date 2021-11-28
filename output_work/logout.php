<?php
//共通変数・関数を読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('ログアウトページ「「「「「「「「「「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//セッションを破棄する
debug('ログアウトします。');
session_destroy();

//ログイン画面へ遷移する
debug('ログイン画面へ遷移します。');
$_SESSION['msg-success'] = SUC08;
header("Location:login.php");

 ?>
