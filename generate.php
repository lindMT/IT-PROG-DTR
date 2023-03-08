<html>
<head>
<title>Generate Report </title>
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
    <h1>Generate Report <i>(monthly)</i></h1>
    <hr style='background-color:#7798ab84; height: 3px; border:none;'/>
    
	<!-- validation: user can only view month hired to current month -->
	<?php
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
	?>
	
	<!-- form: ask user for month -->
	<form method = 'POST' action = ''>
		<?php 
			echo "<input type='month' name='input' min='$hyear-$hmonth' max='$year-$month' required/>"; 
			//minimum value: month hired, maximum value: current month
		?>
		<input type='submit' class='input_s' name='submitBtn' />
	</form>
	
	<?php
		if(isset($_POST['submitBtn'])){
			$_SESSION['modWarning'] = 0;
			
			//get input
			$input = $_POST['input'];
			$imonth = date('m', strtotime($input));
			$iyear = date('Y', strtotime($input));
			$mname = date('F', mktime(0, 0, 0, $imonth, 10)); //convert month number to month name
			echo "<p> Listed below is a report of your DTR: </p>";
			
			//get dtr
			$id = $emp['id'];
			$querydtr = mysqli_query($DBConnect, "SELECT *
												FROM dtr d
												WHERE d.employeeID='$id' 
												AND MONTH(d.date)=$imonth
												AND YEAR(d.date)=$iyear
												ORDER BY date");

			//display dtr through table
			echo "<table border='2'>";
			echo "	<tr><td colspan='6' align='center'><p style='font-size:25px;'><b>$mname $iyear</b></p></td></tr>";
			echo "	<tr>";
			echo "		<td><b>Day of Month</b></td>";
			echo "		<td><b>Day of Week</b></td>";
			echo "		<td><b>Status</b></td>";
			echo "		<td><b>Time In</b></td>";
			echo "		<td><b>Time Out</b></td>";
			echo "		<td><b>Total Rendered Hours</b></td>";
			echo "	</tr>";
			
			while($dtr = mysqli_fetch_array($querydtr)){
				$date = date('d', strtotime($dtr['date']));
				$day = date('l', strtotime($dtr['date']));
				$rest = $dtr['isRest'];
				$present = $dtr['isPresent'];
				
				if($rest == true){ //rest day
					$status = 'Rest Day';
				}else if($rest == false && $present == false){ //absent
					$status = 'Absent';
				}else{ //present
					$status = 'Present';
					$in = date('g:i A', strtotime($dtr['startTime']));
					$out = date('g:i A', strtotime($dtr['endTime']));
					$total = $dtr['renderedHours']+$dtr['otHours'];
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
			
			//hidden form: to send data to next page (modify)
			echo "<form method='POST' action='modify.php'>";
			echo "	<input type='hidden' name='month' value='$iyear-$imonth'/>";
			echo "	<input type='hidden' name='id' value='$id'/>";
			echo "	<input type='hidden' name='hd' value='$hd'/>";
			
			echo "	<tr>";
			echo "		<td colspan='2' align='center'><input type='submit' class='input_s' name='modBtn' value='Modify Record' /></td>"; //modify
			echo "</form>";
			
			//hidden form: to send data to next page (delete)
			echo "<form method='POST' action='delete.php'>";
			echo "	<input type='hidden' name='month' value='$iyear-$imonth'/>";
			echo "	<input type='hidden' name='id' value='$id'/>";
			echo "	<input type='hidden' name='hd' value='$hd'/>";
			
			echo "		<td colspan='2' align='center'><input type='submit' class='input_s' name='delBtn' value='Delete Record' /></td>"; //delete
			echo "</form>";
			
			//hidden form: to send data to next page (create xml)
			echo "<form method='POST' action='genxml.php'>";
			echo "	<input type='hidden' name='month' value='$iyear-$imonth'/>";
			echo "	<input type='hidden' name='id' value='$id'/>";
			
			echo "		<td colspan='2' align='center'><input type='submit' class='input_s' name='exportBtn' value='Generate XML' /></td>"; //delete
			echo "</form>";
			
			echo "	</tr>";
			echo "</table>";
			
			//faq
			echo "<p><i>Important Reminders:</i></p>";
			echo "<ul>";
			echo "	<li>If DTR has not yet been filed, it will not show up in report.</li>";
			echo "	<li>Sundays are omitted because they are No Work days.</li>";
			echo "	<li>Total rendered hours is the sum of working hours and overtime hours.</li>";
			echo "</ul>";
		}else{
			//validation: for modified.php
			if (!isset($_SESSION['modWarning']))
				$modWarning = 0;
			else
				$modWarning = $_SESSION['modWarning'];
			switch ($modWarning){
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
					echo "<h1>Please select a valid start/end time. <br/>(Reminder: 13 hours of work is the total MAXIMUM excluding the 1 hour lunchbreak)</h1>";
					break;
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
