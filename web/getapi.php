<?

	require_once("Database.class.php");
	require_once("Item.class.php");
	
	// create an object to work with the database with
	$db = new Database();
	
	// get start and stop dates
	$startDate = $_GET['startdate'];
	$stopDate = $_GET['stopdate'];
	
	// test to make sure at least a start date was supplied
	if( $startDate == "" )
	{
		echo '{"error":"You must provide at least a start date"}';
	}
	else
	{
		
		//
		// TODO: sanitize inputs
		//
	
		$results = $db->GetItems($startDate, $stopDate);

		$json_results = json_encode($results);
	
		echo $json_results;
	}
	
?>