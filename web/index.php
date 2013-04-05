<?php
	require_once("_header.php");
?>


				<center><h1>Welcome to the Monroe County, NY 911 Feed Collator!</h1></center>

				<br>
				<br>

				This site is intended to be used as a simple tool for visualizing Monroe County, NY 911 calls and incidents.  The following pages are available:<br>
				<br>
				
				<p class="tab">
					<a href="status.php">status</a> - This shows the current status of the website.<br>
					<a href="stats.php">stats</a> - This is a series of statistics about the 911 incidents for today.<br>
					<a href="incidents.php">incidents</a> - This displays all of the incidents for a particular day.<br>
					<a href="events.php">events</a> - This is a list of different types of incidents seen by dispatch.<br>
					<a href="agencies.php">agencies</a> - This is an incomplete list of all of the agencies in Monroe County, NY reporting to incidents.<br>
					<a href="about.php">about</a> - More about this site and its developer/maintainer.<br>
				</p>
				
				<br>
				<br>
				
				Don't know where to start?  Check out today's <a href="stats.php">stats</a> and <a href="incidents.php">incident feed</a> first!
				<br>
				<br>
				
<?php
	require_once("_footer.php");
?>