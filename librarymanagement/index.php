<?php 
include("connection/connection.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include "pageinfo.php"; ?>
<meta charset="utf-8">
<link rel="stylesheet" href="css/reset.css" type="text/css" media="all">
<link rel="stylesheet" href="css/layout.css" type="text/css" media="all">
<link rel="stylesheet" href="css/style.css" type="text/css" media="all">
<script type="text/javascript" src="js/jquery-1.5.2.js" ></script>
<script type="text/javascript" src="js/cufon-yui.js"></script>
<script type="text/javascript" src="js/cufon-replace.js"></script>
<script type="text/javascript" src="js/Molengo_400.font.js"></script>
<script type="text/javascript" src="js/Expletus_Sans_400.font.js"></script>
<style type="text/css">
.style5 {
	font-size: 36px
}
.style6 {font-size: 36px; color: #000033; }
.style7 {color: #000033}
</style>
</head>
<body id="page2">
<div class="body1">
  <div class="main">
   <?php 
   include "include/header.php";
   ?>
  </div>
</div>
<div class="body2">
  <div class="main">
    <!-- content -->
    <section id="content">
      <div class="wrapper">
        <div class="pad1 pad_top1">
          <article class="cols marg_right1">
            <figure><img src="images/img1.png" alt=""></figure>
          </article>
          <article class="cols marg_right1">
            <figure><img src="images/img2.png" alt=""></figure>
          </article>
          <article class="cols">
            <figure><img src="images/img3.png" alt=""></figure>
          </article>
        </div>
      </div>
      <div class="box1">
        <div class="wrapper">
          <?php
          echo $row['description'];
         
		  ?>
           
          <?php include "notice.php";?>
          
                    
        </div>
      </div>
    </section>
    <!-- content -->
   <?php include "include/footer.php"; ?>
  </html>