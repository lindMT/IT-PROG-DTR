<html>
<head><title>Add Module Component</title>
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
        include("connect.php");       
      ?>

    <a href="logout.php" style="font-family: 'Archivo', sans-serif; font-weight: 500; font-size: 25px;">
      <i class="fa-solid fa-arrow-right-from-bracket"></i> Back to Login </a><br/>
        
  </div>
  <a href="client.php">
    <div class="fixed1">   
      <button class="btn"><i class="fa-solid fa-comments"> Need help?</i></button>
    </div>
  </a>

  <!-- Page content -->
  <div class="main">
        <?php
            if (isset($_POST["eno"]))
            {
              $eno = $_POST["eno"];
              $lname = $_POST["lname"];
              $fname = $_POST["fname"];
              $uname = $_POST["uname"];
              $pass = md5($_POST["pass"]);
              $status = $_POST["status"];
              $gender = $_POST["gender"];
              $hdate = $_POST["hdate"];

              $query = mysqli_query($DBConnect,   "SELECT *
                                                  FROM employee e");

              while ($fetch = mysqli_fetch_array($query)){
      	        if ($fetch['id'] == $eno){
                  $_SESSION['dupliRegData'] = true;
                }
      	        if ($fetch['username'] == $uname){
                  $_SESSION['dupliRegData'] = true;
                }
              }

              if ($_SESSION['dupliRegData'] == false){// if no duplicate eno and username
                $filename = "images/defaultDP.png";
                $imgData = addslashes(file_get_contents($filename));
                $insert =   mysqli_query($DBConnect,"INSERT INTO 
                                                  employee (id, lastname, firstname, username, password, status, gender, hiredate, positionID, profilePicture) 
                                                  VALUES ('$eno', '$lname', '$fname', '$uname', '$pass', '$status', '$gender', '$hdate', NULL, '$imgData')");
              }
              else{
                header("location:regForm.php");
              }

              if(mysqli_affected_rows($DBConnect))
              {
                echo "<h1>Employee Registration</h1>";
                echo "<hr style='background-color:#7798ab84; height: 3px; border:none;'/>";
                echo "<table>";
                echo "  <tr>";
                echo "    <td> You have successfully registered. </td>";
                echo "  </tr>";
                echo "</table>";
              }
            }
            else{
              $url = "login.php";
              echo "<script type='text/javascript'>document.location.href='{$url}';</script>";
            }
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
    </div>
    </body>
</html>