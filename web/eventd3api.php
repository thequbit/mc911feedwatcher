<?

	require_once("Database.class.php");
	require_once("Time.class.php");
	
	$eventtypeid = $_GET['eventtypeid'];
	$startdate = $_GET['startdate'];
	$period = $_GET['period'];

	$db = new Database();
	
	$time = new Time();
	
	// record start time
	$starttime = $time->StartTime();
	
	switch( $period )
	{
		case 'today':
			$results = $db->GetTodaysItemsByEventTypeID($eventtypeid);
			break;
		case 'week':
			//break;
		case 'month':
			//break;
		case 'all':
		default:
			$results = $db->GetTotalItemsByEventTypeID($eventtypeid, $startdate);
			break;
	}
	
	

	// calculate time taken
	$totaltime = $time->TotalTime($starttime);

	echo $results;
	
	// get the current datetime
	$todaysDate = date( 'Y-m-d H:i:s' );
	
	// record the API call in the database
	$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	$db->AddAPICall($ipaddress, $todaysDate, $totaltime, "EVENTD3API");

?>