<?php

	require_once("Database.class.php");

	$db = new Database();

	// get URL data
	$startdate = $_GET['startdate'];
	$enddate = $_GET['enddate'];
	
	if( $startdate == "" )
	{
		
		echo "event,freq\nBADREQUEST\t0";
		
	}
	else
	{
	
		//
		// TODO: Sanitize/check inputs
		//
		
		$stats = $db->GetStats($startdate, $enddate);
		
		echo $stats;
		
	}

?>