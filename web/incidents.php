<html>
	<title>Monroe County, NY 911 Feed Collator</title>
	
	<meta name="description" content="Monroe County, NY 911 Feed Collator">
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
				<h2>Monroe County, NY 911 Feed Collator</h2>
				<br>
			</div>
			
		</div>
	
		<div class="navwrapper">
		
			<div class="nav">
					
				<div class="navlink">
					<a href="index.php">home</a>
				</div>
				<div class="navlink">
					<a href="status.php">status</a>
				</div>
				<div class="navlink">
					<a href="stats.php">stats</a>
				</div>
				<div class="navlink">
					<a href="incidents.php">incidents</a>
				</div>
				<div class="navlink">
					<a href="events.php">events</a>
				</div>
				<div class="navlink">
					<a href="developers.php">developers</a>
				</div>
				<div class="navlink">
					<a href="about.php">about</a>
				</div>
				
			</div>
		
		</div>
	
		<div class="contentwrapper">

			<div class="content">
			
			
				
					<?php
					
						require_once("./tools/Database.class.php");
						require_once("./tools/Time.class.php");
					
						$time = new Time();
	
						// record start time
						$starttime = $time->StartTime();
					
						// get the posted data variable
						$date = $_GET['date'];
		
						if( $date == "" )
						{
							$date = date("Y-m-d");
						}
						
						// calculate tomorrow
						$tomorrowtime = strtotime ('+1 day', strtotime($date)) ;
						$tommorrow = date('Y-m-d', $tomorrowtime);
						
						// calculate yesterday
						$yesterdaytime = strtotime ('-1 day', strtotime($date)) ;
						$yesterday = date('Y-m-d', $yesterdaytime);
		
						// create an instance of the database
						$db = new Database();
	
						// get all of the incidents for the day
						$incidents = $db->GetIncidentsByDay($date);
					
						// display links to go to previous day and next day
						
						
						
						echo '<div class="yesterdaylink">';
						echo '<a href="incidents.php?date=' . $yesterday . '">See Incidents for ' . $yesterday . '</a>';
						echo '</div>';
						
						echo '<div class="tomorrowlink">';
						echo '<a href="incidents.php?date=' . $tommorrow . '">See Incidents for ' . $tommorrow . '</a>';
						echo '</div>';
					
						echo '<div>';
					
						echo '<br><br>';
					
						if( count($incidents) == 0 )
						{
							echo "<br>";
							echo "<h3>No incidents were found for day: " . $date . "</h3>";
							echo "<br>";
						}
						else
						{
						
							echo "<br>";
							echo "Incidents for <b>" . $date . "</b>";
							echo"<br><br>";
							echo "Total number of incidents today:<b>" . count($incidents) . "</b><br><br>";
						
							echo '<div class="incidents">';
							echo '<table>';
							echo '<tr>';
							echo '<td><b><font size="4">Time</font></b></th>';
							echo '<td><b><font size="4">Event</font></b></th>';
							echo '<td><b><font size="4">Address</font></b></th>';
							echo '<td><b><font size="4">Event ID</font></b></th>';
							echo '</tr>';
						
							// print the events to the page
							foreach($incidents as $incident)
							{
								
								echo '<tr>';
								echo '<td width="100">' . $incident->pubtime . '</td>';
								echo '<td width="400">' . $incident->event . '</td>';
								echo '<td width="250">' . $incident->address . '</td>';
								echo '<td width="100">' . $incident->itemid . '</td>';
								echo '</tr>';
							}
						
							echo '</table>';
							echo '</div>';
						}
						
						echo '</div>';
						
						// calculate time taken
						$totaltime = $time->TotalTime($starttime);
						
						// record the API call in the database as hash of IP
						$ipaddress = md5($_SERVER['HTTP_X_FORWARDED_FOR']);
						$db->AddAPICall($ipaddress, $todaysDate, $totaltime, "INCIDENTS");
						
					?>
				
					<br>
				
				
			
			</div>

		</div>

		<div class="footerwrapper">
		
			<div class="footer">
			
				Copyright 2013 | Two Fifty-Five Labs, LLC | West Henrietta, NY | <a href="https://github.com/thequbit/mc911feedwatcher">Source Code</a>
			
			</div>
		
		</div>
	
	</div>

</body>
</html>