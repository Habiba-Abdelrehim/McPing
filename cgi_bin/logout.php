<?php

	require "utils.php";

	//Delete Cookies
	if (setcookie("email", "", time() - 3600, "/") && setcookie("token", "", time() - 3600, "/")) {
		//No need to unset the token on the database, it will be overwritten upon a new login
		alert_redirect("You have been logged out", "../bin/landingPage.html");
	}
	
	http_response_code(500);
	
	
	

?>
