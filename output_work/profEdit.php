<?php
//共通変数・関数ファイルを用意
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「');
debug('「プロフィール編集画面」');
debug('「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
//画面処理
//================================

//画面表示用データ準備
//================================
//ユーザー情報を取得
$dbFormData = getUser($_SESSION['user_id']);
debug('取得したユーザー情報：'.print_r($dbFormData, true));

//POST送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります。');
  debug('取得したユーザー情報：'.print_r($_POST,true));
  debug('取得したファイル情報：'.print_r($_FILES, true));

  //POSTされた情報を変数に格納
  $name = $_POST['name'];
  $age = $_POST['age'];
  $tel = $_POST['tel'];
  $zip = $_POST['zip'];
  $addr = $_POST['addr'];
  $email = $_POST['email'];
  $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'],'pic') : '';
  $pic = (empty($pic) && !empty($dbFormData['pic'])) ? $dbFormData['pic'] : $pic;

  //バリデーションチェック（未入力・ニックネーム・email）
  validRequired($name, 'name');
  validRequired($email, 'email');

  //バリデーションチェック（その他）
  validMaxLen($name, 'name');
  validNumHalf($age, 'age');
  validNumHalf($tel, 'tel');
  validMaxLen($tel, 'tel');
  validTel($tel, 'tel');
  validNumHalf($zip, 'zip');
  validMaxLen($zip, 'zip');
  validZip($zip, 'zip');
  validMaxLen($addr, 'addr');
  validEmail($email, 'email');

  //Emailを変更したときのみ、重複チェックを実施
  if($email !== $dbFormData['email']){
    validEmailDump($email);
  }
 //バリデーションOKの場合
 if(empty($err_msg)){
   debug('バリデーションOKです。');
   //例外処理
   try {
     //DBへ接続
     $dbh = dbConnect();
     //SQL文作成
     $sql = 'UPDATE users SET username = :name, age = :age, tel = :tel, zip = :zip, addr = :addr, email = :email,
             pic = :pic, update_date = :update_date WHERE id = :u_id AND delete_flg = 0';
     $data = array(':name'=>$name, ':age'=>$age, ':tel'=>$tel, ':zip'=>$zip, ':addr'=>$addr, ':email'=>$email,
                   ':pic'=>$pic, ':update_date'=>date('Y-m-d H:i:s'), ':u_id'=>$_SESSION['user_id']);
     //SQL文実行
     $stmt = queryPost($dbh, $sql, $data);

     if($stmt){
       debug('ユーザー情報の更新に成功しました。マイページへ遷移します。');
       $_SESSION['msg-success'] = SUC02;
       //マイページへ遷移
       header("Location:mypage.php");
     }else{
       debug('ユーザー情報の更新に失敗しました。');
       $err_msg['common'] = MSG07;
     }
   } catch (\Exception $e) {
     error_log('エラー発生：'.$e->getMessage());
     $err_msg['common'] = MSG07;
   }

 }
}
debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<')

 ?>

<?php
$siteTitle = 'プロフィール編集ページ';
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
    <h2 class="form-title">プロフィールを編集する</h2>
    <div class="form-container">
      <form class="product-form" action="" enctype="multipart/form-data" method="post">
        <div class="area-msg">
          <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
        </div>

        <p>
        <label for="name">ニックネーム</label>
        <input type="text" id='name' name="name" value="<?php echo getFormData('username'); ?>" placeholder="ニックネームを入力してください。">
        </p>
        <div class="area-msg">
          <?php if(!empty($err_msg['name'])) echo $err_msg['name']; ?>
        </div>
        <p>
        <label for="age">年齢を選択</label><br>
        <select class="category-select" id="age" name="age">
          <option value="disable">年齢を選択してください</option>
          <?php for ($i=1; $i <101 ; $i++) {
          ?>
          <option <?php if(getFormData('age') == $i){ echo 'selected';} ?> value="<?php echo $i; ?>"><?php echo $i.'才'; ?></option>
          <?php
          }
          ?>
        </select>
        </p>
        <div class="area-msg">
          <?php if(!empty($err_msg['age'])) echo $err_msg['age']; ?>
        </div>
        <p>
        <label for="tel">電話番号</label>
        <input type="text" id="tel" name="tel" value="<?php echo getFormData('tel') ?>" placeholder="半角数字で電話番号を入力してください。">
        </p>
        <div class="area-msg">
          <?php if(!empty($err_msg['tel'])) echo $err_msg['tel']; ?>
        </div>
        <p>
        <label for="zip">郵便番号</label>
        <input type="text" id="zip" name="zip" value="<?php echo getFormData('zip') ?>" placeholder="半角数字で郵便番号を入力してください。">
        </p>
        <div class="area-msg">
          <?php if(!empty($err_msg['zip'])) echo $err_msg['zip']; ?>
        </div>
        <p>
        <label for="addr">住所</label>
        <input type="text" id="addr" name="addr" value="<?php echo getFormData('addr') ?>" placeholder="住所を入力してください。">
        </p>
        <div class="area-msg">
          <?php if(!empty($err_msg['addr'])) echo $err_msg['addr']; ?>
        </div>
        <p>
        <label for="email">メールアドレス<br></label>
        <input type="text" name="email" id='email' value="<?php echo getFormData('email') ?>" placeholder="Emailを入力してください。">
        </p>
        <div class="area-msg">
          <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
        </div>
        <p><label>プロフィール画像</label></p>
        <p>
          <div class="area-drop my-img">
            <span>画像１<br></span>
            <span>ドラッグ＆ドロップ</span>
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
            <img src="<?php echo getFormData('pic'); ?>" alt="" class="prev-img">
            <input type="file" name="pic" value="" class="input_file">
          </div>
        </p>
        <div class="area-msg">
          <?php if(!empty($err_msg['pic'])) echo $err_msg['pic']; ?>
        </div>
        <div class="btn-container">
          <input type="submit" class="button" value="プロフィールを更新する">
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
