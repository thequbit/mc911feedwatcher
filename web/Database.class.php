<?php

	require_once("sqlcredentials.php");
	require_once("Item.class.php");
	
	class Database
	{
		
		function AddAPICall($ipaddress, $startDate, $endDate, $callDateTime)
		{
			// connect to the database
			$this->Connect();
			
			// create the query
			$query = 'INSERT INTO apicalls (ipaddress, startdate, enddate, calldatetime) VALUES("' . $ipaddress . '", "' . $startDate . '", "' . $endDate . '", "' . $callDateTime . '")';
			
			// execute the query
			$results = $this->Query($query);
		}
		
		function GetAllItems()
		{
			// connect to the database
			$this->Connect();
			
			// create the query
			$query = "SELECT * FROM incidents";
			
			// execute the query
			$results = $this->Query($query);
			
			$retVal = array();
			
			// decode the rows
			while($r = mysql_fetch_assoc($results)) {
			
				// create a temp object to populate
				$item = new Item();
			
				// assign the values 
				$item->event = $r['event'];
				$item->address = $r['address'];
				$item->pubdate = $r['pubdate'];
				$item->pudtime = $r['pubtime'];
				$item->status = $r['status'];
				$item->incidentid = $r['itemid'];
				$item->scrapedatetime = $r['event'];
		
				// add the item to the array of items
				$retVal[] = $item;
			}
			
			// return the count
			return $retVal; 
		}
		
		function GetItems($startDate, $endDate)
		{
			// connect to the database
			$this->Connect();
			
			// figure out if we need to use the end date or not
			if( $endDate == "" )
			{
				// create the query
				$query = 'SELECT * FROM incidents where pubdate>="' . $startDate . '"';
			}
			else
			{
				// create the query
				$query = 'SELECT * FROM incidents where pubdate>="' . $startDate . '" AND pubdate<="' . $endDate .'"';
			}
			// execute the query
			$results = $this->Query($query);
			
			$retVal = array();
			
			// decode the rows
			while($r = mysql_fetch_assoc($results)) {
			
				// create a temp object to populate
				$item = new Item();
			
				// assign the values 
				$item->event = $r['event'];
				$item->address = $r['address'];
				$item->pubdate = $r['pubdate'];
				$item->pubtime = $r['pubtime'];
				$item->status = $r['status'];
				$item->incidentid = $r['itemid'];
				$item->scrapedatetime = $r['scrapedatetime'];
		
				// add the item to the array of items
				$retVal[] = $item;
			}
			
			// return the count
			return $retVal; 
		}
		
		function GetNumberOfRuns()
		{
			// connect to the database
			$this->Connect();
			
			// create the query
			$query = "SELECT count(*) FROM runs";
			
			// execute the query
			$results = $this->Query($query);
			
			// get the row
			$r = mysql_fetch_assoc($results);
			
			// return the count
			return $r["count(*)"]; 
		}
		
		function GetNumberOfAPICalls()
		{
			// connect to the database
			$this->Connect();
			
			// create the query
			$query = "SELECT count(*) FROM apicalls";
			
			// execute the query
			$results = $this->Query($query);
			
			// get the row
			$r = mysql_fetch_assoc($results);
			
			// return the count
			return $r["count(*)"]; 
		}
		
		function GetTotalUniqueEntrees()
		{
			// connect to the database
			$chandle = $this->Connect();
			
			// create the query
			$query = "SELECT count(*) FROM incidents";
			
			// execute the query
			$results = $this->Query($query);
			
			// get the row
			$r = mysql_fetch_assoc($results);
			
			// return the count
			return $r["count(*)"];
		}
		
		function Connect()
		{
			// connect to the mysql database server.  Constants taken from sqlcredentials.php
			$chandle = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS)
				or die("Connection Failure to Database");				// TODO: something more elegant than this

			mysql_select_db(MYSQL_DATABASE, $chandle)
				or die ("Database not found (" . MYSQL_DATABASE . ")");	// TODO: something more elegant than this

			return $chandle;
		}
		
		function Query($query)
		{
			// pull from DB
			$result = mysql_db_query(MYSQL_DATABASE, $query)
				or die("Failed Query of " . $query);  			// TODO: something more elegant than this
			
			return $result;
		}
		
	}

?>