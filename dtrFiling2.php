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
    
        <html>
        <head><title>DTR Filing</title></head>
          <body>
            <?php
                echo "<h1>DTR Filing</h1>";
                echo "<hr style='background-color:#7798ab84; height: 3px; border:none;'/>";
                
                include("connect.php");
                $dayArray = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
                $dateArray = $_SESSION["dateArray"];
                $id = $_SESSION['eno'];
                $oneRest = 0;
                $isValid = true;
                $query = mysqli_query($DBConnect, "SELECT d.startOfWeekDate
                                                    FROM dtr d
                                                    WHERE d.employeeID='$id'");

                while ($fetch = mysqli_fetch_array($query)){
                    if ($fetch['startOfWeekDate'] == $_SESSION['startDate']){
                        $_SESSION['dtrFilingWarning'] = 5; // already filed a dtr for this week
                        $isValid = false;
                    }
                }
                
                for($i=2; $i<=7; $i++){
                    if ($isValid){ 

                        if (isset($_POST['present'.$i]) && isset($_POST['rest'.$i])){
                            $_SESSION['dtrFilingWarning'] = 4; // both present and rest on the same day
                            $isValid = false;
                        }

                        if (!isset($_POST['present'.$i]) || isset($_POST['rest'.$i])){// absent or restday
                            if (isset($_POST['rest'.$i])){// rest day
                                $oneRest++;
                                if ($oneRest >=2){
                                    $_SESSION['dtrFilingWarning'] = 1; // more than 1 rest day was selected
                                    $isValid = false;
                                } 
                            }
                        }
                        else{ // present
                            if ($_POST['start'.$i] == NULL || $_POST['end'.$i] == NULL){ // if present and one start/end isnt 
                                $_SESSION['dtrFilingWarning'] = 2; // start/end isnt set
                                $isValid = false;                                                    
                            }
                            else if (isset($_POST['start'.$i]) && isset($_POST['end'.$i])){
                                $startOfWeekDate = $dateArray[0];
                                $date = $dateArray[$i-1];
                                $startTime = $_POST['start'.$i];
                                $endTime = $_POST['end'.$i];
                                $startTime = strtotime($startTime);
                                $endTime = strtotime($endTime);

                                if($startTime >= $endTime){
                                    $_SESSION['dtrFilingWarning'] = 2; // inavlid start and end times
                                    $isValid = false;
                                }

                                $renderedHours = ((abs($endTime - $startTime)/60) - 60); // difference of start and end in minutes - 60 minutes (lunch break)
                                if($renderedHours%60 <= 29){
                                    $renderedHours = $renderedHours/60; // round down
                                } else{
                                    $renderedHours = ($renderedHours/60) + 1; // round up
                                }

                                if($renderedHours < 6){ 
                                    $_SESSION['dtrFilingWarning'] = 3; // too little rendered hours (6 hours minimum)
                                    $isValid = false; 
                                }
								
								if($renderedHours > 13){
									$_SESSION['dtrFilingWarning'] = 6; // too much rendered hours (13 hours total maximum)
									$isValid = false; 
								}
                            }
                        }
                    }
                }
                
                if ($isValid){
                    if($oneRest == 0){
                        $_SESSION['dtrFilingWarning'] = 1; // no selected rest day
                        $isValid = false;
                    }
                }
                echo "<table>";
                echo "<tr><td><b>Sunday: <b></td><td> Rest Day</td><tr>"; 
                if ($isValid){
                    for($i=2; $i<=7; $i++){
                        echo "<tr><td><b>" . $dayArray[$i-1] . ": <b></td>";
                        if (!isset($_POST['present'.$i]) || isset($_POST['rest'.$i])){// absent or restday
                            if (isset($_POST['rest'.$i])){// rest day
                                $isRest = true;
                                $isPresent = false;
                                echo "<td>Rest Day </td><tr>"; 
                            }
                            else { // absent
                                $isRest = false;
                                $isPresent = false;
                                echo "<td>Absent </td><tr>"; 
                            }
                            $startOfWeekDate = $dateArray[0];
                            $date = $dateArray[$i-1];
                            $startTime = NULL;
                            $endTime = NULL;
                            $renderedHours = 0;
                            $otHours = 0;                      
                        }
                        else { // present
                            if (isset($_POST['start'.$i]) && isset($_POST['end'.$i])){
                                echo "<td>Present </td><tr>"; 
                                $isRest = false;
                                $isPresent = true;
                                $startOfWeekDate = $dateArray[0];
                                $date = $dateArray[$i-1];
                                $startTime = $_POST['start'.$i];
                                $endTime = $_POST['end'.$i];

                                $startTime2 = strtotime($startTime);
                                $endTime2 = strtotime($endTime);

                                $renderedHours = ((abs($endTime2 - $startTime2)/60) - 60); // difference of start and end in minutes - 60 minutes (lunch break)
                                if($renderedHours%60 <= 29){
                                    $renderedHours = $renderedHours/60; // round down
                                } else{
                                    $renderedHours = ($renderedHours/60) + 1; // round up
                                }
                                $otHours = 0;
                                if($renderedHours > 8){
                                    $otHours = $renderedHours - 8;
                                    $renderedHours = 8;
                                    if ($otHours > 5){
                                        $otHours = 5;
                                    }
                                }
                            }
                        }
                    
                        $insert =   mysqli_query($DBConnect,"INSERT INTO 
                                                            dtr (employeeID, isRest, isPresent, startOfWeekDate, date, startTime, endTime, renderedHours, otHours)
                                                            VALUES ('$id', '$isRest', '$isPresent', '$startOfWeekDate', '$date', '$startTime', '$endTime', '$renderedHours', '$otHours')");
                    }
                }
                else {
                    $url = "dtrFiling1.php";
                    echo "<script type='text/javascript'>document.location.href='{$url}';</script>";
                }

                
                if(mysqli_affected_rows($DBConnect))
                    echo "<tr><td colspan='2'>The DTR record has been added.</td><tr>";
                else
                    echo "The DTR record was not added.";
                echo "<tr><td colspan='2'><a href='menu.php'><button class='btn'>Back to Menu</button></a></td><tr>";
                echo "</table>";
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