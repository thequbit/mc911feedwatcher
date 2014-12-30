<?php

	// allow cross-domain access
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET');
	
	// tell the client that it is a JSON object
	header('content-type: application/json; charset=utf-8');
	

	require_once("../tools/UtilityManager.class.php");
	require_once("../tools/LocationManager.class.php");

    if( isset($_GET['agency']) )
        $agencyShortName = $_GET['agency'];
    else
        $agencyShortName = "";

	$date = $_GET["date"];
    $typeid = "";
	if( isset($_GET['year']) ) {
    
        $typeid = $_GET["typeid"];
        
        if( $util->IsNumber($typeid) == false )
        {
            $typeid = "";
        }
        
    }
	
	// do some sanity checking
	$util = new UtilityManager();
	if( $date == "" || $util->IsValidDate($date) == 0 || $util->IsValidDate($date) == False )
	{
		// not a valid date, set to today's date
		$date = date("Y-m-d");
	}
	
	$mgr = new LocationManager();
	
	if( $typeid == "" ) {
		$locations = $mgr->GetLocationsByDay($date,$agencyShortName);
        echo json_encode($locations);
    }
	else {
		$locations = $mgr->GetLocationsByDayByType($date,$typeid);
        echo '{"typeid": "' . $typeid . '", "locations": ' . json_encode($locations) . '}';
    }
?>