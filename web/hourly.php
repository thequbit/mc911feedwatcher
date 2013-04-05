<?php
	require_once("_header.php");
?>

	<?php

		//
		// Sanity Check Inputs
		//
		
		require_once("./tools/UtilityManager.class.php");
		
		$util = new UtilityManager();
		
		$eventtypeid = $_GET['eventtypeid'];
		
		// check that the ID is valid
		if( $util->IsNumber($eventtypeid) == False )
		{
			// not a valid number

			echo '<script>';
			echo 'window.location = "./index.php"';
			echo '</script>';
		}
		
		$date = $_GET["date"];
		
		// check that the date is valid
		if( $util->IsValidDate($date) == 0 || $util->IsValidDate($date) == False )
		{
			// not a valid date

			echo '<script>';
			echo 'window.location = "./index.php"';
			echo '</script>';
		}
		
	?>

	<center><h3>Hourly Data for <?php if( $_GET["date"] == "" ) echo date("l F j, Y"); else echo date("l F j, Y",strtotime($_GET["date"])); ?></h3></center>
	<br>
	<center><h2>
		<?php
			require_once("./tools/EventManager.class.php");
			
			//$bgd = new BarGraphData();
			
			$eventManager = new EventManager();
			
			//
			// TODO: Sanitize this
			//
			$eventtypeid = $_GET['eventtypeid'];
			
			$eventtext = $eventManager->GetEventTextFromID($eventtypeid);
			
			echo '"' . strtoupper($eventtext) . '"';
			
		?>
	</h2></center>

	<br>

	<div id="HourlyTodayDiv"></div>
		
		
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
					
					
					//require_once("./tools/BarGraphData.Class.php");
				
					//$bargraph = new BarGraphData();
				
					require_once("./tools/EventManager.class.php");
			
					$eventManager = new EventManager();
				
					$eventtypeid = $_GET['eventtypeid'];
				
					$date = $_GET["date"];
					
					if( $date == "" )
						$date = date("Y-m-d");
				
					// get the counts for the day
					$counts = $eventManager->GetHourlyCountsByEventId($eventtypeid, $date);

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
	
	</div>

	

<?php
	require_once("_footer.php");
?>