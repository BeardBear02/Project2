<?php
session_start();
require_once('../config.php');
include_once('outputNavLink.php');
include_once('outputPage.php');
if(!isset($_SESSION['UID'])){
    echo '<script type="text/javascript">location.replace("../index.php")</script>';
}
function outputFavorPics() {
    try {
        $pdo = new PDO(DBCONNSTRING,DBUSER,DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if(isset($_GET['page']) ){
            $page = intval( $_GET['page'] );
        }
        else{
            $page = 1;
        }
        $PageSize = 6;
        $sql = 'SELECT COUNT(*) AS amount from travelimage JOIN travelimagefavor ON travelimagefavor.UID = '.$_SESSION['UID'].' WHERE travelimage.ImageID = travelimagefavor.ImageID';
        $result = $pdo->query($sql);
        $row = $result->fetch();
        $amount = $row['amount'];
        if( $amount ){
            if( $amount % $PageSize ){                                  //取总数据量除以每页数的余数
                $totalPage = (int)($amount / $PageSize) + 1;           //如果有余数，则页数等于总数据量除以每页数的结果取整再加一
            }else{
                $totalPage = $amount / $PageSize;                      //如果没有余数，则页数等于总数据量除以每页数的结果
            }
        }
        else{
            $totalPage = 0;
        }
        $startNum = 6*($page-1);
        $sql = 'select travelimage.ImageID,Title,Description,PATH from travelimage JOIN travelimagefavor ON travelimagefavor.UID = :id WHERE travelimage.ImageID = travelimagefavor.ImageID LIMIT '.$startNum.',6';
        $id =  $_SESSION['UID'];
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':id', $id);
        $statement->execute();

        echo '<ul id="FavoritesList">';
        while($row = $statement->fetch()){
            if($row['Description'] == null){
                $row['Description'] = "The author is so lazy that he/she doesn't give any detailed description about this Photo.TUT";
            }
            outputSinglePic($row);
        }
        echo '</ul>';
        outputPageLink($page,$totalPage,'Favorites');
        $pdo = null;
    }catch (PDOException $e) {
        die( $e->getMessage() );
    }
}
function outputSinglePic($row) {
    echo '<li>';
    echo '<div class="favoritePic">';
    echo '<figure>';
    echo '<div class="PicWrapper">';
    $img = '<img class="normalPic" src="../images/normal/medium/'.$row['PATH'].'">';
    echo constructPicLink($row['ImageID'], $img);
    echo '</div><figcaption><div class="favorPicTopic">';
    echo $row['Title'];
    echo '</div><div class="description">';
    echo $row['Description'];
    echo '</div></figcaption>';
    echo '<input class="removeFavor" type="button" name="Remove" value="remove" alt="'.$row['ImageID'].'">';
    echo '</figure></div></li>';
}

/*
  Constructs a link given the genre id and a label (which could
  be a name or even an image tag
*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>收藏页</title>
    <link href="../CSS/reset.css" rel="stylesheet">
    <link href="../CSS/HeaderNavMainFooterPic.css" rel="stylesheet">
    <link href="../CSS/pageNavFooter.css" rel="stylesheet">
    <link href="../CSS/Favorites.css" rel="stylesheet">
</head>
<body onresize="squareClip()" onload="squareClip()">
<header>
    <div class="title">
        <h1><a name="header" style="color:#000000">Photo Life</a></h1>
        <div class = "slogan">Share your Life here</div>
    </div>
    <nav>
        <ul id="navPublic">
            <li><a href="../index.php"><img class="icon" src="../images/icons/首页-未选中.png">Home</a></li>
            <li><a href="Browser.php"><img class="icon" src="../images/icons/浏览-未选中.png">Browser</a></li>
            <li><a href="Search.php"><img class="icon" src="../images/icons/搜索-未选中.png">Search</a></li>
            <?php LoginOrOut('Favorites');?>
        </ul>
    </nav>
</header>
<main>
    <div class="NoFavorPic">
        <p>
        </p>
    </div>
    <?php outputFavorPics(); ?>

</main>
<footer>
    <div class="Information">
        <div class="introduction">BeardBear 版权所有</div>
        <div>联系我们 19302010014@fudan.edu.cn</div>
    </div>
</footer>
<script type="text/javascript" src="../jquery.js"></script>
<script type="text/javascript" src="../JavaScript/ImgClip.js"></script>
<script type="text/javascript" src="../JavaScript/WhenNothing.js"></script>
<script type="text/javascript" src="../JavaScript/RemoveLikePic.js"></script>
</body>
</html>
