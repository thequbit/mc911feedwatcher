<?php

	require_once("DatabaseTool.class.php");
	require_once("Incident.class.php");
	
	class LocationManager
	{
		function GetLocationsByDay($date, $agencyShortName)
		{
			dprint( "GetLocationsByDay() Start." );
			
			try
			{
		
				$db = new DatabaseTool();
			
				if( $date == "" )
					$date = date("Y-m-d");
			
				
                if ( $agencyShortName == "" ) 
                {
                
                    // create the query
                    $query = 'SELECT DISTINCT itemid,event,fulladdress,lat,lng,pubdate,pubtime,agencies.longname AS agencyname FROM incidents JOIN agencies ON incidents.agencyid = agencies.agencyid WHERE pubdate = ? AND lat <> "" AND lng <> "" GROUP BY itemid ORDER BY pubtime DESC';
                
                    $mysqli = $db->Connect();
                    $stmt = $mysqli->prepare($query);
                    $stmt->bind_param("s", $date); // bind the varibale
                    
                }
                else
                {
                
                    $agencyManager = new AgencyManager();
                    $agency = $agencyManager->GetAgencyFromShortName($agencyShortName);
                
                    // create the query
                    $query = 'SELECT DISTINCT itemid,event,fulladdress,lat,lng,pubdate,pubtime,agencies.longname AS agencyname FROM incidents JOIN agencies ON incidents.agencyid = agencies.agencyid WHERE incidents.agencyid = ? AND pubdate = ? AND lat <> "" AND lng <> "" GROUP BY itemid ORDER BY pubtime DESC';
                
                    $mysqli = $db->Connect();
                    $stmt = $mysqli->prepare($query);
                    $stmt->bind_param("is", $agency->agencyid, $date); // bind the varibale
                }
                
				$results = $db->Execute($stmt);
			
				// create an array to put our results into
				$incidents = array();
				
				// decode the rows
				foreach( $results as $result )
				{
					$incident = (object) array(
													'itemid' => $result['itemid'],
													'event' => $result['event'],
													'fulladdress' => $result['fulladdress'],
													'lat' => $result['lat'],
													'lng' => $result['lng'],
                                                    'publishdate' => $result['pubdate'],
                                                    'publishtime' => $result['pubtime'],
                                                    'agencyname' => $result['agencyname']
												  );
					
					$incidents[] = $incident;
				}
			
				// close our DB connection
				$db->Close($mysqli, $stmt);
			
			}
			catch (Exception $e)
			{
				dprint( "Caught exception: " . $e->getMessage() );
			}
			
			dprint("GetLocationsByDay() Done.");
			
			return $incidents;
		}
		
		function GetLocationsByDayByType($date,$type)
		{
			dprint( "GetLocationsByDayByType() Start." );
			
			try
			{
		
				$db = new DatabaseTool();
			
				if( $date == "" )
					$date = date("Y-m-d");
			
				// create the query
				$query = 'SELECT DISTINCT itemid,event,fulladdress,lat,lng FROM incidents JOIN eventtypes ON incidents.event = eventtypes.eventtype WHERE pubdate = ? AND lat <> "" AND lng <> "" AND eventtypes.eventtypeid = ? GROUP BY itemid ORDER BY pubtime DESC;';
			
				$mysqli = $db->Connect();
				$stmt = $mysqli->prepare($query);
				$stmt->bind_param("ss", $date,$type); // bind the varibale
				$results = $db->Execute($stmt);
			
				// create an array to put our results into
				$incidents = array();
				
				// decode the rows
				foreach( $results as $result )
				{
					$incident = (object) array(
													'itemid' => $result['itemid'],
													'incident' => $result['event'], // renamed for consistancy in web interface
													'fulladdress' => $result['fulladdress'],
													'lat' => $result['lat'],
													'lng' => $result['lng']
												  );
					
					$incidents[] = $incident;
				}
			
				// close our DB connection
				$db->Close($mysqli, $stmt);
			
			}
			catch (Exception $e)
			{
				dprint( "Caught exception: " . $e->getMessage() );
			}
			
			dprint("GetLocationsByDayByType() Done.");
			
			return $incidents;
		}
	}

?>