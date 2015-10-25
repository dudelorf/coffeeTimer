var deleteSavedRecipes = false;
var editSavedRecipe = false;

function takeSelection(methodName)
{

	if(deleteSavedRecipes)
	//deletes recipe from database and from display
	{

		$.ajax({
			type: 'POST',  
			url: './php_scripts/deleteRecipe.php',
			data: { selectedRecipe: methodName },
			success: function(data) {
				$("#" + spaceToUnderscore(methodName)).hide(500);
			}
		});

	}
	else if (editSavedRecipe)
	//navigate to edit recipe form
	{
		$("html").fadeOut(function(){
			window.location = "recipeForm.php?toEdit=" + methodName;
		});
	}
	else
	//navigate to timer screen for selected recipe
	{
		$("html").fadeOut(function(){
			window.location = "./timerScreen.php?brewSelect=" + methodName;

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
	$.get("php_scripts/logout.php", function(){
		$("html").fadeOut(function(){
			window.location = "./index.htm";
		});
	});
}

$(document).ready(function(){	
	//Builds html for recipes and adds boxes to window

	$.getJSON("./php_scripts/getrecipenames.php", function(recipeArr){
		for (var r = 0; r < recipeArr.length; r++)
		{
			var rName = recipeArr[r];
			var elem = document.createElement("li");
			(function(rName){
				$(elem).text(rName).click(function(){
					takeSelection(rName);
				})
				.addClass("recipe")
				.addClass("cstBtn")
				.attr("id", (spaceToUnderscore(rName)));
			}(rName));
			document.getElementById("methods").appendChild(elem);
		}
	});
	//assigns event handlers
	$("#controls").click(function(){$("#dropdownMenu").slideToggle();});
	//closes menu if clicked off
	$("html").mouseup(function(e){
		if( ($("#dropdownMenu").css("display") != "none") 			//menu is visible
		 && (!$("#dropdownMenu").is(e.target))   //menu was not clicked
		 && ($("#controls").has(e.target).length === 0)   
		 && ($("#dropdownMenu").has(e.target).length === 0) )  //child of menu not clicked
		 {
			 $("#dropdownMenu").slideUp();
		 }		
	});
	$("#newOption").click(function(){
		$("html").fadeOut(function(){window.location='recipeForm.php';});
	});
	$("body").fadeIn(100);
});