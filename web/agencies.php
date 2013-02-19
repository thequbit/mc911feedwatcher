<?php
	require_once("_header.php");
?>

	<h2>An Incomplete List of Agencies Keeping Monroe County Safe</h2>
	<br>
	<br>

	<?php
	
		require_once("./tools/Database.class.php");
		require_once("./tools/Agency.class.php");

		$db = new Database();
		
		$agencies = $db->GetAllAgencies();

		echo "<b>This list is generated based on the four letter code used by Monroe County, NY dispatch.  There are still codes that have not yet been decoded.";
		echo "If you don't see an agency that you think should be on the list, please contact Tim at <a href=\"mailto:twofiftyfivelabs@gmail.com\">twofiftyfivelabs@gmail.com</a></b>";
		echo "<br><br>";
		
		echo '<p class="tab">';
		
		echo '<table>';
		echo '<tr>';
		echo '<td><b><font size="3">911 Code</font></b></th>';
		echo '<td><b><font size="3">' . date("Y") . ' Calls</font></b></th>';
		echo '<td><b><font size="3">Agency</font></b></th>';
		echo '<td><b><font size="3">Info</font></b></th>';
		echo '</tr>';
		
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
			
			echo "<tr>\n";
			echo "<td width=\"100\"><font size=\"2\">" . $agency->shortname . "</font></td>\n";
			echo "<td width=\"100\"><font size=\"2\">" . $agency->callcount . "</font></td>\n";
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