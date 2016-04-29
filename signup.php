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
		
			/*Processing Signup*/
			$failureMsg = "";
			$email = "";
			$password = "";
			$password2 = "";
			$validSuccess = true;
			
			if(isset($_POST["submitLogin"]))
			{
				$email = $_POST["email"];
				$password = $_POST["password"];
				$password2 = $_POST["password2"];
				
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
					$failureMsg.= "Kindly enter a password which takes only alphanumerics and space. ";
					$validSuccess = false;
				}
				else
				{
					$password2 = sanitize($password2);
					if(!validateAlphaNumeric($password2) || strlen($password2)<=0)
					{
						$failureMsg.= "Kindly Re-enter password which takes only alphanumerics and space. ";
						$validSuccess = false;
					}
				}
				
				if($password2 != $password)
				{
					$failureMsg.= "Passwords do not match. ";
					$validSuccess = false;
				}
				
				if($validSuccess)
				{
					$user = new BLUser();
					$user->setEmail($email);
					$user->setPassword($password);
					$user->setRole("customer");
					try
					{
						if(!$user->ifEmailExists())
						{						
							if($user->signup())
							{
								$_SESSION["uid"] = $user->getUid();
								$_SESSION["role"] = $user->getRole();
								
								header("location: index.php");
							}
							else
							{
								$failureMsg = "Something went wrong. Please Try again. ";
								
							}
						}
						else
						{
							$failureMsg = "Email already exists. Please Try again. ";
							
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
			<h3>Signup Page</h3>
			<form action="signup.php" method="post">
				Email:</br> 
				<input type="email" name="email" value="<?=$email?>"></br>
				Password:</br>
				<input type="password" name="password" value="<?=$password?>"></br>
				Re-Password:</br>
				<input type="password" name="password2" value="<?=$password2?>"></br></br>
				<input type="submit" name="submitLogin" value="Signup">
			</form>
		</div>
	</body>
</html>