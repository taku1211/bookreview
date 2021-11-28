<?php
//共通変数・関数ファイルを用意
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「');
debug('「書籍記事登録・編集画面」');
debug('「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
//画面処理
//================================

//画面表示用データ準備
//================================
//GETデータを格納
$p_id =(!empty($_GET['p_id'])) ? $_GET['p_id'] :'';
//DBから商品情報を取得する
$dbFormData = (!empty($_GET['p_id'])) ? getProduct($_SESSION['user_id'], $p_id): '';
//新規登録画面か既存記事編集画面か判断する
$editFlg = (empty($_GET['p_id'])) ? 0 : 1;
//DBからカテゴリーデータを取得する
$dbCategoryData = getCategory();

debug('商品ID:'.$p_id);
debug('フォーム入力用データ：'.print_r($dbFormData, true));
debug('カテゴリーデータ：'.print_r($dbCategoryData, true));

//パラメータ改竄チェック
//=============================================
//GETパラメータはあるが、改竄されている（URLをいじくった）場合、正しい商品データが取れないのでマイページへ遷移させる。
if(!empty($p_id) && empty($dbFormData)){
  debug('GETパラメータの商品IDが違います。エラーページへ遷移します。');
  header("Location:errorPage.php");
}

//POST送信処理
if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST送信情報：'.print_r($_POST, true));
  debug('FILE情報：'.print_r($_FILES, true));

  //変数に記事情報を代入する
  $postTitle = $_POST['title'];
  $bookTitle = $_POST['bookname'];
  $author = $_POST['author'];
  $publisher = $_POST['publisher'];
  $star = $_POST['evalution'];
  $category = $_POST['category'];
  $reviewer = $_POST['reviewer'];
  //画像をアップロードし、パスを格納
  $pic1 =(!empty($_FILES['pic1']['name'])) ? uploadImg($_FILES['pic1'],'pic1') : '';
  //画像をPOSTしていない（登録していない）がすでに登録されている場合、DBのパスを入れる(POSTには反映されないので)
  $pic1 =(empty($pic1) && !empty($dbFormData['pic1'])) ? $dbFormData['pic1'] : $pic1;
  $pic2 =(!empty($_FILES['pic2']['name'])) ? uploadImg($_FILES['pic2'],'pic2') : '';
  $pic2 =(empty($pic2) && !empty($dbFormData['pic2'])) ? $dbFormData['pic2'] : $pic2;
  $pic3 =(!empty($_FILES['pic3']['name'])) ? uploadImg($_FILES['pic3'],'pic3') : '';
  $pic3 =(empty($pic3) && !empty($dbFormData['pic3'])) ? $dbFormData['pic3'] : $pic3;
  $pic4 =(!empty($_FILES['pic4']['name'])) ? uploadImg($_FILES['pic4'],'pic4') : '';
  $pic4 =(empty($pic4) && !empty($dbFormData['pic4'])) ? $dbFormData['pic4'] : $pic4;

  $review = $_POST['review'];
  $summary = $_POST['summary'];


  if(empty($dbFormData)){
    //未入力チェック
    validRequired($postTitle,'title');
    validRequired($bookTitle,'bookname');
    validRequired($author,'author');
    validRequired($publisher,'publisher');
    validRequired($star,'evalution');
    validRequired($category,'category');
    validRequired($reviewer,'reviewer');
    validRequired($summary,'summary');
    validRequired($review,'review');
    //文字数チェック
    validMaxLen($postTitle,'title');
    validMaxLen($bookTitle,'bookname');
    validMaxLen($author,'author');
    validMaxLen($publisher,'publisher');
    validMaxLen($reviewer,'reviewer');
    validMaxLen($summary,'summary',500);
    validMaxLen($review,'review',500);
  }else{
    if($dbFormData['title'] !== $postTitle){
      validRequired($postTitle,'title');
      validMaxLen($postTitle,'title');
    }
    if($dbFormData['bookname'] !== $bookTitle){
      validRequired($bookTitle,'bookname');
      validMaxLen($bookTitle,'bookname');
    }
    if($dbFormData['author'] !== $author){
      validRequired($author,'author');
      validMaxLen($author,'author');
    }
    if($dbFormData['publisher'] !== $publisher){
      validRequired($publisher,'publisher');
      validMaxLen($publisher,'publisher');
    }
    if($dbFormData['summary'] !== $summary){
      validRequired($summary,'summary');
      validMaxLen($summary,'summary',500);
    }
    if($dbFormData['review'] !== $review){
      validRequired($review,'review');
      validMaxLen($review,'review',500);
    }
    if($dbFormData['reviewer'] !== $reviewer){
      validRequired($reviewer,'reviewer');
      validMaxLen($reviewer,'reviewer');
    }

    if($dbFormData['evalution'] !== $star){
      validRequired($star,'evalution');
    }
    if($dbFormData['category_id'] !== $category){
      validRequired($category,'category');
    }
  }

  if(empty($err_msg)){
    debug('バリデーションOKです。');
    //例外処理
    try {
      //editFlgでINSERTするかUPDATEするかを判定する
      if(!empty($editFlg)){
        debug('DB更新です。');
        //DBへ接続
        $dbh = dbConnect();
        $sql = 'UPDATE book_post SET title=:postTitle, bookname=:bookTitle, author=:author, publisher=:publisher, evalution=:star, category_id=:category,
                reviewer=:reviewer, pic1=:pic1, pic2=:pic2, pic3=:pic3, pic4=:pic4, summary=:summary, review=:review, update_date=:update_date WHERE user_id = :u_id AND id = :p_id';
        $data = array(':postTitle'=>$postTitle,':bookTitle'=>$bookTitle, ':author'=>$author, ':publisher'=>$publisher, ':star'=>$star, ':category'=>$category,
                       ':reviewer'=>$reviewer, ':pic1'=>$pic1, ':pic2'=>$pic2, ':pic3'=>$pic3, ':pic4'=>$pic4, ':summary'=>$summary, ':review'=>$review,
                       ':update_date'=>date('Y-m-d H;i;s'), ':u_id'=>$_SESSION['user_id'], ':p_id'=>$p_id);
      }else{
        debug('DB新規登録です。');
        debug('バリデーションpic1：'.$pic1);
        debug('デバッグpic1'.print_r($pic1, true));

        //DBへ接続
        $dbh = dbConnect();
        $sql = 'INSERT into book_post (title,bookname,author,publisher,evalution,category_id,reviewer,create_date,pic1,pic2,pic3,pic4,summary,review,user_id)
                VALUES(:postTitle, :bookTitle, :author, :publisher, :star, :category, :reviewer, :create_date, :pic1, :pic2, :pic3, :pic4, :summary, :review, :u_id)';
        $data = array(':postTitle'=>$postTitle,':bookTitle'=>$bookTitle, ':author'=>$author, ':publisher'=>$publisher, ':star'=>$star, ':category'=>$category,
                      ':reviewer'=>$reviewer, ':create_date'=>date('Y-m-d H;i;s'), ':pic1'=>$pic1, ':pic2'=>$pic2, ':pic3'=>$pic3, ':pic4'=>$pic4, ':summary'=>$summary, ':review'=>$review, 'u_id'=>$_SESSION['user_id']);
      }
      //クエリ実行
      $stmt= queryPost($dbh, $sql, $data);

      if($stmt){
        debug('クエリに成功しました。マイページへ遷移します。');
        if(!empty($editFlg)){
          $_SESSION['msg-success'] = SUC10;
        }else{
          $_SESSION['msg-success'] = SUC04;
        }
        header("Location:mypage.php");
      }else{
        debug('クエリに失敗しました。');
        $err_msg['common'] = MSG07;
      }
    } catch (\Exception $e) {
      error_log('エラー発生：'. $e->getMessage());
      $err_msg['common'] = MSG07;

    }
  }
}
debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<')

 ?>

<?php
$siteTitle = (empty($editFlg))?'記事登録ページ':'記事編集ページ';
require('head.php');
require('header.php');
 ?>


 <div class="mypage-img">
   <div class="mypage-img-container">
     <img src="img/book-254048_1920.jpg" alt="">
   </div>
 </div>

<div class="page-main">
  <div class="main-container">
    <h2 class="form-title"><?php if(empty($editFlg)){echo '記事を投稿する';}else{echo '記事を更新する';}  ?></h2>
    <div class="form-container">
      <form class="product-form" action="" enctype="multipart/form-data" method="post">
        <div class="area-msg">
          <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
        </div>

        <p>
        <label for="title">記事のタイトル</label>
        <input type="text" id='post_title' name="title" value="<?php echo getFormData('title'); ?>" placeholder="記事のタイトルを入力してください。">
        </p>
        <div class="area-msg">
          <?php if(!empty($err_msg['post_title'])) echo $err_msg['title']; ?>
        </div>
        <p>
        <label for="bookname">書籍名</label>
        <input type="text" name="bookname" value="<?php echo getFormData('bookname'); ?>" placeholder="書籍名を入力してください。">
        </p>
        <div class="area-msg">
          <?php if(!empty($err_msg['book_title'])) echo $err_msg['bookname']; ?>
        </div>
        <p>
        <label for="author">著者名</label>
        <input type="text" name="author" value="<?php echo getFormData('author'); ?>" placeholder="著者名を入力してください。">
        </p>
        <div class="area-msg">
          <?php if(!empty($err_msg['author'])) echo $err_msg['author']; ?>
        </div>
        <p>
        <label for="publisher">出版社</label>
        <input type="text" name="publisher" value="<?php echo getFormData('publisher'); ?>" placeholder="出版社を入力してください。">
        </p>
        <div class="area-msg">
          <?php if(!empty($err_msg['publisher'])) echo $err_msg['publisher']; ?>
        </div>
        <p>
        <label for="">お勧め度</label>
        <div class="evalution">
          <input type="radio" id="star1" name="evalution" value="5" <?php if(!empty(getFormData('evalution')) && getFormData('evalution') == 5){echo 'checked';} ?>>
          <label for="star1"><span>最高</span>★</label>
          <input type="radio" id="star2" name="evalution" value="4" <?php if(!empty(getFormData('evalution')) && getFormData('evalution') == 4) {echo 'checked';} ?>>
          <label for="star2"><span>良い</span>★</label>
          <input type="radio" id="star3" name="evalution" value="3" <?php if(!empty(getFormData('evalution')) && getFormData('evalution') == 3) {echo 'checked';} ?>>
          <label for="star3"><span>普通</span>★</label>
          <input type="radio" id="star4" name="evalution" value="2" <?php if(!empty(getFormData('evalution')) && getFormData('evalution') == 2) {echo 'checked';} ?>>
          <label for="star4"><span>悪い</span>★</label>
          <input type="radio" id="star5" name="evalution" value="1" <?php if(!empty(getFormData('evalution')) && getFormData('evalution') == 1) {echo 'checked';} ?>>
          <label for="star5"><span>最悪</span>★</label>
        </div>
        </p>
        <div class="area-msg">
          <?php if(!empty($err_msg['star'])) echo $err_msg['evalution']; ?>
        </div>
        <p>
        <label for="category">書籍カテゴリー</label><br>
        <select class="category-select" name="category" id='category'>
          <option value="0" name="category" selected="<?php if(getFormData('category_id') == 0) echo 'selected'; ?>">--</option>
          <?php
            foreach ($dbCategoryData as $key => $value) {
              ?>
              <option value="<?php echo $value['id']; ?>" <?php if(getFormData('category_id') == $value['id']) echo 'selected'; ?>>
                <?php echo $value['name']; ?></option>
          <?php
                }
              ?>
        </select>
        </p>
        <div class="area-msg">
          <?php if(!empty($err_msg['category'])) echo $err_msg['category']; ?>
        </div>
        <p>
        <label for="reviewer">記事作成者<br></label>
        <input type="text" name="reviewer" id='reviewer' value="<?php echo getFormData('reviewer'); ?>" placeholder="記事に表示する名前を入力してください。">
        </p>
        <div class="area-msg">
          <?php if(!empty($err_msg['reviewer'])) echo $err_msg['reviewer']; ?>
        </div>
        <p><label>書籍画像</label></p>
        <p>
          <div class="area-drop">
            <span>画像１<br></span>
            <span>ドラッグ＆ドロップ</span>
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
            <img src="<?php echo getFormData('pic1'); ?>" alt="" class="prev-img" >
            <input type="file" name="pic1" value="" class="input_file">
          </div>
        </p>
        <div class="area-msg">
          <?php if(!empty($err_msg['pic1'])) echo $err_msg['pic1']; ?>
        </div>
        <p>
          <div class="area-drop">
            <span>画像２<br></span>
            <span>ドラッグ＆ドロップ</span>
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
            <img src="<?php echo getFormData('pic2'); ?>" alt="" class="prev-img" >
            <input type="file" name="pic2" value="" class="input_file">
          </div>
        </p>
        <div class="area-msg">
          <?php if(!empty($err_msg['pic2'])) echo $err_msg['pic2']; ?>
        </div>
        <p>
          <div class="area-drop">
            <span>画像３<br></span>
            <span>ドラッグ＆ドロップ</span>
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
            <img src="<?php echo getFormData('pic3'); ?>" alt="" class="prev-img" >
            <input type="file" name="pic3" value="" class="input_file">
          </div>
        </p>
        <div class="area-msg">
          <?php if(!empty($err_msg['pic3'])) echo $err_msg['pic3']; ?>
        </div>
        <p>
          <div class="area-drop">
            <span>画像４<br></span>
            <span>ドラッグ＆ドロップ</span>
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
            <img src="<?php echo getFormData('pic4'); ?>" alt="" class="prev-img" >
            <input type="file" name="pic4" value="" class="input_file">
          </div>
        </p>
        <div class="area-msg">
          <?php if(!empty($err_msg['pic4'])) echo $err_msg['pic4']; ?>
        </div>
        <p>
        <label>
          書籍の概要
          <textarea name="summary" id="js-count" rows="20" cols="50" ><?php if(!empty(getFormData('summary'))) echo getFormData('summary'); ?></textarea>
        </label>
        <p class="counter-text"><span id="js-count-view">0</span>/500文字</p>
        </p>
        <div class="area-msg">
          <?php if(!empty($err_msg['summary'])) echo $err_msg['summary']; ?>
        </div>
        <p>
        <label>
          書籍のレビュー
          <textarea name="review" id="js-count2" rows="20" cols="50"><?php if(!empty(getFormData('review'))) echo getFormData('review'); ?></textarea>
        </label>
        <p class="counter-text"><span id="js-count-view2">0</span>/500文字</p>
        </p>
        <div class="area-msg">
          <?php if(!empty($err_msg['review'])) echo $err_msg['review']; ?>
        </div>
        <div class="btn-container">
          <input type="submit" class="button" value="<?php if(empty($editFlg)){echo '投稿する';}else{echo '更新する';}  ?>">
        </div>

      </form>
    </div>

    </form>
  </div>
  <?php
    require('js-to-top-button.php');
   ?>

</div>



 <?php
require('footer.php');
  ?>
