<?php
	session_start();
?>
<!DOCTYPE html>
<head>
	<title>Coffee Timer</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<link rel="stylesheet" type="text/css" href="styles/timer.css" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
</head>
<body style="display:none" onload="window_onload()">
	<div id="container" class="container">
		<nav>
			<div id="backNav">
				<span class="glyphicon glyphicon-backward"></span>
				<p>Go Back</p>
			</div>
		</nav>
		<h1 id="brewName">Brew Name</h1>
		<section class="row" id="mainDisplay">
			<div class="col-xs-4">
				<input class="cstBtn" type="button" value="Start" id="startBtn" /></br>
				<input class="cstBtn" type="button" value="Stop" id="stopPauseBtn" />
			</div>
			<div class="col-xs-8">
				<span id="clock">69:00</span>
			</div>
		</section>
		<p id="memo">Saggy balls</p>
		<section id="volumeContainer">
			<h3>Volume</h3>
			<div id="volumeNav">
				<div class="cstBtn" type="button" id="increaseVolume">
					<span class="glyphicon glyphicon-chevron-up"></span>
				</div>
				<div id="ctr">
					<i id="volumeDisplay">?</i><label for="volumeDisplay">oz</label>
				</div>
				<div class="cstBtn" type="button" id="decreaseVolume">
					<span class="glyphicon glyphicon-chevron-down"></span>
				</div>
			</div>
		</section>
		<section class="row">
			<div id="gramsCoffeeDisplay" class="col-xs-6 brewSpecs">
				<h4>Grams Coffee</h4>
				<p id="gramsCoffee"></p>
			</div>
			<div id="gramsWaterDisplay" class="col-xs-6 brewSpecs">
				<h4>Grams Water</h4>
				<p id="gramsWater"></p>
			</div>
		</section>
	</div>
	<script type="text/javascript" src="./js/timer.js"></script>
	<script type="text/javascript">
		//global variables
		<?php
			$theMethod = $_GET["brewSelect"];
			
			require('./php_scripts/serverlogin.php');
			
			@ $db = new mysqli($hostname, $userName, $password, $database);
			
			if (mysqli_connect_errno())
			{
				echo "Could not connect to database. Try something else.";
				exit;
			}
			
			$tableName = "recipesid".$_SESSION['userId'];
			
			$query = "Select * from $tableName where methodName='".$theMethod."'";
			
			$result = $db->query($query);
			$theRecipe = $result->fetch_assoc();
			$theRecipe['phaseMemos'] = unserialize($theRecipe['phaseMemos']);
			$theRecipe['phaseTimes'] = unserialize($theRecipe['phaseTimes']);
			$theRecipe['phaseRatios'] = unserialize($theRecipe['phaseRatios']);
			
			$db->close();
		?>
		var timer;
	
		function window_onload()
		//page init
		{
			//creates timer object
			timer = new Timer(
			//recipe object
			<?php echo json_encode($theRecipe); ?>,
			//assign inputs 
			document.getElementById("startBtn"),
			document.getElementById("stopPauseBtn"),
			document.getElementById("increaseVolume"),
			document.getElementById("decreaseVolume"),
			//assign outputs: 
			document.getElementById("brewName"),
			document.getElementById("volumeDisplay"),
			document.getElementById("clock"),
			document.getElementById("gramsCoffee"),
			document.getElementById("gramsWater"),
			document.getElementById("memo")
			);
			
			//activates display
			timer.activateDisplay();
			
			$("#backNav").click(function(){
				$("html").fadeOut(function(){window.location='../recipes.html';});
			});
				
			$("body").fadeIn(100);
		}
	</script>
	<audio id="beepElem" src="lib/beep.wav" autostart="false" width="0" height="0" enablejavascript="true"/>
</body>
</html>