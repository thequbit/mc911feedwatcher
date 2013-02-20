<?php
	require_once("_header.php");
?>

	<?php
	
		require_once("./tools/Database.class.php");
		require_once("./tools/Agency.class.php");

		$db = new Database();
		
		$agencies = $db->GetAllAgencies();


		echo '<h2>An Incomplete List of Agencies Keeping Monroe County Safe</h2>';
		echo '<br>';
		echo '<br>';

		echo "This list is generated based on the four letter code used by Monroe County, NY dispatch.  There are still codes that have not yet been decoded.";
		echo "In general, codes ending with <b>F are Fire</b>, codes ending in <b>P are Police</b>, and codes ending in <b>E are EMS/Ambulance.</b>";
		echo "<br><br>";
		
		echo '<p class="tab">';
		echo '<b>Thanks to John from Henrietta for helping to fill in some of the unknowns!</b>';
		echo '</p>';
	
		echo "<br>";
		echo "If you see an agency that you think you know what the code is, or don't see one that you think should be on the list, please contact Tim at <a href=\"mailto:twofiftyfivelabs@gmail.com\">twofiftyfivelabs@gmail.com</a><br><br>";

		echo '<br>';
		
		
		echo '<p class="tab">';
		
		echo '<table>';
		echo '<tr>';
		echo '<td><b><font size="2">911 Code</font></b></th>';
		echo '<th><font size="2">' . date("Y") . ' Call Count</font></th>';
		echo '<th><font size="2">Today Call Count</font></th>';
		echo '<td><b><font size="2">Agency</font</th>';
		echo '<td><b><font size="2">Info</font></b></th>';
		echo '</tr>';
		
		echo "<td width=\"120\"></td>\n";
		echo "<td width=\"120\"></td>\n";
		echo "<td width=\"160\"></td>\n";
		echo "<td width=\"400\"></td>\n";
		echo "<td width=\"100\"></td>\n";
		
		foreach($agencies as $agency)
		{
			/*
			echo '<div class="agencytype">';
			echo '<p class="tab">';
			echo $agency->longname . " ";
			echo '(<a href="viewagency.php?agency=' . $agency->shortname . '">info</a>)<br>';
			echo '</p>';
			echo '</div>';
			*/
			
			$todaycallcount = $db->GetTodaysIncidentCountByAgencyShortName($agency->shortname);
			
			//$todaycallcount = 0;
			
			echo "<tr>\n";
			echo '<td width="80"><font size="2">' . $agency->shortname . "</font></td>\n";
			echo '<td class="centeredcell" width="120"><font size="2">' . $agency->callcount . "</font></th>\n";
			echo '<td class="centeredcell" width="160"><font size="2">' . $todaycallcount . "</font></th>\n";
			echo "<td width=\"400\"><font size=\"2\">" . $agency->longname . "</font></td>\n";
			
			// if we have the information about the agency, then display the info link
			if( $agency->longname != "" )
			{
				echo "<td width=\"100\"><a href=\"viewagency.php?agency=" . $agency->shortname . "\">info</a></td>\n";
			}
			else
			{
				echo "<td width=\"100\"></td>\n";	
			}
			
			echo "</tr>\n";
		}
		
		
		echo '</table>';
		
		echo '</p>';
		
	?>

<?php
	require_once("_footer.php");
?>