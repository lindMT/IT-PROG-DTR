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
        <head><title>Modify Record</title></head>
          <body>
            <?php
                echo "<h1>Modify Record</h1>";
                echo "<hr style='background-color:#7798ab84; height: 3px; border:none;'/>";
                include("connect.php");
                $dayArray = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
				$fulldateArray = $_SESSION["fulldateArray"];
                $id = $_SESSION['eno'];
                ################################################################################
                $oneRest = 0;
                $isValid = true;
                for($i=2; $i<=7; $i++){
					$date = $fulldateArray[$i-1];
                    if (!isset($_POST['present'.$i.'']) || isset($_POST['rest'.$i.''])){// absent or restday
                        if (isset($_POST['rest'.$i.''])){// rest day
                            $oneRest++;
                            if ($oneRest >=2){
                                $_SESSION['modWarning'] = 1; // more than 1 rest day 
                                $isValid = false;
                            } 
                        }
						if (isset($_POST['present'.$i.'']) && isset($_POST['rest'.$i.''])){ // both present and rest are checked
							$_SESSION['modWarning'] = 1;
							$isValid = false;
						}
                    }
                    else{ // present
                        if ($_POST['start'.$i.''] == null || $_POST['end'.$i.''] == null){ // if present and may hindi na set
                            $_SESSION['modWarning'] = 2; // not set yung start and end
                            $isValid = false;                                                    
                        }
                        else if (isset($_POST['start'.$i.'']) && isset($_POST['end'.$i.''])){
                            $startTime = $_POST['start'.$i.''];
                            $endTime = $_POST['end'.$i.''];
    
                            $startTime = strtotime($startTime);
                            $endTime = strtotime($endTime);

                            if($startTime >= $endTime){
                                $_SESSION['modWarning'] = 2; // invalid start and end
                                $isValid = false;
                            }

                            $renderedHours = ((abs($endTime - $startTime)/60) - 60); // diff in minutes - 60 (lunch break)
                            if($renderedHours%60 <= 29){
                                $renderedHours = $renderedHours/60; // round down
                            } else{
                                $renderedHours = ($renderedHours/60) + 1; // round up
                            }

                            if($renderedHours < 6){ // if renderedhours is below minimum
                                $_SESSION['modWarning'] = 3; // too little rendered hours
                                $isValid = false; 
                            }
							
							if($renderedHours > 13){ // if renderedhours is above maximum
								$_SESSION['modWarning'] = 4; // too much rendered hours
                                $isValid = false; 
							}
                        }
                    }
                }
                
                if($oneRest == 0){
                    $_SESSION['modWarning'] = 1; // 0 rest day
                    $isValid = false;
                }
                ################################################################################
                if ($isValid){
                    for($i=2; $i<=7; $i++){
						$date = $fulldateArray[$i-1];
                        if (!isset($_POST['present'.$i.'']) || isset($_POST['rest'.$i.''])){// absent or restday
                            if (isset($_POST['rest'.$i.''])){// rest day
                                $isRest = true;
                                $isPresent = false;
                            }
                            else { // absent
                                $isRest = false;
                                $isPresent = false;
                            }
                            $startTime = NULL;
                            $endTime = NULL;
                            $renderedHours = 0;
                            $otHours = 0;                      
                        }
                        else { // present
                            if (isset($_POST['start'.$i.'']) && isset($_POST['end'.$i.''])){
                                $isRest = false;
                                $isPresent = true;
                                $startTime = $_POST['start'.$i.''];
                                $endTime = $_POST['end'.$i.''];

                                $startTime2 = strtotime($startTime);
                                $endTime2 = strtotime($endTime);

                                $renderedHours = ((abs($endTime2 - $startTime2)/60) - 60); // diff in minutes - 60 (lunch break)
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

                        $update =   mysqli_query($DBConnect,"UPDATE dtr
                                                            SET isRest='$isRest', isPresent='$isPresent', startTime='$startTime', endTime='$endTime', renderedHours='$renderedHours', otHours='$otHours'
															WHERE employeeID='$id'
															AND date='$date'");
                    }
                }
                else {
                    $url = "generate.php";
                    echo "<script type='text/javascript'>document.location.href='{$url}';</script>";
                }

                if($update == true){
                    echo "The DTR record has successfully been modified. New record is as follows: <br/><br/>";
					
					//display modified record
					$sdate = $fulldateArray[0];
					$query = mysqli_query($DBConnect, "SELECT *
														FROM dtr d
														WHERE d.employeeID='$id' 
														AND d.startOfWeekDate='$sdate'
														ORDER BY date");
					$day = date('w', strtotime($sdate));
					$endweek = date('m-d-Y', strtotime($sdate.'+'.(6-$day).' days'));
					$title = $sdate.' to '.$endweek;
					
					echo "<table border='2'>";
					echo "	<tr><td colspan='6' align='center'><p style='font-size:25px;'><b>$title</b></p></td></tr>";
					echo "	<tr>";
					echo "		<td><b>Date</b></td>";
					echo "		<td><b>Day of Week</b></td>";
					echo "		<td><b>Status</b></td>";
					echo "		<td><b>Time In</b></td>";
					echo "		<td><b>Time Out</b></td>";
					echo "		<td><b>Total Rendered Hours</b></td>";
					echo "	</tr>";
					
					while($record = mysqli_fetch_array($query)){
						$date = date('M d, Y', strtotime($record['date']));
						$day = date('l', strtotime($record['date']));
						$rest = $record['isRest'];
						$present = $record['isPresent'];
						
						if($rest == true){ //rest day
							$status = 'Rest Day';
						}else if($rest == false && $present == false){ //absent
							$status = 'Absent';
						}else{ //present
							$status = 'Present';
							$in = date('g:i A', strtotime($record['startTime']));
							$out = date('g:i A', strtotime($record['endTime']));
							$total = $record['renderedHours']+$record['otHours'];
						}
						
						//print one row (one record)
						echo "<tr>";
						echo "	<td>$date</td>";
						echo "	<td>$day</td>";
						echo "	<td>$status</td>";
						
						if($present == true){ //present
							echo "	<td>$in</td>";
							echo "	<td>$out</td>";
							echo "	<td>$total</td>";
						}else{ //rest day, absent
							echo "	<td colspan='3' align='center'><hr style='border:1px solid black; width:80%;'></td>";
						}
						
						echo "</tr>";
					}
					echo "</table>";
				}
                else
                    echo "Error: Record modification failed.";
                echo "<br/><a href='menu.php'><button class='btn'><i class='fa-solid fa-circle-arrow-left'> Back to Menu</i> </button></a>";
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