<?php
	require_once("_header.php");
?>

	<?php

		//
		// Sanity Check Inputs
		//
		
		require_once("./tools/UtilityManager.class.php");
		
		$eventtypeid = $_GET['eventtypeid'];
		
		$util = new UtilityManager();
		
		if( $util->IsNumber($eventtypeid) == False )
		{
			// not a valid number was passed in

			echo '<script>';
			echo 'window.location = "./index.php"';
			echo '</script>';
		}
	?>

	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<!--[if IE]><script src="js/excanvas.js"></script><![endif]-->
	<script src="js/html5-canvas-bar-graph.js"></script>
	
	<!-- Script for Bar Gragh, gets printed in HourlyTodayDiv -->
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
			var ctx = createCanvas("HourlyTodayDiv");
			
			// setup the bargraph
			var graph = new BarGraph(ctx);
			graph.width = 940;
			graph.height = 400;
			//graph.maxValue = 30;
			graph.margin = 2;
			graph.colors = [ "#49a0d8"];
			graph.rotateAxisText = true;
			
			
			<?php
				
				//require_once("tools/BarGraphData.Class.php");
			
				//$bargraph = new BarGraphData();
			
				require_once("./tools/EventManager.class.php");

			
				$eventtypeid = $_GET['eventtypeid'];
			
				$eventManager = new EventManager();
				

			
				// get the counts for the day
				$counts = $eventManager->GetAllTimeHourlyCountsByEventId($eventtypeid);

				// set the maximum for the graph
				echo "graph.maxValue = " . max($counts) . ";\n";
				
				// set the x axis labels
				$hour = 0;
				echo "graph.xAxisLabelArr = [";
				for($i = 0; $i < (count($counts)-1); $i++)
				{
					if( $hour < 10 )
						echo '"  ' . $hour . ':00",';
					else
						echo '"' . $hour . ':00",';
					$hour++;
				}
				echo '"' . $hour . ':00"';
				echo "];\n";

				// graph bogus data
				echo 'graph.update([';
				for($i = 0; $i < (count($counts)-1); $i++)
					echo "0,";
				
				echo "0]);\n";
			
				// graph the real data (this causes the animation)
				$jsonResults = json_encode($counts);
				echo 'graph.update(' . $jsonResults . ");\n";

			?>
		});

	</script>

	<A HREF="javascript:history.back()">< Back</a><br><br>

	<center><h3>Summation of All Time Hourly Data</h3></center>
	<br>
	<center><h2>
	
		<?php
			
			require_once("./tools/EventManager.class.php");
			
			$eventManager = new EventManager();
			
			//
			// TODO: Sanity Check This!
			//
			$eventtypeid = $_GET['eventtypeid'];
			
			// get the text from the ID
			$eventtext = $eventManager->GetEventTextFromID($eventtypeid);
			
			// print it as the header on the page
			echo '"' . strtoupper($eventtext) . '"';
			
		?>
		
	</h2></center>

	<br>

	<div id="HourlyTodayDiv"></div>

<?php
	require_once("_footer.php");
?>