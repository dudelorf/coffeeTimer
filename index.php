<?php
	session_start();
?>
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
			<li id="newOption">New Recipe</a></li>
			<li id="editOption" onclick="activateEdit()">Edit Recipe</li>
			<li id="deleteOption" onclick="activateDelete()">Delete Recipe</li>
			<li id="exitOption" onclick="logout()">Log out</li>
		</ul>
	</div>
	<h1>How are you brewing today?</h1>
	<ul id="methods">
	</ul>
	<div id="buttonContainer">
		<div id="completeDeleteButton" onclick="finishDelete()">Finished Deleting</div>
		<div id="completeEditButton" onclick="finishEdit()">Cancel Edit</div>
	</div>
	</div>
<script type="text/javascript">
	//load available recipes from database
	<?php
		$tableName = "recipesid".$_SESSION['userId'];
		@ $db = new mysqli("localhost", "coffeeTimer", "potato", "coffeetimer");
		
		if (mysqli_connect_errno())
		{
			echo "Could not connect to database. Try something else.";
			exit;
		}
		
		$query = "Select methodName from $tableName";
		
		$result = mysqli_query($db, $query);
		
		$recipes = [];
		$row = mysqli_fetch_row($result);
		while(!empty($row))
		{
			$recipes[] = $row[0];
			$row = mysqli_fetch_row($result);
		}

	?>
	var deleteSavedRecipes = false;
	var editSavedRecipe = false;
	
	function takeSelection(methodName)
	{

		if(deleteSavedRecipes)
		//deletes recipe from database and from display
		{
			$.ajax({  
				type: 'POST',  
				url: 'deleteRecipe.php', 
				data: { selectedRecipe: methodName },
				success: function(data) {
					var sel = "#" + spaceToUnderscore(data);
					$(sel).hide(500);
				}
			});
	
		}
		else if (editSavedRecipe)
		//navigate to edit recipe form
		{
			$("html").fadeOut(function(){
				window.location = "recipeForm.php?toEdit="+methodName;
			});
		}
		else
		//navigate to timer screen for selected recipe
		{
			$("html").fadeOut(function(){
				document.getElementById("brewSelect").value = methodName;
				document.forms["theForm"].submit();
			});
		}
	}
	
	function activateDelete()
	{
		if(editSavedRecipe || deleteSavedRecipes)
			//cancels effect if a menu option is already active
			return;
		$("#dropdownMenu").slideToggle();
		$(".recipe").toggleClass("removeRecipe");
		deleteSavedRecipes = true;
		setTimeout(flashDeleteAlert(), 500);
		$("#completeEditButton").hide();
		$("#completeDeleteButton").show();
		$("#buttonContainer").toggle(500);
	}
	
	function flashDeleteAlert()
	{
		if(deleteSavedRecipes)
		{
			$(".recipe").toggleClass("removeRecipe");
			setTimeout(function(){flashDeleteAlert();}, 500);
		}
		else
		{
			$(".recipe").removeClass("removeRecipe");
		}
	}
	function finishDelete()
	{
		deleteSavedRecipes = false;
		$("#buttonContainer").toggle(500);
	}
	
	function activateEdit()
	{
		if(editSavedRecipe || deleteSavedRecipes)
			//cancels effect if a menu option is already active
			return;
		editSavedRecipe = true;
		$("#dropdownMenu").slideToggle();
		$("#completeDeleteButton").hide();
		$("#completeEditButton").show();
		$("#buttonContainer").toggle(500);
	}
	
	function finishEdit()
	{
		editSavedRecipe = false;
		$("#buttonContainer").toggle(500);
	}
	
	function spaceToUnderscore(str)
	/*utility function
	converts space characters to underscore in str*/
	{
		var result = ""
		var strLength = str.length;
		var currentChar = "";
		for(var i = 0; i < strLength; i++)
		{
			currentChar = str.charAt(i);
			if(currentChar == " ")
			{
				currentChar = "_";
			}
			result += currentChar;
		}
		return result;
	}
	
	function logout()
	{
		$("html").fadeOut(function(){
			window.location = "logout.php";
		});
	}
	
	function window_onload()
	{	
		//Builds html for recipes and adds boxes to window
		var recipeArr = <?php echo json_encode($recipes); ?>;
		
		for (var r = 0; r < recipeArr.length; r++)
		{
			var rName = recipeArr[r];
			var elem = document.createElement("li");
			(function(rName){
				$(elem).text(rName).click(function(){
					takeSelection(rName);
				}).addClass("recipe").attr("id", (spaceToUnderscore(rName)));
			}(rName));
			document.getElementById("methods").appendChild(elem);
		}
		
		//assigns event handlers
		$("#menuDiv").click(function(){$("#dropdownMenu").slideToggle();});
		$("html").mouseup(function(e){
			if( ($("#dropdownMenu").css("display") != "none") 			//menu is visible
			 && (!$("#dropdownMenu").is(e.target))   //menu was not clicked
			 && ($("#menuDiv").has(e.target).length === 0)   
		     && ($("#dropdownMenu").has(e.target).length === 0) )  //child of menu not clicked
			 {
				 $("#dropdownMenu").slideUp();
			 }		
		});
		$("#newOption").click(function(){
			$("html").fadeOut(function(){window.location='recipeForm.php';});
		});
		$("body").fadeIn(100);
	}
</script>
	<form id="theForm" action="timerScreen.php" method="post">
		<input type="hidden" id="brewSelect" name="brewSelect" />
	</form>
</body>
</html>