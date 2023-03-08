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
  <!-- Update logic (with all echo statements commented out) -->
  <?php
		if(isset($_POST['submitUpdate'])){
			include("connect.php");
			$eno = $_SESSION['eno'];        
			$lname = $_POST["lname"];
			$fname = $_POST["fname"];
			$uname = $_POST["uname"];
			$status = $_POST["status"];
			$gender = $_POST["gender"];
			$hdate = $_POST["hdate"];
			$position = $_POST["position"];
			$_SESSION['dupli'] = false;

			// this code segment checks if the new username has a duplicate
			$query = mysqli_query($DBConnect, "SELECT * FROM employee e WHERE e.id != '$eno'");
			while($retrieve = mysqli_fetch_array($query)){
				if ($uname == $retrieve['username']){
					$_SESSION['dupli'] = true; 
				}
			}

			if (!$_SESSION['dupli']){
				//echo "The user has been updated.<br/>";
				// this code segment sets the appropriate encrypted password
				if (strlen($_POST["pass"]) == 0){
					$sessionID = $_SESSION["getLogin"];
					$query = mysqli_query($DBConnect,   "SELECT * 
														FROM employee e
														WHERE e.username='$sessionID'");
					$fetch = mysqli_fetch_array($query);
					$pass = $fetch['password'];
				}
				else{
					$pass = md5($_POST["pass"]);
				}
				// this code segment finds the equivalent pos name of the pos id
				$posQuery = mysqli_query($DBConnect,    "SELECT * 
														FROM position p
														WHERE p.positionID = '$position'");
				$posFetch = mysqli_fetch_array($posQuery);
				//echo "Position: <b>" . $posFetch['positionName'] . "</b><br />";
				
				$_SESSION['getLogin'] = $uname;
					
					if (!file_exists($_FILES['photo']['tmp_name']) || !is_uploaded_file($_FILES['photo']['tmp_name'])){
						$update = mysqli_query ($DBConnect, "UPDATE employee 
															SET lastname = '$lname',
															firstname = '$fname',
															username = '$uname',
															password = '$pass',
															status = '$status',
															gender = '$gender',
															hiredate = '$hdate',
															positionID = '$position'
															WHERE id='$eno'");
						//echo "<br/>photo is NOT set";
					}
					else {
						$imgData = addslashes(file_get_contents($_FILES['photo']['tmp_name']));
						$update = mysqli_query ($DBConnect, "UPDATE employee 
															SET lastname = '$lname',
															firstname = '$fname',
															username = '$uname',
															password = '$pass',
															status = '$status',
															gender = '$gender',
															hiredate = '$hdate',
															positionID = '$position',
															profilePicture = '$imgData'
															WHERE id='$eno'");
						//echo "<br/>photo is set";
					}
			}
			/*
			else{
				echo "Please enter valid changes.<br/>";
			}
			*/
		}
	?>
  
  <!-- Page content -->
  <div class="main">
    
        <html>
        <head><title>Menu</title></head>
          <body>
            <?php
              $sessionID = $_SESSION["getLogin"];
              $_SESSION['dupli']=false;

              echo "<h1>User Data</h1>";
              echo "<hr style='background-color:#7798ab84; height: 3px; border:none;'/>";
                $query = mysqli_query($DBConnect,   "SELECT * 
                                                    FROM employee e
                                                    WHERE e.username='$sessionID'");
                $fetch = mysqli_fetch_array($query);
                
              echo "<div id='header' style='height:15%;width:100%;'>";
                echo "<div style='float:left'>";
                  echo "<table>";
                  echo '&nbsp;&nbsp;<img src="data:image/png;base64,'. base64_encode($fetch['profilePicture']) . '"height="200" width="200" style="border:3px solid white;"/>' . "<br /><br />";
                  echo "  <tr>";
                  echo "    <td style='background-color:#2b2e3b; color:#ddd;'>". $fetch['firstname'];
                  echo " ". $fetch['lastname'] ."</td>";
                 
                  echo "  </tr>";
                  echo "</table>";
                echo "</div>";
                echo "<div style='float:left'>";
                  echo "<table style='margin-left:20%;float:top;'>";
                    echo "  <tr>";
                    echo "    <td>ID</td>";
                    echo "    <td>". $fetch['id'] ."</td>";
                    echo "  </tr>";
                    echo "  <tr>";
                    echo "    <td>Username</td>";
                    echo "    <td>". $fetch['username'] ."</td>";
                    echo "  </tr>";
                    echo "  <tr>";
                    echo "    <td>Status</td>";
                    echo "    <td>". $fetch['status'] ."</td>";
                    echo "  </tr>";
                    echo "  <tr>";
                    echo "    <td>Gender</td>";
                    echo "    <td>". $fetch['gender'] ."</td>";
                    echo "  </tr>";
                    echo "  <tr>";
                    echo "    <td>Hire Date</td>";
                    echo "    <td style='width:150px'>". $fetch['hiredate'] ."</td>";
                    echo "  </tr>";
                    echo "  <tr>";
                    echo "    <td>Position</td>";
                    echo "    <td>";
                echo "</div>";
              echo "</div>";
                //if positionID isnt set yet/null
                if(!$fetch['positionID']){
                  echo "Please register your position!</td>"; 
                }
                else{
                  $posQuery = mysqli_query($DBConnect,   "SELECT * 
                                                    FROM employee e JOIN position p
                                                    ON e.positionID = p.positionID
                                                    WHERE e.username='$sessionID'");
                  $posFetch = mysqli_fetch_array($posQuery);
                  echo $posFetch['positionName']."</td>";
                }
                  echo "  </tr>";
                echo "</table>";
                $_SESSION['eno'] = $fetch['id'];
                $_SESSION['dtrFilingWarning'] = 0;
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
