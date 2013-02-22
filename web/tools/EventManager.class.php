<?php

	require_once("DatabaseManager.class.php");
	require_once("Event.class.php");
	require_once("EventType.class.php");

	class EventManager
	{
	
		function GetEventTypes()
		{
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();
			
			// create the query
			$query = 'SELECT * FROM eventtypes';
			
			// execute the query
			$results = $db->Query($query);
			
			// create an array to put our objects into
			$eventtypes = array();
			
			// decode the rows
			while($r = mysql_fetch_assoc($results)) {
			
				// create a new event object
				$eventtype = new EventType();
			
				// pull the information from the row
				$eventtype->eventtypeid = $r['eventtypeid'];
				$eventtype->eventtype = $r['eventtype'];
				
				// add out event type to the array
				$eventtypes[] = $eventtype;
			}
			
			// return the array of event types
			return $eventtypes;
		}
	
	}
	
?>