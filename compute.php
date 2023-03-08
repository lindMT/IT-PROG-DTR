<html>
<head>
<title>Compute Compensation </title>
<link rel="stylesheet" href="style.css">
<script src="https://kit.fontawesome.com/b4e98bb39f.js" crossorigin="anonymous"></script>
<link href="https://fonts.googleapis.com/css?family=Archivo:500|Open+Sans:300,700" rel="stylesheet">
</head>
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
          echo '<img src="data:image/png;base64,'. base64_encode($fetch['profilePicture']) . '"height="100" width="100"/>' . "<br /><br />";
        }
      ?>
    Profile
      <a href="menu.php" style="font-family: 'Open Sans', sans-serif;font-weight: 300;">&nbsp;<i class="fa-solid fa-eye" style="color: white"></i> Show</a>
      <a href="update.php" style="font-family: 'Open Sans', sans-serif;font-weight: 300;">&nbsp;<i class="fa-solid fa-file-arrow-up" style="color: white"></i> Update</a>
    </p>

    <p>File DTR (weekly)
      <a href="dtrFiling1.php" style="font-family: 'Open Sans', sans-serif;font-weight: 300;">&nbsp;<i class="fa-solid fa-file" style="color: white"></i> File through Portal </a>
      <a href="uploadxml.php" style="font-family: 'Open Sans', sans-serif;font-weight: 300;">&nbsp;<i class="fa-solid fa-list-check" style="color: white"></i> Upload XML </a>
    </p>
      
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
    <h1>Compute Salary Compensation</h1>
    <hr style='background-color:#7798ab84; height: 3px; border:none;'/>
    
    <?php
    $query = mysqli_query($DBConnect, "SELECT * FROM employee WHERE username='$sessionID'");
    $fetch = mysqli_fetch_array($query);
    if ($fetch['positionID'] == NULL)
    {
      echo "You do not have a job position yet. Please update your profile before checking your compensation.";
    }
    else
    { 
      echo "<h3> Select the salary month to be computed: </h3>";
    
      //get user info
      $sessionID = $_SESSION["getLogin"];
      $queryemp = mysqli_query($DBConnect, "SELECT e.id, e.hiredate
                              FROM employee e
                              WHERE e.username='$sessionID'");
      $emp = mysqli_fetch_array($queryemp);
      //get hire date
      $hd = $emp['hiredate'];
      $hmonth = date('m', strtotime($emp['hiredate']));
      $hyear = date('Y', strtotime($emp['hiredate']));
      
      //get current date
      $month = date('m');
      $year = date('Y');
      
      if(isset($_POST['submitBtn'])){
      }else{
        echo "<p> Select Month: </p>";
      }
        
      echo "<form method = 'POST' action = ''>";
      echo "<input type='month' name='month' min='$hyear-$hmonth' max='$year-$month' required/>"; 
      //minimum value: month hired, maximum value: current month
      echo " <input type='submit' class='input_s' name='submitBtn' />";
      echo "</form>";

			if(isset($_POST['submitBtn'])){
        $month = $_POST['month'];
        echo "<h3> Choose whether to see the 15th/30th pay:</h3>";
        echo "<form method = 'POST' action = 'compute2.php'>";
        echo "  <input type='radio' name='day' value='15'/> 15th <br>";
        echo "  <input type='radio' name='day' value='30'/> 30th <br>";
        echo "<input type='hidden' name='monthHidden' value='$month;'/>";
        echo "<br><input type='submit' class='input_s' name='confirmBtn' />";
        echo "</form>";
      }
    }
    ?>
    <a href="menu.php">
        <div class="fixed3">   
        <button class="btn"><i class="fa-solid fa-circle-arrow-left"> Back to Menu</i> </button>
        </div>
    </a>
  </div>

</body>
</html>
