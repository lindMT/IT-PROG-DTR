<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/b4e98bb39f.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Archivo:500|Open+Sans:300,700" rel="stylesheet">
    
    <br>
        <a href="client.php">
        <div class="fixed1">   
        <button class="btn"><i class="fa-solid fa-comments"> Need help?</i></button>
        </div>
        </a>
</head>
    <body> 
        <h2 align="center"> Welcome to Employee's Login</h2>
        <div align="center">
        <i class="fa-solid fa-computer fa-6x" style="color: #2b2e3b"></i>
        </div></br>
        <table class="data" align = "center"> 
            <tr>
                <form method="POST" action="check.php"> 
                    <td>Username:</td>
                    <td><input type="text" name="username"/></td>
                    <tr><td>Password: </td><td><input type="password" name="pass"/></td></tr> 
                    <tr>
                        <td colspan="2" align="center">
                            <input type="submit" class="input_s" value="Login" name="loginBtn"/><br/><br/>
                            New? Register <a href="regForm.php">here.</a>
                        </td>
                    </tr>
                </form> 
        </table>
        <?php 
            if(isset($_GET["error"])) {
                $error=$_GET["error"];
                if ($error==1) {
                    echo "<p align='center'>Username and/or password invalid<br/></p>";
                }
            }
            session_start();
            $_SESSION['dupliRegData'] = false;
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