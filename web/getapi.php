<?

	require_once("Database.class.php");
	require_once("Item.class.php");
	require_once("Time.class.php");
	
	// create an object to work with the database with
	$db = new Database();
	
	// get start and stop dates
	$startDate = $_GET['startdate'];
	$endDate = $_GET['enddate'];
	
	// get the current datetime
	$todaysDate = date( 'Y-m-d H:i:s' );
	
	// get the ip of the client
	//$ipaddress = $_SERVER['REMOTE_ADDR'];
	$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	
	// record the API call in the database
	$db->AddAPICall($ipaddress, $startDate, $endDate, $todaysDate);
	
	$errorcode = 0;
	$errortext = "No Errors Reported";
	
	// test to make sure at least a start date was supplied
	if( $startDate == "" )
	{
		$errorcode = 1;
		$errortext = "No Start Date Specified";
	}
	else
	{
		
		//
		// TODO: sanitize inputs
		//
	
		$time = new Time();
		
		// record start time
		$starttime = $time->StartTime();
	
		$results = $db->GetItems($startDate, $stopDate);

		// calculate time taken
		$totaltime = $time->TotalTime($starttime);
		
		// get results count
		$resultscount = count($results);
		
		// json encode the results to be returnedx
		$json_results = json_encode($results);
	
		echo '{"apiversion": "1.0","errorcode": "' . $errorcode . '", "errortext": "' . $errortext . '", "querytime": "' . $totaltime . '", "resultcount": "' . $resultscount . '", "results": ' . $json_results . '}';
	}
	
?>