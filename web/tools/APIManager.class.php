<?php

	require_once("DatabaseManager.class.php");

	//
	// API Manager Helper Objects
	//

	class Item
	{
		public $date;
		public $count;
	}
	
	class IncidentCount
	{
		public $incidentname;
		public $count;
		public $letter;
	}
	
	//
	// API Manager 
	//
	
	class APIManager
	{
	
	
		//
		// Dailing counts by incident API function
		//
		function GetCountsByDay($date)
		{
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();
			
			// sanitize inputs
			$date = $db->SanitizeInput($date);
			
			// create the query
			$query = 'select tmp.event as incidentname, count(tmp.zeecount) as count from (select DISTINCT itemid, event, count(incidentid) as zeecount from incidents where pubdate = "' . $date . '" group by itemid order by event) as tmp group by tmp.event;';
			
			// execute the query
			$results = $db->Query($query);
			
			// create an array to put our results into
			$items = array();
			
			$letter = 'A';
			
			// decode the rows
			while($r = mysql_fetch_assoc($results)) {
			
				$item = new IncidentCount();
			
				// pull the information from the row
				$item->incidentname = $r['incidentname'];
				$item->count = $r['count'];
				$item->letter = $letter;
				
				$letter++;
				
				$items[] = $item;
			}
			
			return $items;
		}
	
	
		//
		// Specific Incident Types API Functions
		//
	
		function GetMVACounts()
		{
			
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();
			
			// sanitize inputs
			$date = $db->SanitizeInput($date);
			
			// create the query
			$query = 'select tmp.pubdate as date, count(tmp.zeecount) as count from (select DISTINCT itemid, event, pubdate, count(incidentid) as zeecount from incidents where (lower(event) like "%mva%" or lower(event) like "%vehicle%" or lower(event) like "%hit and run%") group by itemid order by event) as tmp group by pubdate';
			
			// execute the query
			$results = $db->Query($query);
			
			// create an array to put our results into
			$items = array();
			
			// decode the rows
			while($r = mysql_fetch_assoc($results)) {
			
				$item = new Item();
			
				// pull the information from the row
				$item->date = $r['date'];
				$item->count = $r['count'];
				
				$letter++;
				
				$items[] = $item;
			}
			
			return $items;
		}
	
		function GetAllDogCounts()
		{
			//echo "here";
		
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();
			
			// sanitize inputs
			$date = $db->SanitizeInput($date);
			
			// create the query
			$query = 'select tmp.pubdate as date, count(tmp.zeecount) as count from (select DISTINCT itemid, pubdate, count(incidentid) as zeecount from incidents where lower(event) = "barking dogs" group by itemid order by pubdate) as tmp group by pubdate;';
			
			// execute the query
			$results = $db->Query($query);
			
			// create an array to put our results into
			$items = array();
			
			// decode the rows
			while($r = mysql_fetch_assoc($results)) {
			
				$item = new Item();
			
				// pull the information from the row
				$item->date = $r['date'];
				$item->count = $r['count'];
				
				$items[] = $item;
			}
			
			return $items;
		}

		//
		// All-Time summations by day
		//

		function GetAllTimeSum()
		{
			//echo "ZOMG";
		
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();
			
			// sanitize inputs
			$date = $db->SanitizeInput($date);
			
			// create the query
			$query = 'select tmp.pubdate as date, count(tmp.zeecount) as count from (select DISTINCT itemid, pubdate, count(incidentid) as zeecount from incidents group by itemid order by pubdate) as tmp group by pubdate;';
			
			// execute the query
			$results = $db->Query($query);
			
			// create an array to put our results into
			$items = array();
			
			// decode the rows
			while($r = mysql_fetch_assoc($results)) {
			
				$item = new Item();
			
				// pull the information from the row
				$item->date = $r['date'];
				$item->count = $r['count'];
				
				$items[] = $item;
			}
			
			return $items;
		}
	
	}

?>