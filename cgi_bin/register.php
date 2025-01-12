<?php
	require "utils.php";

	if ($con->connect_error) {
		die("Internal Server Error: " . $conn->connect_error);
	}
	
	$sql_test = "SELECT * FROM `Users` WHERE `email`='" . $_POST["email"] . "'";
	
	$result = $con->query($sql_test);
	
	//Check if a user with that email already exists
	if ($result->num_rows != 0) {
		// Return error - "user already exists with that email"
		alert_redirect("A user with that email address already exists. Please try with a different email.", "../bin/registrationPage.html");
	}
	
	//User ID must be numeric and be 9 digits long
	if (!is_numeric($_POST["studentorstaffid"]) || floor(log10($_POST["studentorstaffid"])) + 1 != 9) {
		// Return error - ID number must be 9 digits long.
		alert_redirect("That is not a valid ID number. Please try again.", "../bin/registrationPage.html");
	}
	
	
	$sql_insert = sprintf("INSERT INTO `Users`(`email`, `password`, `studentorstaffid`, `firstname`, `lastname`, `type`) 
	VALUES ('%s','%s','%d','%s','%s','%s')", $_POST["email"], password_hash($_POST["password"], PASSWORD_DEFAULT), $_POST["studentorstaffid"], 
	$_POST["firstname"], $_POST["lastname"], $_POST["type"]);
	
	$con->query($sql_insert);
	
	
	alert_redirect("Registration Successful!", "../bin/landingPage.html")
?>


