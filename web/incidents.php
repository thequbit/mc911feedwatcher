<?php
	require_once("_header.php");
?>
	
	<?php

		//
		// Sanity Check Inputs
		//
		
		require_once("./tools/UtilityManager.class.php");
		
		$util = new UtilityManager();
	
		$date = $_GET["date"];
		
		// check for none-case ... we handle as the current date later in code
		if( $date != "" )
		{
		
			// check that the date is valid
			if( $util->IsValidDate($date) == 0 || $util->IsValidDate($date) == False )
			{
				// not a valid date

				echo '<script>';
				echo 'window.location = "./index.php"';
				echo '</script>';
			}
			
		}
		
	?>
	
	<?php
	
		//require_once("./tools/Database.class.php");
		
		require_once("./tools/IncidentManager.class.php");
		require_once("./tools/Incident.class.php");
		require_once("./tools/AgencyManager.class.php");
		require_once("./tools/Agency.class.php");
		
		//require_once("./tools/Time.class.php");
	
		//$time = new Time();

		// record start time
		//$starttime = $time->StartTime();
	
		// get the posted data variable
		$date = $_GET['date'];

		if( $date == "" )
		{
			$date = date("Y-m-d");
		}
		
		// calculate tomorrow
		$tomorrowtime = strtotime ('+1 day', strtotime($date)) ;
		$tommorrow = date('Y-m-d', $tomorrowtime);
		
		// calculate yesterday
		$yesterdaytime = strtotime ('-1 day', strtotime($date)) ;
		$yesterday = date('Y-m-d', $yesterdaytime);
	
		// create an instance of the database
		//$db = new Database();

		// get all of the incidents for the day
		//$incidents = $db->GetIncidentsByDay($date);
	
		// get all of the incidents for the date passed in by the user
		$incidentManager = new IncidentManager();
		$incidents = $incidentManager->GetIncidentsByDay($date);
	
		// to handle all agency related querys
		$agencyManager = new AgencyManager();
	
		// display links to go to previous day and next day
		
		echo '<div class="yesterdaylink">';
		echo '<a href="incidents.php?date=' . $yesterday . '">Incidents for ' . date("l F j, Y",strtotime($yesterday)) . '</a>';
		echo '</div>';
		
		if( $date != date("Y-m-d") )
		{
			echo '<div class="tomorrowlink">';
			echo '<a href="incidents.php?date=' . $tommorrow . '">Incidents for ' . date("l F j, Y",strtotime($tommorrow)) . '</a>';
			echo '</div>';				
		}

		echo '<br><br>';

		echo '<div>';

		echo '<br>';

		echo '<center><h2>Incidents for ' . date("l F j, Y",strtotime($date)) . '</h2></center>';

		echo '<center>';
		echo '<br>';
		echo '<a href="stats.php?date=' . $date . '">See Stats For ' . date("l F j, Y",strtotime($date)) . '</a>';
		echo '</center>';
	
		echo '</div>';
	
		echo '<div>';
	
		//echo '';
	
		if( count($incidents) == 0 )
		{
			echo "<br>";
			echo "<h3>No incidents were found for day: " . $date . "</h3>";
			echo "<br>";
		}
		else
		{
			echo"<br><br>";
			echo "Total number of incidents today:<b>" . count($incidents) . "</b><br><br>";
		
			echo '<div class="incidents">';
			echo '<table>';
			echo '<tr>';
			echo '<td><b><font size="4">Time</font></b></th>';
			echo '<td><b><font size="4">Event</font></b></th>';
			echo '<td><b><font size="4">Address</font></b></th>';
			echo '<td><b><font size="4">Responding Agency</font></b></th>';
			echo '<td><b><font size="4">Event ID</font></b></th>';
			echo '</tr>';
		
			// generate dictionaries so we don't have to query the DB every time.
			$longNameDict = $agencyManager->GetAgencyLongNameDictionary();
			$shortNameDict = $agencyManager->GetAgencyShortNameDictionary();
		
			// print the events to the page
			foreach($incidents as $incident)
			{
				
				// print out the row
				echo '<tr>';
				echo '<td width="100">' . $incident->pubtime . '</td>';
				echo '<td width="400">' . $incident->event . '</td>';
				echo '<td width="300">' . $incident->address . '</td>';
				echo '<td width="250"><a href="viewagency.php?agency=' . $shortNameDict[$incident->agencyid] . '">' . $longNameDict[$incident->agencyid] . '</a></td>';
				echo '<td width="100">' . $incident->itemid . '</td>';
				echo '</tr>';
			}
		
			echo '</table>';
			echo '</div>';
		}
		
		echo '</div>';
		
		// calculate time taken
		//$totaltime = $time->TotalTime($starttime);
		
		// record the API call in the database as hash of IP
		//$ipaddress = md5($_SERVER['HTTP_X_FORWARDED_FOR']);
		//$db->AddAPICall($ipaddress, $todaysDate, $totaltime, "INCIDENTS");
		
	?>	
			
<?php
	require_once("_footer.php");
?>