<?php
    /*Script to create new user acount
    code is passed back to javascript to indicate success of creation
        1 = successful creation
        2 = invalid username
    */
	session_start();
	
	require('../php_scripts/serverlogin.php');
	
	@ $db = new mysqli($hostname, $userName, $password, $database);
	
	if (mysqli_connect_errno())
	{
		echo "Could not connect to database. Try something else.";
		exit;
	}

    //sanitize input
	$userName = mysqli_real_escape_string($db, $_POST['userName']);
	$userPassword = mysqli_real_escape_string($db, $_POST['userPassword']);
	
	//checks to see if username exists
	$query = 'SELECT * FROM users WHERE userName="'.$userName.'"';
	$result = $db->query($query);
	if ($result->num_rows > 0)
	{
		//user name exists
		echo 2;
		exit;
	}
	
	//adds username to database
	$query = 'INSERT INTO users (userName, userPassword) VALUES ("'.$userName.'", "'.$userPassword.'")';
	
	if($db->query($query))
	{
		$query = 'SELECT userId FROM users WHERE userName="'.$userName.'"';
		$result = $db->query($query);
		
		//run script to initialize basic recipe library
		$newUserId = $result->fetch_assoc()['userId'];
		$tableName = "recipesid".$newUserId;
		
		$newTableQuery = 'CREATE TABLE '.$tableName.' (
			methodName char(30),
			defaultVolume tinyint(2),
			brewRatio decimal(3,1),
			grindSize varchar(20),
			phaseMemos varchar(200),
			phaseRatios varchar(200),
			phaseTimes varchar(200),
			dilutionRatio decimal(3,1)
		)';
		
		if(!$db->query($newTableQuery))
		{
			echo "issue creating the new table";
			exit;
		}
		
		addAeropress($db, $tableName);
		addFrenchPress($db, $tableName);
		addV60($db, $tableName);
		
		$_SESSION['userId'] = $newUserId;
        echo '1';
        $db->close();
	}
	else
	{
		echo "There was a problem creating user";
        exit;
	}
	
	//adds default aeropress recipe
	function addAeropress($connection, $table)
	{
		$phaseMemos = serialize(["Bloom", "Steep", "Press"]);
		$phaseRatios = serialize([2.0, 12.5, 0]);
		$phaseTimes = serialize([30, 120, 15]);
		
		$query = "INSERT INTO ".$table." (methodName, defaultVolume, brewRatio, grindSize,
				phaseMemos, phaseRatios, phaseTimes, dilutionRatio)
		VALUES ('Aeropress',
				 8,
				 14.5,
				 'Fine',
				 '".$phaseMemos."',
				 '".$phaseRatios."',
				 '".$phaseTimes."', 
				 0.0)";

		if(!$connection->query($query))
		{
			echo "probelem with aeropress";
			exit;
		}
	}
	
	//adds default french press recipe
	function addFrenchPress($connection, $table)
	{
		
		$phaseMemos = serialize(["Bloom", "Steep", "Press"]);
		$phaseRatios = serialize([3.5, 11.5, 0]);
		$phaseTimes = serialize([30, 120, 15]);
		
		$query = "INSERT INTO ".$table." (methodName, defaultVolume, brewRatio, grindSize,
				phaseMemos, phaseRatios, phaseTimes, dilutionRatio)
		VALUES ('French Press',
				 16,
				 15.0,
				 'Coarse',
				 '".$phaseMemos."',
				 '".$phaseRatios."',
				 '".$phaseTimes."', 
				 0.0)";
				 
		if(!$connection->query($query))
		{
			echo "probelem with french press";
			exit;
		}
	}
	
	//adds default V60 recipe
	function addV60($connection, $table)
	{
		$phaseMemos = serialize(["Bloom", "Steep", "Press"]);
		$phaseRatios = serialize([1.5, 12.5, 0]);
		$phaseTimes = serialize([30, 225, 15]);
		
		$query = "INSERT INTO ".$table." (methodName, defaultVolume, brewRatio, grindSize,
				phaseMemos, phaseRatios, phaseTimes, dilutionRatio)
		VALUES ('V60',
				 12,
				 14.0,
				 'Medium',
				 '".$phaseMemos."',
				 '".$phaseRatios."',
				 '".$phaseTimes."', 
				 0.0)";

		if(!$connection->query($query))
		{
			echo "probelem with v60";
			exit;
		}
	}
?>