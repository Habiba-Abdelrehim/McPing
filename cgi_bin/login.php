<?php
	require "utils.php";

	if ($con->connect_error) {
		die("Internal Server Error: " . $conn->connect_error);
	}
	
	$sql_test = "SELECT `email`, `password` FROM `Users` WHERE `email`='" . $_POST["email"] . "'";
	
	$result = $con->query($sql_test);
	
	//Check if a user with that email actually exists
	if ($result->num_rows == 0) {
		
		//var_dump($_POST);
		alert_redirect("No user with that email address exists", "../bin/loginPage.php");
	}
	
	$data = $result->fetch_assoc();
	
	
	//Password hashes match!
	if (password_verify($_POST["password"], $data["password"])) {
		
		//Create token and encode it
		$token = bin2hex(random_bytes(128));
		$expiry_time = time() + (60 * 60 * 24 * 7); //Expires after 7 days
		$sql_insert = "UPDATE `Users` SET `token`='" . password_hash($token, PASSWORD_DEFAULT) . "', `token_expiry`='" . $expiry_time . "' WHERE `email`='" . $_POST["email"] . "'";
		$con->query($sql_insert);
		
		setcookie("email", $_POST["email"], time() + (60 * 60 * 24 * 7), "/"); //Set token for 7 days
		setcookie("token", $token, time() + (60 * 60 * 24 * 7), "/"); //Set token for 7 days
		
		alert_redirect("Login successful!", "../bin/selectDiscussion.html");
	}
	else {
		alert_redirect("Incorrect password. Please try again.", "../bin/loginPage.php");
	}
	

?>
