<?php

	require_once("../tools/UtilityManager.class.php");
	require_once("../tools/APIManager.class.php");
	
	$year = "";
	
	if( isset($_GET['year']) )
		$date = $_GET['year'];
	
	// do some sanity checking
	/*
	$util = new UtilityManager();
	if( $date == "" || $util->IsValidDate($date) == 0 || $util->IsValidDate($date) == False )
	{
		// not a valid date, set to today's date
		$date = date("Y-m-d");
	}
	*/
	
	// create an instance of our manager
	$mgr = new APIManager();
	
	if($year != "")	
		$results = $mgr->GetAverages($year);
	else
		$results = "";
	
	// deturmine what API we are returning
	switch($type)
	{
		//
		// Daily Incident Counts API Call
		//
		case "dailycounts":
			// from passed in $_GET[] ...
			if( $date == "" )
				$date = date("Y-m-d");
		
			$results = $mgr->GetCountsByDay($date);
			break;
	
		//
		// Incident Specific API Calls
		//
		case "barkingdogs":
			$results = $mgr->GetAllDogCounts();
			break;
	
		case "mva":
			$results = $mgr->GetMVACounts();
			break;
	
		//
		// All time summation of all incidents
		//
		case "alltimesum":
			$results = $mgr->GetAllTimeSum();
			break;
			
		//
		// I no API good ...
		//
		default:
			$results = array(
				"error" => "Invalid Input."
			);
			break;
	}
	
	// encode and return
	$jsonResults = json_encode($results);
	echo $jsonResults;

?>