<?php
	require_once("_header.php");
?>
	
	<?php

		//
		// Sanity Check Inputs
		//
		
		require_once("./tools/UtilityManager.class.php");
		
		$util = new UtilityManager();
	
		$date = $_GET["date"];
		
		// check for none-case ... we handle as the current date later in code
		if( $date != "" )
		{
		
			// check that the date is valid
			if( $util->IsValidDate($date) == 0 || $util->IsValidDate($date) == False )
			{
				// not a valid date

				echo '<script>';
				echo 'window.location = "./index.php"';
				echo '</script>';
			}
			else
			{
				// move on to rest of page, no need to do anything
			}
			
		}
		
	?>

	
	<?php
	
		//require_once("./tools/Database.class.php");
		
		require_once("./tools/IncidentManager.class.php");
		require_once("./tools/Incident.class.php");
		require_once("./tools/AgencyManager.class.php");
		require_once("./tools/Agency.class.php");
	
		// get the posted data variable
		$date = $_GET['date'];

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
	
		// get all of the incidents for the date passed in by the user
		$incidentManager = new IncidentManager();
		$incidents = $incidentManager->GetIncidentsByDay($date);
	
		// to handle all agency related querys
		$agencyManager = new AgencyManager();
	
		// display links to go to previous day and next day
		
		echo '<div class="yesterdaylink">';
		echo '<a href="incidents.php?date=' . $yesterday . '">Incidents for ' . date("l F j, Y",strtotime($yesterday)) . '</a>';
		echo '</div>';
		
		if( $date != date("Y-m-d") )
		{
			echo '<div class="tomorrowlink">';
			echo '<a href="incidents.php?date=' . $tommorrow . '">Incidents for ' . date("l F j, Y",strtotime($tommorrow)) . '</a>';
			echo '</div>';				
		}

		echo '<br><br>';

		echo '<div>';

		echo '<br>';

		echo '<center><h2>Incidents for ' . date("l F j, Y",strtotime($date)) . '</h2></center>';

		echo '<center>';
		echo '<br>';
		echo '<a href="stats.php?date=' . $date . '">See Stats For ' . date("l F j, Y",strtotime($date)) . '</a>';
		echo '</center>';
	
		echo '</div>';
	
		//
		// MAP
		// 
		echo '</br>';
		
		echo '<div id="mapwrapper" class="mapwrapper">';
		
		echo '<div id="map" class="map" style="width: 500px; height: 400px;"></div>';
		echo '<div id="mapsettings" class="mapsettings"></div>';
		echo '<div class="clear"></div>';
		echo '</div>';
	
	
		echo '<div>';
		if( count($incidents) == 0 )
		{
			echo "<br>";
			echo "<h3>No incidents were found for day: " . $date . "</h3>";
			echo "<br>";
		}
		else
		{
			echo"<br><br>";
			echo "Total number of incidents today:<b>" . count($incidents) . "</b><br><br>";
		
			echo '<div class="incidents">';
			echo '<table>';
			echo '<tr>';
			echo '<td><b><font size="4">Time</font></b></th>';
			echo '<td><b><font size="4">Event</font></b></th>';
			echo '<td><b><font size="4">Address</font></b></th>';
			echo '<td><b><font size="4">Responding Agency</font></b></th>';
			echo '<td><b><font size="4">Event ID</font></b></th>';
			echo '</tr>';
		
			// generate dictionaries so we don't have to query the DB every time.
			$longNameDict = $agencyManager->GetAgencyLongNameDictionary();
			$shortNameDict = $agencyManager->GetAgencyShortNameDictionary();
		
			// print the events to the page
			foreach($incidents as $incident)
			{
				
				// print out the row
				echo '<tr>';
				//echo '<a name="' . $incident->itemid . '"></a>';
				echo '<td width="100">' . $incident->pubtime . '</td>';
				echo '<td width="400">' . $incident->event . '</td>';
				if( $incident->lat == "" || $incident->lng == "" )
					echo '<td width="300">' . $incident->address . '</td>';
				else
					echo '<td width="300"><a href="https://maps.google.com/maps?z=16&t=m&q=loc:' . $incident->lat . "+" . $incident->lng . '">' . $incident->address . '</a></td>';
				echo '<td width="250"><a href="viewagency.php?agency=' . $shortNameDict[$incident->agencyid] . '">' . $longNameDict[$incident->agencyid] . '</a></td>';
				echo '<td width="100">' . $incident->itemid . '</td>';
				echo '</tr>';
			}
		
			echo '</table>';
			echo '</div>';
		}
		
		echo '</div>';
		
	?>	

	<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
	<!-- <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script> -->
	
	
	<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.5/leaflet.css" />
	<!--[if lte IE 8]>
		<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.5/leaflet.ie.css" />
	<![endif]-->
	<script src="http://cdn.leafletjs.com/leaflet-0.5/leaflet.js"></script>
	
	<script type="text/javascript">

		// create our map
		var map = L.map('map').setView([43.1547, -77.6158], 10);

		// place some tiles on it
		L.tileLayer('http://{s}.tile.cloudmade.com/a2152c679f334e08942fcf64d85decc1/997/256/{z}/{x}/{y}.png', {
			attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
			maxZoom: 18
		}).addTo(map);

		// array of markers
		var markers = [];

		// create checkboxes, and setup 'checked' events to add markers to map
		function createcheckboxes()
		{
		
			var html = '<div class="left">';
		
			url = "api/counts.php?date=<?php echo $date; ?>&type=dailycounts";
			$.getJSON(url, function (response) {
				
				// create check boxes
				for(n=0; n<response.length; n++)
				{
					html += '<input class="checkbox" type="checkbox" name="' + response[n].incidentname + '" value="' + response[n].id + '">' + response[n].incidentname + '</br>';
				}
				
				// add clear button
				html += '</br><button type="button" id="btnclearmap" name="btnclearmap">Clear Map</button>';
				
				html += '</div>';
				$("#mapsettings").html(html);
				
				$("#btnclearmap").click( function()
				{
					// clear the map
					clearmarkers();
					
					// clear the check boxes
					$(":checked").each( function() {
						this.checked = false;
					});
				});
				
				$(".checkbox").change(function() {

					if(this.checked == true) {
						$(":checked").each(
							function(i,data){
								var url = "api/getgeo.php?date=<?php echo $date; ?>&type=" + $(data).val();
								$.getJSON(url, function (response) { 
									var n;
									for(n=0; n<response.length; n++)
									{
										//name = response.drivers[n].name;
										//alert(name);
										lat = response[n].lat;
										lng = response[n].lng;
										event = response[n].event;
										/*
										var myLatLng = new google.maps.LatLng(lat,lng);
										var marker = new google.maps.Marker({
											position: myLatLng,
											//shadow: shadow,
											//icon:image,
											map: map,
											title: event,
											zIndex: 1//,
											//itemid: response[n].itemid
										});
										
										google.maps.event.addListener(marker, 'click', function() {
											//window.location = "#" + marker.itemid;
										});
										*/
										
										var marker = L.marker([lat, lng]).addTo(map);
										marker.event = event;
										
										markers.push(marker);
									}   
								});
							}
						);
					}
					else
					{
						// clear map of check box type
						if (markers) {
							for (i in markers) {
								if( markers[i].event == this.name )
								{
									map.removeLayer(markers[i])
								}
							}
						}
					}
				});
				
				// check all check boxes
				$(".checkbox").each( function()
				{
					$(this).attr('checked', true);
					$(this).trigger('change');
				});
			});
			
			createcheckboxes = Function("");
		}
		
		function clearmarkers()
		{
			// clear map of check box type
			if (markers) {
				for (i in markers) {
					map.removeLayer(markers[i])
				}
				markers.length = 0;
			}
		}
		
		createcheckboxes();

	</script>
			
<?php
	require_once("_footer.php");
?>