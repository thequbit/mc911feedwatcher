<?php

	require_once("DatabaseManager.class.php");
	require_once("Incident.class.php");
	
	require_once("EventManager.class.php");
	require_once("EventType.class.php");
	
	class IncidentManager
	{
	
		function GetIncidentsByDay($date)
		{
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();
			
			// create the query
			$query = 'SELECT DISTINCT itemid,event,address,pubdate,pubtime,status,itemid,scrapedatetime,agencyid FROM incidents WHERE pubdate="' . $date . '" GROUP BY itemid ORDER BY pubtime DESC';
			
			// execute the query
			$results = $db->Query($query);
			
			// create an array to put our results into
			$incidents = array();
			
			// decode the rows
			while($r = mysql_fetch_assoc($results)) {
			
				$incident = new Incident();
			
				// pull the information from the row
				$incident->event = $r['event'];
				$incident->address = $r['address'];
				$incident->pubdate = $r['pubdate'];
				$incident->pubtime = $r['pubtime'];
				$incident->status = $r['status'];
				$incident->itemid = $r['itemid'];
				$incident->scrapedatetime = $r['scrapedatetime'];
				$incident->agencyid = $r['agencyid'];
				
				$incidents[] = $incident;
			}
			
			return $incidents;
		}
		
		function GetIncidentsByAgencyShortName($agencyshortname, $count)
		{
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();
			
			// create the query
			$query = 'SELECT DISTINCT itemid,event,address,pubdate,pubtime,status,itemid,scrapedatetime FROM incidents WHERE itemid LIKE "%' . $agencyshortname . '%" GROUP BY itemid ORDER BY pubdate DESC LIMIT ' . $count;
			
			// execute the query
			$results = $db->Query($query);
			
			// create an array to put our results into
			$incidents = array();
			
			// decode the rows
			while($r = mysql_fetch_assoc($results)) {
			
				$incident = new Incident();
			
				// pull the information from the row
				$incident->event = $r['event'];
				$incident->address = $r['address'];
				$incident->pubdate = $r['pubdate'];
				$incident->pubtime = $r['pubtime'];
				$incident->status = $r['status'];
				$incident->itemid = $r['itemid'];
				$incident->scrapedatetime = $r['scrapedatetime'];
				
				$incidents[] = $incident;
			}
			
			return $incidents;
		}
		
		function GetIncidentsByAgencyID($agencyid, $count)
		{
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();
			
			//
			// note: I don't like how we are doing ORDER BY here, but we don't have enough information to do it differently.
			// TODO: Look into better ways to do this.
			//
			
			// create the query
			$query = 'SELECT DISTINCT itemid,event,address,pubdate,pubtime,status,itemid,scrapedatetime FROM incidents WHERE agencyid = ' . $agencyid . ' GROUP BY itemid ORDER BY pubdate DESC LIMIT ' . $count;
			
			// execute the query
			$results = $db->Query($query);
			
			$incidents = array();
			
			// decode the rows
			while($r = mysql_fetch_assoc($results)) {
			
				$incident = new Incident();
			
				// pull the information from the row
				$incident->event = $r['event'];
				$incident->address = $r['address'];
				$incident->pubdate = $r['pubdate'];
				$incident->pubtime = $r['pubtime'];
				$incident->status = $r['status'];
				$incident->itemid = $r['itemid'];
				$incident->scrapedatetime = $r['scrapedatetime'];
				
				$incidents[] = $incident;
			}
			
			return $incidents;
		}
		
		function GetYearIncidentsByAgencyID($agencyID)
		{
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();
			
			// create the query
			$query = 'SELECT COUNT(DISTINCT itemid)  FROM incidents WHERE agencyid = ' . $agencyID . ' AND pubdate >= "' . date("Y") . '-01-01"';
			
			// execute the query
			$results = $db->Query($query);
			
			// get the row
			$r = mysql_fetch_assoc($results);
			
			// get the count
			$count = $r['COUNT(DISTINCT itemid)'];
			
			//echo $count;
			
			// return our created object
			return $count;
		}
		
		function GetIncidentCountByAgencyIDAndDate($agencyid, $date)
		{
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();
			
			// create the query
			$query = 'SELECT COUNT(DISTINCT itemid) FROM incidents WHERE agencyid = ' . $agencyid . ' AND pubdate = "' . $date . '"';

			// execute the query
			$results = $db->Query($query);
			
			// get the row
			$r = mysql_fetch_assoc($results);
			
			// get the count
			$count = $r['COUNT(DISTINCT itemid)'];
			
			//echo $count;
			
			// return our created object
			return $count;
		}
		
		function GetIncidentCountsByDate($date)
		{
			
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();
		
			// get the counts for all incidents seen today
			$query = 'select count(distinct itemid) as count, event from incidents where pubdate="' . $date . '" group by event';
			
			// execute the query
			$results = $db->Query($query);
		
			// create a dictionary to hold our results in
			$dict = array();
		
			// generate dictionary of results
			while($r = mysql_fetch_assoc($results)) {
			
				// decode the row data
				$event = strtolower($r['event']);
				$count = $r['count'];
				
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
				$counts[] = $dict[$id];
			}
			
			// return our array of counts for the event types
			return $counts;
			
		}
		
	}

?>