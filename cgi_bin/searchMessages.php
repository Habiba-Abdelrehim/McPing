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
} else if ($_POST["request"] == "searchChannelMessages") {
    searchChannelMessages($con);
}

function searchChannelMessages($database_connection) {
    if (!isset($_POST["board"]) || !isset($_POST["channel"]) || !isset($_POST["query"])) {
        http_response_code(400); // Bad request
        exit;
    }

    $boardName = filter_var($_POST["board"], FILTER_SANITIZE_STRING);
    $channelName = filter_var($_POST["channel"], FILTER_SANITIZE_STRING);
    $query = filter_var($_POST["query"], FILTER_SANITIZE_STRING);

    $sql = "SELECT message FROM Messages WHERE board = ? AND channel = ? AND message LIKE ?";
    $stmt = $database_connection->prepare($sql);
    
    $searchTerm = "%$query%";

    $stmt->bind_param("sss", $boardName, $channelName, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo json_encode(["error" => "No messages found for the specified channel"]);
    } else {
        $messagesData = $result->fetch_all();
        echo json_encode(array_column($messagesData, 0));
    }
}
?>

