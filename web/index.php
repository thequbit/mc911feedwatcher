<html>
	<title>Monroe County 911 Feed API Status</title>
<head>
</head>
<body>

	<?php
	
		include_once("Database.class.php");
		
		// create a database tool to use to pull information from the database
		$db = new Database();
		
		// get the total number of times the scraper has run
		$runCount = $db->GetNumberOfRuns();
		
		// get the total number of unique entrees in the database
		$uniqueEntrees = $db->GetTotalUniqueEntrees();
		
		// get the total number of API calls that have been made
		$apicalls = $db->GetNumberOfAPICalls();
		
		$results = $db->GetAllItems();
		
		// display the results from the database
		echo "Total scraper runs: " . $runCount . "<br>";
		echo "Total unique incidents: " . $uniqueEntrees . "<br>";
		echo "Total number of API calls: " . $apicalls . "<br>";
	?>

	<br>
	Want access to the API?  Well here it is!
	<a href="http://monroe911.mycodespace.net/getapi.php?startdate=1970-1-1">Web API</a><br>
	Note: The API takes in two parameters: startdate and enddate, with enddate being optional (it will return all items up to todays date).<br>
	Note: An example of running the API would look like this:<br>
	<br>
	http://monroe911.mycodespace.net/getapi.php?startdate=2013-1-1<br>
	<br>
	Here is an example of what the returned json looks like:<br>
	<br>
	[{<br>
	"event":"Parking complaint",<br>
	"address":"1200 BROOKS AV, Rochester",<br>
	"pubdate":"2013-01-09",<br>
	"pudtime":"23:37:00",<br>
	"status":"ONSCENE",<br>
	"incidentid":"MCOP130093564",<br>
	"scrapedatetime":"Parking complaint"<br>
	}]<br>
	<br>
	Note: if you don't pass in at least a startdate, you will get this:<br>
	<br>
	{<br>
	"error":"You must provide at least a start date"<br>
	}<br>
	<br>
	Enjoy!<br>

</body>
</html>