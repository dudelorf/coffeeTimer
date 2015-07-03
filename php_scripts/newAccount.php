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
		
		if(!$db->query($newTableQuery))
		{
			echo "issue creating the new table";
			exit;
		}
		
		addAeropress($db, $tableName);
		addFrenchPress($db, $tableName);
		addV60($db, $tableName);
		
		$_SESSION['userId'] = $newUserId;
		
		echo "mission success! User Id is: ".$_SESSION['userId'];
	}
	else
	{
		echo "There was a problem";
	}
	
	function addAeropress($connection, $table)
	{
		$query = "INSERT INTO $table (methodName, defaultVolume, brewRatio, grindSize,
				phaseMemos, phaseRatios, phaseTimes, dilutionRatio)
		VALUES ('Aeropress',
				 8,
				 14.5,
				 'Fine',
				 'a:3:{i:0;s:5:\"Bloom\";i:1;s:5:\"Steep\";i:2;s:5:\"Press\";}',
				 'a:3:{i:0;d:3.5;i:1;d:9.5;i:2;i:0;}',
				 'a:3:{i:0;i:30;i:1;i:120;i:2;i:15;}',
				 2.0)";
		$connection->query($query);
	}
	
	function addFrenchPress($connection, $table)
	{
		$query = "INSERT INTO $table (methodName, defaultVolume, brewRatio, grindSize,
				phaseMemos, phaseRatios, phaseTimes, dilutionRatio)
		VALUES ('French Press',
				 16,
				 15.0,
				 'Coarse',
				 'a:3:{i:0;s:5:\"Bloom\";i:1;s:5:\"Steep\";i:2;s:5:\"Press\";}',
				 'a:3:{i:0;d:3.5;i:1;d:9.5;i:2;i:0;}',
				 'a:3:{i:0;i:30;i:1;i:120;i:2;i:15;}',
				 0.0)";
		$connection->query($query);
	}
	function addV60($connection, $table)
	{
		$query = "INSERT INTO $table (methodName, defaultVolume, brewRatio, grindSize,
				phaseMemos, phaseRatios, phaseTimes, dilutionRatio)
		VALUES ('V60',
				 12,
				 14.0,
				 'Medium-Fine',
				 'a:3:{i:0;s:5:\"Bloom\";i:1;s:5:\"Steep\";i:2;s:5:\"Press\";}',
				 'a:3:{i:0;i:5;i:1;i:10;i:2;i:0;}',
				 'a:3:{i:0;i:30;i:1;i:225;i:2;i:15;}',
				 0.0)";
		$connection->query($query);
	}
?>