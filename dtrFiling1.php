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
				echo "<h3>Please select the day you want to file the weekly DTR in.</h3>";
		
				//get user info
				$sessionID = $_SESSION["getLogin"];
				$queryemp = mysqli_query($DBConnect, "SELECT e.id, e.hiredate
										FROM employee e
										WHERE e.username='$sessionID'");
				$emp = mysqli_fetch_array($queryemp);
				//get hire date
				$hd = $emp['hiredate'];
				$hdate = date("Y-m-d", strtotime($emp['hiredate']));
				//get current date
				$cdate = date("Y-m-d");

				echo "<form method = 'POST' action = ''>";
				echo "<input type='date' name='input' min='$hdate' max='$cdate' required/>"; 
				//minimum value: month hired, maximum value: current month
				echo " <input type='submit' class='input_s' name='submitBtn' />";
				echo "</form>";
				
				$dtrFilingWarning = $_SESSION['dtrFilingWarning'];
				switch ($dtrFilingWarning){
					case 1:
						echo "<h1>Please select 1 rest day.</h1>";
						break;
					case 2:
						echo "<h1>Please select a valid start/end time.</h1>";
						break;
					case 3:
						echo "<h1>Please select a valid start/end time. <br/>(Reminder: 6 hours of work is the MINIMUM excluding the 1 hour lunchbreak)</h1>";
						break;
					case 4:
						echo "<h1>Please don't select both present and rest day on the same day.</h1>";
						break;
					case 5:
						echo "<h1>You have already filed a DTR for this week, please select another.</h1>";
						break;
					case 6:
						echo "<h1>Please select a valid start/end time. <br/>(Reminder: 13 hours of work is the total MAXIMUM excluding the 1 hour lunchbreak)</h1>";
						break;
					default:
						$_SESSION['dtrFilingWarning'] = 0;
						break;
				}
				echo "<br/>"; 
				if(isset($_POST['submitBtn'])){
					$INPUT = $_POST['input'];
					$dayArray = ['Sunday','Monday','Tuesday','Wednesday','Thursday', 'Friday','Saturday'];
					$dateArray = [];
					$selectedDayIndex = date('w',strtotime($INPUT));
			
					echo "<form method = 'POST' action = 'dtrFiling2.php'>";
					echo "<table border='2' align = 'center' cellspacing='21' cellpadding='21'>";
					echo "	<tr>";
					echo "		<td colspan='7'><h1 align='center'>"; 
						for($i=$selectedDayIndex; $i >= 0; $i--){
							if($i == $selectedDayIndex){
								echo date('Y-m-d', strtotime($INPUT. ' - '.$i.' days'));
								$_SESSION['startDate'] = date('Y-m-d', strtotime($INPUT. ' - '.$i.' days'));
							}
							array_push($dateArray, date('Y-m-d', strtotime($INPUT. ' - '.$i.' days')));
						} 
					
						for($i=$selectedDayIndex + 1; $i < 7; $i++){
							if($i + 1 == 7){
								echo " to " . date('Y-m-d', strtotime($INPUT. ' + '.($i - $selectedDayIndex).' days')) ."<br>";
								$_SESSION['endDate'] = date('Y-m-d', strtotime($INPUT. ' + '.($i - $selectedDayIndex).' days'));
							}
							array_push($dateArray, date('Y-m-d', strtotime($INPUT. ' + '.($i - $selectedDayIndex).' days')));
						} 
						$_SESSION["dateArray"] = $dateArray;
					echo "		</h1></td>";
					echo "	</tr>";
					echo "		<td><b>Sunday</b></td>";
					echo "		<td><b>Monday</b></td>";
					echo "		<td><b>Tuesday</b></td>";
					echo "		<td><b>Wednesday</b></td>";
					echo "		<td><b>Thursday</b></td>";
					echo "		<td><b>Friday</b></td>";
					echo "		<td><b>Saturday</b></td>";
					echo "	</tr>";
			
					echo "	<tr>";
						for($i=$selectedDayIndex; $i >= 0; $i--){
							echo "<td>" . date("d", strtotime($INPUT. ' - '.$i.' days')) . "</td>";
						}
			
						for($i=$selectedDayIndex + 1; $i < 7; $i++){
							echo "<td>" . date("d", strtotime($INPUT. ' + '.($i - $selectedDayIndex).' days')) . "</td>";
						}
					echo "	</tr>";
					echo "</table>";	
			
					echo "<table border='2' align = 'center' cellspacing='21' cellpadding='21'>";
					echo "	<tr align='center'>";
					echo "		<td><b> Day </b></td>";
					echo "		<td><b> Rest </b></td>";
					echo "		<td><b> Present </b></td>";
					echo "		<td><b> Start Time </b></td>";
					echo "		<td><b> End Time </b></td>";
					echo "	</tr>";		
			
					for ($i=2; $i<=7; $i++){
						echo "<tr align='center'>";
							echo "<td>" . $dayArray[$i-1] . "</td>";
							echo "<td> <input type='checkbox' name='rest$i'> </td>";
							echo "<td> <input type='checkbox' name='present$i'> </td>";
							echo "<td> <input type='time' name='start$i' min='07:00' max='09:00'> </td>";
							echo "<td> <input type='time' name='end$i' min='14:00' max='23:00'> </td>";
						echo "</tr>";
					}
					echo "<tr><td colspan='7'> <input type='submit' class='input_s' name='submitFileBtn' align='center' /> </td></tr>";
					echo "</table>";
					$_SESSION['dtrFilingWarning'] = 0;
					echo "</form>"; 
				}
				echo "<br/>";
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