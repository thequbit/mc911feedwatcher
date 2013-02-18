

<html>
	<title>Monroe County, NY 911 Feed Collator</title>
	
	<meta name="description" content="Monroe County, NY 911 Feed Collator">
	<meta name="keywords" content="Monroe, Monroe County, 911, Public Safty, Rochester, Feed, API, Application Programming Interface, Application, Programming, Interface, FOSS, Open Source, Open Data, Open, Source, Data">
	
	<link rel="shortcut icon" href="media/favicon.png" type="image/x-icon" />
	
	<link href="../css/main.css" rel="stylesheet" type="text/css">
	
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
			
				<div>
				
					<br>
			
					<h3>Add a new Blog Post</h3>
			
					<br>
			
					<?php

						require_once("../tools/Database.class.php");

						//echo $_SERVER['REQUEST_METHOD'];

						// test to see if it is a post back
						if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
						{
							//echo "inside.<br>";
						
							//echo "title: " . $_POST['title'] . "<br>";
							//echo "body: " . $_POST['body'] . "<br>";
						
							if( $_POST['title'] != "" && $_POST['body'] != "" )
							{
								//echo "inside<br>";
							
								$db = new Database();
								
								//echo "db obj created.<br>";
								
								$title = $_POST['title'];
								$body =  $_POST['body'];
								
								$db->CreateBlogPost($title,$body);
								
								echo '<font color="Green">Blog post added successfully</font><h4>';
								
								//echo "done.<br>";
							}
						}
						
					?>
			
					<br>
			
					<form name="input" action="addblogpost.php" method="post">
						Post: Title: <br>
						<input type="text" name="title"><br>
						<br>
						Post Body:<br>
						<textarea name="body" rows="8" cols="70"></textarea><br>
						<br>
						<input type="submit" value="Submit">
					</form>
				
					<br>
				
				</div>
			
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