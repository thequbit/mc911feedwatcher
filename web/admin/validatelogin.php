<?php

	session_start();
	
	//require_once("../tools/Database.php");

	// check to see if we are already logined in
	if( isset($_SESSION['username']) == true )
	{
		// there is already a user logined in, invalidate their login so we can log a new user in
		unset($_SESSION['username']);
	}
	
	if( $username == "tim" && $password == "password")
	{
		
	}
?>