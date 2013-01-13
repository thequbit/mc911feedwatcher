<?

	require_once("Database.class.php");
	
	$eventtypeid = $_GET['eventtypeid'];
	$startdate = $_GET['startdate'];
	
	$db = new Database();
	
	$results = $db->GetTotalItemsByEventTypeID($eventtypeid, $startdate);

	echo $results;

?>