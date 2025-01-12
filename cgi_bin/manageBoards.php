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
	else if ($_POST["request"] == "createBoard") {
		createBoard($con);
	}
	else if ($_POST["request"] == "joinBoard") {
		joinBoard($con);
	}	
	else if ($_POST["request"] == "deleteBoard") {
		deleteBoard($con);
	}	
	
	
	function deleteBoard($database_connection) {
		$sql_test = "SELECT * FROM `Boards` WHERE `board`='" . $_POST["board"] . "'";
		$result = $database_connection->query($sql_test);
		
		//Check if any board exists with this name
		if ($result->num_rows == 0) {
			alert_redirect("There is no board with that name", "../bin/selectDiscussion.html");
		}
		
		//Check if this user is the owner of the board
		$owner = $result->fetch_assoc()["owner"];
		if ($owner != $_COOKIE["email"]) {
			alert_redirect("You do not have permission to delete this board", "../bin/selectDiscussion.html");
		}
		
		//Now we can proceed
		
		$sql_delete_board = "DELETE FROM `Boards` WHERE `board`='" . $_POST["board"] . "'";
		$sql_delete_memberships = sprintf("DELETE FROM `Memberships` WHERE `board`='%s'", $_POST["board"]);
		
		if ($database_connection->query($sql_delete_board) == false || $database_connection->query($sql_delete_memberships) == false) {
			alert_redirect("Server Error", "../bin/error500.html");
		}
		
		alert_redirect("Board has been deleted", "../bin/selectDiscussion.html");
	}
	
	
	function joinBoard($database_connection) {
		$sql_test = "SELECT * FROM `Boards` WHERE `invite_id`='" . $_POST["invCode"] . "'";
		$result = $database_connection->query($sql_test);
		
		//Check if any board exists with this name
		if ($result->num_rows == 0) {
			alert_redirect("This is not a valid invite code", "../bin/selectDiscussion.html");
		}
		
		$board_name = $result->fetch_assoc()["board"];
		
		$sql_test = "SELECT * FROM `Memberships` WHERE `board`='" . $board_name . "' and `email`='" . $_COOKIE["email"] . "'";
		$result = $database_connection->query($sql_test);
		
		//Check if user is already a member of this board
		if ($result->num_rows != 0) {
			alert_redirect("You are already a member of this board.", "../bin/selectDiscussion.html");
		}
		
		$sql_insert_membership = sprintf("INSERT INTO `Memberships`(`email`, `board`, `role`) VALUES ('%s','%s','%s')", $_COOKIE["email"], $board_name, "user");
		
		if ($database_connection->query($sql_insert_membership) == false) {
			alert_redirect("Server Error", "../bin/error500.html");
		}
		
		alert_redirect("Joined board: " . $board_name, "../bin/selectDiscussion.html");
	}
	
	function createBoard($database_connection) {
		
		$sql_test = "SELECT * FROM `Boards` WHERE `board`='" . $_POST["newTitle"] . "'";
		$result = $database_connection->query($sql_test);
		
		//Check if any boards exist with this name already
		if ($result->num_rows != 0) {
			alert_redirect("A board with this name already exists. Please choose a different name.", "../bin/selectDiscussion.html");
		}
		
		$new_id = bin2hex(random_bytes(4));
		$sql_test = "SELECT * FROM `Boards` WHERE `invite_id`='" . $new_id . "'";
		
		//Just in case of duplicate IDs.
		while ($database_connection->query($sql_test)->num_rows != 0) {
			$new_id = bin2hex(random_bytes(4));
			$sql_test = "SELECT * FROM `Boards` WHERE `invite_id`='" . $new_id . "'";
		}
		
		
		
		$sql_insert_board = sprintf("INSERT INTO `Boards`(`board`, `invite_id`, `owner`) VALUES ('%s','%s','%s')", $_POST["newTitle"], $new_id, $_COOKIE["email"]);
		$sql_insert_membership = sprintf("INSERT INTO `Memberships`(`email`, `board`, `role`) VALUES ('%s','%s','%s')", $_COOKIE["email"], $_POST["newTitle"], "owner");
		
		if ($database_connection->query($sql_insert_board) == false || $database_connection->query($sql_insert_membership) == false) {
			alert_redirect("Server Error", "../bin/error500.html");
		}
		
		alert_redirect("New board has been created!", "../bin/selectDiscussion.html");
	}
	
	
	function getList($database_connection) {
		
		$sql_test = "SELECT `board` FROM `Memberships` WHERE `email`='" . $_COOKIE["email"] . "'";
	
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
	
	
	
?>
