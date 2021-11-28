<?php

//共通変数・関数を読み込み
require('function.php');

//デバッグ開始
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「エラーページ」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//セッション情報の表示
debug('セッション情報：'.print_r($_SESSION, true));

debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');


 ?>



<!-- head部分-->
<?php
$siteTitle = 'エラーページ';
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
        <h2>予期せぬエラーが発生しました。</h2>
        <p>トップページへ戻ります。 <a href="index.php">トップページへ</a></p>
      </div>
      <?php
        require('js-to-top-button.php');
       ?>

    </div>

        <!-- footer部分-->
        <?php
        require('footer.php');
         ?>
