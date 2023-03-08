<html>
<head><title>Update Module Component</title>
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
        <form method = "POST" action = "menu.php" enctype="multipart/form-data">
            <?php
                $sessionID = $_SESSION["getLogin"];
                $dupli = $_SESSION['dupli']; 
                $_SESSION['dupli']=false;
                $eno = $_SESSION['eno'];
                $query = mysqli_query($DBConnect, "SELECT * FROM employee WHERE id='$eno'");
                $fetch = mysqli_fetch_array($query);
                // if there was an username input error (existing in the db)
                if($dupli){
                    echo "Please enter a username non-existing on the database.<br>";
                }
                /////
                #############################################################################################
                $sessionID = $_SESSION["getLogin"];

                $query = mysqli_query($DBConnect,   "SELECT * 
                                                    FROM employee e
                                                    WHERE e.username='$sessionID'");
                $fetch = mysqli_fetch_array($query); 

                echo "<h1>Update Profile Information</h1>";
                echo "<hr style='background-color:#7798ab84; height: 3px; border:none;'/>";
                echo "Profile Photo</br>";
                echo '<img src="data:image/png;base64,'. base64_encode($fetch['profilePicture']) . '"height="100" width="100" style="border:3px solid white"/>' . "</br>";
                
                echo "<input type='file' name='photo' accept='image/png'/></br>";
                #############################################################################################   
                echo "<table>";
                echo "  <tr>";   
                echo "<td>Last Name:</td>";
                echo "<td> <input type = 'text' name='lname' value='".$fetch['lastname']."' /> </td>";
                echo "<td>First Name:</td>";
                echo "<td> <input type = 'text' name='fname' value='".$fetch['firstname']."' /> </td>";
                echo "  </tr>";
                echo "  <tr>"; 
                echo "<td>Username:</td>";
                echo "<td><input type = 'text' name='uname' value='".$fetch['username']."' /> </td>";
                echo "<td>Password (leave blank if you do not wish to change):</td>"; 
                echo "<td><input type = 'password' name='pass'/>  </td>";
                
                    //Gender
                echo "  <tr>"; 
                    if ($fetch['gender']=="M") {
                        echo "<td>Gender:</td>   <td><select name='gender'/>
                                                    <option value='M' selected>M</option>
                                                    <option value='F'>F</option>
                                                    <option value='Other'>Other</option>
                                                </select></td>";
                    } 
                    else if ($fetch['gender']=="F") {
                        echo "<td>Gender:</td>   <td><select name='gender'/>
                                                    <option value='M'>M</option>
                                                    <option value='F' selected>F</option>
                                                    <option value='Other'>Other</option>
                                                </select></td>";
                    } 
                    else {
                        echo "<td>Gender:</td>   <td><select name='gender'/>
                                                    <option value='M'>M</option>
                                                    <option value='F'>F</option>
                                                    <option value='Other' selected>Other</option>
                                                </select></td>";
                    } echo "<br>";    

                echo "<td>Hire Date:</td>";
                echo"<td> <input type = 'date' name='hdate' value='".$fetch['hiredate']."' /> </td>";
                    
                    //Position
                    $posQuery = mysqli_query($DBConnect,    "SELECT * 
                                                            FROM employee te JOIN position p 
                                                            WHERE id='$eno' AND te.positionID=p.positionID");
                    $posFetch = mysqli_fetch_array($posQuery);
                echo "  </tr>";
                echo "  <tr>"; 
                    if($posFetch != NULL){
                        echo "<td>Old Position:</td><td> ".$posFetch['positionName']." </td>";
                    }
                
                    echo "<td>New Position</td>  <td><select name='position' required/>
                                                    <option disabled selected value>- Select an Option -</option>
                                                    <option value='1'>Manager</option>
                                                    <option value='2'>Programmer</option>
                                                    <option value='3'>Encoder</option>
                                                    <option value='4'>Secretary</option>
                                                    <option value='5'>Network Admin</option>
                                                </select>
                                                </td>";
                echo "  </tr>";

                    //Status
                echo "  <tr>"; 

                    if ($fetch['status']=="Regular") {
                        echo "<td>Status:</td> <td><select name='status'/>
                                                        <option value='Regular' selected>Regular</option>
                                                        <option value='Probation'>Probation</option>
                                                    </select></td>";
                    } 
                    else {
                        echo "<td>Status:</td> <td><select name='status'/>
                                                    <option value='Regular'>Regular</option>
                                                    <option value='Probation' selected>Probation</option>
                                                </select></td>";
                    }                
                         
            ?>

            <td colspan="2" align="center"><input type="submit" class="input_s" name="submitUpdate" /></td>
                </tr>
                </table>
        </form>
        <a href="menu.php">
            <div class="fixed3">   
            <button class="btn"><i class="fa-solid fa-circle-arrow-left"> Back to Menu</i> </button>
            </div>
        </a>
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