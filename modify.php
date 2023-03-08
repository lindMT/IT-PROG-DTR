<html>
<head>
<title>Modify Report</title>
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
    <h1>Modify Record</h1>
    <hr style='background-color:#7798ab84; height: 3px; border:none;'/>
    
	<?php
		//get input
		$month = $_POST['month'];
		$imonth = date('m', strtotime($month));
		$iyear = date('Y', strtotime($month));
		$mname = date('F', mktime(0, 0, 0, $imonth, 10)); //convert month number to month name
		
		//get id
		$id = $_POST['id'];
		
		//get hiredate
		$hd = $_POST['hd'];
		$hmonth = date('m', strtotime($hd));
		$hyear = date('Y', strtotime($hd));
		
		//get current date
		$curdate = date ('Y-m-d');
		$cmonth = date('m');
		$cyear = date('Y');
		
		//validation: minimum date
		if($hmonth == $imonth && $hyear == $iyear){ //if month chosen is the same as month hired
			$fdate = date("Y-m-d", strtotime($hd)); //get date hired
		}else{
			$fdate = date("Y-m-d", strtotime($month.'-1')); //get first day of month
		}
		
		//validation: maximum date
		if ($cmonth == $imonth && $cyear == $iyear){ //if month chosen is the same as current month
			$ldate = date("Y-m-d"); //get current date
		}else{
			$ldate = date("Y-m-t", strtotime($month)); //get last day of month
		}
		
		//form: ask user for date
		echo "<p>Select record from the month of <b>$mname $iyear</b> to MODIFY:</p>";
		echo "<form method = 'POST' action = ''>";
		echo "	<input type='date' name='input' min='$fdate' max='$ldate' required /> ";
		echo "	<input type='submit' class='input_s' name='submitBtn' />";
		echo "	<input type='hidden' name='month' value='$iyear-$imonth'/>";
		echo "	<input type='hidden' name='id' value='$id'/>";
		echo "	<input type='hidden' name='hd' value='$hd'/>";
		echo "</form>";
		echo "<p><i>Note: You can only modify on a per-week basis. Weeks start on Sundays and end on Saturdays. Sundays are omitted from output (No Work).</i></p>";
		
		if(!isset($_POST['submitBtn'])){ //submit button not yet clicked
			//display report from previous page
			$query = mysqli_query($DBConnect, "SELECT *
												FROM dtr d
												WHERE d.employeeID='$id' 
												AND MONTH(d.date)=$imonth
												AND YEAR(d.date)=$iyear
												ORDER BY date");
			
			//display through table
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
			
			while($record = mysqli_fetch_array($query)){
				$date = date('d', strtotime($record['date']));
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
		}else{ //submit button clicked
			//modify
			
			//get week
			$input = $_POST['input'];
			$querysdate = mysqli_query($DBConnect, "SELECT *
												FROM dtr d
												WHERE d.employeeID='$id' 
												AND d.date='$input'");
			$retrieve = mysqli_fetch_array($querysdate);
			
			if($retrieve == null){ //if chosen date is not found in database
				$sdate = $input; //assume it is start date
			}else{
				$sdate = $retrieve['startOfWeekDate'];
			}
			
			$query = mysqli_query($DBConnect, "SELECT *
												FROM dtr d
												WHERE d.employeeID='$id' 
												AND d.startOfWeekDate='$sdate'
												ORDER BY date");
			$day = date('w', strtotime($input));
			$startweek = date('m-d-Y', strtotime($input.'-'.$day.' days'));
			$endweek = date('m-d-Y', strtotime($input.'+'.(6-$day).' days'));
			$title = $startweek.' to '.$endweek;
			
			if(mysqli_num_rows($query)==0){ //no records for chosen week
				echo "<p><b>DTR for chosen week does not exist.</b><br>$title</p>";
			}else{
				//taken from DTR Filing 1				
				$dayArray = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
				$dateArray = [];
				$fulldateArray = [];
		
				echo "<form method = 'POST' action = 'modified.php'>";
				echo "<table border='2' align = 'center' cellspacing='21' cellpadding='21'>";
				echo "	<tr><td colspan='7'><h1 align='center'>$title</h1></td></tr>";
				for ($i=0; $i<7; $i++){
					echo "	<td><b>$dayArray[$i]</b></td>"; //print days
				}
				echo "	<tr>";
				
				//fill dateArray & fulldateArray
				$j=0;
				$fulldateArray[$j] = $sdate;
				$dateArray[$j]= date('d', strtotime($sdate)); //store start date (Sunday)
				while($record = mysqli_fetch_array($query)){
					$j++;
					$fulldateArray[$j] = $record['date'];
					$dateArray[$j] = date('d', strtotime($record['date']));
				}
				foreach($dateArray as $print){
					echo "<td>$print</td>"; //print dates
				}
				echo "	</tr>";
				echo "</table>";	
		
				echo "<table border='2' align = 'center' cellspacing='21' cellpadding='21'>>";
				echo "	<tr>";
				echo "		<td align = 'center'><b> Day </b></td>";
				echo "		<td align = 'center'><b> Rest </b></td>";
				echo "		<td align = 'center'><b> Present </b></td>";
				echo "		<td align = 'center'><b> Start Time </b></td>";
				echo "		<td align = 'center'><b> End Time </b></td>";
				echo "	</tr>";		
				
				$_SESSION["fulldateArray"] = $fulldateArray;
				$_SESSION["dateArray"] = $dateArray;
				
				//query again (same as earlier)
				$query = mysqli_query($DBConnect, "SELECT *
												FROM dtr d
												WHERE d.employeeID='$id' 
												AND d.startOfWeekDate='$sdate'
												ORDER BY date");
				for ($i=2; $i<=7; $i++){
					$record = mysqli_fetch_array($query);
					echo "<tr align='center'>";
						echo "<td>" . $dayArray[$i-1] . "</td>";
						if ($record['isRest'] == true){
							echo "<td> <input type='checkbox' name='rest$i' checked> </td>";
						}else{
							echo "<td> <input type='checkbox' name='rest$i'> </td>";
						}
						if ($record['isPresent'] == true){
							echo "<td> <input type='checkbox' name='present$i' checked> </td>";
						}else{
							echo "<td> <input type='checkbox' name='present$i'> </td>";
						}
						if	($record['startTime'] == date('H:i:s', strtotime('00:00:00'))){
							echo "<td> <input type='time' name='start$i' min='07:00' max='09:00'> </td>";
						}else{
							echo "<td> <input type='time' name='start$i' min='07:00' max='09:00' value='$record[startTime]'> </td>";
						}
						if	($record['endTime'] == date('H:i:s', strtotime('00:00:00'))){
							echo "<td> <input type='time' name='end$i' min='14:00' max='23:00'> </td>";
						}else{
							echo "<td> <input type='time' name='end$i' min='14:00' max='23:00' value='$record[endTime]'> </td>";
						}
						
					echo "</tr>";
				}
				echo "<tr><td colspan='7'> <input type='submit' class='input_s' name='modBtn' align='center' /> </td></tr>";
				echo "</table>";
				echo "</form>"; 
				
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
			<?php
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
