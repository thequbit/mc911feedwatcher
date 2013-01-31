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
			
				<br>
				<h3>Different Event Types Used by Monroe County, NY Dispatch</h3>
				<br>
				
				There are lots of different types of incidents used by Monroe Count, NY 911 dispatch.  The list below is the currently used types by dispatch.<br>
				<br>
			
				<?
				
					require_once("tools/Database.class.php");
					require_once("tools/EventType.class.php");

					$db = new Database();

					$eventtypes = $db->GetEventTypes();

					//$eventtype->eventtype

					foreach($eventtypes as $eventtype)
					{
						echo '<div class="eventtype">';
						echo '<p class="tab">';
						echo $eventtype->eventtype . " ";
						//echo '(<a href="http://monroe911.mycodespace.net/hourly.php?eventtypeid=' . $eventtype->eventtypeid . '&period=today">today</a>) ';
						//echo '(<a href="http://monroe911.mycodespace.net/hourly.php?eventtypeid=' . $eventtype->eventtypeid . '&period=week">week</a>) ';
						//echo '(<a href="http://monroe911.mycodespace.net/hourly.php?eventtypeid=' . $eventtype->eventtypeid . '&period=month">month</a>)';
						echo '(<a href="http://monroe911.mycodespace.net/hourly.php?eventtypeid=' . $eventtype->eventtypeid . '&period=alltime">all-time by hour</a>)<br>';
						echo '</p>';
						echo '</div>';
					}
				
				?>
			
				<br>
				Note: this list is dynamicly added too as new types are seen by the web scrapers.  For more information how how this data is created, check out the 
				<a href="about.php">about</a> page and the <a href="developers.php">developers</a> page.<br>
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