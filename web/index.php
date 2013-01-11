<html>
	<title>Monroe County, NY 911 Feed API</title>
	
	<meta name="description" content="Monroe County, NY 911 Feed API">
	<meta name="keywords" content="Monroe, Monroe County, 911, Public Safty, Rochester, Feed">
	
	<style>
		<!--
		.tab { margin-left: 40px; }
		.tab2 { margin-left: 80px; }
		-->
	</style>
<head>
</head>
<body>

	<h3>Monroe County 911 Incident Feed API</h3>

	<br>
	This is the landing page for the Monroe County 911 Indident Feed API.<br>
	<br>
	
	<?php
	
		include_once("Database.class.php");
		
		// create a database tool to use to pull information from the database
		$db = new Database();
		
		//
		// System Stats
		//
		
		// get the total number of times the scraper has run
		$runCount = $db->GetNumberOfRuns();
		// get the total number of API calls that have been made
		$apicalls = $db->GetNumberOfAPICalls();
		// get the number of unique api users today
		$totalUniqueApiUsers = $db->GetTotalUniqueAPIUsers();
		// get the number of unique api users today
		$uniqueApiUsersToday = $db->GetUniqueAPIUsersToday();
		// get the average querytime today
		$averageQueryToday = $db->GetAverageQueryToday();
		
		// display the results from the database
		echo "<b><br>System Stats: </b><br>";
		echo '<p class="tab">';
		echo "Total scraper runs: " . $runCount . "<br>";
		echo "Total number of API calls: " . $apicalls . "<br>";
		echo "Total Unique API users: " . $totalUniqueApiUsers . "<br>";
		echo "Unique API Users Today: " . $uniqueApiUsersToday . "<br>";
		echo "Average Query Time Today: " . number_format($averageQueryToday,4) . " Seconds<br>";
		echo '</p>';
		
		
		//
		// Data Stats
		//
		
		// get the total number of unique entrees in the database
		$uniqueIncidents = $db->GetTotalUniqueIncidents();
		// get the total number of event types logged into the system
		$totalEventTypes = $db->GetTotalEventTypes();
		// get the total number of status types logged into the system
		$totalStatusTypes = $db->GetTotalStatusTypes();
			
		echo "<b>Data Stats: </b><br>";
		echo '<p class="tab">';
		echo "Total Unique Incidents: " . $uniqueIncidents . "<br>";
		echo "Total Incident Types: " . $totalEventTypes . "<br>";
		echo "Total Status Types: " . $totalStatusTypes . "<br>";
		echo '</p>';
		
	?>

	Want access to the API?  Well here it is!<br>
	Note: The API takes in two parameters: startdate and enddate, with enddate being optional (it will return all items up to todays date).<br>
	<br>
	Example API Call:<br>
	<p class="tab">
	<a href="http://monroe911.mycodespace.net/getapi.php?startdate=2013-1-1">http://monroe911.mycodespace.net/getapi.php?startdate=2013-1-1</a>
	</p>
	{<br>
	"apiversion": "1.0",<br>
	"errorcode": "0",<br>
	"errortext": "No errors reported.",<br>
	"querytime": "0.0031869411468506",<br>
	"resultcount": "244",<br>
	"results":<br>
		<p class="tab">
			[{<br>
			<p class="tab2">
				"event":"Parking complaint",<br>
				"address":"1200 BROOKS AV,Rochester",<br>
				"pubdate":"2013-01-09",<br>
				"pubtime":"23:37:00",<br>
				"status":"ONSCENE",<br>
				"incidentid":"MCOP130093564",<br>
				"scrapedatetime":"2013-01-09 23:45:00"<br>
			</p>
			<p class="tab">
			},<br>
			</p>
		</p>
		<p class="tab">
			{<br>
			<p class="tab2">
				"event":"Parking complaint",<br>
				"address":"67 BROOKFIELD RD, Rochester",<br>
				"pubdate":"2013-01-09",<br>
				"pubtime":"23:09:00",<br>
				"status":"WAITING",<br>
				"incidentid":"CTYP130093511",<br>
				"scrapedatetime":"2013-01-09 23:45:00"<br>
			</p>
			<p class="tab">
			},<br>
			</p>
		</p>
	}<br>

	<br>
	Check out the source code on Github <a href="https://github.com/thequbit/mc911feedwatcher">here</a>!

</body>
</html>