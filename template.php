<link rel="stylesheet" href="style.css">
<script src="https://kit.fontawesome.com/b4e98bb39f.js" crossorigin="anonymous"></script>
<link href="https://fonts.googleapis.com/css?family=Archivo:500|Open+Sans:300,700" rel="stylesheet">

<body>
  <!-- Side navigation -->
  <div class="sidenav">
    <p>
      <?php
        session_start();
        if(!isset($_SESSION["getLogin"])){
           header("location:login.php");
        } else {
          $sessionID = $_SESSION["getLogin"];
          include("connect.php");
          $query = mysqli_query($DBConnect, "SELECT e.firstname, e.profilePicture, e.positionID
                                            FROM employee e
                                            WHERE e.username='$sessionID'");
          $fetch = mysqli_fetch_array($query);
          echo "Good Day, " . $fetch['firstname'] . "!<br/><br/>";
          echo '<img src="data:image/png;base64,'. base64_encode($fetch['profilePicture']) . '"height="100" width="100" style="border:3px solid white"/>' . "<br /><br />";
        }
      ?>
    Profile
    </p>

    <a href="menu.php">&nbsp;<i class="fa-solid fa-eye" style="color: white"></i> Show</a>
    <a href="update.php">&nbsp;<i class="fa-solid fa-file-arrow-up" style="color: white"></i> Update</a><br/>

    <p>File DTR (weekly)</p>
    <a href="dtrFiling1.php">&nbsp;<i class="fa-solid fa-file" style="color: white"></i> File through Portal </a>
    <a href="uploadxml.php">&nbsp;<i class="fa-solid fa-list-check" style="color: white"></i> Upload XML </a><br/>

    <a href="compute.php" style="font-family: 'Archivo', sans-serif; font-weight: 500; font-size: 25px;">
      <i class="fa-solid fa-calculator" style="color: white"></i> Compute Compensation </a><br/>

    <a href="generate.php" style="font-family: 'Archivo', sans-serif; font-weight: 500; font-size: 25px;">
      <i class="fa-solid fa-chart-column" style="color: white"></i> Generate Report </a><br/>
    
    <a href="logout.php" style="font-family: 'Archivo', sans-serif; font-weight: 500; font-size: 25px;">
      <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout </a><br/>
        
  </div>
  <a href="client.php">
    <div class="fixed1">   
      <button class="btn"><i class="fa-solid fa-comments"> Need help?</i></button>
    </div>
  </a>
  <!-- Page content -->
  <div class="main">
    
        <html>
        <head><title>INSERT TITLE HERE</title></head>
          <body>
            <?php
              echo "<h1>Insert Page Title</h1>";
              echo "<hr style='background-color:#7798ab84; height: 3px; border:none;'/>";

              /* INSERT DATA HERE */
            ?>
              
              <script type="text/javascript">
                function preventBack() {
                  window.history.forward();
                }
                setTimeout("preventBack()", 0);
                window.onunload = function () {
                  null
                };
              </script>

          </body>
        </html>
  </div>

</body>
