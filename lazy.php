<!DOCTYPE html>
<head></head>
<body>
<p>
<?php
	@ $db = new mysqli("localhost", "root", "Dud3Lorf", "coffeerecipes");
			
	if (mysqli_connect_errno())
	{
		echo "Could not connect to database. Try something else.";
		exit;
	}
	
	$query = "SELECT * FROM savedrecipes where methodName = 'V60'";
	$result = $db->query($query);
	$recipe = $result->fetch_assoc();
	
	echo var_dump($recipe);
?>
</p>
</body>
</html>