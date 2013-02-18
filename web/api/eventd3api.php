<?

	
	require_once("../tools/Database.class.php");
	require_once("../tools/Time.class.php");
	
	$eventtypeid = $_GET['eventtypeid'];
	$startdate = $_GET['startdate'];
	$period = $_GET['period'];

	//
	// TODO: sanitize inputs
	//

	$db = new Database();
	
	$time = new Time();
	
	// record start time
	$starttime = $time->StartTime();
	
	switch( $period )
	{
		case 'today':
			$results = $db->GetTodaysItemsByEventTypeID($eventtypeid);
			break;
		case 'alltime':
			$results = $db->GetAllTimeItemsByEventTypeID($eventtypeid);
			break;
		case 'week':
			//break;
		case 'month':
			//break;
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