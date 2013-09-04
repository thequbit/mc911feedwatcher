<?php

	require_once("DatabaseTool.class.php");
	require_once("Event.class.php");
	require_once("EventType.class.php");

	class EventManager
	{
	
		function GetEventTypes()
		{
			dprint( "GetEventTypes() Start." );
			
			try
			{
		
				$db = new DatabaseTool();
			
				// create the query
				$query = 'SELECT eventtypeid,eventtype FROM eventtypes';
			
				$mysqli = $db->Connect();
				$stmt = $mysqli->prepare($query);
				$results = $db->Execute($stmt);
			
				dprint( "Processing " . count($results) . " Results ..." );
			
				// create an array to put our objects into
				$eventtypes = array();
			
				// decode the rows
				foreach( $results as $result )
				{
					// create a new event object
					$eventtype = new EventType();
				
					// pull the information from the row
					$eventtype->eventtypeid = $result['eventtypeid'];
					$eventtype->eventtype = $result['eventtype'];
					
					// add out event type to the array
					$eventtypes[] = $eventtype;
				}
			
				// close our DB connection
				$db->Close($mysqli, $stmt);
			
			}
			catch (Exception $e)
			{
				dprint( "Caught exception: " . $e->getMessage() );
			}
			
			dprint("GetEventTypes() Done.");
			
			return $eventtypes;
		}
		
		function GetEventTextFromID($id)
		{
			dprint( "GetEventTextFromID() Start." );
			
			try
			{
		
				$db = new DatabaseTool();
	
				// create the query
				$query = 'SELECT eventtype FROM eventtypes WHERE eventtypeid= ?';
			
				$mysqli = $db->Connect();
				$stmt = $mysqli->prepare($query);
				$stmt->bind_param("s", $id); // bind the varibale
				$results = $db->Execute($stmt);
				
				dprint( "Processing " . count($results) . " Results ..." );
				
				// close our DB connection
				$db->Close($mysqli, $stmt);
			
				$eventtype = $results[0]["eventtype"];
			
			}
			catch (Exception $e)
			{
				dprint( "Caught exception: " . $e->getMessage() );
			}
			
			dprint("GetEventTextFromID() Done.");
		
			return $eventtype;
		}
		
		function GetAllTimeHourlyCountsByEventId($eventtypeid)
		{
			dprint( "GetAllTimeHourlyCountsByEventId() Start." );
			
			try
			{
		
				$db = new DatabaseTool();

				// get the eventtype via the id
				$eventtype = $this->GetEventTextFromID($eventtypeid);

				// generate query
				$query = 'SELECT COUNT(DISTINCT itemid) as count, pubtime FROM incidents WHERE LOWER(event) = LOWER(?) GROUP BY HOUR(pubtime)';
			
				$mysqli = $db->Connect();
				$stmt = $mysqli->prepare($query);
				$stmt->bind_param("s", $eventtype); // bind the varibale
				$results = $db->Execute($stmt);
			
				dprint( "Processing " . count($results) . " Results ..." );
				
				// create an array to place the events into
				$events = array();
			
				// populate the array of event counts
				foreach($results as $result)
				{
					$event = new Event();
					
					$event->pubtime = $result['pubtime'];
					$event->count = $result['count'];
					
					$events[] = $event;
				}
		
				// create an array to put the counts into
				$counts = array();
			
				$hour = 0;
			
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
		
				// close our DB connection
				$db->Close($mysqli, $stmt);
			
			}
			catch (Exception $e)
			{
				dprint( "Caught exception: " . $e->getMessage() );
			}
			
			dprint("GetAllTimeHourlyCountsByEventId() Done.");

			return $counts;
		}
		
		function GetHourlyCountsByEventId($eventtypeid, $date)
		{
			dprint( "GetHourlyCountsByEventId() Start." );
			
			try
			{
		
				$db = new DatabaseTool();

				// get the eventtype via the id
				$eventtype = $this->GetEventTextFromID($eventtypeid);
				if( $date == "" )
					$date = date("Y-m-d");

				// genrate the query
				$query = 'SELECT COUNT(DISTINCT itemid) as count, pubtime FROM incidents WHERE LOWER(event)= ? AND pubdate = ? GROUP BY HOUR(pubtime)';
			
				$mysqli = $db->Connect();
				$stmt = $mysqli->prepare($query);
				$stmt->bind_param("ss", $eventtype, $date); // bind the varibale
				$results = $db->Execute($stmt);
			
				dprint( "Processing " . count($results) . " Results ..." );
			
				// create an array to place the events into
				$events = array();
			
				// populate the array of event counts
				foreach($results as $result)
				{
					$event = new Event();
					
					$event->pubtime = $result['pubtime'];
					$event->count = $result['count'];
					
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
		
				// close our DB connection
				$db->Close($mysqli, $stmt);
			
			}
			catch (Exception $e)
			{
				dprint( "Caught exception: " . $e->getMessage() );
			}
			
			dprint("GetHourlyCountsByEventId() Done.");

			return $counts;
		}
	
	}
	
?>