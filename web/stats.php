<?php
	require_once("_header.php");
?>
		
		<?php
		
			// get the posted data variable
			$date = $_GET['date'];

			// see if we got a date passed in, or if we should be use todays date
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
		
			echo '<div class="yesterdaylink">';
			echo '<a href="stats.php?date=' . $yesterday . '">Stats for ' . date("l F j, Y",strtotime($yesterday)) . '</a>';
			echo '</div>';
			
			if( $date != date("Y-m-d") )
			{
				echo '<div class="tomorrowlink">';
				echo '<a href="stats.php?date=' . $tommorrow . '">Stats for ' . date("l F j, Y",strtotime($tommorrow)) . '</a>';
				echo '</div>';				
			}
			
			echo '<br>';
			echo '<br>';
		
		?>
		
		<div>
		
			<br>
			<center><h2>Monroe Count, NY 911 Calls Statistics for <?php if( $_GET["date"] == "" ) echo date("l F j, Y"); else echo date("l F j, Y",strtotime($_GET["date"])); ?> </h2></center>
			<br>
		
		</div>
	
		<div id="IncidentsDiv"></div>
		
		<script src="http://code.jquery.com/jquery-latest.js"></script>
		<!--[if IE]><script src="js/excanvas.js"></script><![endif]-->
		<script src="js/html5-canvas-bar-graph.js"></script>
		
		<script>
	
			$(document).ready(function() {
			
				// function to create the canvas that we are going to draw the bar graph in
				function createCanvas(divName) {
					
					var div = document.getElementById(divName);
					var canvas = document.createElement('canvas');
					div.appendChild(canvas);
					if (typeof G_vmlCanvasManager != 'undefined') {
						canvas = G_vmlCanvasManager.initElement(canvas);
					}	
					var ctx = canvas.getContext("2d");
					return ctx;
				}
				
				// create the canvas object
				var ctx = createCanvas("IncidentsDiv");
				
				// setup the bargraph
				var graph = new BarGraph(ctx);
				graph.width = 740;
				graph.height = 400;
				//graph.maxValue = 30;
				graph.margin = 2;
				graph.colors = [ "#49a0d8", "#d353a0", "#ffc527", "#df4c27"];
				
				
				<?php
					
					require_once("tools/BarGraphData.Class.php");
				
					$bargraph = new BarGraphData();
				
					$date = $_GET['date'];
					
					if( $date == "" )
						$date = date("Y-m-d");
				
					$results = $bargraph->GetIncidentCounts($date);

					// set the maximum for the graph
					echo "graph.maxValue = " . max($results) . ";\n";
					
					// set the x axis labels
					$letter = "A";
					echo "graph.xAxisLabelArr = [";
					for($i = 0; $i < (count($results)-1); $i++)
					{
						echo '"' . $letter . '",';
						$letter++;
					}
					echo '"' . $letter . '"';
					echo "];\n";

					// graph bogus data
					echo 'graph.update([';
					for($i = 0; $i < (count($results)-1); $i++)
						echo "0,";
					
					echo "0]);\n";
				
					// graph the real data
					$jsonResults = json_encode($results);
					echo 'graph.update(' . $jsonResults . ");\n";
			
			?>
		});
	
	</script>
				
	<div class="decoder">

		<?php
		
			require_once("./tools/Database.class.php");
		
			$db = new Database();
			
			$eventtypes = $db->GetEventTypes();
		
			$letter = "A";
		
			echo '<br><font size="2">';
		
			foreach($eventtypes as $eventtype)
			{
				echo "<b>" . $letter . "</b>: " . $eventtype->eventtype;
				echo ' (<a href="http://monroe911.mycodespace.net/hourly.php?eventtypeid=' . $eventtype->eventtypeid . '&date=';
				
				$date = $_GET["date"];
				
				if(  $date == "" )
					$date = date("Y-m-d");
					
				$date = $_GET["date"];
				
				echo $date . '">hourly</a>)<br>';
				
				$letter++;
			}
		
			echo '</font>';
		
		?>

		<br>

	</div>

<?php
	require_once("_footer.php");
?>