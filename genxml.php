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
    
	<h1>Generate XML</h1>
	<?php
		//get hidden data
		$id = $_POST['id'];
		$my = $_POST['month'];
		$month = date('m', strtotime($my));
		$year = date('Y', strtotime($my));
		
		//get records from dtr
		$query = mysqli_query($DBConnect, "SELECT *
											FROM dtr d
											WHERE d.employeeID='$id' 
											AND MONTH(d.date)=$month
											AND YEAR(d.date)=$year
											ORDER BY date");
		
		//check if records exist
		if (mysqli_num_rows($query) == 0){ //no records
			echo "<p>Report does not exist.</p>";
		}else{
			//create xml
			$xml = new DOMDocument('1.0', 'utf-8');
			
			$rootElem = $xml->createElement('report');
			$empAttr = $rootElem->setAttribute('empNo', $id);
			$monthAttr = $rootElem->setAttribute('month', $my);
			$sDate = null;
			$append = false;
			
			while($retrieve = mysqli_fetch_array($query)){
				//get data from records from dtr
				$date = $retrieve['date'];
				$sTime = $retrieve['startTime'];
				$eTime = $retrieve['endTime'];
				$isRest = $retrieve['isRest'];
				$isPresent = $retrieve['isPresent'];
				$rHours = $retrieve['renderedHours'];
				$oHours = $retrieve['otHours'];
				$totalHrs = $rHours + $oHours;
				
				//create elements and attributes			
				if(is_null($sDate) || $sDate != $retrieve['startOfWeekDate']){
					$sDate = $retrieve['startOfWeekDate'];
					$sDateElem = $xml->createElement('startOfWeekDate');
					$sDateAttr = $sDateElem->setAttribute('value', $sDate);
					$append = true;
				}
				
				$dateElem = $xml->createElement('date');
				$dateAttr = $dateElem->setAttribute('value', $date);
				
				if($isRest == true) //rest day
					$status = 'Rest Day';
				else if($isPresent == true) //present
					$status = 'Present';
				else //absent
					$status = 'Absent';
				
				$statElem = $xml->createElement('status', $status);
				$restAttr = $statElem->setAttribute('isRest', $isRest);
				$presentAttr = $statElem->setAttribute('isPresent', $isPresent);
				
				$sTimeElem = $xml->createElement('timeIn', $sTime);
				$eTimeElem = $xml->createElement('timeOut', $eTime);
				
				$totalHrsElem = $xml->createElement('totalHours', $totalHrs);
				$rHoursAttr = $totalHrsElem->setAttribute('renderedHours', $rHours);
				$oHoursAttr = $totalHrsElem->setAttribute('overtimeHours', $oHours);
				
				//append child nodes to their parents
				$dateElem->appendChild($statElem);
				$dateElem->appendChild($sTimeElem);
				$dateElem->appendChild($eTimeElem);
				$dateElem->appendChild($totalHrsElem);
				
				$sDateElem->appendChild($dateElem);
				
				if($append == true){
					$rootElem->appendChild($sDateElem);
					$append = false;
				}
				
			}
			
			$xml->appendChild($rootElem);
			$xml->formatOutput = true; //adds line breaks and indentation
			
			//validation against xsd
			if($xml->schemaValidate('xmlreport.xsd')){
				echo "<p>The generated report is valid.</p>";
				echo "<textarea readonly rows='25' cols='110'>".htmlspecialchars($xml->saveXML())."</textarea>";
			}else{
				echo "<p>The generated report is invalid.</p>";
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
