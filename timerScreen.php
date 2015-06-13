<!DOCTYPE html>
<head>
	<title>Coffee Timer</title>
	<link rel="stylesheet" type="text/css" href="styles/timer.css" />
</head>
<body onload="window_onload()">
	<div id="container">
	<div id="backNav" onclick="window.location='index.php'">
		<img src="images/backNavArrow.png" />
		<label>Go Back</label>
	</div>
	<h1 id="brewName">Brew Name</h1>	
	<div id="volumeContainer">
		<h2>Volume</h2>
		<div id="volumeNav">
		<img src="images/upArrow.png" onclick="changeVolume('+')"/>
		<p id="volumeDisplay">?</p><label> oz</label>
		<img src="images/downArrow.png" onclick="changeVolume('-')"/>
		</div>
	</div>
	<div id="gramsCoffeeDisplay">
		<h2>Grams Coffee</h2>
		<p id="gramsCoffee"></p>
	</div>
	<div id="gramsWaterDisplay">
		<h2>Grams Water</h2>
		<p id="gramsWater"></p>
	</div>
	<div id="timer">
	</div>
	</div>
	<script type="text/javascript" src="lib/timer.js"></script>
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
			
			$query = "Select * from savedrecipes where methodName='".$theMethod."'";
			
			$result = $db->query($query);
			$theRecipe = $result->fetch_assoc();
			$theRecipe['phaseMemos'] = unserialize($theRecipe['phaseMemos']);
			$theRecipe['phaseTimes'] = unserialize($theRecipe['phaseTimes']);
			$theRecipe['phaseRatios'] = unserialize($theRecipe['phaseRatios']);
			
			$db->close();
		?>
		var timer;
		
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
		
		function changeVolume(opr)
		{
			if (opr == '+') timer.increaseVolume();
			else timer.decreaseVolume();
			document.getElementById("volumeDisplay").innerHTML = timer.getVolume();
			document.getElementById("gramsCoffee").innerHTML = getGramsCoffee(timer.getVolume(), timer.getBrewRatio());
			document.getElementById("gramsWater").innerHTML = getGramsWater(timer.getVolume(), timer.getBrewRatio());
		}
	
	//timer functions
	
	//general functions	
		function loadRecipe()
		//makes recipe data avilable to app
		{
			document.getElementById("brewName").innerHTML = timer.getMethodName();
			document.getElementById("volumeDisplay").innerHTML = timer.getVolume();
			document.getElementById("gramsCoffee").innerHTML = getGramsCoffee(timer.getVolume(), timer.getBrewRatio());
			document.getElementById("gramsWater").innerHTML = getGramsWater(timer.getVolume(), timer.getBrewRatio());
			timer.showTime(timer.getTotalTime());
			
		}
		function window_onload()
		//page init
		{
			//creates timer object
			timer = new Timer(<?php echo json_encode($theRecipe); ?>, document.getElementById("timer"));
			//loads timer into window
			loadRecipe();
		}
	</script>
</body>
</html>