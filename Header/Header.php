<link rel="stylesheet" href="../Header/css/Header.css" />
<div class="header">
        <div class="left">
            <div class="logo">
                <!-- トップページへの遷移 -->
                <a href="../Top/TopPage.php">
                    <img src="../image/IconDesign.svg" alt="サイトロゴ" width="100">
                </a>
            </div>
        </div>
        <div class="center">
            <!-- プロフィールへの遷移 -->
            <div class="icon">
                <?php
                if(!empty($_SESSION)){
                    echo '<a href="../Profile/Profile.php">';
                } else {
                    echo '<a href="../Login/LoginIn.php">';
                }
                ?>
                    <div class="circle">
                        <!-- ここの画像はデータベースから取得する-->
                        <?php
                            if(isset($_SESSION['users']['icon'])) {
                                echo '<img src="'. $_SESSION['users']['icon'] .'" alt="profileIcon">';
                            } else {
                                echo '<img src="../image/DefaultIcon.svg" alt="profileIcon">';
                            }
                        ?>
                    </div>
                </a>
            </div>
        </div>
        <div class="right">
            <div class="search">
                <form action="../Search/Search.php" method="post">
                    <input type="text" autocomplete="off" aria-autocomplete="list" aria-controls="react-autowhatever-1" class="search-input" placeholder="キーワードを入力" name="search" value="" spellcheck="false" data-ms-editor="true">
                    <button type="submit" class="search-button">
                        <img src="../image/SearchIcon.svg" alt="検索">
                    </button>
                </form>
                <a href="../Search/Search.php" class="search-link">
                    <img src="../image/SearchIcon.svg" alt="検索">
                </a>
            </div>
        </div>
</div>

