<?php

	require_once("../tools/APIManager.class.php");
	
	$mgr = new APIManager();
	
	$type = $_GET['type'];
	
	//echo $type;
	
	switch($type)
	{
		//
		// Daily Incident Counts API Call
		//
		case "dailycounts":
			$date = $_GET["date"];
		
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
	
	//$results = $mgr->GetAllCounts();
	
	$jsonResults = json_encode($results);
	
	echo $jsonResults;

?>