<?php
//php_scripts/getrecipename.php
/*
	returns a JSON encoded array of recipe names in a users recipe table
*/
	session_start();
	
	require('../php_scripts/serverlogin.php');

	
	//load available recipes from database
	$tableName = "recipesid".$_SESSION['userId'];
 
	@ $db = new mysqli($hostname, $userName, $password, $database);
	
	if (mysqli_connect_errno())
	{
		echo "Could not connect to database. Try something else.";
		exit;
	}
	
	$query = 'Select methodName from '.$tableName;
	
	$result = mysqli_query($db, $query);
	
	$recipes = [];
	$row = mysqli_fetch_row($result);
	while(!empty($row))
	{
		$recipes[] = $row[0];
		$row = mysqli_fetch_row($result);
	}
	
	echo json_encode($recipes);
?>
