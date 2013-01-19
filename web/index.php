<html>
	<title>Monroe County, NY 911 Feed API</title>
	
	<meta name="description" content="Monroe County, NY 911 Feed API">
	<meta name="keywords" content="Monroe, Monroe County, 911, Public Safty, Rochester, Feed, API, Application Programming Interface, Application, Programming, Interface, FOSS, Open Source, Open Data, Open, Source, Data">
	
	<link rel="shortcut icon" href="media/favicon.png" type="image/x-icon" />
	
	<link href="css/main.css" rel="stylesheet" type="text/css">
	
<head>
</head>
<body>

	<div class="top">
	
		<div class="headerwrapper">

			<div class="header">
				<br>
				<h2>Monroe County 911 Incident Feed API</h2>
				<br>
			</div>
			
		</div>
	
		<div class="navwrapper">
		
			<div class="nav">
					<!--
					<a href="index.php">HOME</a> | 
					<a href="stats.php">STATS</a> | 
					<a href="system.php">SYSTEM</a> | 
					<a href="developers.php">DEVELOPERS</a> | 
					<a href="about.php">ABOUT</a>
					-->
			</div>
		
		</div>
	
		<div class="topwrapper">

			<div class="content">

				<?php
				
					include_once("tools/Database.class.php");
					
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

				<p class="tab">
					<a href="stats.php">See Today's Stats</a><br>
				</p>
				
				<h4>See day-by-day occurrence rates for each event type</h4>

				<p class="tab">
				
					<?
					
						require_once("tools/Database.class.php");
						require_once("tools/EventType.class.php");

						$db = new Database();

						$eventtypes = $db->GetEventTypes();

						//$eventtype->eventtype

						foreach($eventtypes as $eventtype)
						{
							echo $eventtype->eventtype . " ";
							echo '(<a href="http://monroe911.mycodespace.net/visdata.php?eventtypeid=' . $eventtype->eventtypeid . '&period=today">today</a>) ';
							echo '(<a href="http://monroe911.mycodespace.net/visdata.php?eventtypeid=' . $eventtype->eventtypeid . '&period=week">week</a>) ';
							echo '(<a href="http://monroe911.mycodespace.net/visdata.php?eventtypeid=' . $eventtype->eventtypeid . '&period=month">month</a>)<br>';
						}
					
					?>
				
				</p>

				<h4>Developers</h4>

				<p class="tab">
					Want access to the API?  Check out the documentation and code on Github <a href="https://github.com/thequbit/mc911feedwatcher">here</a>!<br>
					<br>
				</p>

			</div>

		</div>
	
	</div>

</body>
</html>