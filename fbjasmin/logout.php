<?php
session_start();
if(isset($_GET['session']))
{
	unset($_SESSION[$_GET['session']]);
}

header("location:Album.php");
?>