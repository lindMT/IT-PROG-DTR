<?php 
    require("connect.php"); 
    if (isset($_POST["loginBtn"])){
        $user=$_POST["username"];
        $_SESSION['user'] = $user;
        $pass = md5($_POST["pass"]);
        $query = mysqli_query($DBConnect,   "SELECT username, password FROM employee
                                            WHERE username='$user'
                                            AND password='$pass'");
        $fetch = mysqli_fetch_array($query);
        
        if($user==$fetch["username"] && $pass==$fetch["password"]){
            session_start();
            $_SESSION['getLogin'] = $user; 
            header("location:menu.php");
        }
        else {
            header("location:login.php?error=1");
        }
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
