<?php
	require_once("_header.php");
?>

	<h2>An Incomplete List of Agencies Keeping Monroe County Safe</h2>
	<br>
	<br>

	This list is generated based on the four letter code used by Monroe County, NY dispatch.  There are still codes that have not yet been decoded.
	In general, codes ending with <b>F are Fire</b>, codes ending in <b>P are Police</b>, and codes ending in <b>E are EMS/Ambulance.</b>
	<br><br>
	
	<p class="tab">
	<b>Thanks to John from Henrietta for helping to fill in some of the unknowns!</b><br>
	<b>Thanks to Andy from DOT Traffic Operations center for helping to fill in last of the unknowns!</b>
	</p>

	<br>
	If you see an agency that you think you know what the code is, or don't see one that you think should be on the list, please contact Tim at <a href=\"mailto:twofiftyfivelabs@gmail.com\">twofiftyfivelabs@gmail.com</a><br><br>

	<br>		
	<p class="tab">
	
	<table>
	<tr>
	<td><b><font size="2">911 Code</font></b></th>
	<th><font size="2"><?php echo date("Y") ?> Call Count</font></th>
	<th><font size="2">Today Call Count</font></th>
	<td><b><font size="2">Agency</font</th>
	<td><b><font size="2">Info</font></b></th>
	</tr>
	
	<td width=\"120\"></td>
	<td width=\"120\"></td>
	<td width=\"160\"></td>
	<td width=\"400\"></td>
	<td width=\"100\"></td>

	<?php
	
		require_once("./tools/IncidentManager.class.php");
		require_once("./tools/AgencyManager.class.php");
		require_once("./tools/Agency.class.php");

		$agencyManager = new AgencyManager();
		$incidentManager = new IncidentManager();

		$agencies = $agencyManager->GetAllAgencies();
		$dailycountsdict = $agencyManager->GetTodayAllAgencyCounts();

		foreach($agencies as $agency)
		{
			// get todays date
			//$todaysdate = date("Y-m-d");
			
			// get the total number of calls for today for the agency
			//$todaycallcount = $incidentManager->GetIncidentCountByAgencyIDAndDate($agency->agencyid, $todaysdate);
			if( isset($dailycountsdict[$agency->agencyid]) )
			{
				$todaycallcount = $dailycountsdict[$agency->agencyid];
			}
			else
			{
				$todaycallcount = 0;
			}

			echo "<tr>\n";
			echo '<td width="80"><font size="2">' . $agency->shortname . "</font></td>\n";
			echo '<td class="centeredcell" width="120"><font size="2">' . $agency->callcount . "</font></th>\n";
			echo '<td class="centeredcell" width="160"><font size="2">' . $todaycallcount . "</font></th>\n";
			echo "<td width=\"400\"><font size=\"2\">" . $agency->longname . "</font></td>\n"; 
			echo "<td width=\"100\"><a href=\"viewagency.php?agency=" . $agency->shortname . "\">info</a></td>\n";
			echo "</tr>\n";
		}
		
		echo '</table>';
		
	?>
	
	</p>

<?php
	require_once("_footer.php");
?>