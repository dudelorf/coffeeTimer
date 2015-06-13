<!DOCTYPE html>
<head>
	<title>New Recipe</title>
	<link rel="stylesheet" type="text/css" href="styles/phaseForm.css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="lib/handlebars-v3.0.1.js"></script>
	<script id="volOptionTemplate" type="text/x-handlebars-template">
		<option value={{volume}}> {{volume}} </option></script>
	<script id="phaseFormTemplate" type="text/x-handlebars-template">
	
		<tbody class="phaseField" id="phase{{phaseNum}}">
		<tr class="phaseMemo">
			<td>Phase Memo</td>
			<td><input type="text" id="memop{{phaseNum}}" name="memop{{phaseNum}}"></input></td>
		</tr>
		<tr>
		<td>Phase Volume</td>
		<td><input type="text" size="3" maxlength="2"
				name="ratioTensp{{phaseNum}}" id="ratioTensp{{phaseNum}}"></input>
			. <input type="text" size="2" maxlength="1"
				name="ratioDecimalp{{phaseNum}}" id="ratioDecimalp{{phaseNum}}"></input>mL water/g Coffee</td>
		</tr>
		<tr>
		<td>Phase Time</td>
		<td>
			<select name="minutesp{{phaseNum}}" id="minutesp{{phaseNum}}">
				<option value="0"> 0 </option>
				<option value="1"> 1 </option>
				<option value="2"> 2 </option>
				<option value="3"> 3 </option>
				<option value="4"> 4 </option>
				<option value="5"> 5 </option>
			</select> : 
			<select name="secondsp{{phaseNum}}" id="secondsp{{phaseNum}}">
				<option value="0"> 00 </option>
				<option value="15"> 15 </option>
				<option value="30" selected="true"> 30 </option>
				<option value="45"> 45 </option>
			</select>
			<div class="removePhaseButton" id="removep{{phaseNum}}"> Remove phase </div>
		</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center">-------------</td>
		</tr>
		</tbody>
		
	</script>

	<script>
		//global variable to reference phase field template
		var phaseFormTemplate;
		
		$(document).ready(function()
		//initialization of document
		{
			//compile templates
			phaseFormTemplate = Handlebars.compile($("#phaseFormTemplate").html());
			var volOptionTemplate = Handlebars.compile($("#volOptionTemplate").html());

			//adds volume options to form
			var outputStr = "";
			var context = {};
			for (var vol = 6; vol < 32; vol++)
			{
				context = {volume: vol};
				outputStr += volOptionTemplate(context);
			}
			$("#volSelect").append(outputStr);
			
			//adds event handlers
			$("#dilutionCheck").change(function(){$("#dilutionField").toggle();})
			
			populateForm(loadRecipe());
		});
		
		function loadRecipe()
		//returns object containing recipe information
		{
			var theRecipe = createDefaultRecipe(); 
			
			return theRecipe;
		}
		
		function createDefaultRecipe()
		//creates and returns a default recipe object
		{
			var defaultRecipe = {}
			
			defaultRecipe['methodName'] = "New Recipe";
			defaultRecipe['defaultVolume'] = 13;
			defaultRecipe['dilutionRatio'] = 15.0;
			defaultRecipe['grindSize'] = "Coarse";
			defaultRecipe['phaseMemos'] = [
				"Bloom", "Steep", "Drawdown"
				];
			defaultRecipe['phaseRatios'] = [
				5.0, 10.0, 0.0
				]
			defaultRecipe['phaseTimes'] = [
				30, 135, 30
				]
	
			return defaultRecipe;
		}
		
		
		function populateForm(recipeObject)
		//uses supplied recipe object to populate form
		{
			$("#methodname").val(recipeObject['methodName']);
			$("#volSelect > option").each(function() {
				if ($(this).val() == recipeObject['defaultVolume']){
					$(this).prop('selected', true);
				}
			});
			$("#brewRatioTens").val(recipeObject['dilutionRatio']/1);
			$("#brewRatioDecimal").val((recipeObject['dilutionRatio'] * 10) % 10);
			$("#grindSize > option").each(function() {
				if ($(this).val() == recipeObject['grindSize'])
					$(this).prop('selected', true);
			});
			
			//populate saved phases
			var numPhases = recipeObject['phaseMemos'].length;
			for (var p = 1; p <= numPhases; p++)
			{
				var context = {phaseNum: p /*current value of p in for loop*/};
				//adds blank phase
				addPhase(context);
				//populates phase fields
				$("#memop" + p).val(recipeObject['phaseMemos'][p - 1 /*index into array*/]);
				$("#ratioTensp" + p).val(recipeObject['phaseRatios'][p - 1] / 1);
				$("#ratioDecimalp" + p).val((recipeObject['phaseRatios'][p - 1] * 10) % 10);
				
				var minutesStr = "#minutesp" + p + " > option";
				var minutes = Math.floor(recipeObject['phaseTimes'][p-1] / 60);

				$(minutesStr).each(function(){
					if ($(this).val() == minutes)
						$(this).prop('selected', true);
				});
				
				var secondsStr = "#secondsp" + p + " > option";
				var seconds = recipeObject['phaseTimes'][p - 1] % 60;
				
				$(secondsStr).each(function(){
					if ($(this).val() == seconds)
						$(this).prop('selected', true);
				});
			}
		}
		
		
		/*function loadDefaultForm()
		{
			//add initial phases
			var initPhases = [
					{phaseNum: 1,
					 initPlaceholder: "Bloom"},
					{phaseNum: 2,
					 initPlaceholder: "Pour"},
					{phaseNum: 3,
					 initPlaceholder: "Drawdown"}
				];
			for (var p=0; p < initPhases.length; p++)
			{
				context = initPhases[p];
				addPhase(context);
			}
			//adjusts default placeholder values
			$("#ratioTensp1").attr("placeholder", 5);
			$("#ratioTensp2").attr("placeholder", 10);
			$("#ratioTensp3").attr("placeholder", 0);
			document.getElementById("minutesp2").options[2].setAttribute("selected", "true");
			document.getElementById("volSelect").options[6].setAttribute("selected", "true");

		}
		
		function loadSavedRecipe()
		//loads recipe from database and populates form fields
		{
		<?php
			if (isset($_GET['toEdit']))
			{
			//run sql to get recipe data
			@ $db = new mysqli("localhost", "eric", "Dud3Lorf", "coffeeRecipes");
			if (mysqli_connect_errno($db))
			{
				echo "There was an error connecting to the database";
				exit;
			}
			$query = "SELECT * FROM savedRecipes WHERE methodname='".$_GET['toEdit']."'";
			
			$result = mysqli_query($db, $query);
			$existingRecipe = mysqli_fetch_assoc($result);
			}
			
			//populate javascript variables
			//echo "var loadedMethodName = '".$existingRecipe['methodname']."';";
			//echo "var loadedDefaultVolume = '".$existingRecipe['defaultvolume']."';";
			
			
		?>
			//populate form elements
			$('#methodname').val(loadedMethodName);
			var volOptions = document.getElementById("volSelect").options;
			for (entry in volOptions)
			{
				if ($(entry).val() == loadedDefaultVolume)
				{
					$(entry).attr("selected", "true");
					break;
				}
			}
			
		}*/
		
		function removePhase(phaseNumber)
		//removes phase supplied as phaseNumber and re-indexes phase fields if necessary
		{
			var totalPhases = document.getElementsByClassName("phaseField").length;
			if(totalPhases == 1)
			{
				alert("At least one phase is required.");
				return;
			}
			if(phaseNumber < totalPhases)
			//re-indexing is required
			{
				$("#phase" + phaseNumber).remove();
				totalPhases--;
				while(phaseNumber <= totalPhases)
				{
					$("#phase" + (phaseNumber + 1)).attr("id", "phase" + phaseNumber);
					$("#memop" + (phaseNumber + 1)).attr("id", "memop" + phaseNumber).attr("name", "memop" + phaseNumber);
					$("#ratioDecimalp" + (phaseNumber + 1)).attr("id", "ratioDecimalp" + phaseNumber)
						.attr("name", "ratioDecimalp" + phaseNumber);
					$("#ratioTensp" + (phaseNumber + 1)).attr("id", "ratioTensp" + phaseNumber)
						.attr("name", "ratioTensp" + phaseNumber);
					$("#minutesp" + (phaseNumber + 1)).attr("id", "minutesp" + phaseNumber).attr("name", "minutesp" + phaseNumber);
					$("#secondsp" + (phaseNumber + 1)).attr("id", "secondsp" + phaseNumber).attr("name", "secondsp" + phaseNumber);
					(function(phaseNumber){
						$("#removep" + (phaseNumber + 1)).attr("id", "removep" + phaseNumber)
						.off("click").on("click", function(){removePhase(phaseNumber);});
						}(phaseNumber));
					phaseNumber++;
				}
			}
			else
			{
				$("#phase" + phaseNumber).remove();
			}
			
		}
		
		function addPhase(context)
		//builds html for phase field and adds it to form
		{
			$("#phaseFields").append(phaseFormTemplate(context));
			$("#removep" + context.phaseNum).on("click", function(){removePhase(context.phaseNum);});
		}
		
		function addNewPhase()
		//generates context for new phase and adds it to form
		{
			var newPhaseNumber = document.getElementsByClassName("phaseField").length + 1;
			var context = {phaseNum: newPhaseNumber};
			addPhase(context);
		}
		
		function saveRecipe()
		{
			document.forms['recipeForm'].submit();
		}
		
	</script>
</head>
<body>
	<div id="container">
	<div id="backNav" onclick="window.location='index.php'">
		<img src="images/backNavArrow.png" />
		<label>Go Back</label>
	</div>
	<div id="formContainer">
	<form id="recipeForm" action="addRecipe.php" method="POST">
		<table id="formTable">
		<tr>
			<td class="fieldName">Method Name</td>
			<td class="fieldData"><input type="text" size="25" id="methodname" name="methodname"></input></td>
		</tr>
		<tr>
			<td class="fieldName">Default Volume</td>
			<td class="fieldData"><select id="volSelect" name="defaultvolume"></select> Oz</td>
		</tr>
		<tr>
			<td class="fieldName">Brew Ratio</td>
			<td class="fieldData">
			<input type="text" size="3" placeholder="15" maxlength="2"
				id="brewRatioTens" name="brewRatioTens"></input>
			. <input type="text" size="2" placeholder="0" maxlength="1"
				id="brewRatioDecimal" name="brewRatioDecimal"></input>mL water/g Coffee</td>
		</tr>
		<tr>
			<td class="fieldName">Grind Size</td>
			<td class="fieldData"><select id="grindSize" name="grindSize">
				<option value="Fine">Fine</option>
				<option value="Medium-Fine">Medium-Fine</option>
				<option value="Medium">Medium</option>
				<option value="Medium-Coarse">Medium-Coarse</option>
				<option value="Coarse">Coarse</option>
				</select></td>
		</tr>
		<tr><td colspan="2" style="text-align:center">Brew Phases</td></tr>
		<tr>
			<td colspan="2" id="phaseFields"></td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="checkbox" id="dilutionCheck" name="dilutionCheck" value="1">Include dilution phase?</input></td>
		</tr>
		<tr style="display:none" id="dilutionField">
			<td class="fieldName">Dilution Ratio</td>
			<td class="fieldData">
				<input type="text" size="3" placeholder="5" maxlength="2"
					name="dilutionRatio"></input> .
				<input type="text" size="2" placeholder="0" maxlength="1"
					name="dilutionRatioDecimal"></input>mL water/g Coffee</td>
		</tr>
		</table>
	</form>
	</div>
	<footer id="controls">
	<div class="controlButton" onclick="addNewPhase()">New Phase</div>
	<div class="controlButton" onclick="saveRecipe()" >Save Recipe</div>
	</footer>
	</div>
</body>
</html>