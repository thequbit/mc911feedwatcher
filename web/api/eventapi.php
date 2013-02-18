<?php

	require_once("Database.class.php");
	require_once("Time.class.php");

	$eventtypeid = $_GET['eventtypeid'];
	$startdate = $_GET['startdate'];

	$db = new Database();
	
	$time = new Time();
	// get the current datetime
	$todaysDate = date( 'Y-m-d H:i:s' );
		
	// record start time
	$starttime = $time->StartTime();

	$results = $db->GetItemsByEventID($eventtypeid, $startdate);

	// calculate time taken
	$totaltime = $time->TotalTime($starttime);

	$json_results = json_encode($results);

	echo $json_results;
	
	// record the API call in the database
	$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	$db->AddAPICall($ipaddress, $todaysDate, $totaltime, "EVENTAPI");

?>