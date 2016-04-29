<?php
	require_once("utility.php");//This file consists of all the helper/utility functions which would be used throughout the application layer.
	
	//redirecting when user is already logged in
	if(isset($_SESSION["uid"]))
	{
		header("location: index.php");
	}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>CMH - Interview 2</title>
	</head>
	<body>
		<div id="navigation">
			<?php 
				echo getNavigation();
			?>
		</div>
		<?php
		
			/*Processing Login*/
			$failureMsg = "";
			$email = "";
			$password = "";
			$validSuccess = true;
			
			if(isset($_POST["submitLogin"]))
			{
				$email = $_POST["email"];
				$password = $_POST["password"];
				
				//Sanitize Email
				$email = sanitize($email);
				if(strlen($email)<=0)
				{
					$failureMsg.= "Kindly enter your Email ID. ";
					$validSuccess = false;
				}
				
				//Validate and Sanitize Password
				$password = sanitize($password);
				if(!validateAlphaNumeric($password) || strlen($password)<=0)
				{
					$failureMsg.= "Kindly enter a password which takes only alphanumerics and space.";
					$validSuccess = false;
				}
				
				if($validSuccess)
				{
					$user = new BLUser();
					$user->setEmail($email);
					$user->setPassword($password);
					try
					{
						$user = $user->login();
						if($user!==null)
						{
							$_SESSION["uid"] = $user->getUid();
							$_SESSION["role"] = $user->getRole();
							
							header("location: index.php");
						}
						else
						{
							$failureMsg = "Invalid Login. Please Try again.";
							
						}
					}
					catch(DLException $dle)
					{
						$failureMsg = "Something went wrong. Please contact admin.";
					}
				}
			}
		
		?>
		<div class="error">
			<?php
				echo $failureMsg;
			?>
		</div>
		<div id="loginForm">
			<h3>Login Page</h3>
			<form action="login.php" method="post">
				Email:</br> 
				<input type="email" name="email" value="<?=$email?>"></br>
				Password:</br>
				<input type="password" name="password" value="<?=$password?>"></br></br>
				<input type="submit" name="submitLogin" value="Login"><a href="signup.php">Not a Member Yet? Click Here</a>
			</form>
		</div>
	</body>
</html>