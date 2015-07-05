<?php
	session_start();
?>
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
            <td><input type="text" maxlength="25" id="memop{{phaseNum}}" name="memop{{phaseNum}}" class="memoText"/></td>
        </tr>
        <tr>
            <td>Phase Volume</td>
            <td><input type="text" size="3" maxlength="2" class="ratioTens" value="0"
                       name="ratioTensp{{phaseNum}}" id="ratioTensp{{phaseNum}}" />
                . <input type="text" size="2" maxlength="1" class="ratioDecimal" value="0"
                         name="ratioDecimalp{{phaseNum}}" id="ratioDecimalp{{phaseNum}}"/>mL water/g Coffee</td>
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
			for (var vol = 6; vol <= 36; vol++)
			{
				context = {volume: vol};
				outputStr += volOptionTemplate(context);
			}
			$("#volSelect").append(outputStr);

			//adds event handlers
			$("#dilutionCheck").change(function(){$("#dilutionField").toggle();})

			populateForm(loadRecipe());

			$("#backNav").click(function(){
				$("html").fadeOut(function(){window.location='index.php';});
			});
            //adds necessary event handlers
            if($("#editSignal").val())
            {
                //in edit mode, sets method name field to read only
                document.getElementById("methodName").readOnly = true;
            }
            else
            {
                //adds validating mechanism to method name field
                $("#methodName").on("blur", function () {
                    checkDatabase();
                });
            }
			$("body").fadeIn(100);
		});

		function loadRecipe()
		//returns object containing recipe information
		{
			var theRecipe = <?php

			//checks to see if editing existing recipe
			if (isset($_GET['toEdit']))
			{
				$theMethod = $_GET['toEdit'];
				$tableName = "recipesid".$_SESSION['userId'];

				@ $db = new mysqli("localhost", "coffeeTimer", "potato", "coffeetimer");

				if (mysqli_connect_errno())
				{
					echo "Could not connect to database. Try something else.";
					exit;
				}

				$query = "Select * from $tableName where methodName='".$theMethod."'";

				$result = $db->query($query);
				$theRecipe = $result->fetch_assoc();

				//unserialize data
				$theRecipe['phaseMemos'] = unserialize($theRecipe['phaseMemos']);
				$theRecipe['phaseRatios'] = unserialize($theRecipe['phaseRatios']);
				$theRecipe['phaseTimes'] = unserialize($theRecipe['phaseTimes']);

				//pass recipe object to javascript
				echo json_encode($theRecipe);
			}
			else
				//signals script on page to create default recipe
				echo "undefined";
			?>;

			//no recipe selected to edit, loads default recipe
			if (theRecipe == undefined){
				theRecipe = createDefaultRecipe();
			}
			else
			//editing a selected recipe
			{
				//tells php to update existing recipe instead of saving new one
				$("#editSignal").val(true);
			}

			return theRecipe;

		}

		function createDefaultRecipe()
		//creates and returns a default recipe object
		{
			var defaultRecipe = {};

			defaultRecipe['methodName'] = "New Recipe";
			defaultRecipe['defaultVolume'] = 13;
			defaultRecipe['brewRatio'] = 15.0;
			defaultRecipe['grindSize'] = "Coarse";
			defaultRecipe['phaseMemos'] = [
				"Bloom", "Steep", "Drawdown"
				];
			defaultRecipe['phaseRatios'] = [
				5.0, 10.0, 0.0];
			defaultRecipe['phaseTimes'] = [
				30, 135, 30];
			defaultRecipe['dilutionRatio'] = 0.0;

			return defaultRecipe;
		}

		function populateForm(recipeObject)
		//uses supplied recipe object to populate form
		{
			$("#methodName").val(recipeObject['methodName']);
			$("#volSelect > option").each(function() {
				if ($(this).val() == recipeObject['defaultVolume']){
					$(this).prop('selected', true);
				}
			});
			$("#brewRatioTens").val(Math.floor(recipeObject['brewRatio']));
			$("#brewRatioDecimal").val((recipeObject['brewRatio'] * 10) % 10);
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
				$("#ratioTensp" + p).val(Math.floor(recipeObject['phaseRatios'][p - 1]));
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

			if (recipeObject['dilutionRatio'] > 0.0)
			{
				$("#dilutionCheck").prop('checked', true);
				$("#dilutionField").show(0);
				$("#dilutionRatio").val(Math.floor(recipeObject['dilutionRatio']));
				$("#dilutionRatioDecimal").val((recipeObject['dilutionRatio'] * 10) % 10);
			}

		}

		function validateForm()
		//verifies all fields are filled and and details check out
		//returns true if recipe is valid and false otherwise
        {
		//check name
            //checks to see form is in edit mode
            if(!$("#editSignal").val()) {

                var methodName = $("#methodName");

                //recipe has no name
                if (methodName.val() == "") {
                    alert("Need to name recipe");
                    return false;
                }
                //verifies unique recipe name in database
                if (methodName.hasClass("bad")) {
                    alert("Recipe name exists");
                    return false;
                }
            }
		//check phase memos
            var memos = document.getElementsByClassName("memoText");

            for (var i = 0; i < memos.length; i++)
            {
               if($(memos[i]).val() == "")
                {
                    alert("Memo number " + (i + 1) + " is empty.");
                    return false;
                }
            }
		//make sure phase ratios don't exceed total ratio
            var totalTensObj = $("#brewRatioTens");
            var totalDecimalObj = $("#brewRatioDecimal");

            var brewRatio = 0.0;
                brewRatio += Number(totalTensObj.removeClass("bad").val());
                brewRatio += (Number(totalDecimalObj.removeClass("bad").val()) / 10);

            var phaseTens = document.getElementsByClassName("ratioTens");
            var phaseDecimals = document.getElementsByClassName('ratioDecimal');

            var recipeRatio = 0.0;
            for (var p = 0; p < phaseTens.length; p++)
            {
                recipeRatio += Number($(phaseTens[p]).val());
                recipeRatio += (Number($(phaseDecimals[p]).val()) / 10);

                if (recipeRatio > brewRatio)
                //phase ratios exceed total ratio
                {
                    $(phaseTens[p]).addClass("bad");
                    $(phaseDecimals[p]).addClass("bad");
                    alert("Phase ratios exceed brew ratio.");
                    return false;
                }

                $(phaseTens[p]).removeClass("bad");
                $(phaseDecimals[p]).removeClass("bad");
            }

            if (brewRatio > recipeRatio)
            //total ratio exceeds phase ratios
            {
                totalTensObj.addClass("bad");
                totalDecimalObj.addClass("bad");
                alert("Brew ratio exceeds phase ratios.");
                return false
            }

            return true;
		}

		function checkDatabase()
        //checks method name to see if it already exists in saved recipes
        //if it does, alerts user by adding class
        {
            $.get("php_scripts/checkName.php", {method: $("#methodName").val()}, function (isValid) {

                    if(isValid == "false")
                    {
                        $("#methodName").addClass("bad");
                    }
                    else
                    {
                        $("#methodName").removeClass("bad");
                    }
                }
            );

        }

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
            if(validateForm())
            {
				$("html").fadeOut(function(){
					document.forms['recipeForm'].submit();
                    window.location = 'index.php';
				});
			}
		}

	</script>
</head>
<body>
<div id="container">
	<div id="backNav">
		<img src="images/backNavArrow.png" />
		<label>Go Back</label>
	</div>
	<div id="formContainer">
        <form id="recipeForm" action="php_scripts/addRecipe.php" method="POST">
            <table id="formTable">
            <tr>
                <td class="fieldName"><label for="methodName">Method name</label></td>
                <td class="fieldData"><input type="text" size="25" maxlength="25" id="methodName" name="methodName"/></td>
            </tr>
            <tr>
                <td class="fieldName">Default Volume</td>
                <td class="fieldData"><select id="volSelect" name="defaultVolume"></select> Oz</td>
            </tr>
            <tr>
                <td class="fieldName">Brew Ratio</td>
                <td class="fieldData">
                <input type="text" size="3" placeholder="15" maxlength="2"
                    id="brewRatioTens" name="brewRatioTens"/>
                . <input type="text" size="2" placeholder="0" maxlength="1"
                    id="brewRatioDecimal" name="brewRatioDecimal"/>mL water/g Coffee</td>
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
                    <input type="text" size="3" placeholder="5" maxlength="2" id="dilutionRatio"
                        name="dilutionRatio"></input> .
                    <input type="text" size="2" placeholder="0" maxlength="1" id="dilutionRatioDecimal"
                        name="dilutionRatioDecimal"></input>mL water/g Coffee</td>
            </tr>
            </table>
            <input type="hidden" id="editSignal" name="editSignal"/>
        </form>
	</div>
	<footer id="controls">
	<div class="controlButton" onclick="addNewPhase()">New Phase</div>
	<div class="controlButton" onclick="saveRecipe()" >Save Recipe</div>
	</footer>
</div>
</body>
</html>