<?php

	require_once("Database.class.php");

	$db = new Database();

	$stats = $db->GetStats();

	echo $stats;

	//$json_stats = json_encode($stats);
	//echo $json_stats;

?>