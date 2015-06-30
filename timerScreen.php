<?php
	session_start();
?>
<!DOCTYPE html>
<head>
	<title>Coffee Timer</title>
	<link rel="stylesheet" type="text/css" href="styles/timer.css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
</head>
<body style="display:none" onload="window_onload()">
	<div id="container">
	<div id="backNav">
		<img src="images/backNavArrow.png" />
		<label>Go Back</label>
	</div>
	<h1 id="brewName">Brew Name</h1>	
	<div id="volumeContainer">
		<h2>Volume</h2>
		<div id="volumeNav">
		<img src="images/upArrow.png" id="increaseVolume"/>
		<p id="volumeDisplay">?</p><label> oz</label>
		<img src="images/downArrow.png" id="decreaseVolume"/>
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
		<p id="clock">69:00</p>
		<p id="memo">Saggy balls</p>
		<input type="button" value="start" id="startBtn" />
		<input type="button" value="Stop / Reset" id="stopPauseBtn" />
	</div>
	</div>
	<script type="text/javascript" src="lib/timer.js"></script>
	<script type="text/javascript">
		//global variables
		<?php
			$theMethod = $_POST["brewSelect"];
		
			@ $db = new mysqli("localhost", "coffeeTimer", "potato", "coffeetimer");
			
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
				$("html").fadeOut(function(){window.location='index.php';});
			});
				
			$("body").fadeIn(100);
		}
	</script>
	<audio id="beepElem" src="lib/beep.wav" autostart="false" width="0" height="0" enablejavascript="true"/>
</body>
</html>