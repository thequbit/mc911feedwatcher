<html>
	<title>Monroe County 911 Feed API Status</title>
<head>
</head>
<body>

	Status:<br><br>
	
	<?php
	
		include_once("Database.class.php");
		
		// create a database tool to use to pull information from the database
		$db = new Database();
		
		// get the total number of times the scraper has run
		$runCount = $db->GetNumberOfRuns();
		
		// get the total number of unique entrees in the database
		$uniqueEntrees = $db->GetTotalUniqueEntrees();
		
		$results = $db->GetAllItems();
		
		// display the results from the database
		echo "Total scraper runs: " . $runCount . "<br><br>";
		echo "Total unique entrees: " . $uniqueEntrees . "<br><br>";
		
	?>

</body>
</html>