<?php

	require_once("../tools/UtilityManager.class.php");
	require_once("../tools/LocationManager.class.php");

	$date = $_GET["date"];
	
	// do some sanity checking
	$util = new UtilityManager();
	if( $date == "" || $util->IsValidDate($date) == 0 || $util->IsValidDate($date) == False )
	{
		// not a valid date, set to today's date
		$date = date("Y-m-d");
	}
	
	$mgr = new LocationManager();
	
	$locations = $mgr->GetLocationsByDay($date);
	
	echo json_encode($locations);
	
?>