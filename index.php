<!DOCTYPE html>
<head>
	<title>Select Method</title>
	<link rel="stylesheet" type="text/css" href="styles/main.css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
</head>
<body onload="window_onload()">
	<div id="container">
	<div id="controls">
		<div id="menuDiv">
		<img id="menuButton" src="images/upArrow.png"/>
			<label>Menu</label>
		</div>
	</div>
	<div id="dropdownMenu">
		<ul id="menuOptions">
			<li id="newOption"><a href="recipeForm.html">New Recipe</a></li>
			<li id="editOption">Edit Recipe</li>
			<li id="deleteOption" onclick="activateDelete()">Delete Recipe</li>
		</ul>
	</div>
	<h1>How are you brewing today?</h1>
	<ul id="methods">
	</ul>
	<div id="buttonContainer">
		<div id="completeDeleteButton">Finished Deleting</div>
	</div>
	</div>
<script type="text/javascript">
	//load available recipes from database
	<?php
		@ $db = new mysqli("localhost", "eric", "Dud3Lorf", "coffeeRecipes");
		
		if (mysqli_connect_errno())
		{
			echo "Could not connect to database. Try something else.";
			exit;
		}
		
		$query = "Select methodname from savedrecipes";
		
		$result = mysqli_query($db,$query);
		$recipes = mysqli_fetch_all($result , MYSQLI_NUM);

	?>
	var deleteSavedRecipes = false;

	function takeSelection(methodName)
	{
		document.getElementById("brewSelect").value = methodName;
		document.forms["theForm"].submit();
	}
	
	function activateDelete()
	{
		$("#dropdownMenu").slideToggle();
		$(".recipe").toggleClass("removeRecipe");
		deleteSavedRecipes = true;
		setTimeout(flashDeleteAlert(), 500);
		$("#buttonContainer").toggle(500);
	}
	
	function flashDeleteAlert()
	{
		$(".recipe").toggleClass("removeRecipe");
		if(deleteSavedRecipes)
		{
			setTimeout(function(){flashDeleteAlert();}, 500);
		}
	}
	
	function window_onload()
	{	
		//Builds html for recipes and adds boxes to window
		var recipeArr = <?php echo json_encode($recipes); ?>;
		
		for (var r = 0; r < recipeArr.length; r++)
		{
			var rName = recipeArr[r];
			var elem = document.createElement("li");
			$(elem).text(rName).click(function(){takeSelection(this.innerHTML);}).addClass("recipe");
			document.getElementById("methods").appendChild(elem);
		}
		
		$("#menuDiv").click(function(){$("#dropdownMenu").slideToggle();});
		
	}
</script>
	<form id="theForm" action="timerScreen.php" method="post">
		<input type="hidden" id="brewSelect" name="brewSelect" />
	</form>
</body>