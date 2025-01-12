<?php
require "utils.php";

if ($con->connect_error) {
    die("Internal Server Error: " . $conn->connect_error);
}

if (sizeof($_POST) == 0 || !isset($_POST["request"])) {
    http_response_code(400); // Bad request
    exit;
} else if (!verify_token($con)) {
    http_response_code(401); // Unauthorized
    exit;
}
  else if ($_POST["request"] == "getBoardChannels") {
    getBoardChannels($con);
}
   else if ($_POST["request"] == "getBoardMembers") {
    getBoardMembers($con);
}

else if ($_POST["request"] == "getInviteCode") {
    getInviteCode($con);
}


function getBoardChannels($database_connection) {
    if (!isset($_POST["board"])) {
        http_response_code(400); // Bad request
        exit;
    }

    $sql = "SELECT channel_name FROM Channels WHERE board = ?";
    $boardName = filter_var($_POST["board"], FILTER_SANITIZE_STRING);
   
    $stmt = $database_connection->prepare($sql);
    $stmt->bind_param("s", $boardName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo json_encode(["error" => "Board not found"]);
    } else {
        $channelsData = $result->fetch_all();
        echo json_encode(array_column($channelsData, 0));
    }


	
	
}

function getBoardMembers($database_connection) {
    if (!isset($_POST["board"])) {
        http_response_code(400); // Bad request
        exit;
    }

    $boardName = filter_var($_POST["board"], FILTER_SANITIZE_STRING);

    $sql = "SELECT Users.firstname
    FROM Memberships
    INNER JOIN Users ON Memberships.email = Users.email
    WHERE Memberships.board = ?";
    
    $stmt = $database_connection->prepare($sql);
    $stmt->bind_param("s", $boardName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo json_encode(["error" => "Board not found"]);
    } else {
        $membersData = $result->fetch_all();
        echo json_encode(array_column($membersData, 0));
    }
}



function getInviteCode($database_connection) {
    if (!isset($_POST["board"])) {
        http_response_code(400); // Bad request
        exit;
    }

    $boardName = filter_var($_POST["board"], FILTER_SANITIZE_STRING);

    $sql = "SELECT invite_id FROM Boards WHERE board = ?";
    $stmt = $database_connection->prepare($sql);
    $stmt->bind_param("s", $boardName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo json_encode(["error" => "Board not found"]);
    } else {
        $codeData = $result->fetch_assoc();
        echo json_encode($codeData["invite_id"]);
    }
}

?>
