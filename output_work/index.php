<?php
//共通変数・関数を読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('トップページ「「「「「「「「「「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//=====================================================
//画面処理
//=====================================================

//画面表示用データを取得
//=====================================================
//カレントページのGETパラメータを取得（デフォルトは1ページ目に設定）
$currentPageNum = (!empty($_GET['p'])) ? (int)$_GET['p'] : (int)1;
debug('ページ数は'.$currentPageNum);
//カテゴリーを取得
$category = (!empty($_GET['c_id'])) ? $_GET['c_id'] : 0;
//ソート順を取得
$sort = (!empty($_GET['sort'])) ? $_GET['sort'] : 0;

//パラメータに不正な値が入っていないか確認する
if(empty($currentPageNum)){
  error_log('エラー発生：指定ページに不正なページが入りました。エラーページへ遷移します。');
  header("Location:errorPage.php");
}
//表示件数の設定

if(!is_mobile()){
  $listSpan = 8;
  debug('PC用表示です。表示件数は8件です。');
}else{
  $listSpan = 6;
  debug('スマホ用表示です。表示件数は6件です。');
}
//現在の表示件数の最初の件数を取得
$currentMinNum = (($currentPageNum - 1)* $listSpan);
//DBから商品データを取得する
$dbProductData = getProductList($currentMinNum, $category, $sort, $listSpan);
//DBからカテゴリーデータを取得する
$dbCategoryData = getCategory();

debug('現在のページ：'.$currentPageNum);

debug('画面表示処理終了＜＜＜＜＜＜＜＜＜＜＜＜＜＜＜＜＜');
 ?>


<!-- head部分-->
<?php
$siteTitle = 'トップページ';
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

    <div class="top-image">
      <h1>Read Happiness!!</h1>
      <div class="image-container js-slideshow">
        <img src="img/book-254048_1920.jpg" alt="トップ画像">
        <img src="img/top-image2.jpg" alt="トップ画像">
        <img src="img/top-image3.jpg" alt="トップ画像">
        <img src="img/top-image4.jpg" alt="トップ画像">

      </div>
    </div>
    <div class="page-main">
      <div class="main-container">
        <h2>さあ、素敵な書籍との出会いを。 <br>
            １冊の本との出会いがあなたの生き方を変えるかもしれない!!
          </h2>
        <div class="columns">
          <h2>INDEX</h2>
          <div class="columns-main">
            <div class="home-index">
              <?php
              foreach ($dbProductData['data'] as $key => $val) {
               ?>
              <div class="index-item">
                <a href="bookDetail.php<?php
                  if(!empty(appendGetParam())){
                    echo appendGetParam().'&p_id='.$val['id'];
                  }else{
                    echo '?p='.$currentPageNum.'&p_id='.$val['id'];
                  }
                ?>">
                 <div class="index-image">
                  <img src="<?php echo showImg(sanitize($val['pic1'])); ?> " alt="">
                </div>
                 <div class="index-text">
                  <h3><?php echo sanitize($val['title']); ?></h3>
                 </div>
                 <div class="index-tag">
                  <span><?php echo sanitize($val['publisher']); ?></span>
                 </div>
                </a>
              </div>
              <?php
              }
               ?>


            </div>
          </div>

        </div>
<!--
          <div class="side-form">
            <p>検索フォーム <br></p>
            <label for="sort">並び替え</label>
            <select class="sort-box" name="sort">
              <option value="">-</option>
              <option value="1">更新日順</option>
              <option value="2">登録順</option>
            </select>
            <label for="select">絞り込み</label>
            <select class="select-box" name="select">
              <option value="">-</option>
              <option value="1">小説</option>
              <option value="2">新書・評論</option>
              <option value="3">学術書籍</option>
            </select>
          </div>
        -->
          <?php
          pagenation($currentPageNum, $dbProductData['total_page'], '&c_id='.$category.'&sort='.$sort);
           ?>
        <!--
        <div class="button-area">
          <input type="submit" name="" value="&lt;">
          <input type="submit" name="" value="1">
          <input type="submit" name="" value="2">
          <input type="submit" name="" value="3">
          <span><input type="submit" name="" value="4">
          <input type="submit" name="" value="5"></span>
          <input type="submit" name="" value="&gt;">
        </div> -->
      </div>
      <?php
        require('js-to-top-button.php');
       ?>
    </div>

<!-- footer部分-->
<?php
require('footer.php');
 ?>
