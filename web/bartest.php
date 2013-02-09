<?php
	require_once("_header.php");
?>

	
	<div id="IncidentsDiv" style="margin: auto"></div>
		
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
			graph.width = 800;
			graph.height = 400;
			//graph.maxValue = 30;
			graph.margin = 2;
			graph.colors = [ "#49a0d8", "#d353a0", "#ffc527", "#df4c27"];
			
			
			<?php
				
				require_once("tools/BarGraphData.Class.php");
			
				$bargraph = new BarGraphData();
			
				$results = $bargraph->GetTodaysIncidentCounts();

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
				echo "$.getJSON('api/bargraphapi.php', function(data) {\n";
				echo 'graph.update(' . $jsonResults . ");\n";
				echo "});\n";
		
		?>
		});
	
	</script>
	
	<br>
	<br>
	
	<?php
			
		require_once("./tools/Database.class.php");
	
		$db = new Database();
		
		$eventtypes = $db->GetEventTypes();
	
		$letter = "A";
	
		echo '<br><font size="2">';
	
		foreach($eventtypes as $eventtype)
		{
			echo "<b>" . $letter . "</b>: " . $eventtype->eventtype;
			echo ' (<a href="http://monroe911.mycodespace.net/hourly.php?eventtypeid=' . $eventtype->eventtypeid . '&period=today">hourly</a>)<br>';
			
			$letter++;
		}
	
		echo '</font>';
	
	?>

<?php
	require_once("_footer.php");
?>