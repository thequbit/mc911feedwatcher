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
				
				<?php 
					
					require_once("./tools/Database.class.php");
					$db = new Database();
					if( $_GET['eventtypeid'] == "" )
					{
						echo "You must pass in a eventtypeid and period, like this: http://monroe911.mycodespace.net/visdata.php?eventtypeid=13&period=today";
					}
					else
					{
						
						switch($_GET['period'])
						{
							case 'today':
								echo '<h2>Hourly Graph of Daily Incidents For Event: "';
								echo $db->GetEventTextFromID($_GET['eventtypeid']);
								echo '"</h2>';
								break;
							case 'week':
								echo '<h2>Week Graph of Daily Incidents For Event: "';
								echo $db->GetEventTextFromID($_GET['eventtypeid']);
								echo '"</h2>';
								break;
							case 'month':
								echo '<h2>Month Graph of Daily Incidents For Event: "';
								echo $db->GetEventTextFromID($_GET['eventtypeid']);
								echo '"</h2>';
								break;
							case 'alltime':
								echo '<h2>Hourly Graph of All Incidents For Event: "';
								echo $db->GetEventTextFromID($_GET['eventtypeid']);
								echo '"</h2>';
								break;
							default:
								echo '<h2>Graph of ALL Daily Incidents For Event: "';
								echo $db->GetEventTextFromID($_GET['eventtypeid']);
								echo '"</h2>';
								break;
						}
						
					}
				?>
				
				<br>
				<A HREF="javascript:history.back()">Back to Previous Page</a>
				<br>

			</div>

			<div class="graph" id="graph">

				<script src="http://d3js.org/d3.v3.min.js"></script>
				<script>

				var margin = {top: 20, right: 20, bottom: 30, left: 40},
					width = 900 - margin.left - margin.right,
					height = 450 - margin.top - margin.bottom;

				//var formatPercent = d3.format(".0%");

				var x = d3.scale.ordinal()
					.rangeRoundBands([0, width], .1);

				var y = d3.scale.linear()
					.range([height, 0]);

				var xAxis = d3.svg.axis()
					.scale(x)
					.orient("bottom");

				var yAxis = d3.svg.axis()
					.scale(y)
					.orient("left")
					//.tickFormat(formatPercent);

				var svg = d3.select("#graph").append("svg")
					.attr("width", width + margin.left + margin.right)
					.attr("height", height + margin.top + margin.bottom)
				  .append("g")
					.attr("transform", "translate(" + margin.left + "," + margin.top + ")")
					.attr("class", "chart");

				var apiurl = "api/eventd3api.php?eventtypeid=<?php echo $_GET['eventtypeid'];?>&period=<?php echo $_GET['period']?>&startdate=2012-1-1";
				//var apiurl = "data.tsv";

				//alert(apiurl);

				d3.tsv(apiurl, function(error, data) {

				  data.forEach(function(d) {
					d.theyval = +d.theyval;
				  });

				  x.domain(data.map(function(d) { return d.thexval; }));
				  y.domain([0, d3.max(data, function(d) { return d.theyval; })]);

				  svg.append("g")
					  .attr("class", "x axis")
					  .attr("transform", "translate(0," + height + ")")
					  .call(xAxis);

				  svg.append("g")
					  .attr("class", "y axis")
					  .call(yAxis)
					.append("text")
					  .attr("transform", "rotate(-90)")
					  .attr("y", 6)
					  .attr("dy", ".71em")
					  .style("text-anchor", "end")
					  .text("theyval");

				  svg.selectAll(".bar")
					  .data(data)
					.enter().append("rect")
					  .attr("class", "bar")
					  .attr("x", function(d) { return x(d.thexval); })
					  .attr("width", x.rangeBand())
					  .attr("y", function(d) { return y(d.theyval); })
					  .attr("height", function(d) { return height - y(d.theyval); });

				});

				</script>
			
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