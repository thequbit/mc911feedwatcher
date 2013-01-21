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
	
		<div class="topwrapper">

			<div class="content">

				<div class="incidents">
				
					<?php
					
						require_once("./tools/Database.class.php");
					
						// get the posted data variable
						$date = $_GET['date'];
		
						if( $date == "" )
						{
							$date = date("Y-m-d");
						}
		
						// create an instance of the database
						$db = new Database();
	
						// get all of the incidents for the day
						$incidents = $db->GetIncidentsByDay($date);
					
						if( count($incidents) == 0 )
						{
							echo "<br>";
							echo "No incidents were found for day: " . $date;
							echo "<br>";
						}
						else
						{
						
							echo "<br>Incidents for " . $date . ": <b>" . count($incidents) . "</b><br><br>";
						
							echo '<div class="incident">';
							echo '<table>';
							echo '<tr>';
							echo '<td><font size="3" face="sans-serif"><b>Event</b></font></th>';
							echo '<td><font size="3" face="sans-serif"><b>Address</b></font></th>';
							echo '<td><font size="3" face="sans-serif"><b>Time</b></font></th>';
							echo '<td><font size="3" face="sans-serif"><b>Event ID</b></font></th>';
							echo '</tr>';
						
							// print the events to the page
							foreach($incidents as $incident)
							{
								
								echo '<tr>';
								echo '<td width="100"><font size="1" face="sans-serif">' . $incident->pubtime . '</font></td>';
								echo '<td width="400"><font size="1" face="sans-serif">' . $incident->event . '</font></td>';
								echo '<td width="250"><font size="1" face="sans-serif">' . $incident->address . '</font></td>';
								echo '<td width="100"><font size="1" face="sans-serif">' . $incident->itemid . '</font></td>';
								echo '</tr>';
							}
						
							echo '</table>';
							echo '</div>';
						
						}
						
						// record the API call in the database
						$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
						$db->AddAPICall($ipaddress, $todaysDate, $totaltime, "INCIDENTS");
						
					?>
				
					<br>
				
				</div>
				
			</div>

		</div>
	
	</div>

</body>
</html>