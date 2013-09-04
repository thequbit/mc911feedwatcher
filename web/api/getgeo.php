<?php

	require_once("../tools/UtilityManager.class.php");
	require_once("../tools/LocationManager.class.php");

	$date = $_GET["date"];
	$typeid = $_GET["typeid"];
	
	// do some sanity checking
	$util = new UtilityManager();
	if( $date == "" || $util->IsValidDate($date) == 0 || $util->IsValidDate($date) == False )
	{
		// not a valid date, set to today's date
		$date = date("Y-m-d");
	}
	
	if( $util->IsNumber($typeid) == false )
	{
		$typeid = "";
	}
	
	$mgr = new LocationManager();
	
	if( $typeid == "" )
		$locations = $mgr->GetLocationsByDay($date);
	else
		$locations = $mgr->GetLocationsByDayByType($date,$typeid);
	
	echo json_encode($locations);
	
?>