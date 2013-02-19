<?php
	require_once("_header.php");
?>

	<?php
		
		require_once("./tools/Database.class.php");
		require_once("./tools/Incident.class.php");
		require_once("./tools/Agency.class.php");
	
		// create an instance of our database helper class
		$db = new Database();
	
		// get the agency we are viewing
		$agencyshortname = $_GET['agency']; // TODO: sanity check this
	
		// get the last 25 incidents this agency has seen
		$incidents = $db->GetIncidentsByAgencyShortName($agencyshortname, 25); // note: 25 here is the number of incidents to return
	
		// pull all of the agency information based on the passed in short name
		$agency = $db->GetAgencyFromShortName($agencyshortname);
	
		$date = date("Y");
	
		// get the total number of calls for this year for this agency
		$totalcalls = $db->GetTotalIncidentsByAgencyShortName($agencyshortname,$date);
	
		echo '<A HREF="javascript:history.back()">< Back</a><br><br>';
	
		echo '<h2>' . $agency->longname . '</h2>';
		echo '<br><br>';
		echo 'Organization Name: <b>' . $agency->longname . '</b><br>';
		echo 'Agency Website: <b><a href="' . $agency->websiteurl . '">' . $agency->websiteurl . '</a></b><br>';
		echo 'Monroe County 911 Code: <b>' . $agency->shortname . '</b><br>';
		echo 'Calls for ' . $date . ': <b>' . $totalcalls . '</b><br>';
		echo '<br>';
		echo '<br>';
		echo "The last " . count($incidents) . " incidents for this agency:<br>"; // use the count incase less than 25 come back
		
		echo '<div>';
	
		// check to make sure there are incidents to display
		if( count($incidents) == 0 )
		{
			echo "<br>";
			echo "<br>";
			echo "<h3>No incidents were found for this agency: " . $date . "</h3>";
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