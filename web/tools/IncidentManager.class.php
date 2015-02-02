<?php

	require_once("DatabaseTool.class.php");
	require_once("Incident.class.php");
	
	require_once("EventManager.class.php");
	require_once("EventType.class.php");
	
	class IncidentManager
	{
	
		//
		// Page Functions
		//
	
		function GetIncidentsByDay($date, $agencyShortName)
		{
			dprint( "GetIncidentsByDay() Start." );
			
			try
			{
		
				$db = new DatabaseTool();
			
				if( $date == "" )
					$date = date("Y-m-d");
			
			if ( $agencyShortName == "" )
					{
			
			// create the query
					$query = 'SELECT itemid,event,address,pubdate,pubtime,status,itemid,scrapedatetime,agencyid,fulladdress,lat,lng,zipcode FROM incidents WHERE pubdate = ? ORDER BY pubtime DESC';
			
					$mysqli = $db->Connect();
					$stmt = $mysqli->prepare($query);
					$stmt->bind_param("s", $date); // bind the varibale
					
				}
				else 
				{
					
					$agencyManager = new AgencyManager();
					$agency = $agencyManager->GetAgencyFromShortName($agencyShortName);
					
					// create the query
					$query = 'SELECT itemid,event,address,pubdate,pubtime,status,itemid,scrapedatetime,agencyid,fulladdress,lat,lng,zipcode FROM incidents WHERE agencyid = ? AND pubdate = ? ORDER BY pubtime DESC';
			
					$mysqli = $db->Connect();
					$stmt = $mysqli->prepare($query);
					$stmt->bind_param("is", $agency->agencyid, $date); // bind the varibale
					
				}
			
                                $start_get = getmicrotime();
		
				$results = $db->Execute($stmt);
			
				$end_get = getmicrotime();

                                echo "\n\n<!-- sql get incidents took: " . ($end_get - $start_get). "-->";

				// create an array to put our results into
				$incidents = array();
                                $ids = array();			

				$start_loop = getmicrotime();
	
				// decode the rows
				foreach( $results as $result )
				{
					if ( !in_array( $result['itemid'], $ids ) ) {
						$incident = new Incident();
		
						// pull the information from the row
						$incident->event = $result['event'];
						$incident->address = $result['address'];
						$incident->pubdate = $result['pubdate'];
						$incident->pubtime = $result['pubtime'];
						$incident->status = $result['status'];
						$incident->itemid = $result['itemid'];
						$incident->scrapedatetime = $result['scrapedatetime'];
						$incident->agencyid = $result['agencyid'];
						$incident->fulladdress = $result['fulladdress'];
						$incident->lat = $result['lat'];
						$incident->lng = $result['lng'];
						$incident->zipcode = $result['zipcode'];
					
						$incidents[] = $incident;

						$ids[] = $result['itemid'];
					}
				}
			
				$end_loop = getmicrotime();

				echo "\n\n<!-- incidents loop took: " . ($end_loop - $start_loop). "-->";

				// close our DB connection
				$db->Close($mysqli, $stmt);
		
			}
			catch (Exception $e)
			{
				dprint( "Caught exception: " . $e->getMessage() );
			}
		
			dprint("GetIncidentsByDay() Done.");
			
			return $incidents;
		}
		
		function GetIncidentsByAgencyID($agencyid, $count)
		{
			dprint( "GetIncidentsByAgencyID() Start." );
			
			try
			{
		
				$db = new DatabaseTool();
				
				if( $count == "" || $count == 0 )
					$count = 25; // default value
			
				// create the query
				$query = 'SELECT DISTINCT itemid,event,address,pubdate,pubtime,status,scrapedatetime,agencyid,fulladdress,lat,lng,zipcode FROM incidents WHERE agencyid = ? and pubdate = ? GROUP BY itemid ORDER BY pubtime DESC LIMIT ?';
			
				$today = date("Y-m-d");
			
				$mysqli = $db->Connect();
				$stmt = $mysqli->prepare($query);
				$stmt->bind_param("isi", $agencyid, $today, $count); // bind the variable
				$results = $db->Execute($stmt);
			
				// create an array to put our results into
				$incidents = array();
				
				// decode the rows
				foreach( $results as $result )
				{
					$incident = new Incident();
				
					// pull the information from the row
					$incident->itemid = $result['itemid'];
					$incident->event = $result['event'];
					$incident->address = $result['address'];
					$incident->pubdate = $result['pubdate'];
					$incident->pubtime = $result['pubtime'];
					$incident->status = $result['status'];
					$incident->scrapedatetime = $result['scrapedatetime'];
					$incident->agencyid = $result['agencyid'];
					$incident->fulladdress = $result['fulladdress'];
					$incident->lat = $result['lat'];
					$incident->lng = $result['lng'];
					$incident->zipcode = $result['zipcode'];
					
					$incidents[] = $incident;
				}
			
				// close our DB connection
				$db->Close($mysqli, $stmt);
			
			}
			catch (Exception $e)
			{
				dprint( "Caught exception: " . $e->getMessage() );
			}
			
			dprint("GetIncidentsByAgencyID() Done.");
			
			return $incidents;
		}
		
		function GetYearIncidentsByAgencyID($agencyID)
		{
			dprint( "GetYearIncidentsByAgencyID() Start." );
			
			try
			{
		
				$db = new DatabaseTool();
				
				$date = date("Y") . "-01-01";
			
				// create the query
				$query = 'SELECT COUNT(DISTINCT itemid) as count FROM incidents WHERE agencyid = ? AND pubdate >= ?';
			
				$mysqli = $db->Connect();
				$stmt = $mysqli->prepare($query);
				$stmt->bind_param("is", $agencyid, $date); // bind the variable
				$results = $db->Execute($stmt);
			
				// get the count
				$count = $results[0]['count'];
			
				// close our DB connection
				$db->Close($mysqli, $stmt);
			
			}
			catch (Exception $e)
			{
				dprint( "Caught exception: " . $e->getMessage() );
			}
			
			dprint("GetYearIncidentsByAgencyID() Done.");
			
			return $count;
		}
		
		function GetIncidentCountByAgencyIDAndDate($agencyid, $date)
		{
			dprint( "GetIncidentCountByAgencyIDAndDate() Start." );
			
			try
			{
		
				$db = new DatabaseTool();
				
				if( $date == "" )
					$date = date("Y-m-d");
			
				// create the query
				$query = 'SELECT COUNT(DISTINCT itemid) as count FROM incidents WHERE agencyid = ? AND pubdate = ?';
			
				$mysqli = $db->Connect();
				$stmt = $mysqli->prepare($query);
				$stmt->bind_param("is", $agencyid, $date); // bind the variable
				$results = $db->Execute($stmt);
			
				// get the count
				$count = $results[0]['count'];
			
				// close our DB connection
				$db->Close($mysqli, $stmt);
			
			}
			catch (Exception $e)
			{
				dprint( "Caught exception: " . $e->getMessage() );
			}
			
			dprint("GetIncidentCountByAgencyIDAndDate() Done.");
			
			return $count;
		}
		
		function GetIncidentCountsByDate($date)
		{
			
			dprint( "GetIncidentCountsByDate() Start." );
			
			try
			{
		
				$db = new DatabaseTool();
		
				if( $date == "" )
					$date = date("Y-m-d");
			
				// get the counts for all incidents seen today
				$query = 'select count(distinct itemid) as count, event from incidents where pubdate = ? group by event';
			
				$mysqli = $db->Connect();
				$stmt = $mysqli->prepare($query);
				$stmt->bind_param("s", $date); // bind the variable
				$results = $db->Execute($stmt);
		
				// create a dictionary to hold our results in
				$dict = array();
		
				foreach( $results as $result )
				{
			
					// decode the row data
					$event = strtolower($result['event']);
					$count = $result['count'];
					
					// add the key and value to the dictionary
					$dict[$event] = $count;
				
				}
		
				// create an instance of our event manager
				$eventManager = new EventManager();
				
				// get all of the known event types
				$eventtypes = $eventManager->GetEventTypes();
				
				// create an array to return that has our counts in it
				$counts = array();
				
				// create our count list based on our dictionary entries
				foreach($eventtypes as $eventtype)
				{
					$id = strtolower($eventtype->eventtype);
				
					// add the count for the event type to the counts array.  If it doesn't exist, it will be zero
					if( isset($dict[$id]) )
						$counts[] = $dict[$id];
					else
						$counts[] = 0;
				}
				
				// close our DB connection
				$db->Close($mysqli, $stmt);
			
			}
			catch (Exception $e)
			{
				dprint( "Caught exception: " . $e->getMessage() );
			}
			
			dprint("GetIncidentCountsByDate() Done.");
				
			// return our array of counts for the event types
			return $counts;
			
		}
		
	}

?>
