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
			
			}
			
		}
		
	?>

	
	<?php
	
		//require_once("./tools/Database.class.php");
		
		require_once("./tools/IncidentManager.class.php");
		require_once("./tools/Incident.class.php");
		require_once("./tools/AgencyManager.class.php");
		require_once("./tools/Agency.class.php");
		
		//require_once("./tools/Time.class.php");
	
		//$time = new Time();

		// record start time
		//$starttime = $time->StartTime();
	
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
	
		// create an instance of the database
		//$db = new Database();

		// get all of the incidents for the day
		//$incidents = $db->GetIncidentsByDay($date);
	
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
		
		// calculate time taken
		//$totaltime = $time->TotalTime($starttime);
		
		// record the API call in the database as hash of IP
		//$ipaddress = md5($_SERVER['HTTP_X_FORWARDED_FOR']);
		//$db->AddAPICall($ipaddress, $todaysDate, $totaltime, "INCIDENTS");
		
	?>	

	<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
	<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
	<script type="text/javascript">

	var mapdiv = document.getElementById('map');

	var markerArray = [];

    var map = new google.maps.Map(mapdiv, {
        zoom: 10,
        center: new google.maps.LatLng(43.1547, -77.6158),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow();

	/*
    setTimeout(function () {
        loadData(); 
    },500);
	*/
	
	createcheckboxes();

	function handleData(response)
    {
        var n;
        for(n=0; n<response.length; n++)
        {
            //name = response.drivers[n].name;
            //alert(name);
            lat = response[n].lat;
            lng = response[n].lng;
			event = response[n].event;
            var myLatLng = new google.maps.LatLng(lat,lng);
            var marker = new google.maps.Marker({
                position: myLatLng,
                //shadow: shadow,
                //icon:image,
                map: map,
                title: event,
                zIndex: 1
            });
        }   
    }

	function createcheckboxes()
	{
		var html = '<div class="left">';
	
		url = "http://mcsafetyfeed.org/api/counts.php?date=<?php echo $date; ?>&type=dailycounts";
		$.getJSON(url, function (response) {
			for(n=0; n<response.length; n++)
			{
				html += '<input class="checkbox" type="checkbox" name="' + response[n].incidentname + '" value="' + response[n].id + '">' + response[n].incidentname + '</br>';
			}
			html += '</div>';
			$("#mapsettings").html(html);
			
			$(".checkbox").change(function() {
			
				if(this.checked) {
					$(":checked").each(
						function(i,data){
							var url = "http://mcsafetyfeed.org/api/getgeo.php?date=<?php echo $date; ?>&type=" + $(data).val();
							$.getJSON(url, function (response) { 
								var n;
								for(n=0; n<response.length; n++)
								{
									//name = response.drivers[n].name;
									//alert(name);
									lat = response[n].lat;
									lng = response[n].lng;
									event = response[n].event;
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
									
									markerArray.push(marker);
								}   
							});
						}
					);
				}
				else
				{
					// clear map of check box type
					if (markerArray) {
						for (i in markerArray) {
							if( markerArray[i].title == this.name )
							{
								markerArray[i].setMap(null);
								
								// TODO: remove item from the array ... becaues this is an empic memory leak
							}
						}
						//markerArray.length = 0;
					}
				}
			});
		});
		
	}

    /*
	function loadData()
    {
        //alert("Loading"); 
        var marker, i;

		var type = 1;

		for(type=5; type<49; type++)
		{
			var url = "http://mcsafetyfeed.org/api/getgeo.php?date=<?php echo $date; ?>&type=" + type;
			
			var name;
			var lat;
			var lon;
			var locations;
			

			$.getJSON(url, function (response) {handleData(response)});
		}
    }
	*/
	
	</script>
			
<?php
	require_once("_footer.php");
?>