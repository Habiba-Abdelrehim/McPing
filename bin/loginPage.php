
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name=viewport content="width-device-width, inital-scale=1.0">
    
        <title>McPing Login</title>
        <script>
            function Login() {
            
            isMissing = false

            if (document.getElementById("email").value == "") {
                document.getElementById("email1").style.color = "red";
                isMissing = true;
            }
            if (document.getElementById("password").value == "") {
                document.getElementById("password1").style.color = "red";
                isMissing = true;
            }
            else {
                document.getElementById("email1").style.color = "black";
            }

            if (isMissing) {
                alert("Please fill all required fields");
                return
            }

            if (!document.getElementById("email").value.includes("@")) {
                alert("Verify email format to be x@y ");
                return;
            }
            
            document.getElementById("form").submit();

            //if passwor doesnt match the one in the database then raise an alert

        }
        
        document.addEventListener("keypress", function(event) {
			if (event.key == "Enter") {
				document.getElementById("loginButton").click();
			}
		});
        </script>
        <style>
            * {
                color: black;
            }
    
            .samewidth {
                width: 100px;
            }

            body{
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
                margin: 0;
            }

            form{
                width: 300px;
                padding: 20px;
                border: 1px solid red;
                border-radius: 5px;

            }
        </style>
    </head>
    <body style="background-image: url(imgs/light\ electirc.jpg);background-repeat: no-repeat; background-size: cover;">
		<?php 
			//If logged in already via a token, auto-redirect to the select discussion page
			require "../cgi_bin/utils.php";

			if ($con->connect_error) {
				die("Internal Server Error: " . $conn->connect_error);
			}

			if (verify_token($con)) {
				?>

				<script type="text/javascript">
					window.location.href = "selectDiscussion.html";
				</script>
				<?php
				exit();
			}

		?>


        <form id="form" action="../cgi_bin/login.php" name="input" method="post" autocomplete=”on” style="background-color:white">

            <h1><b>Login</b></h1>
            <h5>Please fill in all the fields and click Register</h5>
    
    
            <h3><b>User Information</b></h3>
            <table>

                <tr>
                    <td class="samewidth">
                        <b id="email1">Email:</b>
                    </td>
                    <td>
                        <input id="email" type="text" name="email"> <br />
                    </td>
                </tr>
    

                <tr>
                    <td>
                        <b id="password1">Password:</b>
                    </td>
                    <td>
                        <input id="password" type="password" name="password" minlength="1"> <br />
                    </td class="samewidth">
                </tr>

            </table>

            <table>
            <tr>
                <td class="samewidth"></td>
                <td> 
                <button type="button" id="loginButton" value="Register" name="submitButton" onclick="Login()" > Login </button>
                 </td>
                
            </tr>
        </table>
            

        </form>
    
    </body>
</html>
