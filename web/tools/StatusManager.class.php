<?php
	
	require_once("DatabaseTool.class.php");
	
	class StatusManager
	{
		function GetNumberOfRuns()
		{
			dprint( "GetNumberOfRuns() Start." );
			
			try
			{
		
				$db = new DatabaseTool();
			
				// create the query
				$query = "SELECT COUNT(*) AS count FROM runs";
			
				$mysqli = $db->Connect();
				$stmt = $mysqli->prepare($query);
				$results = $db->Execute($stmt);
			
				$count = $results[0]["count"];
			
				// close our DB connection
				$db->Close($mysqli, $stmt);
			
			}
			catch (Exception $e)
			{
				dprint( "Caught exception: " . $e->getMessage() );
			}
			
			dprint("GetNumberOfRuns() Done.");
			
			// return the count
			return $count;
		}
		
		function GetTotalUniqueIncidents()
		{
			dprint( "GetTotalUniqueIncidents() Start." );
			
			try
			{
		
				$db = new DatabaseTool();
			
				// create the query
				$query = "SELECT COUNT(DISTINCT itemid) AS count FROM incidents";
			
				$mysqli = $db->Connect();
				$stmt = $mysqli->prepare($query);
				$results = $db->Execute($stmt);
			
				$count = $results[0]["count"];
			
				// close our DB connection
				$db->Close($mysqli, $stmt);
			
			}
			catch (Exception $e)
			{
				dprint( "Caught exception: " . $e->getMessage() );
			}
			
			dprint("GetTotalUniqueIncidents() Done.");
			
			// return the count
			return $count;
		}

		function GetTotalEventTypes()
		{
			dprint( "GetTotalEventTypes() Start." );
			
			try
			{
		
				$db = new DatabaseTool();
			
				// create the query
				$query = "SELECT COUNT(*) AS count FROM eventtypes";
			
				$mysqli = $db->Connect();
				$stmt = $mysqli->prepare($query);
				$results = $db->Execute($stmt);
			
				$count = $results[0]["count"];
			
				// close our DB connection
				$db->Close($mysqli, $stmt);
			
			}
			catch (Exception $e)
			{
				dprint( "Caught exception: " . $e->getMessage() );
			}
			
			dprint("GetTotalEventTypes() Done.");
			
			// return the count
			return $count;
		}
		
		function GetTotalStatusTypes()
		{
			dprint( "GetTotalStatusTypes() Start." );
			
			try
			{
		
				$db = new DatabaseTool();
			
				// create the query
				$query = "SELECT COUNT(*) AS count FROM statustypes";
			
				$mysqli = $db->Connect();
				$stmt = $mysqli->prepare($query);
				$results = $db->Execute($stmt);
			
				$count = $results[0]["count"];
			
				// close our DB connection
				$db->Close($mysqli, $stmt);
			
			}
			catch (Exception $e)
			{
				dprint( "Caught exception: " . $e->getMessage() );
			}
			
			dprint("GetTotalStatusTypes() Done.");
			
			// return the count
			return $count;
		}
	}
	
?>