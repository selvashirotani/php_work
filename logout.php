<?php
session_start();

$session_name = session_name();
$_SESSION = array();

if(isset($_COOKIE[$session_name])){
    setcookie($session_name,'',time() -3600);
}

session_destroy();

header('Location: https://153.127.18.207/top.php');

?>
