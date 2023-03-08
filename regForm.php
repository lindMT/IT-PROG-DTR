<html>
<head><title>Entry Form for Employee's Record</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/b4e98bb39f.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Archivo:500|Open+Sans:300,700" rel="stylesheet">
</head>
    <body>
    <div class="sidenav">
        <p>
        <?php
            session_start();
            include("connect.php");
        ?>
        <a href="logout.php" style="font-family: 'Archivo', sans-serif; font-weight: 500; font-size: 25px;">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Back to Login page </a><br/>
            
    </div>

    <a href="client.php">
        <div class="fixed1">
        <button class="btn"><i class="fa-solid fa-comments"> Need help?</i></button>
        </div>
    </a>

    <div class="main">
        <h1>Register User Information</h1>
        <hr style='background-color:#7798ab84; height: 3px; border:none;'/>
        <p>Enter the following information. Please note that all fields are required.</p>
            <?php
            //if there was a duplicate eno or username
            if ($_SESSION['dupliRegData']){
                echo "Please enter a nonexisting username/id number.<br>"; 
                $_SESSION['dupliRegData'] = false;
            }
			
			//get current date
			$cdate = date("Y-m-d");
            ?>
            <form method="POST" action="add.php">
                    <table>
                        <tr>
                        <td>ID Number :</td>
                        <td><input type="number" name="eno" size="30" required/></td>
                        </tr>	
                    
                        <tr>
                        <td>Last Name : </td>
                        <td><input type="text" name="lname" size="25" required/></td>
                        </tr>
                    
                        <tr>
                        <td>First Name : </td>
                        <td><input type="text" name="fname" size="25" required/></td>
                        </tr>	
                    
                        <tr>
                        <td>Username : </td>
                        <td><input type="text" name="uname" size="25" required/></td>
                        </tr>
                    
                        <tr>
                        <td>Password</td>
                        <td><input type="password" name="pass" size="25" required/></td>
                        </tr>
                    
                        <tr>
                            <td>Status : </td>
                            <td>
                                <select name='status' required/>
                                    <option disabled selected value>- Select an Option -</option>
                                    <option value='Regular'>Regular</option>
                                    <option value='Probation'>Probation</option>
                                </select>
                            </td>
                        </tr>
                    
                        <tr>
                            <td>Gender : </td>
                            <td>
                                <select name='gender' required/>
                                    <option disabled selected value>- Select an Option -</option>
                                    <option value='M'>M</option>
                                    <option value='F'>F</option>
                                    <option value='Other'>Other</option>
                                </select>
                            </td>
                        </tr>
                    
                    
                        <tr>
                            <td>Hire Date : </td>
						<?php
                            echo "<td><input type='date' name='hdate' size='25' max='$cdate' required/></td>";
						?>
                        </tr>
                        <tr>
                            <td colspan="2" align="center"><input type="submit" class="input_s" name="submitRegForm" /></td>
                        </tr>
                    </table>
            </form>

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