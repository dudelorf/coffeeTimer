<?php
	/*
	Script attempts to log in user and store userId as a session variable.
	Code is passed back to javascript to indicate result of attempt:
		1 = successful attempt
		2 = bad password
		3 = bad user name
	*/
	session_start();
	$success = 0;
	
	$userName = $_POST['userName'];
	$userPassword = $_POST['userPassword'];
	
	@ $db = new mysqli("localhost", "coffeeTimer", "potato", "coffeetimer");
			
		if (mysqli_connect_errno())
		{
			echo "Could not connect to database. Try something else.";
			exit;
		}
			
		$query = "SELECT userId FROM users WHERE userName='$userName' AND userPassword = '$userPassword'";
			
		$result = $db->query($query);

		
		if($result->num_rows > 0)
		{
			//success
			$_SESSION['userId'] = $result->fetch_assoc()['userId'];
			echo '1';
		}
		else
		{	
			//bad login attempt
			//test query to diagnose issue
			$query = "SELECT * FROM users WHERE userName='$userName'";
			
			$result = $db->query($query);
			
			if ($result->num_rows > 0)
			{
				//user name is correct, password is not
				echo '2';
			}
			else
			{
				//user name is incorrect
				echo '3';
			}
		}
		$db->close();	
?>