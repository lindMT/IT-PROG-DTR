<html>
<head>
<title>Upload XML File </title>
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
		  $eno = $_SESSION['eno'];
          include("connect.php");
          $query = mysqli_query($DBConnect, "SELECT e.firstname, e.profilePicture, e.positionID, e.id
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
    <h1>Upload XML File of DTR</h1>
    <hr style='background-color:#7798ab84; height: 3px; border:none;'/>
    <p> Submit your XML file below. </p>
	<form method = "POST" action = "" enctype="multipart/form-data">
	  <input type='file' name='xml' accept=".xml"/>
	  <input type="submit" class="input_s" name="submitUpdate" />
	</form>
	<?php
	    if(isset($_POST['submitUpdate']))
	    {
			$xmlFile = file_get_contents($_FILES['xml']['tmp_name']);
			$xml = simplexml_load_string($xmlFile) or die("cant load xml");
			$query = mysqli_query($DBConnect, "SELECT employeeID, date, startOfWeekDate
                                               FROM dtr");
											   
			$query2 = mysqli_query($DBConnect, "SELECT hiredate, id
                                               FROM employee");
			$employeeID = $xml->employee->attributes();
			$startOfWeekDate = $xml->employee[0]->startOfWeekDate->attributes();
			$test_date = date('Y-m-d', strtotime($startOfWeekDate. ' + 7 days'));
			$test_startdate = date('Y-m-d', strtotime($startOfWeekDate));
			$min_starttime = "07:00:00";
			$max_starttime = "09:00:00";
			$max_endtime = "22:00:00";
			$_SESSION['dupli'] = false;
			$_SESSION['err'] = false;
			date_default_timezone_set('Asia/Manila');
			$currentDate = date('Y-m-d');
				if ($employeeID == $eno)
				{
					$restCount = 0;
					$checkStart = date('w', strtotime($startOfWeekDate));
					if ($checkStart != 0)
					{
						echo "Invalid start of week date.";
					}
					else
					{
						foreach ($xml->employee->startOfWeekDate->date as $d)
						{
							$date = $d->attributes();
							$isRest = $d->isRest;
							$isPresent = $d->isPresent;
							$startTime = $d->startTime;
							$endTime = $d->endTime;	
							$renderedHours = $d->renderedHours;
							$otHours = $d->otHours;
							
							if ((preg_match("/^(2[0-4]|[0-1][0-9]):([0-5][0-9]):([0-5][0-9])$/",$startTime)) && (preg_match("/^(2[0-4]|[0-1][0-9]):([0-5][0-9]):([0-5][0-9])$/",$endTime))) 
							{
								$test_starttime = explode(':', $startTime);
								$test_endtime = explode(':', $endTime);
								$starttime_mins = (((int)$test_starttime[0]) * 60) + (int)$test_starttime[1];						
								$endtime_mins = (((int)$test_endtime[0]) * 60) + (int)$test_endtime[1];
								$test_renderedHours = ((abs($endtime_mins - $starttime_mins)) - 60); // difference of start and end in minutes - 60 minutes (lunch break)
								
								if($test_renderedHours%60 <= 29)
								{
									$test_renderedHours = $test_renderedHours/60; // round down
								} else
								{
									$test_renderedHours = ($test_renderedHours/60) + 1; // round up
								}
								if($test_renderedHours < 0)
								{
									$test_renderedHours = 0;
								}
								$test_otHours = $test_renderedHours - 8;
								
								if($test_otHours < 0)
								{
									$test_otHours = 0;
								}	
								
								if($isRest == 1)
								{
									$test_otHours = 0;
									$test_renderedHours = 0;
									$restCount = $restCount + 1;
								}

								$test_arr  = explode('-', $date);
								
								if (($test_startdate > $date) || ($date > $test_date))
								{
									$_SESSION['err'] = true;
									$err = "Date is out of bounds.";
								}
								
								if (!(checkdate($test_arr[1], $test_arr[2], $test_arr[0]))) 
								{
									$_SESSION['err'] = true;
									$err = "Invalid date.";
								}
								
								if($isRest == 1 && $isPresent == 1)
								{
									$_SESSION['err'] = true;
									$err = "Invalid status. (Only choose one, present or rest)";
								}
								
								if($isRest == 0 && $isPresent == 0)
								{
									if($endtime_mins != 0 || $starttime_mins != 0)
									{
										$_SESSION['err'] = true;
										$err = "Invalid absence. (Can't have start/end time if absent)";
									}
									$test_otHours = 0;
									$test_renderedHours = 0;
								}
								
								if($isPresent == 1 && $renderedHours > 8)
								{
									$_SESSION['err'] = true;
									$err = "Rendered hours exceeds the maximum requirement.";
								}
								
								if($restCount != 1) 
								{
									$_SESSION['err'] = true;
									$err = "Invalid number of rest days. (DTR must only have one chosen rest day)";
								}
								
								if($startTime < $min_starttime && $isPresent == 1 && $isRest == 0)
								{
									$_SESSION['err'] = true;
									$err = "Invalid start time. (Must be 7:00 A.M. or later)";
								}
								
								if($endTime > $max_endtime && $isPresent == 1)
								{
									$_SESSION['err'] = true;
									$err = "Invalid end time. (Must be 10:00 P.M. or earlier)";
								}
								
								if($renderedHours != $test_renderedHours)
								{
									$_SESSION['err'] = true;
									$err = "Invalid rendered hours.";
								}
								
								if($otHours != $test_otHours || ($renderedHours <= 8 && $otHours > 0) || $otHours > 5)
								{
									$_SESSION['err'] = true;
									$err = "Invalid overtime hours.";
								}
								
								if($renderedHours <= 6 && ($isPresent == 1 && $isRest == 0))
								{
									$_SESSION['err'] = true;
									$err = "Rendered hours does not meet the minimum requirement.";
								}
								
								if($isRest == 1 && ($starttime_mins != 0 && $endtime_mins != 0))
								{
									$_SESSION['err'] = true;
									$err = "Invalid rest day.";
								}
								
								if($startTime > $max_starttime)
								{
									$_SESSION['err'] = true;
									$err = "Invalid start time.";
								}
								
								if($date > $currentDate)
								{
									$_SESSION['err'] = true;
									$err = "Date is after current date.";
								}
								
								if($date >= $test_date)
								{
									$_SESSION['err'] = true;
									$err = "Inconsistent date.";
								}
								
								while ($fetch = mysqli_fetch_array($query2))
								{
									if ($fetch['id'] == $employeeID)
									{
										if ($fetch['hiredate'] > $startOfWeekDate)
										{
											$_SESSION['err'] = true;
											$err = "Date is before hire date.";
											
										}
									}
								}
								
								while ($fetch = mysqli_fetch_array($query))
								{
									if ($fetch['employeeID'] == $employeeID)
									{
										if ($fetch['startOfWeekDate'] == $startOfWeekDate)
										{
											if ($fetch['date'] == $date)
											{
												$_SESSION['dupli'] = true;
												echo "Duplicate file.";
											}
										}
									}
								}
								
								if ($_SESSION['err'] == false && $_SESSION['dupli'] == false){// if no file errors
									$insert =  mysqli_query($DBConnect,"INSERT INTO 
																	dtr (employeeID, isRest, isPresent, startOfWeekDate, date, startTime, endTime, renderedHours, otHours) 
																	VALUES ('$employeeID', '$isRest', '$isPresent', '$startOfWeekDate', '$date', '$startTime', '$endTime', '$renderedHours', '$otHours') ");
								}
							}
							else
							{
								$_SESSION['err'] = true;
								$err = "Invalid time format.";
							}
						}
						if ($_SESSION['err'] == true)
						{
							echo $err;
							$query2 = mysqli_query($DBConnect, "SELECT * FROM dtr d");
								while ($get = mysqli_fetch_array($query2))
								{
									$delete = mysqli_query($DBConnect, "DELETE FROM dtr WHERE employeeID='$employeeID' AND startOfWeekDate='$startOfWeekDate'");
								}
						}
						else if (($_SESSION['dupli'] == false) && ($_SESSION['err'] == false))
						{
							echo "You have successfully registered.";
						}
					}
				}
				else
				{
					echo "Invalid employee ID.";
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

	




