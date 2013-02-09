<html>
	<title>Monroe County, NY 911 Feed Collator</title>
	
	<meta name="description" content="Monroe County, NY 911 Feed Collator">
	<meta name="keywords" content="Monroe, Monroe County, 911, Public Safty, Rochester, Feed, API, Application Programming Interface, Application, Programming, Interface, FOSS, Open Source, Open Data, Open, Source, Data">
	
	<link rel="shortcut icon" href="media/favicon.png" type="image/x-icon" />
	
	<link href="css/main.css" rel="stylesheet" type="text/css">
	
	<script type="text/javascript">

	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-38308300-1']);
	  _gaq.push(['_setDomainName', 'mycodespace.net']);
	  _gaq.push(['_trackPageview']);

	  (function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>
	
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
					<a href="groups.php">groups</a>
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
				<h3>Here are some stats about the Monroe 911 feed scraper system:</h3>
			
				<?
			
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
					echo '<p class="tab">';
					echo "<b><br>System Stats: </b><br>";
					echo '</p>';
					echo '<p class="tab2">';
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
					
					echo '<p class="tab">';
					echo "<b>Data Stats: </b><br>";
					echo '</p>';
					echo '<p class="tab2">';
					echo "Total Unique Incidents: " . $uniqueIncidents . "<br>";
					echo "Total Incident Types: " . $totalEventTypes . "<br>";
					echo "Total Status Types: " . $totalStatusTypes . "<br>";
					echo '</p>';
					
				?>
			
				<br>
				<h3>So what does all that mean?</h3>
				<br>
				
				<p class="tab">
				The above data represents the current status of this site and its associated web scrapers.  What is a web scraper?  In this case, there are pieces of code that go
				out on the internet and collect information from the Monroe County, NY dispatch center and collate it.  If you would like more information about the Monroe County dispatch
				center, check out the <a href="about.php">About</a> page.  If you would like to know more about the data that is being collected, and how you can use it, check out
				the <a href="developers.php">Developers</a> page.
				</p>
				<br>
				<br>
				
				<h3>System Stats</h3>
				<br>
				
				<p class="tab">
				<b>Total Scraper Runs</b>
				</p>
				<p class="tab2">
				this represents the total number of times the web scrapers have run.  The scrapers run every 60 seconds to ensure all of the information that the
				dispatch center is providing is captured, and nothing is missed.
				</p>
				<br>
				
				<p class="tab">
				<b>Total Number of API calls</b>
				</p>
				<p class="tab2">
				API, in this context, stands for <a href="http://en.wikipedia.org/wiki/Application_programming_interface">Application Programming Interface</a>.  This site provides 
				a number of free and open API's to allow for anyone to access the data that is being captured by the web scrapers.  For more information on how to take advantage of this
				data, check out the <a href="developers.php">Developers</a> page.
				</p>
				<br>
				<br>
				
				<p class="tab">
				<b>Total Unique API users</b>
				</p>
				<p class="tab2">
				This site keeps track of the number of unique users use its API's.  Since anonymity is an important part of why the Internet is so important, IP addresses are hashed 
				and only the hash is saved.  This prevents any information that could identify an individual from being saved by this site.
				</p>
				<br>
				<br>
				
				<p class="tab">
				<b>Unique API Users Today</b>
				</p>
				<p class="tab2">
				This is the total number of unique people that have used the sites API's today.
				</p>
				<br>
				<br>
				
				<p class="tab">
				<b>Average Query Time Today</b>
				</p>
				<p class="tab2">
				All of the information this site serves up with respect to 911 incident information and data is stored within a database.  This value represents the average amount of time for
				the current day it takes the website to pull the data from the database.  This metric is important to monitor because high query times (larger then half a second) means the
				site is under heavy load, or there could be a technical problem with the server(s).
				</p>
				<br>
				<br>
				
				<br>
				<h3>Data Stats</h3>
				<br>
				
				<p class="tab">
				<b>Total Unique Incidents</b>
				</p>
				<p class="tab2">
				An incident is defined as a unique incident ID being generated by the 911 dispatch center.  If a call comes in for a Motor Vehicul Accident, it is possible that Monroe County 
				Police, Brighton Fire, and Brighton Ambulance could all be dispatched to the scene.  This would result in three unique incidents, even though only one phone call was made.  It
				is possible to deduce the number of unique "events" based on the address that different entities are dispatched too, however this site does not, yet, provide that meta data.
				</p>
				<br>
				<br>
				
				<p class="tab">
				<b>Total Incident Types</b>
				</p>
				<p class="tab2">
				When a call comes into Monroe County 911 dispatch, the dispatcher needs to select the type of call it is.  An example of this would be "Barking Dog" or "hit and run, no injury 
				and no blocking".  There are a number of different incident types, this number represents the total different types seen by the web scrapers.
				</p>
				<br>
				<br>
				
				<p class="tab">
				<b>Total Status Types</b>
				</p>
				<p class="tab2">
				When a call comes in it immediately is placed into a "DISPATCHED" state.  There are a number of other states such as "ENROUTE" and "ONSCENE" an incident can have.  This site keeps
				track of when each incident moves between each status type.  This value represents the total number of status types seen by the web scrapers.
				</p>
				<br>
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