<?php
	session_start();
	$tableName = "recipesid".$_SESSION['userId'];
	
	require('../php_scripts/serverlogin.php');
	
	@ $db = new mysqli($hostname, $userName, $password, $database);
			
	if (mysqli_connect_errno())
	{
		echo "Could not connect to database. Try something else.";
		exit;
	}
	
	//extract values from form as local variables
	$methodname = mysqli_real_escape_string($db, $_POST['methodName']);
	
	$defaultvolume = $_POST['defaultVolume'];
	$brewratio = $_POST['brewRatioTens'] + ($_POST['brewRatioDecimal'] / 10);
	$grindsize = $_POST['grindSize'];
	
	$phasememos = [];
	$phaseratios = [];
	$phasetimes = [];
	
	//populates arrays
	$phaseIndex = 1;
	$phaseMemoKey = "memop1";
	while(array_key_exists($phaseMemoKey, $_POST))
	{ 
		$phaseRatioTensKey = "ratioTensp".$phaseIndex;
		$phaseRatioDecimalKey = "ratioDecimalp".$phaseIndex;
		$phaseMinutesKey = "minutesp".$phaseIndex;
		$phaseSecondsKey = "secondsp".$phaseIndex;
		
		array_push($phasememos, $_POST[$phaseMemoKey]);
		array_push($phaseratios,
			($_POST[$phaseRatioTensKey] + ($_POST[$phaseRatioDecimalKey] / 10)));
		array_push($phasetimes,
			($_POST[$phaseSecondsKey] + ($_POST[$phaseMinutesKey] * 60)));
		
		$phaseIndex++;
		$phaseMemoKey = "memop".$phaseIndex;
	}
	$phasememos_safe = mysqli_real_escape_string($db, serialize($phasememos));
	$phaseratios_safe = mysqli_real_escape_string($db, serialize($phaseratios));
	$phasetimes_safe = mysqli_real_escape_string($db, serialize($phasetimes));
	
	//sets up dilution column
	if (!empty($_POST['dilutionCheck']) && ($_POST['dilutionRatio'] + ($_POST['dilutionRatioDecimal'] / 10)) > 0)
	{
		$dilutionratio = $_POST['dilutionRatio'] + ($_POST['dilutionRatioDecimal'] / 10);
	}
	else
	{
		$dilutionratio = 0;
	}
	
	if ($_POST['editSignal'] == true)
	//create update query
	{
	$query = 'UPDATE '.$tableName.' SET
				defaultVolume='.$defaultvolume.',
				brewRatio='.$brewratio.',
				grindSize="'.$grindsize.'",
				phaseMemos="'.$phasememos_safe.'",
				phaseRatios="'.$phaseratios_safe.'",
				phaseTimes="'.$phasetimes_safe.'",
				dilutionRatio='.$dilutionratio.'
			  WHERE methodName="'.$methodname.'"';
	}
	else
	//create query to save new recipe
	{
	$query = 'INSERT INTO '.$tableName.' (methodName, defaultVolume, brewRatio, grindSize,
				phaseMemos, phaseRatios, phaseTimes, dilutionRatio)
		VALUES ("'.$methodname.'", '.$defaultvolume.', '.$brewratio.', "'.$grindsize.'",
				"'.$phasememos_safe.'", "'.$phaseratios_safe.'", "'.$phasetimes_safe.'", '.$dilutionratio.')';
	}
	//runs query

	$result = $db->query($query);
	
	if(!$result)
	{
		echo "Extreme boner";
		echo $query;
	}
?>
<<!DOCTYPE html>
<head>
<script>
	function backToHome()
	{
		window.location = "../recipes.php";
	}
</script>
</head>
<body onload="backToHome()"></body>
</html>