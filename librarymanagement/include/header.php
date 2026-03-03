<header>
  <div class="wrapper">
    <nav>
      <ul id="menu">
        <li><a href="index.php">Home</a></li>
        <li><a href="courses.php">Courses</a></li>

        <li><a href="rulesregulation.php">Rules & Regulation</a></li>
        <li><a href="about_us.php">About us</a></li>
        <li class="end"><a href="contacts.php">Contact Us</a></li>
      </ul>

    </nav>
    <div style="margin-top: 15px; float: right; height:45;">

      <a href="<?= BASE_URL ?>librarian/login.php" title="Teacher Login"><img src="images/admin1.png" alt=""></a>
      <a href="<?= BASE_URL ?>student/login.php" title="Student Login"><img src="images/staff1.png" alt=""></a>

    </div>

  </div>
  <div class="wrapper">

    <?php
    include("connection/connection.php");

    $sql = "SELECT * FROM setting WHERE id = '1'";
    $header = mysqli_query($conn, $sql);

    if ($header && mysqli_num_rows($header) > 0) {
      $rowheader = mysqli_fetch_assoc($header);
    } else {
      $rowheader = [
        'webtitle' => 'Library Management System'
      ];
    }
    ?>


    <h1 class="style6 style1 style2"><?php echo $rowheader['webtitle']; ?></h1>
    <p class="style6 style1">&nbsp;</p>
    <p class="style5">&nbsp;<span class="style1">&nbsp;&nbsp;&nbsp;College for quality education</span></p>
  </div>
  <div id="slogan"> We Will Open The World<span>of knowledge for you!</span> </div>
</header>