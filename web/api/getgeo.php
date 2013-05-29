<?php

	require_once("../tools/UtilityManager.class.php");
	require_once("../tools/LocationManager.class.php");

	$date = $_GET["date"];
	$type = $_GET["type"];
	
	// do some sanity checking
	$util = new UtilityManager();
	if( $date == "" || $util->IsValidDate($date) == 0 || $util->IsValidDate($date) == False )
	{
		// not a valid date, set to today's date
		$date = date("Y-m-d");
	}
	
	if( $util->IsNumber($type) == false )
	{
		$type = "";
	}
	
	$mgr = new LocationManager();
	
	if( $type == "" )
		$locations = $mgr->GetLocationsByDay($date);
	else
		$locations = $mgr->GetLocationsByDayByType($date,$type);
	
	echo json_encode($locations);
	
?>