<?php

	require_once("../tools/Database.class.php");
	require_once("../tools/Time.class.php");

	$db = new Database();

	$time = new Time();
	// get the current datetime
	$todaysDate = date( 'Y-m-d H:i:s' );

	// get URL data
	$thedate = $_GET['date'];
	//$startdate = $_GET['startdate'];
	//$enddate = $_GET['enddate'];
	
	//echo $thedate;
	
	if( $thedate == "" )
	{
		echo "event\tfrequency\n";
	}
	else
	{
	
		//
		// TODO: Sanitize/check inputs
		//
		
		// record start time
		$starttime = $time->StartTime();
		
		$stats = $db->GetStatsByDay($thedate);
		
		// calculate time taken
		$totaltime = $time->TotalTime($starttime);
		
		echo $stats;
	
	}
	/*
	else
	{
		
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
		
	}
	*/
	
	// record the API call in the database
	$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	$db->AddAPICall($ipaddress, $todaysDate, $totaltime, "STATSAPI");

?>