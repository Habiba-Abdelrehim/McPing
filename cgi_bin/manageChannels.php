<?php
require "utils.php";

if ($con->connect_error) {
    die("Internal Server Error: " . $con->connect_error);
}

if (sizeof($_POST) == 0 || !isset($_POST["request"])) {
    http_response_code(400); // Bad request
    exit;
} else if (!verify_token($con)) {
    http_response_code(401); // Unauthorized
    exit;
}

$request = $_POST["request"];

switch ($request) {
    case "renameChannel":
        renameChannel($con);
        break;
    case "addChannel":
        addChannel($con);
        break;
    case "deleteChannel":
        deleteChannel($con);
        break;
    default:
        http_response_code(400); // Bad request
        exit;
}

function renameChannel($database_connection) {
    if (!isset($_POST["board"]) || !isset($_POST["oldChannelName"]) || !isset($_POST["newChannelName"])) {
        http_response_code(400); // Bad request
        exit;
    }

    $boardName = filter_var($_POST["board"], FILTER_SANITIZE_STRING);
    $oldChannelName = filter_var($_POST["oldChannelName"], FILTER_SANITIZE_STRING);
    $newChannelName = filter_var($_POST["newChannelName"], FILTER_SANITIZE_STRING);


    $sql_rename_channel = "UPDATE Channels SET channel_name = ? WHERE board = ? AND channel_name = ?";
    $stmt = $database_connection->prepare($sql_rename_channel);
    $stmt->bind_param("sss", $newChannelName, $boardName, $oldChannelName);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => "Channel renamed successfully"]);
    } else {
        echo json_encode(["error" => "Failed to rename channel"]);
    }
}

function addChannel($database_connection) {
    if (!isset($_POST["board"]) || !isset($_POST["newChannelName"])) {
        http_response_code(400); // Bad request
        exit;
    }

    $boardName = filter_var($_POST["board"], FILTER_SANITIZE_STRING);
    $newChannelName = filter_var($_POST["newChannelName"], FILTER_SANITIZE_STRING);

    $sql_add_channel = "INSERT INTO Channels (board, channel_name) VALUES (?, ?)";
    $stmt = $database_connection->prepare($sql_add_channel);
    $stmt->bind_param("ss", $boardName, $newChannelName);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Channel added successfully"]);
    } else {
        echo json_encode(["error" => "Failed to add channel"]);
    }
}

function deleteChannel($database_connection) {
    if (!isset($_POST["board"]) || !isset($_POST["channelName"])) {
        http_response_code(400); // Bad request
        exit;
    }

    $boardName = filter_var($_POST["board"], FILTER_SANITIZE_STRING);
    $channelName = filter_var($_POST["channelName"], FILTER_SANITIZE_STRING);

    $sql_delete_channel = "DELETE FROM Channels WHERE board = ? AND channel_name = ?";
    $stmt = $database_connection->prepare($sql_delete_channel);
    $stmt->bind_param("ss", $boardName, $channelName);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Channel deleted successfully"]);
    } else {
        echo json_encode(["error" => "Failed to delete channel"]);
    }
}


function getUsersList($database_connection) {
    if (!isset($_POST["board"]) || !isset($_POST["channelName"])) {
        http_response_code(400); // Bad request
        exit;
    }

    $boardName = filter_var($_POST["board"], FILTER_SANITIZE_STRING);
    $channelName = filter_var($_POST["channel"], FILTER_SANITIZE_STRING);


    $sql = "SELECT user FROM Messages WHERE board = ? AND channel = ? " ;
    $stmt = $database_connection->prepare($sql);
    

    $stmt->bind_param("ss", $boardName, $channelName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo json_encode(["error" => "No users found for the specified channel"]);
    } else {
        $usersData = $result->fetch_all();
        echo json_encode(array_column($usersData, 0));
    }

    
}

function sendMessageInChannel($database_connection) {
    if (!isset($_POST["board"]) || !isset($_POST["channel"]) || !isset($_POST["user"]) || !isset($_POST["message"])) {
        http_response_code(400); // Bad request
        exit;
    }

    $boardName = filter_var($_POST["board"], FILTER_SANITIZE_STRING);
    $channel = filter_var($_POST["channel"], FILTER_SANITIZE_STRING);
    $user = filter_var($_POST["user"], FILTER_SANITIZE_STRING);
    $message = filter_var($_POST["message"], FILTER_SANITIZE_STRING);

    // Insert the message into the Messages table
    $sql_insert_message = sprintf("INSERT INTO `Messages`(`board`, `channel`, `user`, `message`) VALUES ('%s','%s','%s','%s')", $boardName, $channel, $user, $message);
    if ($database_connection->query($sql_insert_message) == false) {
        echo json_encode(["error" => "Server Error"]);
        exit;
    }

    // Refresh the messages for the channel and return the updated messages
    getMessages($database_connection, $boardName, $channel);
}

function getMessages($database_connection, $boardName, $channel) {
    $sql_get_messages = "SELECT * FROM `Messages` WHERE `board`='" . $boardName . "' AND `channel`='" . $channel . "'";
    $result_get_messages = $database_connection->query($sql_get_messages);

    if ($result_get_messages->num_rows > 0) {
        $messagesData = $result_get_messages->fetch_all();
        echo json_encode(array_column($messagesData, 0));
    } else {
        echo json_encode([]);
    }
}

?>
