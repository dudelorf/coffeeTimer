<?php
	$methodToDelete = $_POST['selectedRecipe'];
	
	@ $db = new mysqli("localhost", "eric", "Dud3Lorf", "coffeeRecipes");
	
	$query = "DELETE FROM savedrecipes WHERE methodname='$methodToDelete'";
	
	$result = mysqli_query($db, $query);
	
	echo $methodToDelete;
?>