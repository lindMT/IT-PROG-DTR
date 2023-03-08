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
		//display record to be deleted
		$sdate = $_POST['sdate'];
		$id = $_POST['id'];
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
		
		//delete record
		$delete = mysqli_query($DBConnect, "DELETE FROM dtr WHERE employeeID='$id' AND startOfWeekDate='$sdate'");
		if ($delete == false){ //failed
			echo "<p>Error: Record deletion failed.</p>";
		}else{ //success
			echo "<p>The record above has successfully been deleted.</p>";
		}
		
		echo "<br/><a href='menu.php'><button class='btn'><i class='fa-solid fa-circle-arrow-left'> Back to Menu</i> </button></a>";
	?>
	
    <a href="menu.php">
        <div class="fixed3">   
        <button class="btn"><i class="fa-solid fa-circle-arrow-left"> Back to Menu</i> </button>
        </div>
    </a>
  </div>

</body>
</html>
