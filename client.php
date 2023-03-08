<html>
<head><title>Chat Client</title>
<link rel="stylesheet" href="style.css">
<script src="https://kit.fontawesome.com/b4e98bb39f.js" crossorigin="anonymous"></script>
<link href="https://fonts.googleapis.com/css?family=Archivo:500|Open+Sans:300,700" rel="stylesheet">
</head>
<body>
<h2>Welcome to our employee chat system.</h2>
<p>Compose below to start a session and talk with on of our employees.</p>

<form method="POST">
    <?php  
    $host = "127.0.0.1";
    $port = 4950;
    set_time_limit(0);

    if (isset($_POST["btnSend"])){
            $msg = $_REQUEST["txtMessage"];
            $sock = socket_create(AF_INET, SOCK_STREAM, 0);
            socket_connect($sock, $host, $port);
            
            socket_write($sock, $msg, strlen($msg));

            $msg = "You said:\t".$msg."\n\n";

            $reply = socket_read($sock, 1924);
            $reply = trim($reply);
            $reply = "Server says:\t". $reply;
    }
    ?>

    <textarea rows="15" cols="45" style="resize:none;">
    <?php
        echo @$msg;
        echo @$reply; 
    ?>
    </textarea>
    </br></br>
    <input type="text" name="txtMessage" size="39" placeholder="Enter message...">
    <input type="submit" class="input_s" name="btnSend" value="Send" ><br/><br/>
</form>
<br>
<?php
        session_start();
        if(!isset($_SESSION["getLogin"])){
?>
            <a href="login.php">
            <div class="fixed2">   
            <button class="btn"><i class="fa-solid fa-circle-arrow-left"> Go back</i> </button>
            </div>
            </a>
<?php
        } else {
          $sessionID = $_SESSION["getLogin"];
          include("connect.php");
?>
          <a href="menu.php">
            <div class="fixed2">   
            <button class="btn"><i class="fa-solid fa-circle-arrow-left"> Go back</i> </button>
            </div>
            </a>
<?php
        }
?>
</body>
</html>