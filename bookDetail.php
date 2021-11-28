<?php
//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「');
debug('商品詳細ページ」');
debug('「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//=============================================
//画面処理
//=============================================

//画面表示用データ取得
//=============================================
//商品IDのGETパラメータを取得
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
$p_category = (!empty($_GET['p_category'])) ? $_GET['p_category'] : '';
//DBから商品情報を取得する
$viewData = getProductOne($p_id);
debug('取得したp_id：'.$p_id);
debug('取得したp_category：'.$p_category);
debug('取得したviewData：'.print_r($viewData, true));
//パラメータに不正な値が入っているか確認
if(empty($viewData)){
  error_log('エラー発生：指定ページに不正な値が入りました。');
  header("Location:errorPage.php");
}
if(strcmp($p_category, "mypost") !== 0){
  if(strcmp($p_category, "favorite") !== 0){
    if(strcmp($p_category, "") !== 0){
      error_log('エラー発生：指定ページに不正な値が入りました。');
      header("Location:errorPage.php");
    }
  }
}
 ?>


<!-- head部分-->
<?php
$siteTitle = '記事詳細ページ';
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

    <div class="page-main">
      <div class="main-container product-main-container">
        <p><a href="<?php if($p_category == 'mypost' or $p_category == 'favorite'){echo 'mypage.php';}else{echo 'index.php'.appendGetParam(array('p_id'));}  ?> ">一覧ページへ戻る</a></p>

        <h2><?php echo sanitize($viewData['title']); ?></h2>
        <div class="favorite-area">
          <i class="fas fa-star js-click-like <?php if(isLike($_SESSION['user_id'], $viewData['id'])){
            echo 'active';} ?>" aria-hidden="true" data-productid="<?php echo sanitize($viewData['id']); ?>"></i>
        </div>
        <div class="product-img-container">
          <div class="mobile-main">
            <div class="main-img">
              <img src="<?php echo showImg(sanitize($viewData['pic1'])); ?>" alt="">
            </div>
            <div class="book-info">
              <h3><?php echo '書籍名：'.sanitize($viewData['bookname']); ?> </h3>
              <p><?php echo '著者名：'.sanitize($viewData['author']); ?></p>
              <p><?php echo '著者名：'.sanitize($viewData['publisher']); ?></p>
              <p>お勧め度：<span class="recommend">
                <?php
              if($viewData['evalution'] == 5){
                echo '★★★★★';
              }elseif($viewData['evalution'] == 4){
                echo '★★★★';
              }elseif($viewData['evalution'] == 3){
                echo '★★★';
              }elseif($viewData['evalution'] == 2){
                echo '★★';
              }elseif($viewData['evalution'] == 1){
                echo '★';
              } ?></span></p>
              <p><?php echo 'ジャンル：'.sanitize($viewData['category']); ?></p>
              <p><?php echo '投稿者：'.sanitize($viewData['reviewer']); ?></p>
              <p><?php echo '投稿日時：'.sanitize($viewData['create_date']); ?></p>
            </div>
          </div>
          <div class="sub-img">
            <img src="<?php echo showImg(sanitize($viewData['pic2'])); ?>" alt="">
            <img src="<?php echo showImg(sanitize($viewData['pic3'])); ?>" alt="">
            <img src="<?php echo showImg(sanitize($viewData['pic4'])); ?>" alt="">
            <img src="<?php echo showImg(sanitize($viewData['pic4'])); ?>" alt="">
          </div>
        </div>

          <div class="book-detail">
            <div class="book-info">
              <h3><?php echo '書籍名：'.sanitize($viewData['bookname']); ?> </h3>
              <p><?php echo '著者名：'.sanitize($viewData['author']); ?></p>
              <p><?php echo '著者名：'.sanitize($viewData['publisher']); ?></p>
              <p>お勧め度：
                <?php
              if($viewData['evalution'] == 5){
                echo '★★★★★';
              }elseif($viewData['evalution'] == 4){
                echo '★★★★';
              }elseif($viewData['evalution'] == 3){
                echo '★★★';
              }elseif($viewData['evalution'] == 2){
                echo '★★';
              }elseif($viewData['evalution'] == 1){
                echo '★';
              } ?></p>
              <p><?php echo 'ジャンル：'.sanitize($viewData['category']); ?></p>
            </div>
            <div class="book-summary">
              <h3>書籍概要</h3>
              <p><?php echo sanitize($viewData['summary']); ?></p>
            </div>
          </div>
            <div class="comment-area">
              <h3>投稿者の感想</h3>
              <p><?php echo sanitize($viewData['review']); ?></p>
            </div>
      </div>
      <?php
        require('js-to-top-button.php');
       ?>

    </div>
    <!-- footer部分-->
    <?php
    require('footer.php');
     ?>
