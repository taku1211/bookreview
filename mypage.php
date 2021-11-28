<?php
//共通変数・関数を読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('マイページ「「「「「「「「「「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// 画面表示用データを取得
$span = 3;
$u_id = $_SESSION['user_id'];
$myProduct = getMyProduct($u_id);
$myLike = getMyLike($u_id);
$myUser = getMyUser($u_id);

debug('取得したユーザー情報：'.$u_id);
debug('取得した自分の記事情報：'.print_r($myProduct, true));
debug('取得した自分のお気に入り情報：'.print_r($myLike, true));
debug('取得した自分のユーザー情報：'.print_r($myUser, true));


debug('画面表示処理終了＜＜＜＜＜＜＜＜＜＜＜＜＜＜＜＜＜');
 ?>

<?php
$siteTitle = 'マイページ';
require('head.php');
require('header.php');
 ?>
 <!-- JSエリアメッセージ-->
 <?php
 require('js-msg.php');
  ?>

<div class="mypage-img">
  <div class="mypage-img-container">
    <img src="img/book-254048_1920.jpg" alt="">
  </div>
</div>

<div class="mypage-main">
  <div class="mypage-main-container">
    <div class="mypost-area">
      <h2>My Post Artcile</h2>
      <div class="grid-area">
        <?php
        if(!empty($myProduct)){
            foreach ($myProduct as $key => $val) {
              ?>
              <div class="index-item">
                <a href="bookDetail.php<?php if(!empty(appendGetParam())){
                  echo appendGetParam().'&p_id='.$val['id'].'&p_category=mypost';
                }else{
                  echo '?p_id='.$val['id'].'&p_category=mypost';
                }?>">
                 <div class="index-image">
                  <img src="<?php echo showImg(sanitize($val['pic1'])); ?>" alt="">
                 </div>
                 <div class="index-text">
                  <h3><?php  echo sanitize($val['title']); ?></h3>
                 </div>
                 <div class="index-tag">
                  <span><?php echo sanitize($val['publisher']); ?></span>
                 </div>
                </a>
              </div>
        <?php
            }
        }
         ?>
      </div>
      <div class="link-area">
        <a href="mypagePostList.php?p_category=mypost">全ての自分の投稿へ</a>
      </div>
      <h2 class="favorite-title">My Favorite Artcile</h2>
      <div class="grid-area">
        <?php
        if(!empty($myLike)){
            foreach ($myLike as $key => $val) {
              ?>
              <div class="index-item">
                <a href="bookDetail.php<?php if(!empty(appendGetParam())){
                  echo appendGetParam().'&p_id='.$val['id'].'&p_category=favorite';
                }else{
                  echo '?p_id='.$val['id'].'&p_category=favorite';
                }?>">
                 <div class="index-image">
                  <img src="<?php echo showImg(sanitize($val['pic1'])); ?>" alt="">
                 </div>
                 <div class="index-text">
                  <h3><?php  echo sanitize($val['title']); ?></h3>
                 </div>
                 <div class="index-tag">
                  <span><?php echo sanitize($val['publisher']); ?></span>
                 </div>
                </a>
              </div>
        <?php
            }
        }
         ?>
      </div>
      <div class="link-area">
        <a href="mypagePostList.php?p_category=favorite">全てのお気に入りの投稿へ</a>
      </div>


    </div>
    <div class="menu-area">
      <div class="flex-left">
        <h2>My Page Menu</h2>
        <ul class="nav-menu">
          <li><a href="index.php">トップページ</a></li>
          <li><a href="mypage.php">マイページトップ</a></li>
          <li><a href="mypagePostList.php?p_category=mypost">投稿記事一覧</a></li>
          <li><a href="mypagePostList.php?p_category=favorite">お気に入り記事一覧</a></li>
          <li><a href="registProduct.php">新しく記事を投稿する</a></li>
          <li><a href="mypagePostList.php?p_category=mypost&editflg=1">投稿した記事を編集する</a></li>
          <li><a href="profEdit.php">プロフィールを編集する</a></li>
          <li><a href="passRemindSend.php">パスワードを変更する</a></li>
          <li><a href="logout.php">ログアウトする</a></li>
          <li><a href="withdrawal.php">退会する</a></li>
        </ul>
      </div>
      <div class="flex-right">
        <h2>My Profile</h2>
        <div class="my-profimg-area">
          <img src="<?php echo showImg(sanitize($myUser['pic'])); ?>" alt="">
          <p>ニックネーム：<?php echo sanitize($myUser['username']);  ?> </p>
        </div>
      </div>
    </div>
    <?php
      require('js-to-top-button.php');
     ?>

  </div><!-- main-container-->

</div><!-- page-main-->







 <?php
require('footer.php');
  ?>
