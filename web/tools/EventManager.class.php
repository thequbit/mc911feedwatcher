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
		
		function GetEventTextFromID($id)
		{
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();
			
			// create the query
			$query = 'SELECT eventtype FROM eventtypes WHERE eventtypeid=' . $id;
			
			// execute the query
			$results = $db->Query($query);
			
			// get the row
			$r = mysql_fetch_assoc($results);
			
			// return the count
			return $r["eventtype"];
		}
		
		function GetAllTimeHourlyCountsByEventId($eventtypeid)
		{
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();

			// get the eventtype via the id
			$eventtype = $this->GetEventTextFromID($eventtypeid);

			// generate query
			$query = 'SELECT COUNT(DISTINCT itemid), pubtime FROM incidents WHERE LOWER(event)="' . $eventtype . '" GROUP BY HOUR(pubtime)';
			
			// execute the query
			$results = $db->Query($query);
			
			// create an array to place the events into
			$events = array();
			
			// populate the array of event counts
			while($r = mysql_fetch_assoc($results)) {
				$event = new Event();
				
				$event->pubtime = $r['pubtime'];
				$event->count = $r['COUNT(DISTINCT itemid)'];
				
				$events[] = $event;
			}
		
			// create an array to put the counts into
			$counts = array();
			
			// calculate the counts for each hour
			for($hour=0; $hour<24; $hour++)
			{
			
				$count = 0;
			
				// decode the rows
				foreach($events as $event)
				{
				
					// decode the hour of the incident
					$pubhour = (int)substr($event->pubtime,0,2);
				
					// if the current hour is the published hour, then add to the count
					if( $pubhour == $hour )
					{
						$count = $count + $event->count;
					}
				}
			
				// add the count to the array
				$counts[] = $count;
			
			}
		
			// return the count
			return $counts;
		}
		
		function GetHourlyCountsByEventId($eventtypeid, $date)
		{
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();

			// get the eventtype via the id
			$eventtype = $this->GetEventTextFromID($eventtypeid);

			// genrate the query
			$query = 'SELECT COUNT(DISTINCT itemid), pubtime FROM incidents WHERE LOWER(event)="' . $eventtype . '" AND pubdate="' . $date . '" GROUP BY HOUR(pubtime)';
			
			// execute the query
			$results = $db->Query($query);
			
			// create an array to place the events into
			$events = array();
			
			// populate the array of event counts
			while($r = mysql_fetch_assoc($results)) {
				$event = new Event();
				
				$event->pubtime = $r['pubtime'];
				$event->count = $r['COUNT(DISTINCT itemid)'];
				
				$events[] = $event;
			}
		
			// create an array to put the counts into
			$counts = array();
			
			// calculate the counts for each hour
			for($hour=0; $hour<24; $hour++)
			{
			
				$count = 0;
			
				// decode the rows
				foreach($events as $event)
				{
				
					// decode the hour of the incident
					$pubhour = (int)substr($event->pubtime,0,2);
				
					// if the current hour is the published hour, then add to the count
					if( $pubhour == $hour )
					{
						$count = $count + $event->count;
					}
				}
			
				// add the count to the array
				$counts[] = $count;
			
			}
		
			// return the count
			return $counts;
		}
	
	}
	
?>