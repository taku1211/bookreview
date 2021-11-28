<?php
//共通変数・関数を読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('マイページ投稿一覧ページ「「「「「「「「「「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//  投稿一覧ページか、編集画面化を判断
$editFlg = (!empty($_GET['editflg'])) ? 1 : '';
//GET情報を取得
$category = (!empty($_GET['p_category'])) ? $_GET['p_category'] : '';
// 画面表示用データを取得
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;
debug('現在の投稿一覧ページのページ数は'.$currentPageNum);
//記事表示件数を設定
$listSpan = 6;
//現在のページの最初のレコード数を設定
$currentMinNum = (($currentPageNum - 1) * $listSpan);
$u_id = $_SESSION['user_id'];
$myProduct = getMyProductList($u_id, $currentMinNum, $listSpan);
$myLike = getMyLikeList($u_id, $currentMinNum, $listSpan);
$myUser = getMyUser($u_id);

//お気に入り一覧か自分の投稿一覧かを判断してページング用のデータを取得

debug('取得したユーザー情報：'.$u_id);
debug('取得した自分の記事情報：'.print_r($myProduct, true));
debug('取得した自分のお気に入り情報：'.print_r($myLike, true));
debug('取得した自分のユーザー情報：'.print_r($myUser, true));

if(strcmp($category, "mypost") !== 0){
  if(strcmp($category, "favorite") !== 0){
      error_log('エラー発生：指定ページに不正な値が入りました。');
      header("Location:errorPage.php");
  }
}


debug('画面表示処理終了＜＜＜＜＜＜＜＜＜＜＜＜＜＜＜＜＜');
 ?>

<?php
$siteTitle = ($category == 'mypost') ?'投稿記事一覧' : 'お気に入り記事一覧';
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
      <?php if($category == 'mypost'){
        ?>
      <h2>My Post Artcile</h2>
      <?php
    }elseif($category== 'favorite'){
      ?>
      <h2>My Favorite Article</h2>
      <?php
    }
      ?>
      <div class="grid-area">
        <?php
        if($category == 'mypost'){
            foreach($myProduct['data'] as $key => $val){
              ?>
              <div class="index-item">
                <a href="<?php if($editFlg == 1){
                  if(!empty(appendGetParam())){
                    echo 'registProduct.php'.appendGetParam().'&p_id='.$val['id'];
                  }else{
                    echo 'registProduct.php'.'?p_id='.$val['id'];
                  }
                }else{
                  if(!empty(appendGetParam())){
                    echo 'bookDetail.php'.appendGetParam().'&p_id='.$val['id'];
                  }else{
                    echo 'bookDetail.php'.'?p_id='.$val['id'];
                  }
                } ?>">
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
        }elseif($category == 'favorite'){
          foreach($myLike['data'] as $key => $val){
            ?>
            <div class="index-item">
              <a href="bookDetail.php<?php if(!empty(appendGetParam())){
                echo appendGetParam().'&p_id='.$val['id'];
              }else{
                echo '?p_id='.$val['id'];
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
      <?php
      pagenation($currentPageNum, ($category == 'mypost') ? $myProduct['total_page'] : $myLike['total_page'], $link='&p_category='.$category);
       ?>
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
