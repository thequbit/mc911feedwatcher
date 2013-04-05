<?php
	require_once("_header.php");
?>

	<br>
	<h3>Different Event Types Used by Monroe County, NY Dispatch</h3>
	<br>
	
	There are a lot of different types of incidents used by Monroe Count, NY 911 dispatch.  The list below is the currently used types by dispatch.<br>
	<br>

	<?
	
		require_once("./tools/EventManager.class.php");
		require_once("./tools/EventType.class.php");
		
		// create an instance of our event manager object
		$eventManager = new EventManager();
		
		// get a list of all of the event types
		$eventtypes = $eventManager->GetEventTypes();

		// print all of the event types with a link to "all time by hour" page
		foreach($eventtypes as $eventtype)
		{
			echo '<div class="eventtype">';
			echo '<p class="tab">';
			echo $eventtype->eventtype . " ";
			echo '(<a href="alltimehourly.php?eventtypeid=' . $eventtype->eventtypeid . '">all-time by hour</a>)<br>';
			echo '</p>';
			echo '</div>';
		}
	
	?>

	<br>
	Note: this list is dynamically added to as new types are seen by the web scrapers.  For more information how how this data is created, check out the 
	<a href="about.php">about</a> page and the <a href="developers.php">developers</a> page.<br>
	<br>
	
<?php
	require_once("_footer.php");
?>