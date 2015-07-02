<?php
	session_start();
	$tableName = "recipesid".$_SESSION['userId'];
	$methodToDelete = $_POST['selectedRecipe'];
	
	@ $db = new mysqli("localhost", "coffeeTimer", "potato", "coffeetimer");
	
	$query = "DELETE FROM $tableName WHERE methodname='$methodToDelete'";
	
	$result = mysqli_query($db, $query);
	
	echo $methodToDelete;
?>