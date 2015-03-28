<!DOCTYPE html>
<head>
	<title>Select Method</title>
	<link rel="stylesheet" type="text/css" href="styles/main.css" />
</head>
<body onload="window_onload()">
	<div id="container">
	<div id="controls">
		<img id="addMethod" src="images/upArrow.png" />
			<label>Menu</label>
		<img id="editMethods" src="images/backNavArrow.png" />
	</div>
	<h1>How are you brewing today?</h1>
	<ul id="methods">
	</ul>
	</div>
<script type="text/javascript">
	//load available recipes from database
	<?php
		$recipes = ['Chemex', 'French Press'];
	?>
	var recipeArr = <?php echo json_encode($recipes); ?>;

	function takeSelection(methodName)
	{
		document.getElementById("brewSelect").value = methodName;
		document.forms["theForm"].submit();
	}
	
	function window_onload()
	{		
		for (var r = 0; r < recipeArr.length; r++)
		{
			var rName = recipeArr[r];
			var elem = document.createElement("li");
			elem.innerHTML = rName;
			elem.addEventListener("click", function(){
				takeSelection(this.innerHTML);});
			document.getElementById("methods").appendChild(elem);
		}
	}
</script>
	<form id="theForm" action="timerScreen.php" method="post">
		<input type="hidden" id="brewSelect" name="brewSelect" />
	</form>
</body>