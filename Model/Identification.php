<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST["id"]) && isset($_POST["password"])){
        $id = $_POST["id"];
        $password = $_POST["password"];
        echo $id."|".$password;
    }
}
?>