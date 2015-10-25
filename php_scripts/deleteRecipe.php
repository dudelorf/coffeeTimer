<?php
// /php_scripts/deleteRecipe.php
/*
	deletes recipe from user recipes table
*/
	session_start();
	
	require('../php_scripts/serverlogin.php');

	
	$tableName = "recipesid".$_SESSION['userId'];
	
	$methodToDelete = $_POST['selectedRecipe'];

	@ $db = new mysqli($hostname, $userName, $password, $database);
	
	$query = 'DELETE FROM '.$tableName.' WHERE methodname="'.$methodToDelete.'"';
	
	if($result = mysqli_query($db, $query)){
		echo true;
	} else {
		echo false;
	}
	
	$db->close();
?>