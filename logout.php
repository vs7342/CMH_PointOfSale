<?php

	session_start();

	unset($_SESSION["uid"]);
	unset($_SESSION["role"]);

	session_unset();
	session_destroy();

	unset($_COOKIE[session_name()]);
	setcookie(session_name(),"",1,"/");
	header("location: index.php");

?>