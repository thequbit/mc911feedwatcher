<?php

	require_once("DatabaseTool.Class.php");
	require_once("Event.class.php");

	class BarGraphData
	{
	
		function GetEventTypes()
		{
			
			// connect to database
			$dbt = new DatabaseTool();
			$dbt->Connect();
			
			// create the query
			$query = 'SELECT eventtype FROM eventtypes';
			
			// execute the query
			$results = $dbt->Query($query);
			
			// create an array to place the types in
			$eventtypes = array();
			
			// decode the rows
			while($r = mysql_fetch_assoc($results)) {

				$eventtypes[] = $r['eventtype'];
			}
			
			// return the array of types
			return $eventtypes;
			
		}
	
		function GetIncidentCounts($date)
		{
			
			// connect to database
			$dbt = new DatabaseTool();
			$dbt->Connect();
			
			// get the list of event types
			$eventtypes = $this->GetEventTypes();
			
			// create an array to place our list of counts into
			$counts = array();
			
			// iterate through the event types getting the count for today
			foreach($eventtypes as $eventtype)
			{
				// query the count of the eventtype for today
				$query = 'SELECT count(DISTINCT itemid) FROM incidents WHERE LOWER(event)=LOWER("' . $eventtype . '") AND pubdate="' . $date . '"';
				
				// execute the query
				$results = $dbt->Query($query);
				
				// get the row
				$r = mysql_fetch_assoc($results);
				
				// add the result to the array of counts
				$counts[] = intval($r["count(DISTINCT itemid)"]);
			}

			return $counts;

		}
	
		function GetEventTextFromID($id)
		{
			// connect to the database
			$dbt = new DatabaseTool();
			$dbt->Connect();
			
			// create the query
			$query = 'SELECT eventtype FROM eventtypes WHERE eventtypeid=' . $id;
			
			// execute the query
			$results = $dbt->Query($query);
			
			// get the row
			$r = mysql_fetch_assoc($results);
			
			// return the count
			return $r["eventtype"];
		}
	
		function GetTodaysHourlyCountsByEventId($eventtypeid)
		{
			$eventtype = $this->GetEventTextFromID($eventtypeid);
			
			$results = $this->GetTodaysHourlyCountsByEventType($eventtype);
			
			return $results;
			
			//return array();
		}
		
		function GetTodaysHourlyCountsByEventType($eventtype)
		{
			// connect to the database
			$dbt = new DatabaseTool();
			$dbt->Connect();

			$query = 'SELECT COUNT(DISTINCT itemid), pubtime FROM incidents WHERE LOWER(event)="' .$eventtype . '" AND pubdate="' . date("Y-m-d") . '" GROUP BY HOUR(pubtime)';
			
			// execute the query
			$results = $dbt->Query($query);
			
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
		
		function GetAllTimeHourlyCountsByEventId($eventtypeid)
		{
			$eventtype = $this->GetEventTextFromID($eventtypeid);
			
			$results = $this->GetAllTimeHourlyCountsByEventType($eventtype);
			
			return $results;
			
			//return array();
		}
		
		function GetAllTimeHourlyCountsByEventType($eventtype)
		{
			// connect to the database
			$dbt = new DatabaseTool();
			$dbt->Connect();

			$query = 'SELECT COUNT(DISTINCT itemid), pubtime FROM incidents WHERE LOWER(event)="' .$eventtype . '" GROUP BY HOUR(pubtime)';
			
			// execute the query
			$results = $dbt->Query($query);
			
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