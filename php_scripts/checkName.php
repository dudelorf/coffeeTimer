<?php
	session_start();
	
	$tableName = "recipesid".$_SESSION['userId'];
	
	@ $db = new mysqli("localhost", "coffeeTimer", "potato", "coffeetimer");
			
	if (mysqli_connect_errno())
	{
		echo "Could not connect to database. Try something else.";
		exit;
	}
	
	$theMethod = mysqli_real_escape_string($db, $_GET['method']);

	$query = "Select * from $tableName where methodName='$theMethod'";

	$result = $db->query($query);

	if($result->num_rows > 0)
	{
		//recipe name already exists
		echo "false";
	}
	else
	{
		//name is new and valid
		echo "true";
	}
?>