<?php

	require_once("Database.class.php");
	require_once("EventType.class.php");
	require_once("Time.class.php");

	$db = new Database();

	$time = new Time();
	// get the current datetime
	$todaysDate = date( 'Y-m-d H:i:s' );
	
	// record start time
	$starttime = $time->StartTime();

	// get event types
	$eventtypes = $db->GetEventTypes();

	// calculate time taken
	$totaltime = $time->TotalTime($starttime);

	$results = "{";

	foreach($eventtypes as $eventtype)
	{
		$results = $results . '"' . $eventtype->eventtypeid . '": "' . $eventtype->eventtype . '",';
	}

	$results = $results . "}";

	echo $results;
	
	// record the API call in the database
	$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	$db->AddAPICall($ipaddress, $todaysDate, $totaltime, "STATSAPI");

?>