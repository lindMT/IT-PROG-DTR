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
        
  </div>;
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
		  if(isset($_POST['confirmBtn']))
      {
        $month = $_POST['monthHidden'];
        $day = $_POST['day'];
        $month = substr($month, 0,7);
        #must first check if selected month has enough for 2 weeks
        #must be isPresent + count days
        
        # Get user info
        $sessionID = $_SESSION["getLogin"];
        $id = $_SESSION['eno'];
        $query = mysqli_query($DBConnect, "SELECT * FROM dtr WHERE employeeID='$id' and isPresent='1'");
        $query2 = mysqli_query($DBConnect, "SELECT * FROM employee e 
                                                       JOIN position p ON e.positionID = p.positionID
                                                       WHERE e.id='$id'");
        $query3 = mysqli_query($DBConnect, "SELECT * FROM dtr WHERE employeeID='$id' and isRest='1'");
        $query4 = mysqli_query($DBConnect, "SELECT * FROM dtr WHERE employeeID='$id' and isRest='0' AND isPresent='0'");
        
          # Checking validity of present days
          # Computing working hours
          # Computing overtime hours
          $counterP = 0;
          $workHrs = 0;
          $otHrs = 0;
          $week = 0;
          $startDay = 0;
          $startCompare = 0;

          while($fetch = mysqli_fetch_array($query))
          {
            # If the 15th
            if((date("m",strtotime($fetch['date'])) == date("m",strtotime($month))) && $day == 15 && (date("j",strtotime($fetch['date'])) <= 15))
            {
              $counterP = $counterP+1;
              $workHrs = $workHrs + $fetch['renderedHours'];
              $otHrs = $otHrs + $fetch['otHours'];
            }
            # If the 30th
            else if((date("m",strtotime($fetch['date'])) == date("m",strtotime($month))) && $day == 30 && (date("j",strtotime($fetch['date'])) >= 15))
            {
              $counterP = $counterP+1;
              $workHrs = $workHrs + $fetch['renderedHours'];
              $otHrs = $otHrs + $fetch['otHours'];
            }
            $startDay = date("j", strtotime($fetch['startOfWeekDate']));
            if($startDay != $startCompare && ($counterP > 0) && (date("m",strtotime($fetch['date'])) == date("m",strtotime($month))))
            {
              $week = $week+1;
            }
            $startCompare = $startDay;
          }

          # Checking rest days
          $counterR = 0;
          while($fetch = mysqli_fetch_array($query3))
          {
            if((date("m",strtotime($fetch['date'])) == date("m",strtotime($month))) && date("j",strtotime($fetch['date'])) <= 15)
            {
              $counterR = $counterR+1;
            }
            else if((date("m",strtotime($fetch['date'])) == date("m",strtotime($month))) && date("j",strtotime($fetch['date'])) >= 15)
            {
              $counterR = $counterR+1;
            }
          }

          # Checking absent days
          $counterA = 0;
          while($fetch = mysqli_fetch_array($query4))
          {
            if((date("m",strtotime($fetch['date'])) == date("m",strtotime($month))) && date("j",strtotime($fetch['date'])) <= 15)
            {
              $counterA = $counterA+1;
            }
            else if((date("m",strtotime($fetch['date'])) == date("m",strtotime($month))) && date("j",strtotime($fetch['date'])) >= 15)
            {
              $counterA = $counterA+1;
            }
          }

          $finalSalary = 0;
          $taxDeduct = 0;
          $workSalary = 0;
          $otSalary = 0;

          # Salary computation
          $retrieve = mysqli_fetch_array($query2);
          $workSalary = $workHrs*$retrieve['salary'];
          $otSalary = $otHrs*$retrieve['ot'];
            
          if(($workSalary+$otSalary) < 10417)
          {
            $finalSalary = $workSalary+$otSalary;
          }
          else if((10417 <= ($workSalary+$otSalary)) && (($workSalary+$otSalary) <= 16666))
          {
            $taxDeduct = (($workSalary+$otSalary) - 10417)*0.20;
            $finalSalary = ($workSalary+$otSalary) - $taxDeduct;
          }
          else if((16667 <= ($workSalary+$otSalary)) && (($workSalary+$otSalary) <= 33332))
          {
            $taxDeduct = ((($workSalary+$otSalary) - 16667)*0.25) + 1250;
            $finalSalary = ($workSalary+$otSalary) - $taxDeduct;
          }
          else if((33333 <= ($workSalary+$otSalary)) && (($workSalary+$otSalary) <= 83332))
          {
            $taxDeduct = ((($workSalary+$otSalary) - 33333)*0.30) + 5416.67;
            $finalSalary = ($workSalary+$otSalary) - $taxDeduct;
          }
          else if((83333 <= ($workSalary+$otSalary)) && (($workSalary+$otSalary) <= 333332))
          {
            $taxDeduct = ((($workSalary+$otSalary) - 83333)*0.32) + 20416.67;
            $finalSalary = ($workSalary+$otSalary) - $taxDeduct;
          }
          else if(333332 > ($workSalary+$otSalary))
          {
            $taxDeduct = ((($workSalary+$otSalary) - 333333)*0.35) + 100416.67;
            $finalSalary = ($workSalary+$otSalary) - $taxDeduct;
          }
        
          setlocale(LC_MONETARY,"en_PH");
          if(($week==0) && ($counterA==0))
          {
            echo "<table>";
            echo "  <tr>";
            echo "    <td> You do not have a DTR filed for the ".$day."th of the month. Please file a DTR first. </td>";
            echo "  </tr>";
            echo "</table>";
          }
          else if(($week==1)  && ($counterA==0))
          {
            echo "<table>";
            echo "  <tr>";
            echo "    <td> You have only filled one week of your chosen computation to view. You must file for two consecutive weeks to view your compensation. </td>";
            echo "  </tr>";
            echo "</table>";
          }
          else
          {
            echo "<p> Listed below is the breakdown of your salary for the <b>".$day."th </b> of ";
            echo"<b>".date("F",strtotime($month))." ".date("Y",strtotime($month)).".</b></p>";

            # Table breakdown
            echo "<table>";
            echo "  <tr>";
            echo "    <td>Position: </td>";
            echo "    <td>".$retrieve['positionName']." </td>";
            echo "  </tr>";
            echo "  <tr>";
            echo "    <td>Days On-Call: </td>";
            echo "    <td>".$counterP."</td>";
            echo "  </tr>";
            echo "  <tr>";
            echo "    <td>Rest days (excluding Sundays): </td>";
            echo "    <td>".$counterR."</td>";
            echo "  </tr>";
            echo "  <tr>";
            echo "    <td> </td>";
            echo "    <td> </td>";
            echo "  </tr>";
            echo "  <tr>";
            echo "    <td>Working hours: </td>";
            echo "    <td>".$workHrs."</td>";
            echo "  </tr>";
            echo "  <tr>";
            echo "    <td>Overtime hours: </td>";
            echo "    <td>".$otHrs." </td>";
            echo "  </tr>";
            echo "  <tr>";
            echo "    <td>Total rendered hours: </td>";
            echo "    <td>".$workHrs+$otHrs."</td>";
            echo "  </tr>";
            echo "  <tr>";
            echo "    <td></td>";
            echo "    <td> </td>";
            echo "  </tr>";
            echo "  <tr>";
            echo "    <td>Compensation: </td>";
            echo "    <td>".number_format($workSalary+$otSalary,2)." </td>";
            echo "  </tr>";
            echo "  <tr>";
            echo "    <td></td>";
            echo "    <td> </td>";
            echo "  </tr>";
            echo "  <tr>";
            echo "    <td>Tax: </td>";
            echo "    <td>".number_format($taxDeduct,2)." </td>";
            echo "  </tr>";
            echo "  <tr>";
            echo "    <td>Final Compensation: </td>";
            echo "    <td>".number_format($finalSalary,2)." </td>";
            echo "  </tr>";
            echo "</table>";
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
