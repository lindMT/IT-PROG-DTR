<?php
    $DBConnect = mysqli_connect("localhost", "root", "") or die ("Unable to Connect" . mysqli_error());
    $db = mysqli_select_db($DBConnect, 'dbemployeedtr');
?>