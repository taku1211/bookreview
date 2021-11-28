<body class="index">
  <header class="page-header">
    <div class="header-container">
      <div class="sitetitle">
        <h1><a href="index.php">Read Happiness!!</a></h1>
      </div>
        <nav>
          <ul class="header-nav">
            <?php if(empty($_SESSION['user_id'])){
              ?>
            <li><a href="login.php">ログイン</a></li>
            <li><a href="userRegist.php">新規会員登録</a></li>
            <?php
          }else{
             ?>
             <li><a href="mypage.php">マイページ</a></li>
             <li><a href="logout.php">ログアウト</a></li>
          <?php
          }
           ?>
          </ul>
        </nav>
    </div>
  </header>
