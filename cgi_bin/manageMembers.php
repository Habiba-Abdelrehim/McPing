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
} else if ($_POST["request"] == "inviteMember") {
    inviteMember($con);
} else if ($_POST["request"] == "addMember") {
    addMember($con);
} else if ($_POST["request"] == "deleteMember") {
    deleteMember($con);
}

function inviteMember($database_connection) {
    if (!isset($_POST["board"]) || !isset($_POST["email"])) {
        http_response_code(400); // Bad request
        exit;
    }

    $boardName = filter_var($_POST["board"], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);

    $sql_check_board = "SELECT * FROM `Boards` WHERE `board`='" . $boardName . "' AND `owner`='" . $_COOKIE["email"] . "'";
    $result_check_board = $database_connection->query($sql_check_board);

    if ($result_check_board->num_rows == 0) {
        echo json_encode(["error" => "You do not have permission to invite members to this board"]);
        exit;
    }

    $inviteCode = bin2hex(random_bytes(4));
    $sql_invite_member = sprintf("INSERT INTO `Invitations`(`board`, `email`, `invite_code`) VALUES ('%s','%s','%s')", $boardName, $email, $inviteCode);

    if ($database_connection->query($sql_invite_member) == false) {
        echo json_encode(["error" => "Server Error"]);
        exit;
    }

    echo json_encode(["success" => "Member invited successfully"]);
}


function addMember($database_connection) {
    if (!isset($_POST["board"]) || !isset($_POST["email"])) {
        http_response_code(400); // Bad request
        exit;
    }

    $boardName = filter_var($_POST["board"], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);

    $sql_check_invitation = "SELECT * FROM `Invitations` WHERE `board`='" . $boardName . "' AND `email`='" . $email . "'";
    $result_check_invitation = $database_connection->query($sql_check_invitation);

    if ($result_check_invitation->num_rows == 0) {
        echo json_encode(["error" => "There is no valid invitation for this member"]);
        exit;
    }

    $sql_add_member = sprintf("INSERT INTO `Memberships`(`email`, `board`, `role`) VALUES ('%s','%s','%s')", $email, $boardName, "user");

    if ($database_connection->query($sql_add_member) == false) {
        echo json_encode(["error" => "Server Error"]);
        exit;
    }

    $sql_delete_invitation = sprintf("DELETE FROM `Invitations` WHERE `board`='%s' AND `email`='%s'", $boardName, $email);
    $database_connection->query($sql_delete_invitation);

    echo json_encode(["success" => "Member added successfully"]);
}


function deleteMember($database_connection) {
    if (!isset($_POST["board"]) || !isset($_POST["email"])) {
        http_response_code(400); // Bad request
        exit;
    }

    $boardName = filter_var($_POST["board"], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);

    $sql_check_membership = "SELECT * FROM `Memberships` WHERE `board`='" . $boardName . "' AND `email`='" . $email . "'";
    $result_check_membership = $database_connection->query($sql_check_membership);

    if ($result_check_membership->num_rows == 0) {
        echo json_encode(["error" => "This member is not part of the board"]);
        exit;
    }

    $sql_delete_member = sprintf("DELETE FROM `Memberships` WHERE `board`='%s' AND `email`='%s'", $boardName, $email);

    if ($database_connection->query($sql_delete_member) == false) {
        echo json_encode(["error" => "Server Error"]);
        exit;
    }
    
    echo json_encode(["success" => "Member deleted successfully"]);
}
?>
