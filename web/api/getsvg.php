<?php

	require_once("../tools/UtilityManager.class.php");

	// get passed in params
	$type = $_GET['type'];
	$date = $_GET['date'];

	// do some sanity checking
	$util = new UtilityManager();
	if( $date == "" || $util->IsValidDate($date) == 0 || $util->IsValidDate($date) == False )
	{
		// not a valid date, set to today's date
		$date = date("Y-m-d");
	}

	// deturmine what API we are returning
	switch($type)
	{
		//
		// Daily Incident Counts API Call
		//
		/*
		case "dailycounts":
			// from passed in $_GET[] ...
			if( $date == "" )
				$date = date("Y-m-d");
		
			$results = $mgr->GetCountsByDay($date);
			break;
		*/
		
		//
		// Incident Specific API Calls
		//
		case "barkingdogs":
			$scriptName = "barkingdogs.py";
			break;
	
		case "mva":
			$scriptName = "mva.py";
			break;
	
		//
		// All time summation of all incidents
		//
		case "alltimesum":
			$scriptName = "alltimesum.py";
			break;
			
		//
		// I no API good ...
		//
		default:
			echo "Invalid Input.";
			break;
	}

	exec("python ./scripts/" .  $scriptName, $svgImage);
	echo $svgImage[1];

?>