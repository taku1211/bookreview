<footer class="page-footer" id="footer">
  <div class="footer-container">
    <h2><a href="index.php">Read Happiness!!</a></h2>
    <ul class="footer-nav1">
      <?php
        if(empty($_SESSION['user_id'])){
       ?>
      <li> <a href="index.php">HOME</a></li>
      <li> <a href="login.php">ログイン</a></li>
      <li> <a href="userregist.php">新規登録</a></li>
      <?php
    }else{
       ?>
       <li> <a href="mypage.php">マイページ</a></li>
       <li> <a href="index.php">HOME</a></li>
       <li> <a href="logout.php">ログアウト</a></li>

      <?php
        }
      ?>
    </ul>
    <!--<ul class="footer-nav2">
      <li><a href="#">このサイトについて</a></li>
      <li><a href="#">サイト運営者について</a></li>
    </ul> -->
    <p>@ Read Happiness!! All rights reserved</p>
  </div>
</footer>

<script src="js/vendor/jquery-2.2.2.min.js"></script>
<script>
$(function(){
  //footerをページ下部に固定
  var $ftr = $('#footer');
  if(window.innerHeight > $ftr.offset().top + $ftr.outerHeight()){
    $ftr.attr({'style':'position:fixed; top:'+(window.innerHeight - $ftr.outerHeight())+'px; width:100%;'});
  }
  //画像ライブビュー
  var $dropArea = $('.area-drop');
  var $fileInput = $('.input_file');

  $dropArea.on('dragover', function(e){
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border', '3px #ccc dashed');
  });
  $dropArea.on('dragleave', function(e){
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border', 'none');
  });
  $fileInput.on('change', function(e){
    e.stopPropagation();
    e.preventDefault();
    $dropArea.css('border', 'none');
    var file = this.files[0];
    var $img = $(this).siblings('.prev-img');
    var fileReader = new FileReader();

    fileReader.onload = function(event){
      $img.attr('src', event.target.result).show();
    };
    fileReader.readAsDataURL(file);
  });

  //テキストエリアカウント1
  var $countUp = $('#js-count');
  var $countView = $('#js-count-view');

  $countUp.on('keyup', function(e){
    $countView.html($(this).val().length);
  });
  //テキストエリアカウント2
  var $countUp2 = $('#js-count2');
  var $countView2 = $('#js-count-view2');

  $countUp2.on('keyup', function(e){
    $countView2.html($(this).val().length);
  });
  //jsメッセージ表示
  var $jsShowMsg = $('#js-show-msg');
  var msg = $jsShowMsg.text();
  if(msg.replace(/^[\s_]+[\s_]+$/g, '').length){
    $jsShowMsg.slideToggle('slow');
    setTimeout(function(){$jsShowMsg.slideToggle('slow');}, 5000);
  }


  //お気に入り登録・解除
  var $like,
      likeProductId;
  $like = $('.js-click-like') || null;
  likeProductId = $like.data('productid') || null;

  if(likeProductId !== undefined && likeProductId !== null){
    $like.on('click', function(){
      var $this = $(this);
      $.ajax({
        type:"POST",
        url:"ajaxLike.php",
        data:{productId : likeProductId}
      }).done(function(data){
        console.log('Ajax Success');
        $this.toggleClass('active');
        var $jsShowMsg = $('#js-show-msg');
        var msg = $jsShowMsg.text();
        console.log(msg);
        if(msg.replace(/^[\s_]+[\s_]+$/g, '').length){
          $jsShowMsg.slideToggle('slow');
          setTimeout(function(){$jsShowMsg.slideToggle('slow');}, 5000);
        }
      }).fail(function(msg){
        console.log('Ajax Error');
      });
    });
  }
  //header部分固定

  $('.page-header').each(function(){
    var $window = $(window);
    var $header = $('.page-header');

    //HTMLの上辺からヘッダーの底辺までの高さ
    var contentsHeight = $header.offset().top + $header.outerHeight();

  //スクロール時に処理をするが、回転を1秒間あたり15回に制限
  $window.on('scroll', function(){
    if($window.scrollTop() >= contentsHeight){
      $header.addClass('stastic');
    }else{
      $header.removeClass('stastic');
    }
  });
  //スクロールイベントを発生させ、初期位置を決定する
  $window.trigger('scroll');
  });

  //スライドショー
  $('.js-slideshow').each(function(){

    var $slides = $(this).find('img'),
        slideCount = $slides.length,
        currentIndex = 0;

        //一枚目のスライドをフェードインで表示
        $slides.eq(currentIndex).fadeIn(3000);

        setInterval(showNextSlide, 5000);

        function showNextSlide(){
          var NextIndex = (currentIndex + 1) % slideCount;
          $slides.eq(currentIndex).fadeOut(1000);
          $slides.eq(NextIndex).fadeIn(3000);

          currentIndex = NextIndex;
        }
  });
  //上部へ戻るボタン
  $('.js-to-top').each(function(){

    var $toTopButton = $('.js-to-top');
    //ボタン非表示
    $toTopButton.hide();

    $(window).on('scroll', function(){
      if($(this).scrollTop() > 100){
        $toTopButton.fadeIn();
      }else{
        $toTopButton.fadeOut();
      }
    });
    $toTopButton.on('click', function(){
      $('body, html').stop(true).animate({
        scrollTop:0
      }, 1000);
    });
  });
});
</script>

</body>
</html>
