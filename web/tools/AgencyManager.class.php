<?php

	require_once("DatabaseManager.class.php");
	require_once("Agency.class.php");

	class AgencyManager
	{
		function GetAgencyFromID($agencyid)
		{
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();
			
			// create the query
			$query = 'SELECT * FROM agencies WHERE agencyid = ' . $agencyid;
			
			// execute the query
			$results = $db->Query($query);
			
			// get all agencies
			$dict = $this->GetYearAllAgencyCounts();
			
			// get the row from the result
			$r = mysql_fetch_assoc($results);
			
			// create a new agency object to return
			$agency = new Agency();
			
			// pull the information from the row into the agency object
			$agency->agencyid = $r['agencyid'];
			$agency->shortname = $r['shortname'];
			$agency->longname = $r['longname'];
			$agency->type = $r['type'];
			$agency->description = $r['description'];
			$agency->websiteurl = $r['websiteurl'];
			
			$id = $agency->agencyid;
			$agency->callcount = $dict[$id];
			
			// return our created object
			return $agency;
		}
		
		function GetAgencyFromShortName($agencyshortname)
		{
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();
			
			// create the query
			$query = 'SELECT * FROM agencies WHERE shortname = "' . $agencyshortname . '"';
			
			// execute the query
			$results = $db->Query($query);
			
			// get all agencies
			$dict = $this->GetYearAllAgencyCounts();
			
			// get the row from the result
			$r = mysql_fetch_assoc($results);
			
			// create a new agency object to return
			$agency = new Agency();
			
			// pull the information from the row into the agency object
			$agency->agencyid = $r['agencyid'];
			$agency->shortname = $r['shortname'];
			$agency->longname = $r['longname'];
			$agency->type = $r['type'];
			$agency->description = $r['description'];
			$agency->websiteurl = $r['websiteurl'];
			
			$id = $agency->agencyid;
			$agency->callcount = $dict[$id];
			
			// return our created object
			return $agency;
		}
		
		function GetAllAgencies()
		{
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();
			
			// create the query
			$query = 'SELECT * FROM agencies ORDER BY shortname ASC';
			
			// execute the query
			$results = $db->Query($query);
			
			// get all agencies
			$dict = $this->GetYearAllAgencyCounts();
			
			// create an array to place our agency objects into
			$agencies = array();
			
			// decode the rows
			while($r = mysql_fetch_assoc($results)) {
			
				$agency = new Agency();
			
				// pull the information from the row
				$agency->agencyid = $r['agencyid'];
				$agency->shortname = $r['shortname'];
				$agency->longname = $r['longname'];
				$agency->type = $r['type'];
				$agency->description = $r['description'];
				$agency->websiteurl = $r['websiteurl'];
				
				$id = $agency->agencyid;
				$agency->callcount = $dict[$id];
				
				$agencies[] = $agency;
			}	
			
			// return our created object
			return $agencies;
		}
		
		function GetYearAllAgencyCounts()
		{
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();
		
			// create the query
			$query = 'SELECT agencyid, COUNT(DISTINCT itemid) AS count FROM incidents WHERE pubdate >= "2013-01-01" GROUP BY agencyid';
			
			// execute the query
			$results = $db->Query($query);
			
			$dict = array();
			
			// create dictionary
			while($r = mysql_fetch_assoc($results)) {
			
				// decode row
				$id = $r['agencyid'];
				$count = $r['count'];
				
				// insert value into dictionary
				$dict[$id] = $count;
			
			}
			
			return $dict;
		}
		
		function GetTodayAllAgencyCounts()
		{
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();
		
			// create the query
			$query = 'SELECT agencyid, COUNT(DISTINCT itemid) AS count FROM incidents WHERE pubdate >= "' . date("Y-m-d") . '" GROUP BY agencyid';
			
			// execute the query
			$results = $db->Query($query);
			
			$dict = array();
			
			// create dictionary
			while($r = mysql_fetch_assoc($results)) {
			
				// decode row
				$id = $r['agencyid'];
				$count = $r['count'];
				
				// insert value into dictionary
				$dict[$id] = $count;
			
			}
			
			return $dict;
		}
		
		function ValidAgencyByShortName($agencyshortname)
		{
			// connect to the database
			$db = new DatabaseManager();
			$db->Connect();
			
			// create the query
			$query = 'SELECT COUNT(*) as count FROM agencies WHERE shortname = "' . $agencyshortname . '"';
			
			// execute the query
			$results = $db->Query($query);
			
			// get the row from the result
			$r = mysql_fetch_assoc($results);
			
			// see if the shortname exists
			if( $r['count'] == "0" )
				$valid = false;
			else
				$valid = true;
			
			// return our created object
			return $valid;
		}
	}

?>