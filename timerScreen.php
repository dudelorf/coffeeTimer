<!DOCTYPE html>
<head>
	<title>Coffee Timer</title>
	<link rel="stylesheet" type="text/css" href="styles/timer.css" />
</head>
<body onload="window_onload()">
	<div id="container">
	<div id="backNav" onclick="window.location='index.html'">
		<img src="images/backNavArrow.png" />
		<label>Go Back</label>
	</div>
	<h1 id="brewName">Brew Name</h1>	
	<div id="volumeContainer">
		<h2>Volume</h2>
		<div id="volumeNav">
		<img src="images/upArrow.png" onclick="updateVolume(++volume)"/>
		<p id="volumeDisplay">?</p><label> oz</label>
		<img src="images/downArrow.png" onclick="updateVolume(--volume)"/>
		</div>
	</div>
	<div id="gramsCoffeeDisplay">
		<h2>Grams Coffee</h2>
		<p id="gramsCoffee">? grams Coffee</p>
	</div>
	<div id="gramsWaterDisplay">
		<h2>Grams Water</h2>
		<p id="gramsWater">? grams Water</p>
	</div>
	<div id="timer">
		<p id="clock">00:00</p>
		<p id="memo">Click Start Button to Begin!</p>
		<input type="button" value="Start Timer" onclick="alert('Begin!')" />
	</div>
	</div>
	
	<script type="text/javascript">
		//global variables
		<?php
			$theMethod = $_POST["brewSelect"];
		
			@ $db = new mysqli("localhost", "eric", "Dud3Lorf", "coffeeRecipes");
			
			if (mysqli_connect_errno())
			{
				echo "Could not connect to database. Try something else.";
				exit;
			}
			
			$query = "Select * from recipes where methodName='".$theMethod."'";
			
			$result = $db->query($query);
			$theRecipe = json_encode($result->fetch_assoc());
			
			echo "var toBrew = $theRecipe;";
			$db->close();
		?>

		var volume = 0;
		var phase = 0;

	//utility functions to calculate recipe details
		function getGramsCoffee(vol, brewRatio)
		//returns grams of coffee when supplied volume in oz and brew ratio in gramsCoffee/ozWater
		{
			var mlVol = vol * 29.5;
			var gCoffee = mlVol / (brewRatio - 1.5/*retained water*/);
			return Math.round(gCoffee);
		}
		
		function getGramsWater(vol, ratio)
		//returns total grams water for recip when supplied desired brew volume and brew ratio
		{
			var gCoffee = getGramsCoffee(vol, ratio);
			var mlVol = vol * 29.5 + gCoffee * 1.5/*ml/g retained water*/;
			mlVol = Math.round(mlVol * 0.1);
			return mlVol * 10;
		}
		
		function updateVolume(newVol)
		{
			document.getElementById("volumeDisplay").innerHTML = newVol;
			document.getElementById("gramsCoffee").innerHTML = getGramsCoffee(newVol, toBrew.brewRatio);
			document.getElementById("gramsWater").innerHTML = getGramsWater(newVol, toBrew.brewRatio);
		}
	
	//timer functions
	
	//general functions	
		function loadRecipe()
		//makes recipe data avilable to app
		{
			volume = toBrew.defaultVolume;
			document.getElementById("brewName").innerHTML = toBrew.methodName;
			document.getElementById("volumeDisplay").innerHTML = volume;
			document.getElementById("gramsCoffee").innerHTML = getGramsCoffee(volume, toBrew.brewRatio);
			document.getElementById("gramsWater").innerHTML = getGramsWater(volume, toBrew.brewRatio);
			
		}
		function window_onload()
		//page init
		{
			loadRecipe();
		}
	</script>
</body>
</html>