<?php
	require "utils.php";

	if ($con->connect_error) {
		die("Internal Server Error: " . $conn->connect_error);
	}
	
	
	
	if (sizeof($_POST) == 0 || !isset($_POST["request"])) {
		http_response_code(400); //Bad request
		exit;
	}
	else if (!verify_token($con)) {
		http_response_code(401); //Unauthorized
		exit;
	}
	else if ($_POST["request"] == "getList") {
		getList($con);
	}
	else if ($_POST["request"] == "createPM") {
		createPM($con);
	}
	
	function getList($database_connection) {
		
		$sql_test = "SELECT `name` FROM `Private_Memberships` WHERE `email`='" . $_COOKIE["email"] . "'";
	
		$result = $database_connection->query($sql_test);
		
		//Check if any boards exist for this user
		if ($result->num_rows == 0) {
			echo json_encode([]);
			exit;
		}
		
		$rows = $result->fetch_all();
		//Send back an array of those boards for display
		echo json_encode(array_column($rows, 0));
	}
	
	function createPM($database_connection) {
		
		$PM_name = $_COOKIE["email"] . " " . $_POST["receiver"];
		
		$sql_test = "SELECT * FROM `Private_Messages` WHERE `board`='" . $PM_name . "'";
		$result = $database_connection->query($sql_test);
		
		//Check if any boards exist with this name already
		if ($result->num_rows != 0) {
			alert_redirect("This private message already exists.", "../bin/selectDiscussion.html");
		}
		
		if ($_COOKIE["email"] == $_POST["receiver"]) {
			alert_redirect("You cannot message yourself.", "../bin/selectDiscussion.html");
		}
		
		$sql_test = "SELECT `firstname` FROM `Users` WHERE `email`='" . $_POST["receiver"] . "'";
		$result = $database_connection->query($sql_test);
		$name = $result->fetch_assoc()["firstname"];
		
		$sql_insert_PM = sprintf("INSERT INTO `Private_Messages`(`board`, `owner`, `name`) VALUES ('%s','%s','%s')", $PM_name, $_COOKIE["email"], $name);
		$sql_insert_membership1 = sprintf("INSERT INTO `Private_Memberships`(`email`, `board`, `name`) VALUES ('%s','%s','%s')", $_COOKIE["email"], $PM_name, $name);
		
		$sql_test = "SELECT `firstname` FROM `Users` WHERE `email`='" . $_COOKIE["email"] . "'";
		$result = $database_connection->query($sql_test);
		$name = $result->fetch_assoc()["firstname"];
		
		$sql_insert_membership2 = sprintf("INSERT INTO `Private_Memberships`(`email`, `board`, `name`) VALUES ('%s','%s','%s')", $_POST["receiver"], $PM_name, $name);
		
		if ($database_connection->query($sql_insert_PM) == false || $database_connection->query($sql_insert_membership1) == false || $database_connection->query($sql_insert_membership2) == false) {
			alert_redirect("Server Error", "../bin/error500.html");
		}
		
		alert_redirect("New message has been created!", "../bin/selectDiscussion.html");
		
	}
	
?>
