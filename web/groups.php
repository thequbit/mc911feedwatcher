<?php
	require_once("_header.php");
?>

			
				<h3>Incident Type Groups</h3>
			
				<br>
				<br>
				There are a number of different types of incidents reported on the 911 feed, all of which can be found <a href="incidents.php">here</a>.  You may notice that there 
				are several incident types that are similar to each other, such as Motor Vehicle Accidents.  Below are a list of different groups that incident types have been grouped into.
				<br>
				<br>
				<p class="tab">
				
					<?php
					
						require_once("./tools/Database.class.php");
						require_once("./tools/Group.class.php");
					
						$db = new Database();
						
						$groups = $db->GetAllGroups();
						
						//echo count($groups);
						
						foreach($groups as $group)
						{
							//echo '<a href="viewgroup.php?groupid=' . $group->id . '">' . $group->name . '</a> - ' . $group->description . '<br>';
							echo "<b>" . $group->name . "</b> - " . $group->description . "<br>";
						}
						
					?>
					
				</p>
				
				<br>
				<br>
			
<?php
	require_once("_footer.php");
?>