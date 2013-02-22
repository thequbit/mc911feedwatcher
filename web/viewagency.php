<?php
	require_once("_header.php");
?>
	
	<?php

		//
		// Sanity Check Inputs
		//
		
		require_once("./tools/UtilityManager.class.php");
		
		$util = new UtilityManager();
	
		$shortname = $_GET["agency"];
		
		// check that the the short name is valid
		if( $util->IsValidAgenyShortName($shortname) == False )
		{
			// not a valid short name

			echo '<script>';
			echo 'window.location = "./index.php"';
			echo '</script>';
		}
		
	?>


	<?php
	
		require_once("./tools/AgencyManager.class.php");
		require_once("./tools/Agency.class.php");
		require_once("./tools/IncidentManager.class.php");
		require_once("./tools/Incident.class.php");

		// get the agency we are viewing
		$agencyshortname = $_GET['agency']; // TODO: sanity check this
	
		// create an instance of our agency manager object
		$agencyManager = new AgencyManager();
	
		// create an instance of our incident manager
		$incidentManager = new IncidentManager();
	
		// get agency information using the shortname passed in by the user
		$agency = $agencyManager->GetAgencyFromShortName($agencyshortname);
	
		// get the last 25 incidents this agency has seen
		$incidents = $incidentManager->GetIncidentsByAgencyID($agency->agencyid, 25); // note: 25 here is the number of incidents to return
	
		// do some decode if we haven't decoded the 4 letter code yet
		//if( $agency->longname == "" )
		//	$agency->longname = "- unknown -";
	
		// get the current year
		$year = date("Y");
	
		// get todays date for later use
		$todaysdate = date("Y-m-d");
	
		// get the total number of calls for todays date
		$todaystotalcalls = $incidentManager->GetIncidentCountByAgencyIDAndDate($agency->agencyid, $todaysdate);
	
		// get the total number of calls for this year for this agency
		//$totalcalls = $agencyManager->GetTotalIncidentsByAgencyID($agency->agencyid, $year);
	
		echo '<A HREF="javascript:history.back()">< Back</a><br><br>';
	
		echo '<h2>' . $agency->longname . '</h2>';
		echo '<br><br>';
		echo 'Calls for Today: <b>' . $todaystotalcalls . '</b><br>';
		echo 'Calls for ' . $year . ': <b>' . $agency->callcount . '</b><br>';
		echo 'Monroe County 911 Code: <b>' . $agency->shortname . '</b><br>';
		echo 'Organization Name: <b>' . $agency->longname . '</b><br>';
		echo 'Agency Website: <b><a href="' . $agency->websiteurl . '">' . $agency->websiteurl . '</a></b><br>';
		
		echo '<br>';
		echo '<br>';
		echo "The last " . count($incidents) . " incidents for this agency:<br>"; // use the count in case less than 25 come back
		
		echo '<div>';
	
		// check to make sure there are incidents to display
		if( count($incidents) == 0 )
		{
			echo "<br>";
			echo "<br>";
			echo "<h3> - No incidents were found for this agency - " . $date . "</h3>";
			echo "<br>";
			echo "<br>";
		}
		else
		{
			echo '<br>';
		
			echo '<div class="incidents">';
			echo '<table>';
			echo '<tr>';
			echo '<td><b><font size="4">Date</font></b></th>';
			echo '<td><b><font size="4">Time</font></b></th>';
			echo '<td><b><font size="4">Event</font></b></th>';
			echo '<td><b><font size="4">Address</font></b></th>';
			echo '<td><b><font size="4">Event ID</font></b></th>';
			echo '</tr>';
		
			// print the events to the page
			foreach($incidents as $incident)
			{
				echo '<tr>';
				echo '<td width="80">' . $incident->pubdate . '</td>';
				echo '<td width="80">' . $incident->pubtime . '</td>';
				echo '<td width="380">' . $incident->event . '</td>';
				echo '<td width="250">' . $incident->address . '</td>';
				echo '<td width="70">' . $incident->itemid . '</td>';
				echo '</tr>';
			}
		
			echo '</table>';
			echo '</div>';
		}
		
		echo '</div>';
	
	?>

<?php
	require_once("_footer.php");
?>