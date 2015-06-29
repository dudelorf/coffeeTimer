<?php
	session_start();
	$success = 0;
	
	$_SESSION['userName'] = $_POST['userName'];
	$_SESSION['userPassword'] = $_POST['userPassword'];
	
	$success = 1;
	echo $success;
?>