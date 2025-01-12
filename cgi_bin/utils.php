<?php
	$con = new mysqli("localhost", "root", "", "");
	
	function alert_redirect($message, $redirect) { ?>
		<html>
			<script>
				function error() {
					<?php
						echo "alert('" . $message . "');window.location.href = '" . $redirect . "';";
					?>
				}
				
				
			</script>
			<body onload="error()">
			</body>
		
		</html>
	<?php
		exit;
	}
	
	//Verify a token, compared with the hashed value in the database.
	function verify_token($database_connection) {
		
		if (!isSet($_COOKIE["token"])) {
			return false;
		}
		
		$sql_test = "SELECT `token`, `token_expiry` FROM `Users` WHERE `email`='" . $_COOKIE["email"] . "'";
	
		$result = $database_connection->query($sql_test);
		
		//Check if that token exists
		if ($result->num_rows == 0) {
			return false;
		}
		
		$data = $result->fetch_assoc();
		
		if (time() < $data["token_expiry"] && password_verify($_COOKIE["token"], $data["token"]))
		{
			return true;
		}
		
		return false;
	}
?>
