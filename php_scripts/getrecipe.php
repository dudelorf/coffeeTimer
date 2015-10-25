<?php
// /phpscripts/getrecipe.php

/*
	exctracts specified recipe from database and returns it's JSON representation
*/
	session_start();

	require('../php_scripts/serverlogin.php');

	$theMethod = $_GET["recipeName"];

	@ $db = new mysqli($hostname, $userName, $password, $database);

	if (mysqli_connect_errno())
	{
		echo "Could not connect to database. Try something else.";
		exit;
	}

	$tableName = "recipesid".$_SESSION['userId'];

	$query = 'Select * from '.$tableName.' where methodName="'.$theMethod.'"';

	$result = $db->query($query);
	$theRecipe = $result->fetch_assoc();
	$theRecipe['phaseMemos'] = unserialize($theRecipe['phaseMemos']);
	$theRecipe['phaseTimes'] = unserialize($theRecipe['phaseTimes']);
	$theRecipe['phaseRatios'] = unserialize($theRecipe['phaseRatios']);

	echo json_encode($theRecipe);
		
	$db->close();

?>