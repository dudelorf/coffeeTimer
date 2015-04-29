<?php
	@ $db = new mysqli("localhost", "eric", "Dud3Lorf", "coffeeRecipes");
			
	if (mysqli_connect_errno())
	{
		echo "Could not connect to database. Try something else.";
		exit;
	}

	$methodname = $_POST['methodname'];
	$defaultvolume = $_POST['defaultvolume'];
	$brewratio = $_POST['brewRatioTens'] + ($_POST['brewRatioDecimal'] / 10);
	$grindsize = $_POST['grindSize'];
	
	$phasememos = [];
	$phaseratios = [];
	$phasetimes = [];
	
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
	
	
	if (!empty($_POST['dilutionCheck']))
	{
		$dilutionratio = $_POST['dilutionRatio'] + ($_POST['dilutionRatioDecimal'] / 10);
	}
	else
	{
		$dilutionratio = 0;
	}
	
	$query = "INSERT INTO savedrecipes (methodname, defaultvolume, brewratio, grindsize,
				phasememos, phaseratios, phasetimes, dilutionratio)
		VALUES ('$methodname', $defaultvolume, $brewratio, '$grindsize',
				'$phasememos_safe', '$phaseratios_safe', '$phasetimes_safe', $dilutionratio);";
		/*, defaultvolume, brewratio, 
		grindsize, phasememos, phaseratios, phasetimes, dilutionratio*/
		
		/*, $defaultvolume, $brewratio, $grindsize,
				 $phasememos_safe, $phaseratios_safe, $phasetimes_safe, $dilutionratio*/
		
	$result = $db->query($query);
	if(!$result)
	{
		echo "Extreme boner";
		echo $query;
	}
?>