<?php
	require_once("_header.php");
?>

			
				<br>
				<h3>Different Event Types Used by Monroe County, NY Dispatch</h3>
				<br>
				
				There are lots of different types of incidents used by Monroe Count, NY 911 dispatch.  The list below is the currently used types by dispatch.<br>
				<br>
			
				<?
				
					require_once("tools/Database.class.php");
					require_once("tools/EventType.class.php");

					$db = new Database();

					$eventtypes = $db->GetEventTypes();

					//$eventtype->eventtype

					foreach($eventtypes as $eventtype)
					{
						echo '<div class="eventtype">';
						echo '<p class="tab">';
						echo $eventtype->eventtype . " ";
						//echo '(<a href="http://monroe911.mycodespace.net/hourly.php?eventtypeid=' . $eventtype->eventtypeid . '&period=today">today</a>) ';
						//echo '(<a href="http://monroe911.mycodespace.net/hourly.php?eventtypeid=' . $eventtype->eventtypeid . '&period=week">week</a>) ';
						//echo '(<a href="http://monroe911.mycodespace.net/hourly.php?eventtypeid=' . $eventtype->eventtypeid . '&period=month">month</a>)';
						echo '(<a href="http://monroe911.mycodespace.net/alltimehourly.php?eventtypeid=' . $eventtype->eventtypeid . '&period=alltime">all-time by hour</a>)<br>';
						echo '</p>';
						echo '</div>';
					}
				
				?>
			
				<br>
				Note: this list is dynamicly added too as new types are seen by the web scrapers.  For more information how how this data is created, check out the 
				<a href="about.php">about</a> page and the <a href="developers.php">developers</a> page.<br>
				<br>
				
			
<?php
	require_once("_footer.php");
?>