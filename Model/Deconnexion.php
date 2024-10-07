<?php
session_start();
session_destroy();
setcookie("ID", "", time() - 3600, "/");
setcookie("role", "", time() - 3600, "/");


/*
session_start();//Test
echo $_SESSION['role']."\n";
echo $_SESSION['ID']."\n";
echo $_COOKIE['role']."\n";
echo $_COOKIE['ID']."\n";
*/
header("location:../View/Identification.html");
?>
