<?php
	session_start();
	@ $db = new mysqli("localhost", "coffeeTimer", "potato", "coffeetimer");
			
	if (mysqli_connect_errno())
	{
		echo "Could not connect to database. Try something else.";
		exit;
	}
	
	$userName = $_POST['userName'];
	$userPassword = $_POST['userPassword'];
	
	//checks to see if username exists
	$query = "SELECT * FROM users WHERE userName='$userName'";
	$result = $db->query($query);
	if ($result->num_rows > 0)
	{
		//user name exists
		echo "name already exists";
		exit;
	}
	
	//adds username to database
	$query = "INSERT INTO users (userName, userPassword) VALUES ('$userName', '$userPassword')";
	
	if($db->query($query))
	{
		$query = "SELECT userId FROM users WHERE userName='$userName'";
		$result = $db->query($query);
		
		//run script to initialize basic recipe library
		$newUserId = $result->fetch_assoc()['userId'];
		$tableName = "recipesid".$newUserId;
		
		$newTableQuery = "CREATE TABLE $tableName (
			methodName char(30),
			defaultVolume tinyint(2),
			brewRatio decimal(3,1),
			grindSize varchar(20),
			phaseMemos varchar(200),
			phaseRatios varchar(200),
			phaseTimes varchar(200),
			dilutionRatio decimal(3,1)
		)";
		
		addAeropress($db);
		addFrenchPress($db);
		addV60($db);
		
		if(!$db->query($newTableQuery))
		{
			echo "issue creating the new table";
			exit;
		}
		
		$_SESSION['userId'] = $newUserId;
		
		echo "mission success! User Id is: ".$_SESSION['userId'];
	}
	else
	{
		echo "There was a problem";
	}
	
	function

?>