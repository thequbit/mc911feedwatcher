<?php

	require_once("Database.class.php");
	require_once("Time.class.php");

	$db = new Database();

	$time = new Time();
	// get the current datetime
	$todaysDate = date( 'Y-m-d H:i:s' );

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
		
		// record start time
		$starttime = $time->StartTime();
		
		$stats = $db->GetStats($startdate, $enddate);
		
		// calculate time taken
		$totaltime = $time->TotalTime($starttime);
		
		echo $stats;
		
	}
	
	// record the API call in the database
	$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	$db->AddAPICall($ipaddress, $todaysDate, $totaltime, "STATSAPI");

?>