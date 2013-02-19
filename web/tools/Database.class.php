<?php

	require_once("sqlcredentials.php");
	
	require_once("Item.class.php");
	require_once("EventType.class.php");
	require_once("Event.class.php");
	require_once("Incident.class.php");
	require_once("BlogPost.class.php");
	require_once("Group.class.php");
	require_once("Agency.class.php");
	
	class Database
	{
	
		////////////////////////////////////////////////////////////////////////
		//
		// Blog Functions
		//
		////////////////////////////////////////////////////////////////////////
	
		function CreateBlogPost($title, $body)
		{
			
			// connect to the database
			$this->Connect();
			
			// create the query
			$query = 'INSERT INTO blogposts (title, body, creationdate) VALUES("' . $title . '", "' . $body . '", "' . date("Y-m-d") . '")';
			
			//echo "<br>" . $query;
			
			// execute the query
			$results = $this->Query($query);
			
		}
	
		function GetAllBlogPosts()
		{
			// connect to the database
			$this->Connect();
			
			// create the query
			$query = "SELECT title,body,creationdate FROM blogposts";
			
			// execute the query
			$results = $this->Query($query);
			
			$retVal = array();
			
			// decode the rows
			while($r = mysql_fetch_assoc($results)) {
			
				// create a temp object to populate
				$blogpost = new BlogPost();
			
				// assign the values 
				$blogpost->event = $r['event'];
				$blogpost->address = $r['body'];
				$blogpost->pubdate = $r['creationdate'];
		
				// add the item to the array of items
				$retVal[] = $blogpost;
			}
			
			// return the count
			return $retVal; 
		}
	
		////////////////////////////////////////////////////////////////////////
		//
		// Group Functions
		//
		////////////////////////////////////////////////////////////////////////
	
		function GetAllGroups()
		{
			// connect to the database
			$this->Connect();
			
			// create the query
			$query = "SELECT * FROM groups";
			
			// execute the query
			$results = $this->Query($query);
			
			$retVal = array();
			
			// decode the rows
			while($r = mysql_fetch_assoc($results)) {
			
				// create a temp object to populate
				$group = new Group();
			
				// assign the values 
				$group->id = $r['groupid'];
				$group->name = $r['groupname'];
				$group->description = $r['groupdescription'];
		
				// add the item to the array of items
				$retVal[] = $group;
			}
			
			// return the count
			return $retVal; 
		}
	
		////////////////////////////////////////////////////////////////////////
		//
		// API Functions
		//
		////////////////////////////////////////////////////////////////////////
		
		function AddAPICall($ipaddress, $callDateTime, $totalTime, $api)
		{
			// connect to the database
			$this->Connect();
			
			// create the query
			$query = 'INSERT INTO apicalls (ipaddress, calldatetime, querytime, api) VALUES("' . $ipaddress . '", "' . $callDateTime . '", "' . $totalTime . '", "' . $api . '")';
			
			// execute the query
			$results = $this->Query($query);
		}
		
		function GetAllItems()
		{
			// connect to the database
			$this->Connect();
			
			// create the query
			$query = "SELECT * FROM incidents";
			
			// execute the query
			$results = $this->Query($query);
			
			$retVal = array();
			
			// decode the rows
			while($r = mysql_fetch_assoc($results)) {
			
				// create a temp object to populate
				$item = new Item();
			
				// assign the values 
				$item->event = $r['event'];
				$item->address = $r['address'];
				$item->pubdate = $r['pubdate'];
				$item->pudtime = $r['pubtime'];
				$item->status = $r['status'];
				$item->incidentid = $r['itemid'];
				$item->scrapedatetime = $r['event'];
		
				// add the item to the array of items
				$retVal[] = $item;
			}
			
			// return the count
			return $retVal; 
		}
		
		function GetItems($startDate, $endDate)
		{
			// connect to the database
			$this->Connect();
			
			// figure out if we need to use the end date or not
			if( $endDate == "" )
			{
				// create the query
				$query = 'SELECT * FROM incidents where pubdate>="' . $startDate . '"';
			}
			else
			{
				// create the query
				$query = 'SELECT * FROM incidents where pubdate>="' . $startDate . '" AND pubdate<="' . $endDate .'"';
			}
			// execute the query
			$results = $this->Query($query);
			
			$retVal = array();
			
			// decode the rows
			while($r = mysql_fetch_assoc($results)) {
			
				// create a temp object to populate
				$item = new Item();
			
				// assign the values 
				$item->event = $r['event'];
				$item->address = $r['address'];
				$item->pubdate = $r['pubdate'];
				$item->pubtime = $r['pubtime'];
				$item->status = $r['status'];
				$item->incidentid = $r['itemid'];
				$item->scrapedatetime = $r['scrapedatetime'];
		
				// add the item to the array of items
				$retVal[] = $item;
			}
			
			// return the count
			return $retVal; 
		}
		
		function GetAllTimeItemsByEventTypeID($eventtypeid)
		{
			$eventtype = $this->GetEventTextFromID($eventtypeid);
			
			$results = $this->GetAllTimeItemsByEventType($eventtype);
			
			return $results;
		}
		
		function GetAllTimeItemsByEventType($eventtype)
		{
			// connect to the database
			$this->Connect();

			$query = 'SELECT COUNT(DISTINCT itemid), pubtime FROM incidents WHERE LOWER(event)="' .$eventtype . '" GROUP BY HOUR(pubtime)';
			
			//echo $query . "<br>";
			
			// execute the query
			$results = $this->Query($query);
			
			$retVal = "thexval\ttheyval\n";
			
			$events = array();
			
			while($r = mysql_fetch_assoc($results)) {
				$event = new Event();
				
				$event->pubtime = $r['pubtime'];
				$event->count = $r['COUNT(DISTINCT itemid)'];
				
				//echo "Pubtime: " . $event->pubtime . ", Count = " . $event->count . "<br>";
				
				$events[] = $event;
			}
			
			for($hour=0; $hour<24; $hour++)
			{
			
				$count = 0;
			
				// decode the rows
				foreach($events as $event)
				{
				
					$pubhour = (int)substr($event->pubtime,0,2);
				
					//echo "comparing " . $pubhour . " and " . $hour . " ... ";
				
					if( $pubhour == $hour )
					{
						//echo "equals";
						$count = $count + $event->count;
					}
					else
					{
						//echo "not equals";
					}
					
					//echo "<br>";
					
				}
				
				//echo "<br>( " . $hour . " ) Count = " . $count . "<br><br>";
				
				$retVal = $retVal . $hour . ":00\t" . $count . "\n";
			
			}
			
			
			// return the count
			return $retVal;  
		
		}
		
		function GetTotalItemsByEventTypeID($eventtypeid, $startdate)
		{
			$eventtype = $this->GetEventTextFromID($eventtypeid);
			
			$results = $this->GetTotalItemsByEventType($eventtype, $startdate);
			
			return $results;
		}
		
		function GetTotalItemsByEventType($eventtype, $startdate)
		{
			// connect to the database
			$this->Connect();
			
			$query = 'SELECT pubdate,count(distinct itemid) FROM incidents WHERE LOWER(event)="' . $eventtype . '" AND pubdate>="' . $startdate . '" GROUP BY pubdate';
			
			//echo $query . "<br>";
			
			// execute the query
			$results = $this->Query($query);
			
			$retVal = "thexval\ttheyval\n";
			
			// decode the rows
			while($r = mysql_fetch_assoc($results)) {
			
				// assign the values 
				$retVal = $retVal . substr($r['pubdate'],5) . "\t" . $r['count(distinct itemid)'] . "\n";
			}
			
			// return the count
			return $retVal; 
		}
		
		function GetIncidentsByDay($date)
		{
			// connect to the database
			$this->Connect();
			
			// create the query
			$query = 'SELECT DISTINCT itemid,event,address,pubdate,pubtime,status,itemid,scrapedatetime FROM incidents WHERE pubdate="' . $date . '" GROUP BY itemid ORDER BY pubtime DESC';
			
			// execute the query
			$results = $this->Query($query);
			
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
		
		////////////////////////////////////////////////////////////////////////
		//
		// Agency Functions
		//
		////////////////////////////////////////////////////////////////////////
		
		function GetIncidentsByAgencyShortName($agencyshortname, $count)
		{
			// connect to the database
			$this->Connect();
			
			// create the query
			$query = 'SELECT DISTINCT itemid,event,address,pubdate,pubtime,status,itemid,scrapedatetime FROM incidents WHERE itemid LIKE "%' . $agencyshortname . '%" GROUP BY itemid ORDER BY pubdate DESC LIMIT ' . $count;
			
			// execute the query
			$results = $this->Query($query);
			
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
		
		function GetAgencyFromShortName($shortname)
		{
			// connect to the database
			$this->Connect();
			
			// create the query
			$query = 'SELECT * FROM agencies WHERE shortname="' . $shortname . '"';
			
			// execute the query
			$results = $this->Query($query);
			
			$agency = new Agency();
			
			$r = mysql_fetch_assoc($results);
			
			// pull the information from the row
			$agency->shortname = $r['shortname'];
			$agency->longname = $r['longname'];
			$agency->type = $r['type'];
			$agency->description = $r['description'];
			$agency->websiteurl = $r['websiteurl'];
			
			// return our created object
			return $agency;
		}
		
		function GetTotalIncidentsByAgencyShortName($shortname, $year)
		{
			// connect to the database
			$this->Connect();
			
			//echo "HERE!";
			
			// create the query
			$query = 'SELECT COUNT(DISTINCT itemid)  FROM incidents WHERE itemid LIKE "%' . $shortname . '%" AND pubdate >= "' . $year . '-01-01"';
			
			// execute the query
			$results = $this->Query($query);
			
			// get the row
			$r = mysql_fetch_assoc($results);
			
			// get the count
			$count = $r['COUNT(DISTINCT itemid)'];
			
			//echo $count;
			
			// return our created object
			return $count;
		}
		
		function GetAllAgencies()
		{
			// connect to the database
			$this->Connect();
			
			// create the query
			$query = 'SELECT * FROM agencies ORDER BY shortname ASC';
			
			$date = date("Y");
			
			// execute the query
			$results = $this->Query($query);
			
			$agencies = array();
			
			// decode the rows
			while($r = mysql_fetch_assoc($results)) {
			
				$agency = new Agency();
			
				// pull the information from the row
				$agency->shortname = $r['shortname'];
				$agency->longname = $r['longname'];
				$agency->type = $r['type'];
				$agency->description = $r['description'];
				$agency->websiteurl = $r['websiteurl'];
				
				$agency->callcount = $this->GetTotalIncidentsByAgencyShortName($agency->shortname, $date);
				
				$agencies[] = $agency;
			}	
			
			// return our created object
			return $agencies;
		}
		
		////////////////////////////////////////////////////////////////////////
		//
		// System Functions
		//
		////////////////////////////////////////////////////////////////////////
		
		function GetUniqueAPIUsersToday()
		{
			// connect to the database
			$this->Connect();
			
			// create the query
			$query = 'SELECT COUNT(DISTINCT ipaddress) FROM apicalls WHERE calldatetime>="' . date("Y-m-d") . '"';
			
			// execute the query
			$results = $this->Query($query);
			
			// get the row
			$r = mysql_fetch_assoc($results);
			
			// return the count
			return $r["COUNT(DISTINCT ipaddress)"]; 
		}
		
		function GetTotalUniqueAPIUsers()
		{
			// connect to the database
			$this->Connect();
			
			// create the query
			$query = 'SELECT COUNT(DISTINCT ipaddress) FROM apicalls';
			
			// execute the query
			$results = $this->Query($query);
			
			// get the row
			$r = mysql_fetch_assoc($results);
			
			// return the count
			return $r["COUNT(DISTINCT ipaddress)"]; 
		}
		
		function GetNumberOfRuns()
		{
			// connect to the database
			$this->Connect();
			
			// create the query
			$query = "SELECT count(*) FROM runs";
			
			// execute the query
			$results = $this->Query($query);
			
			// get the row
			$r = mysql_fetch_assoc($results);
			
			// return the count
			return $r["count(*)"]; 
		}
		
		function GetNumberOfAPICalls()
		{
			// connect to the database
			$this->Connect();
			
			// create the query
			$query = "SELECT count(*) FROM apicalls";
			
			// execute the query
			$results = $this->Query($query);
			
			// get the row
			$r = mysql_fetch_assoc($results);
			
			// return the count
			return $r["count(*)"]; 
		}
		
		function GetAverageQueryToday()
		{
			// connect to the database
			$this->Connect();
			
			// create the query
			$query = 'SELECT avg(querytime) FROM apicalls where calldatetime>="' . date("Y-m-d") . '"';
			
			// execute the query
			$results = $this->Query($query);
			
			// get the row
			$r = mysql_fetch_assoc($results);
			
			// return the count
			return $r["avg(querytime)"]; 
		}
		
		////////////////////////////////////////////////////////////////////////
		//
		// Data functions
		//
		////////////////////////////////////////////////////////////////////////
		
		function GetTotalUniqueIncidents()
		{
			// connect to the database
			$chandle = $this->Connect();
			
			// create the query
			$query = "SELECT count(*) FROM incidents";
			
			// execute the query
			$results = $this->Query($query);
			
			// get the row
			$r = mysql_fetch_assoc($results);
			
			// return the count
			return $r["count(*)"];
		}
		
		function GetTotalEventTypes()
		{
			// connect to the database
			$chandle = $this->Connect();
			
			// create the query
			$query = "SELECT count(*) FROM eventtypes";
			
			// execute the query
			$results = $this->Query($query);
			
			// get the row
			$r = mysql_fetch_assoc($results);
			
			// return the count
			return $r["count(*)"];
		}
		
		function GetTotalStatusTypes()
		{
			// connect to the database
			$chandle = $this->Connect();
			
			// create the query
			$query = "SELECT count(*) FROM statustypes";
			
			// execute the query
			$results = $this->Query($query);
			
			// get the row
			$r = mysql_fetch_assoc($results);
			
			// return the count
			return $r["count(*)"];
		}
		
		function GetStatsByDay($thedate)
		{
			//
			// first we need to get the list of eventtypes
			//
			
			// get the list of event types
			$eventtypes = $this->GetEventTypes();
		
			//
			// Now we need to get the count of each eventtype for the day
			//
			
			$stats = "event\tfrequency\n";
			
			$letter = "A";
			
			foreach($eventtypes as $event)
			{
			
				// query the count of the eventtype on this day
				$query = 'SELECT count(DISTINCT itemid) FROM incidents WHERE LOWER(event)=LOWER("' . $event->eventtype . '") AND pubdate="' . $thedate . '"';
			
				//echo $query . "<br>";
			
				// execute the query
				$results = $this->Query($query);
			
				// get the row
				$r = mysql_fetch_assoc($results);
				
				// create entry
				$stat = $letter . "\t" . $r["count(DISTINCT itemid)"] . "\n";
				
				// append entry to return string
				$stats = $stats . $stat;
			
				// increment our letter
				$letter++;
			
			}
			
			//$stats = $stats . "}";
			
			return $stats;
		
		}
		
		function GetStats($startdate, $enddate)
		{
			//
			// first we need to get the list of eventtypes
			//
			
			// get the list of event types
			$eventtypes = $this->GetEventTypes();
		
			//
			// Now we need to get the count of each eventtype for the day
			//
			
			$stats = "event\tfrequency\n";
			
			$letter = "A";
			
			foreach($eventtypes as $event)
			{
			
				// query the count of the eventtype on this day
				$query = 'SELECT count(DISTINCT itemid) FROM incidents WHERE LOWER(event)=LOWER("' . $event->eventtype . '") AND pubdate>="' . $startdate . '"';
				
				// check to see if there is an end date passed in
				if( $enddate != "" )
				{
					$query = ' AND pubdate<="' . $enddate . '"';
				}
			
				// execute the query
				$results = $this->Query($query);
			
				// get the row
				$r = mysql_fetch_assoc($results);
				
				//if( $r["count(*)"] != "0" )
				//{
					// create entry
					$stat = $letter . "\t" . $r["count(DISTINCT itemid)"] . "\n";
					
					// append entry to return string
					$stats = $stats . $stat;
				//}
				
				// increment our letter
				$letter++;
			
			}
			
			//$stats = $stats . "}";
			
			return $stats;
			
		}
		
		function GetEventTypes()
		{
			// connect to the database
			$this->Connect();
			
			//
			// first we need to get the list of eventtypes
			//
			
			// create the query
			$query = 'SELECT * FROM eventtypes';
			
			// execute the query
			$results = $this->Query($query);
			
			$eventtypes = array();
			
			// decode the rows
			while($r = mysql_fetch_assoc($results)) {
			
				$eventtype = new EventType();
			
				// pull the information from the row
				$eventtype->eventtypeid = $r['eventtypeid'];
				$eventtype->eventtype = $r['eventtype'];
				
				$eventtypes[] = $eventtype;
			}
			
			return $eventtypes;
		}
		
		function GetEventTextFromID($id)
		{
			// connect to the database
			$chandle = $this->Connect();
			
			// create the query
			$query = 'SELECT eventtype FROM eventtypes WHERE eventtypeid=' . $id;
			
			//echo $query;
			
			// execute the query
			$results = $this->Query($query);
			
			// get the row
			$r = mysql_fetch_assoc($results);
			
			// return the count
			return $r["eventtype"];
		}
		
		function GetItemsByEventID($eventtypeid, $startdate)
		{
			// get event text from id
			$eventtype = $this->GetEventTextFromID($eventtypeid);
			
			//echo $eventtype;
			
			$result = $this->GetItemsByEvent($eventtype, $startdate);
			
			return $result;
		}
		
		function GetItemsByEvent($eventtype, $startdate)
		{
			// connect to the database
			$this->Connect();
			
			// create the query
			$query = 'SELECT * FROM incidents WHERE LOWER(event)="' . $eventtype . '" AND pubdate>="' . $startdate . '"';
			
			//echo $query;
			
			// execute the query
			$results = $this->Query($query);
			
			$retVal = array();
			
			// decode the rows
			while($r = mysql_fetch_assoc($results)) {
			
				// create a temp object to populate
				$item = new Item();
			
				// assign the values 
				$item->event = $r['event'];
				$item->address = $r['address'];
				$item->pubdate = $r['pubdate'];
				$item->pubtime = $r['pubtime'];
				$item->status = $r['status'];
				$item->incidentid = $r['itemid'];
				$item->scrapedatetime = $r['scrapedatetime'];
		
				// add the item to the array of items
				$retVal[] = $item;
			}
			
			// return the count
			return $retVal; 
		}
		
		////////////////////////////////////////////////////////////////////////
		//
		// Private functions
		//
		////////////////////////////////////////////////////////////////////////
		
		function Connect()
		{
			// connect to the mysql database server.  Constants taken from sqlcredentials.php
			$chandle = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS)
				or die("Connection Failure to Database");				// TODO: something more elegant than this

			mysql_select_db(MYSQL_DATABASE, $chandle)
				or die ("Database not found (" . MYSQL_DATABASE . ")");	// TODO: something more elegant than this

			return $chandle;
		}
		
		function Query($query)
		{
			// pull from DB
			$result = mysql_db_query(MYSQL_DATABASE, $query)
				or die("Failed Query of " . $query);  			// TODO: something more elegant than this
			
			return $result;
		}
		
	}

?>