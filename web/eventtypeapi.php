<?php

	require_once("Database.class.php");
	require_once("EventType.class.php");

	$db = new Database();

	$eventtypes = $db->GetEventTypes();

	$results = "{";

	foreach($eventtypes as $eventtype)
	{
		$results = $results . '"' . $eventtype->eventtypeid . '": "' . $eventtype->eventtype . '",';
	}

	$results = $results . "}";

	echo $results;

?>