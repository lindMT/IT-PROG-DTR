<html>
<head>
<title>Delete Record</title>
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
    <h1>Delete Record</h1>
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
		echo "<p>Select record from the month of <b>$mname $iyear</b> to DELETE:</p>";
		echo "<form method = 'POST' action = ''>";
		echo "	<input type='date' name='input' min='$fdate' max='$ldate' required /> ";
		echo "	<input type='submit' class='input_s' name='submitBtn' />";
		echo "	<input type='hidden' name='month' value='$iyear-$imonth'/>";
		echo "	<input type='hidden' name='id' value='$id'/>";
		echo "	<input type='hidden' name='hd' value='$hd'/>";
		echo "</form>";
		echo "<p><i>Note: You can only delete on a per-week basis. Weeks start on Sundays and end on Saturdays. Sundays are omitted from output (No Work).</i></p>";
		
		if(isset($_POST['submitBtn'])){
			$input = $_POST['input'];
			
			//get week
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
			
			
		}else{ //display report from previous page		
			//get dtr
			$query = mysqli_query($DBConnect, "SELECT *
												FROM dtr d
												WHERE d.employeeID='$id' 
												AND MONTH(d.date)=$imonth
												AND YEAR(d.date)=$iyear
												ORDER BY date");
			$title = $mname.' '.$iyear;
		}
		
		//display through table
		if(mysqli_num_rows($query)==0){ //no records for chosen week
			echo "<p><b>DTR for chosen week does not exist.</b><br>$title</p>";
		}else{
			echo "<table border='2'>";
			echo "	<tr><td colspan='6' align='center'><p style='font-size:25px;'><b>$title</b></p></td></tr>";
			echo "	<tr>";
			if(isset($_POST['submitBtn'])){
				echo "	<td><b>Date</b></td>";
			}else{
				echo "	<td><b>Day of Month</b></td>";
			}
			echo "		<td><b>Day of Week</b></td>";
			echo "		<td><b>Status</b></td>";
			echo "		<td><b>Time In</b></td>";
			echo "		<td><b>Time Out</b></td>";
			echo "		<td><b>Total Rendered Hours</b></td>";
			echo "	</tr>";
			
			while($record = mysqli_fetch_array($query)){
				if(isset($_POST['submitBtn'])){
					$date = date('M d, Y', strtotime($record['date']));
				}else{
					$date = date('d', strtotime($record['date']));
				}
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
			
			//confirm deletion
			if(isset($_POST['submitBtn'])){
				echo "<tr>";
				echo "	<td colspan='5' align='right'><p>Delete this record?</p></td>";
				echo "<form method = 'POST' action = 'deleted.php'>";
				echo "	<td colspan='1' align='center'><input type='submit' class='input_s' name='delBtn' value='Delete'/></td>";
				echo "	<input type='hidden' name='sdate' value='$sdate'/>";
				echo "	<input type='hidden' name='id' value='$id'/>";
				echo "</form>";
				echo "</tr>";
			}
			
			echo "</table>";
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
