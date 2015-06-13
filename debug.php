<!DOCTYPE html>
<html>
<head>
<title>Testing</title>
</head>
<body>
<p>
<?php		
	@ $db = new mysqli("localhost", "eric", "Dud3Lorf", "coffeeRecipes");
	if (mysqli_connect_errno($db))
	{
		echo "There was an error connecting to the database";
		exit;
	}
	$query = "SELECT * FROM savedRecipes WHERE methodname='".$_GET['toEdit']."'";
	
	$result = mysqli_query($db, $query);
	$existingRecipe = mysqli_fetch_assoc($result);
	
	foreach($existingRecipe as $value)
	{
		echo "<p>".$value."</p>";
	}
?>
</p>
</body>
</html>
	