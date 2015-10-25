$(document).ready(function() {
	
	var registering = false;
	
	//add event handlers
	$("#login").click(function () {
		beginLogin();
	});

	$("#newAccount").click(function () {
		if (!registering) {
			beginRegistration();
			registering = true;
		}
		else{
			registerNewAccount();
		}
	});

	$("#cancel").click(function(){
		//returns display to normal
		$("#cancel").fadeOut(function(){
			$("#login").fadeIn();
			$("#verifyPasswordField").animate({opacity:"0"});
		});
		registering = false;
	});
	
	$("form input").on("keypress", function(k){
		if(k.which == 13){
			if(registering){
				registerNewAccount();
			} else {
				beginLogin();
			}
		}
	});
	console.log("other js");
});

function beginLogin(){
	$.get("./php_scripts/loginScript.php",
				{
					userName: $("#usernameField").val(),
					userPassword: $("#passwordField").val()
				},
				function (data) {
					processLogin(data);
				});
}

function beginRegistration()
{
	//sets up display for registration
	$("#login").fadeOut(function(){
		$("#cancel").fadeIn();
		$("#verifyPasswordField").animate({opacity:"1"});
	});

}

function registerNewAccount()
{
	//do form validation here
	//check name is filled
	if ($("#usernameField").val() == ""){
		alert("A user name is required");
		return;
	}
	
	//ensures a password is entered
	else if ($("#passwordField").val() == "" || $("#passwordVerify").val() == ""){
		alert("Complete password field");
		return;
	}
	//check passwords match
	else if ($("#passwordField").val() !=  $("#passwordVerify").val()){
		alert("Passwords do not match");
		return;
	}
	//register new account
	else {
		$.post("php_scripts/newAccount.php",
				{
					userName: $("#usernameField").val(),
					userPassword: $("#passwordField").val()
				},
				function (data) {
					switch (data) {
						case '1':
							//successful creation
							window.location = 'recipes.html';
							break;
						case '2':
							//name already exists
							alert("Username is not available.");
							break;
						default:
							alert(data);
							break;
					}
				});
	}

}

function processLogin(key)
{
	//uses code provided by php script to process login attempt
	switch (key)
	{
		case '1':
			//success
			window.location = 'recipes.html';
			break;
		case '2':
			alert("bad password yo");
			break;
		case '3':
			alert("bad user name");
			break;
		default:
			alert(key);
			break;
	}
}
	
	