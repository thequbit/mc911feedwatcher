<?php

	require_once("Database.class.php");

	$eventtypeid = $_GET['eventtypeid'];
	$startdate = $_GET['startdate'];

	$db = new Database();

	$results = $db->GetItemsByEventID($eventtypeid, $startdate);

	$json_results = json_encode($results);

	echo $json_results;

?>