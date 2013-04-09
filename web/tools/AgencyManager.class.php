<?php

	require_once("DatabaseTool.class.php");
	require_once("Agency.class.php");

	class AgencyManager
	{
		function GetAgencyLongNameDictionary()
		{
			dprint( "GetAgencyLongNameDictionary() Start." );
			
			try
			{
		
				$db = new DatabaseTool();
				
				// create the query
				$query = 'SELECT agencyid, longname FROM agencies';
			
				$mysqli = $db->Connect();
				$stmt = $mysqli->prepare($query);
				$results = $db->Execute($stmt);
				
				dprint( "Processing " . count($results) . " Results ..." );
			
				$dict = array();
			
				foreach($results as $result)
				{
					// insert value into dictionary
					$dict[$result['agencyid']] = $result['longname'];
				}
			
				// close our DB connection
				$db->Close($mysqli, $stmt);
			}
			catch (Exception $e)
			{
				dprint( "Caught exception: " . $e->getMessage() );
			}
			
			dprint("GetAgencyLongNameDictionary() Done.");
			
			// return our created object
			return $dict;
		}
		
		function GetAgencyShortNameDictionary()
		{
			dprint( "GetAgencyShortNameDictionary() Start." );
			
			try
			{
		
				$db = new DatabaseTool();
				
				// create the query
				$query = 'SELECT agencyid, shortname FROM agencies';
			
				$mysqli = $db->Connect();
				$stmt = $mysqli->prepare($query);
				$results = $db->Execute($stmt);
				
				dprint( "Processing " . count($results) . " Results ..." );
			
				$dict = array();
			
				foreach($results as $result)
				{
					// insert value into dictionary
					$dict[$result['agencyid']] = $result['shortname'];
				}
			
				// close our DB connection
				$db->Close($mysqli, $stmt);
			}
			catch (Exception $e)
			{
				dprint( "Caught exception: " . $e->getMessage() );
			}
			
			dprint("GetAgencyShortNameDictionary() Done.");
			
			// return our created object
			return $dict;
		}
	
		function GetAgencyFromID($agencyid)
		{
			dprint( "GetAgencyFromID() Start." );
			
			try
			{
		
				$db = new DatabaseTool();
			
				// create the query
				$query = 'SELECT * FROM agencies WHERE agencyid = ?';
			
				$mysqli = $db->Connect();
				$stmt = $mysqli->prepare($query);
				$stmt->bind_param("s", $agencyid); // bind the varibale
				$results = $db->Execute($stmt);
				
				dprint( "Processing " . count($results) . " Results ..." );
				
				// get all agencies
				$dict = $this->GetYearAllAgencyCounts();
			
				// pull the information from the row into the agency object
				$agency = new Agency();
				
				$agency->agencyid = $results[0]['agencyid'];
				$agency->shortname = $results[0]['shortname'];
				$agency->longname = $results[0]['longname'];
				$agency->type = $results[0]['type'];
				$agency->description = $results[0]['description'];
				$agency->websiteurl = $results[0]['websiteurl'];
				$id = $agency->agencyid;
				$agency->callcount = $dict[$id];
				
				// close our DB connection
				$db->Close($mysqli, $stmt);
			}
			catch (Exception $e)
			{
				dprint( "Caught exception: " . $e->getMessage() );
			}
			
			dprint("GetAgencyFromID() Done.");
			
			return $agency;
		}
		
		function GetAgencyFromShortName($agencyshortname)
		{
			dprint( "GetAgencyFromShortName() Start." );
			
			try
			{
		
				$db = new DatabaseTool();
			
				// create the query
				$query = 'SELECT * FROM agencies WHERE shortname = ?';
			
				$mysqli = $db->Connect();
				$stmt = $mysqli->prepare($query);
				$stmt->bind_param("s", $agencyshortname); // bind the varibale
				$results = $db->Execute($stmt);
				
				dprint( "Processing " . count($results) . " Results ..." );
		
				// get all agencies
				$dict = $this->GetYearAllAgencyCounts();
			
				// pull the information from the row into the agency object
				$agency = new Agency();
				
				$agency->agencyid = $results[0]['agencyid'];
				$agency->shortname = $results[0]['shortname'];
				$agency->longname = $results[0]['longname'];
				$agency->type = $results[0]['type'];
				$agency->description = $results[0]['description'];
				$agency->websiteurl = $results[0]['websiteurl'];
				$id = $agency->agencyid;
				$agency->callcount = $dict[$id];
				
				// close our DB connection
				$db->Close($mysqli, $stmt);
			}
			catch (Exception $e)
			{
				dprint( "Caught exception: " . $e->getMessage() );
			}
			
			dprint("GetAgencyFromShortName() Done.");
			
			// return our created object
			return $agency;
		}
		
		function GetAllAgencies()
		{
			dprint( "GetAllAgencies() Start." );
			
			try
			{
		
				$db = new DatabaseTool();
				
				// create the query
				$query = 'SELECT * FROM agencies ORDER BY shortname ASC';
			
				$mysqli = $db->Connect();
				$stmt = $mysqli->prepare($query);
				$results = $db->Execute($stmt);
				
				dprint( "Processing " . count($results) . " Results ..." );
			
				// get all agencies
				$dict = $this->GetYearAllAgencyCounts();
			
				$agencies = array();
				
				foreach($results as $result)
				{
				
					// pull the information from the row into the agency object
					$agency = new Agency();
					
					$agency->agencyid = $result['agencyid'];
					$agency->shortname = $result['shortname'];
					$agency->longname = $result['longname'];
					$agency->type = $result['type'];
					$agency->description = $result['description'];
					$agency->websiteurl = $result['websiteurl'];
					
					$id = $agency->agencyid;
					$agency->callcount = $dict[$id];
					
					$agencies[] = $agency;
				}
			
				// close our DB connection
				$db->Close($mysqli, $stmt);
			}
			catch (Exception $e)
			{
				dprint( "Caught exception: " . $e->getMessage() );
			}
			
			dprint("GetAllAgencies() Done.");
			
			// return our created object
			return $agencies;
		}
		
		function GetYearAllAgencyCounts()
		{
			dprint( "GetYearAllAgencyCounts() Start." );
			
			try
			{
		
				$db = new DatabaseTool();
				
				// create the query
				$query = 'SELECT agencyid, COUNT(DISTINCT itemid) AS count FROM incidents WHERE pubdate >= ? GROUP BY agencyid';
			
				$firstofyear = date("Y") . "-01-01";
			
				$mysqli = $db->Connect();
				$stmt = $mysqli->prepare($query);
				$stmt->bind_param("s", $firstofyear); // bind the varibale
				$results = $db->Execute($stmt);
				
				dprint( "Processing " . count($results) . " Results ..." );
			
				$dict = array();
			
				foreach($results as $result)
				{
					// insert value into dictionary
					$dict[$result['agencyid']] = $result['count'];
				}
			
				// close our DB connection
				$db->Close($mysqli, $stmt);
			}
			catch (Exception $e)
			{
				dprint( "Caught exception: " . $e->getMessage() );
			}
			
			dprint("GetYearAllAgencyCounts() Done.");
			
			// return our created object
			return $dict;
		}
		
		function GetTodayAllAgencyCounts()
		{
			dprint( "GetTodayAllAgencyCounts() Start." );
			
			try
			{
		
				$db = new DatabaseTool();
			
				// create the query
				$query = 'SELECT agencyid, COUNT(DISTINCT itemid) AS count FROM incidents WHERE pubdate = ? GROUP BY agencyid';
			
				$today = date("Y-m-d");
			
				$mysqli = $db->Connect();
				$stmt = $mysqli->prepare($query);
				$stmt->bind_param("s", $today); // bind the varibale
				$results = $db->Execute($stmt);
				
				dprint( "Processing " . count($results) . " Results ..." );
			
				$dict = array();
			
				foreach($results as $result)
				{
					// insert value into dictionary
					$dict[$result['agencyid']] = $result['count'];
				}
			
				// close our DB connection
				$db->Close($mysqli, $stmt);
			}
			catch (Exception $e)
			{
				dprint( "Caught exception: " . $e->getMessage() );
			}
			
			dprint("GetTodayAllAgencyCounts() Done.");
			
			// return our created object
			return $dict;
		}
		
		function ValidAgencyByShortName($agencyshortname)
		{
			dprint( "ValidAgencyByShortName() Start." );
			
			try
			{
		
				$db = new DatabaseTool();
			
				// create the query
				$query = 'SELECT COUNT(*) as count FROM agencies WHERE shortname = ?';
			
				$mysqli = $db->Connect();
				$stmt = $mysqli->prepare($query);
				$stmt->bind_param("s", $agencyshortname); // bind the varibale
				$results = $db->Execute($stmt);
				
				dprint( "Processing " . count($results) . " Results ..." );
		
				// see if the shortname exists
				if( $results[0]['count'] == "0" )
					$valid = false;
				else
					$valid = true;
			
				// close our DB connection
				$db->Close($mysqli, $stmt);
			}
			catch (Exception $e)
			{
				dprint( "Caught exception: " . $e->getMessage() );
			}
			
			dprint("ValidAgencyByShortName() Done.");
			
			// return our created object
			return $valid;
		}
	}

?>