<?php
	
	require_once("DatabaseManager.class.php");
	
	class StatusManager
	{
		function GetNumberOfRuns()
		{
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();
			
			// create the query
			$query = "SELECT COUNT(*) AS count FROM runs";
			
			// execute the query
			$results = $db->Query($query);
			
			// get the row
			$r = mysql_fetch_assoc($results);
			
			// return the count
			return $r["count"]; 
		}
		
		function GetTotalUniqueIncidents()
		{
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();
			
			// create the query
			$query = "SELECT COUNT(*) AS count FROM incidents";
			
			// execute the query
			$results = $db->Query($query);
			
			// get the row
			$r = mysql_fetch_assoc($results);
			
			// return the count
			return $r["count"];
		}
		
		function GetTotalEventTypes()
		{
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();
			
			// create the query
			$query = "SELECT COUNT(*) AS count FROM eventtypes";
			
			// execute the query
			$results = $db->Query($query);
			
			// get the row
			$r = mysql_fetch_assoc($results);
			
			// return the count
			return $r["count"];
		}
		
		function GetTotalStatusTypes()
		{
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();
			
			// create the query
			$query = "SELECT COUNT(*) AS count FROM statustypes";
			
			// execute the query
			$results = $db->Query($query);
			
			// get the row
			$r = mysql_fetch_assoc($results);
			
			// return the count
			return $r["count"];
		}
	}
	
?>