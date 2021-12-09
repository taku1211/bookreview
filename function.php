<?php
//ログをとるかどうか
//ini_set('log_errors', 'on');
//ログの出六ファイルを指定
//ini_set('error_log', 'php.log');
error_reporting(E_ALL);
ini_set("display_errors", 1);
//==============================
//デバッグ関数
//==============================
$debug_flg = true;
function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
    error_log('デバッグ:'.$str);
  }
}

//===============================
//画面表示処理用ログ吐き出し関数
//==============================
function debugLogStart(){
  debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<');
  debug('セッションID'.session_id());
  debug('セッション変数の中身'.print_r($_SESSION,true));
  debug('現在の日時スタンプ'.time());
}

//===========================================
//セッション準備・セッションの有効期限を延ばす
//==========================================
//RedisTOGOへ接続
$redis_url = "tcp://" . parse_url($_ENV['REDISTOGO_URL'], PHP_URL_HOST) . ":" . parse_url($_ENV['REDISTOGO_URL'], PHP_URL_PORT);
if (!is_array(parse_url($_ENV['REDISTOGO_URL'], PHP_URL_PASS))) {
  $redis_url .= "?auth=" . parse_url($_ENV['REDISTOGO_URL'], PHP_URL_PASS);
}
ini_set("session.save_path", $redis_url);
ini_set("session.save_handler", "redis");
//セッションファイルの置き場所を変更する
//session_save_path("C:\WINDOWS\Temp");
//ガベージコレクションが削除するセッションの有効期限を設定（30日以上経過しているものに対してのみ１００分の１の確率で削除）
ini_set('session.gc_maxlifetime', 60*60*24*30);
//ブラウザを閉じてもクッキーが削除されないようにクッキー自体の有効期限を延ばす
ini_set('session.cookie_lifetime', 60*60*24*30);
//セッションを使う
session_start();
//現在のセッションを新しく生成したものと置き換える（なりすましの防止）
session_regenerate_id();


//==============================
//定数（エラーメッセージ・セッションメッセージ）
//==============================
define('MSG01','入力必須です');
define('MSG02', 'Emailの形式で入力してください');
define('MSG03','パスワード（再入力）が合っていません');
define('MSG04','半角英数字のみご利用いただけます');
define('MSG05','8文字以上で入力してください');
define('MSG06','256文字以内で入力してください');
define('MSG07','エラーが発生しました。しばらくたってからやり直してください');
define('MSG08','そのEメールはすでに登録されています');
define('MSG09','メールアドレス、もしくはパスワードが違います。');
define('MSG10','電話番号の形式が違います。');
define('MSG11','郵便番号の形式が違います。');
define('MSG12','半角数字で入力してください。');
define('MSG13','現在のパスワードが違います。');
define('MSG14','現在のパスワードと同じです。');
define('MSG15','文字で入力してください。');
define('MSG16','仮パスワードが正しくありません。');
define('MSG17','有効期限が切れています。');
define('SUC01','パスワードを変更しました。');
define('SUC02','プロフィールを変更しました。');
define('SUC03','メールを送信しました。');
define('SUC04','新しい記事を登録しました。');
define('SUC05','お気に入りに追加しました。');
define('SUC06','お気に入りから削除しました。');
define('SUC07', 'マイページにログインしました。');
define('SUC08', 'ログアウトしました。');
define('SUC09', 'サービスから退会しました。');
define('SUC10', '記事を更新しました。');
define('SUC11', '会員登録しました。');
//==============================
//バリデーション関数
//==============================

$err_msg = array();

//未入力チェック
function validRequired($str,$key){
  if($str === ''){
    global $err_msg;
    $err_msg[$key] = MSG01;
  }
}

//最大文字数チェック
function validMaxLen($str, $key, $max=256){
  if(mb_strlen($str) > $max){
    global $err_msg;
    $err_msg[$key] = MSG06;
  }
}

//最小文字数チェック
function validMinLen($str, $key, $min=8){
  if(mb_strlen($str) < $min){
    global $err_msg;
    $err_msg[$key] = MSG05;
  }
}
//メールアドレス形式チェック
function validEmail($str, $key){
  if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG02;
  }
}
//メールアドレス重複チェック
function validEmailDump($email){
  global $err_msg;
  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql ='SELECT count(email) FROM users WHERE email = :email AND delete_flg = 0';
    $data= array(':email'=> $email);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    //クエリ実行の値を取得
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!empty(array_shift($result))){
      $err_msg['email'] = MSG08;
    }

  } catch (\Exception $e) {
    error_log('エラー発生:'.$e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
//半角英数字チェック関数
function validHalf($str, $key){
  if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG04;
  }
}
//パスワードチェック関数
function validPass($str, $key){
  //半角英数字チェック
  validHalf($str, $key);
  //最大・最小文字数チェック
  validMinLen($str, $key);
  validMaxLen($str, $key);
}

//パスワード同値チェック関数
function validMatch($str1, $str2, $key){
  if($str1 !== $str2){
    global $err_msg;
    $err_msg[$key] = MSG03;
  }
}
//selectboxチェック関数
function validSelect($str, $key){
  if(!preg_match("/^[0-9]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG15;
  }
}
//電話番号形式チェック関数
function validTel($str, $key){
  if(!preg_match("/0\d{1,4}\d{4}/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG10;
  }
}
//郵便番号形式チェック関数
function validZip($str, $key){
  if(!preg_match("/^\d{7}$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG11;
  }
}
//半角数字チェック関数
function validNumHalf($str, $key){
  if(!preg_match("/^[0-9]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG12;
  }
}
//==================================
//サニタイズ
//==================================
function sanitize($str){
  return htmlspecialchars($str, ENT_QUOTES);
}


//==================================
//DB接続関数
//==================================

//DB接続関数
function dbConnect(){

  //DBへの接続準備
  $db = parse_url($_SERVER['CLEARDB_DATABASE_URL']);
  $db['dbname'] = ltrim($db['path'], '/');
  $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset=utf8";
  $user = $db['user'];
  $password = $db['pass'];
  $options = array(
    //SQL実行失敗時にはエラーコードのみ設定
    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
    //デフォルトフェッチモードを連想配列形式に設定
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    //バッファードクエリを使う（一度に結果セットをすべて取得し、サーバ負荷を軽減）
    //SELECTで得た結果に対してもrowCountメソッドを使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  //PDOオブジェクトを作成（DBへ接続）
  $dbh = new PDO($dsn, $user, $password, $options);
  return $dbh;
}

//SQL実行関数
function queryPost($dbh, $sql, $data){
  //クエリ作成
  $stmt = $dbh->prepare($sql);
  //プレースフォルダに値をセットし、SQL文を実行
  if(!$stmt->execute($data)){
    debug('クエリに失敗しました。失敗したSQL'.print_r($stmt, true));
    global $err_msg;
    $err_msg['common'] = MSG07;
  }else{
    debug('クエリに成功しました。');
    return $stmt;
  }
}

//=======================================
//ユーザー情報取得関数
//=======================================
function getUser($u_id){
  debug('ユーザー情報を取得します。');
  debug('ユーザーID：'.$u_id);

  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = 'SELECT id, username, age, tel, zip, addr, email, pic, update_date, delete_flg FROM users
            WHERE id = :u_id AND delete_flg = 0';
    $data = array(':u_id'=> $u_id);
    //SQL文実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      debug('ユーザー情報の取得に成功しました。');
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      debug('ユーザー情報の取得に失敗しました。');
      return false;
    }
  } catch (\Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}

//=======================================
//商品情報取得関数
//=======================================
function getProduct($u_id, $p_id){
  debug('商品情報を取得します。');
  debug('ユーザーID:'.$u_id);
  debug('記事情報:'.$p_id);
  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql ='SELECT * FROM book_post WHERE user_id = :u_id AND id = :p_id AND delete_flg = 0';
    $data = array(':u_id'=> $u_id, ':p_id'=> $p_id);
    //SQL文実行
    $stmt = queryPost($dbh, $sql, $data);
    //結果を返却
    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  } catch (\Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}
//カテゴリーデータ取得関数
function getCategory(){
  debug('カテゴリー情報を取得します。');
  //例外処理
  try {
    //SQL文作成
    $dbh = dbConnect();
    $sql = 'SELECT * FROM category';
    $data = array();
    //SQL文実行
    $stmt = queryPost($dbh, $sql, $data);
    //クエリ実行結果を全て返却
    if($stmt){
      return $stmt->fetchAll();
    }else{
      return false;
    }

  } catch (\Exception $e) {
    error_log('エラー情報：'.$e->getMessage());
  }
}
//自身の投稿記事取得関数
function getMyProductList($u_id, $currentMinNum =1, $span=6){
  debug('自身の投稿記事を指定レコード分取得します。');
  debug('自身のユーザーID:'.$u_id);

  //1.全ての自分の商品データを取得し、件数を確認する
  //例外処理
  try {
    //DBへ接続
    $dbh =dbConnect();
    //SQL文作成
    $sql = 'SELECT id FROM book_post WHERE user_id = :u_id';
    $data = array(':u_id' => $u_id);
    //SQL文実行
    $stmt= queryPost($dbh, $sql, $data);
    //総レコード数を返却
    if($stmt){
      $rst['total'] = $stmt->rowCount();
      $rst['total_page'] = ceil($rst['total']/ $span);
    }else{
      return false;
    }

      //2.ページング用のデータを取得
      $sql = 'SELECT id, title, bookname, publisher, pic1, user_id FROM book_post WHERE user_id = :u_id AND delete_flg = 0';
      $sql .= ' LIMIT :span OFFSET :currentMinNum';
      $stmt = $dbh->prepare($sql);
      $stmt -> bindvalue(':u_id', $u_id, PDO::PARAM_STR);
      $stmt -> bindvalue(':currentMinNum', $currentMinNum, PDO::PARAM_INT);
      $stmt -> bindvalue(':span', $span, PDO::PARAM_INT);

      //SQL文実行
      $result = $stmt->execute();

      //結果を返却
      if($result){
        $rst['data'] = $stmt->fetchALL();
        return $rst;
      }else{
        return false;
      }
  } catch (\Exception $e) {
      error_log('エラー発生：'.$e->getMessage());
  }
}

//記事一覧取得関数
function getProductList($currentMinNum =1, $category, $sort, $span=6){
  debug('商品情報一覧を取得します。');

  //1.全ての商品データを取得し、件数を確認する
  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = 'SELECT id FROM book_post';
    $data = array();

    //SQL文実行
    $stmt =queryPost($dbh, $sql, $data);
    //総レコード数を取得
    $rst['total'] = $stmt->rowCount();
    //総ページ数を計算
    $rst['total_page'] = ceil($rst['total']/$span);

    if(!$stmt){
      return false;
    }
  //2.ページング用のデータを取得する
    $sql = 'SELECT * FROM book_post';
    $sql .= ' LIMIT :span OFFSET :currentMinNum';
    $stmt = $dbh->prepare($sql);
    $stmt -> bindvalue(':currentMinNum', $currentMinNum, PDO::PARAM_INT);
    $stmt -> bindvalue(':span', $span, PDO::PARAM_INT);
    debug('SQL：'.$sql);
    //クエリ実行
    $result = $stmt->execute();

    if($result){
      //クエリ結果のすべてのレコードを返却
      $rst['data'] = $stmt->fetchAll();
      debug('取得した記事情報：'.print_r($rst, true));
      return $rst;
    }else{
      return false;
    }
  } catch (\Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }

}
//記事内容取得関数
function getProductOne($p_id){
  debug('記事情報を取得します。');
  debug('取得する記事情報のID：'.$p_id);

  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = 'SELECT p.id, p.title, p.bookname, p.author, p.publisher, p.evalution, p.category_id, p.reviewer, p.create_date, p.pic1,
            p.pic2, p.pic3, p.pic4, p.summary, p.review, c.name AS category FROM book_post AS p LEFT JOIN category AS c on
            p.category_id = c.id WHERE p.id = :p_id AND p.delete_flg = 0 AND c.delete_flg = 0';
    $data = array(':p_id'=> $p_id);
    //SQL文実行
    $stmt = queryPost($dbh, $sql, $data);

    //クエリ結果を返却
    if($stmt){
      debug('クエリに成功しました。');
      return $stmt->fetch(PDO::FETCH_ASSOC);
      debug('取得した記事情報：'.print_r($stmt, true));
    }else{
      debug('クエリに失敗しました。');
      return false;
    }
  } catch (\Exception $e) {
    error_log('エラー情報：'.$e->getMessage());
  }
}

//自身の投稿記事取得関数
function getMyProduct($u_id){
  debug('自身の投稿記事を取得します。');
  debug('自身のユーザーID:'.$u_id);

  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    $sql = 'SELECT id, title, bookname, publisher, pic1, user_id FROM book_post WHERE user_id = :u_id AND delete_flg = 0 LIMIT 3 OFFSET 0';
    $data = array(':u_id' => $u_id);

    $stmt = queryPost($dbh, $sql, $data);
    //結果を返却
    if($stmt){
      return $stmt->fetchAll();
    }else{
      return false;
    }
  } catch (\Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}

//===================================
//お気に入り機能
//===================================
function isLike($u_id, $p_id){
  debug('お気に入り情報があるか確認します。');
  debug('ユーザーID:'.$u_id);
  debug('記事ID:'.$p_id);

  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = 'SELECT * FROM favorite WHERE user_id = :u_id AND product_id = :p_id';
    $data = array(':u_id' => $u_id, ':p_id' => $p_id);
    //SQL文実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt->rowCount()){
      debug('お気に入りです。');
      return true;
    }else{
      debug('お気に入りではありません。');
      return false;
    }
  } catch (\Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}
//ユーザー情報取得関数
function getMyUser($u_id){
  debug('ユーザー情報を取得します。');
  debug('自分のユーザーID:'.$u_id);

  //例外処理t
  try {
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = 'SELECT username, pic FROM users WHERE id = :u_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id);
    //SQL文実行
    $stmt = queryPost($dbh, $sql, $data);
    //結果を返却
    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  } catch (\Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}
//自分のお気に入り取得関数
function getMyLike($u_id){
  debug('自分のお気に入り情報を取得します。');
  debug('自分のユーザーID:'.$u_id);

  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = 'SELECT * FROM favorite AS f LEFT JOIN book_post AS p ON f.product_id = p.id WHERE f.user_id = :u_id LIMIT 3 OFFSET 0';
    $data = array(':u_id' => $u_id);
    //SQL文実行
    $stmt = queryPost($dbh, $sql, $data);

    //結果を返却
    if($stmt){
      return $stmt->fetchAll();
    }else{
      return false;
    }
  } catch (\Exception $e) {
    error_log('エラー発生:'.$e->getMessage());
  }
}
//自分のお気に入り取得関数
function getMyLikeList($u_id, $currentMinNum = 1, $span = 6){
  debug('自分のお気に入り情報を取得します。');
  debug('自分のユーザーID:'.$u_id);

  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //1.自分のお気に入り件数取得
    $sql = 'SELECT * FROM favorite WHERE user_id = :u_id';
    $data = array(':u_id' => $u_id);
    //SQL文実行
    $stmt= queryPost($dbh, $sql, $data);
    //総レコード数を返却
    if($stmt){
      $rst['total'] = $stmt->rowCount();
      $rst['total_page'] = ceil($rst['total']/ $span);
    }else{
      return false;
    }


    //SQL文作成（ページング用のレコードを取得）
    $sql = 'SELECT * FROM favorite AS f LEFT JOIN book_post AS p ON f.product_id = p.id WHERE f.user_id = :u_id';
    $sql .= ' LIMIT :span OFFSET :currentMinNum';
    $stmt = $dbh->prepare($sql);
    $stmt -> bindvalue(':u_id', $u_id, PDO::PARAM_STR);
    $stmt -> bindvalue(':span', $span, PDO::PARAM_INT);
    $stmt -> bindvalue(':currentMinNum', $currentMinNum, PDO::PARAM_INT);
    //SQL文実行
    $result = $stmt->execute();

    //結果を返却
    if($result){
      $rst['data'] = $stmt->fetchAll();
      return $rst;
    }else{
      return false;
    }
  } catch (\Exception $e) {
    error_log('エラー発生:'.$e->getMessage());
  }
}



//ログイン認証関数
function isLogin(){
  //ログインしている場合
  if(!empty($_SESSION['login_time'])){
    debug('ログイン済みユーザーです。');

    //現在日時がログイン有効期限外の場合
    if($_SESSION['login_time'] + $_SESSION['login_limit'] < time()){
      debug('ログイン有効期限オーバーです。');
      session_destroy();
      return false;
    }else{
      debug('ログイン有効期限内です。');
      return true;
    }
  }else{
    debug('未ログインユーザーです。');
    return false;
  }
}

//画像データアップロード関数
function uploadImg($file, $key){
  debug('画像アップロード開始');
  debug('ファイル情報：'.print_r($file,true));

  if(isset($file['error']) && is_int($file['error'])){
    try {

      //バリデーション 1.ファイルアップロード時のエラー確認
      //$file['error']の中身を確認。配列内には[UPLOAD_ERR_OK]などの定数が入っている。
      //[UPLOAD_ERR_OK]などの定数はphpでファイルアップロード時に自動的に定義される。定数には値として0や1などの数値が入っている。
      switch ($file['error']) {
        case UPLOAD_ERR_OK: //OK
          break;
        case UPLOAD_ERR_NO_FILE: //ファイル未選択の場合
          throw new RuntimeException('ファイルが選択されていません。');
        case UPLOAD_ERR_INI_SIZE: //php.ini定義の最大サイズを超過した場合
        case UPLOAD_ERR_FORM_SIZE: //フォーム定義の最大サイズを超過した場合
          throw new RuntimeException('ファイルサイズが大きすぎます。');
        default: //そのほかの場合
          throw new RuntimeException('そのほかのエラーが発生しました。');
          break;
      }
      //バリデーション　2.ファイル形式の確認を行う
      // $file['mime']の値はブラウザ側で偽造可能なので、MIMEタイプを自動でチェックする
      // exif_imagetype関数は「IMAGETYPE_GIF」「IMAGETYPE_JPEG」などの定数を返す
      $type = @exif_imagetype($file['tmp_name']);
      if(!in_array($type, [IMAGETYPE_GIF,IMAGETYPE_PNG,IMAGETYPE_JPEG], true)){
        throw new RuntimeException('画像形式が未対応です。');
      }
      //ファイルデータからSHA-1ハッシュをとってきてファイル名を決定し、ファイルを保存する
      //ハッシュ化しておかないとアップロードされたファイル名そのままで保存してしまうと、同じファイル名がアップロードされる可能性があり、
      //DBにパスを保存した場合、どっちの画像のパスなのか判断つかなくなってしまう
      //image_type_to_extension関数はファイルの拡張子を取得するもの
      $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);

      if(!move_uploaded_file($file['tmp_name'], $path)){
        throw new RuntimeException('ファイル保存時にエラーが発生しました。');
      }
      //保存したファイルパスのパーミッション（権限）を変更する
      chmod($path, 0644);

      debug('ファイルは正常にアップロードされました。');
      debug('ファイルパス：'. $path);
      return $path;

    } catch (\Exception $e) {
      debug('エラー発生：'. $e->getMessage());
      global $err_msg;
      $err_msg[$key] = $e->getMessage();
    }

  }
}
//フォーム入力保持関数
function getFormData($str, $flg=false){
  if($flg){
    $method = $_GET;
  }else{
    $method = $_POST;
  }
  global $dbFormData;
  //ユーザーデータがある場合
  if(!empty($dbFormData)){
    //エラーメッセージがある場合
    if(!empty($err_msg)){
      //POST送信がある場合はPOSTの情報をサニタイズしたうえで表示
      if(isset($method[$str])){
        return sanitize($method[$str]);
      }else{
        //POST送信がない場合はDBの情報を表示
        return sanitize($dbFOrmData);
      }
    }else{
      //POSTにデータがあり、DBのデータと違う場合（他のフォームエラーで引っかかっている場合）
      if(isset($method[$str]) && $method[$str] !== $dbFormData[$str]){
        return sanitize($method[$str]);
      }else{
        //変更していない場合
        return sanitize($dbFormData[$str]);
      }
    }

  }else{
    //ユーザーデータがない場合
    if(isset($method[$str])){
      return sanitize($method[$str]);
    }
  }
}

//画像表示関数
function showImg($path){
  if(empty($path)){
    return ('img/sample-img.png');
  }else{
    return $path;
  }
}
//GETパラメータ付与関数
function appendGetParam($arr_del_key = array()){
  $str = '?';
  if(!empty($_GET)){
    foreach ($_GET as $key => $val) {
      if(!in_array($key, $arr_del_key, true)){
        $str .= $key.'='.$val.'&';
      }
    }
     $str = mb_substr($str, 0, -1, "UTF-8");
     return $str;
  }else{
    return false;
  }
}



//ページネーション
function pagenation($currentPageNum, $totalPageNum, $link, $pageColNum = 5){
  if($currentPageNum == $totalPageNum && $totalPageNum > $pageColNum){
    //1.現在のぺーず数が最大ページ数と同じ場合で、合計ページ数が表示ペース数より大きい場合、左に4だす
    $minPageNum = $currentPageNum -4;
    $maxPageNum = $currentPageNum;
  }elseif($currentPageNum == ($totalPageNum -1) && $totalPageNum > $pageColNum ){
    //2.現在のページ数が最大ページ数の1つ手前で、合計ページ数が表示ページ数より大きい場合、左に3つ、右に1つだす
    $minPageNum = $currentPageNum -3;
    $maxPageNum = $currentPageNum +1;
  }elseif($currentPageNum == 2 && $totalPageNum > $pageColNum){
    //3.現在のページ数が前から2番目のページで、合計ページ数が表示ページ数より大きい場合、左に1つ、右に３つだす
    $minPageNum = $currentPageNum -1;
    $maxPageNum = $currentPageNum +3;
  }elseif($currentPageNum == 1 && $totalPageNum > $pageColNum){
    //4.現在のページ数が1番目のページで、合計ページ数が表示ページ数より大きい場合、右に4つだす
    $minPageNum = 1;
    $maxPageNum = $currentPageNum +4;
  }elseif($totalPageNum < $pageColNum){
    //5.合計ページ数が表示ページ数より小さい場合、最小ページ数を1、最大ページ数をその合計ページに設定する
    $minPageNum = 1;
    $maxPageNum = $totalPageNum;
  }else{
    //6.それ以外は左に2つ、右に2つ出す
    $minPageNum = $currentPageNum -2;
    $maxPageNum = $currentPageNum +2;
  }
  echo '<div class="pagenation">';
  echo '<ul class="pagenation-list">';
  if($currentPageNum != 1){
    echo '<li class="list-item"><a href="?p=1'.$link.'">&lt;</a></li>';
  }
  for($i = $minPageNum; $i <= $maxPageNum; $i++){
    echo '<li class="list-item ';
    if($currentPageNum == $i){echo 'active';}
    echo '"><a href="?p='.$i.$link.'">'.$i.'</a></li>';
  }
  if($currentPageNum != $maxPageNum && $maxPageNum >1){
    echo '<li class="list-item"><a href="?p='.$maxPageNum.$link.'">&gt;</a></li>';
  }
  echo '</ul>';
  echo '</div>';
}
//---------------------------------------------------------------------------
//スマホならtrue, タブレット・PCならfalseを返す
//---------------------------------------------------------------------------
function is_mobile(){
    $useragents = array(
		'iPhone',          // iPhone
		'iPod',            // iPod touch
		'Android',         // 1.5+ Android
		'dream',           // Pre 1.5 Android
		'CUPCAKE',         // 1.5+ Android
		'blackberry9500',  // Storm
		'blackberry9530',  // Storm
		'blackberry9520',  // Storm v2
		'blackberry9550',  // Storm v2
		'blackberry9800',  // Torch
		'webOS',           // Palm Pre Experimental
		'incognito',       // Other iPhone browser
		'webmate'          // Other iPhone browser
	);
	$pattern = '/'.implode('|', $useragents).'/i';
	return preg_match($pattern, $_SERVER['HTTP_USER_AGENT']);
}
//JSメッセージ表示用セッション関数
function getSessionFlash($key){
  if(!empty($_SESSION[$key])){
    $data = $_SESSION[$key];
    debug('セッション情報1：'.print_r($_SESSION[$key], true));
    $_SESSION[$key] = '';
    debug('データ情報：'.print_r($data,true));
    debug('セッション情報2：'.print_r($_SESSION[$key], true));
    return $data;
  }
}
 ?>
