 <html> 
<head> 
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
  
  
</head> 
<body>
  </br></br>
  <center><div id="map" style="width: 1024px; height: 768px;"></div></center>

	<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.5/leaflet.css" />
	<!--[if lte IE 8]>
		<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.5/leaflet.ie.css" />
	<![endif]-->
	<script src="http://cdn.leafletjs.com/leaflet-0.5/leaflet.js"></script>

	<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
	<script type="text/javascript">

		// create our map
		var map = L.map('map').setView([43.1547, -77.6158], 10);

		// place some tiles on it
		L.tileLayer('http://{s}.tile.cloudmade.com/a2152c679f334e08942fcf64d85decc1/997/256/{z}/{x}/{y}.png', {
			attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
			maxZoom: 18
		}).addTo(map);

		// array of markers
		//var markers = [];

		loadData();

		function loadData()
		{
			var url = "./api/getgeo.php";
			$.getJSON(url, function (response) {handleData(response)});
		}

		function handleData(response)
		{
			var n;
			for(n=0; n<response.length; n++)
			{
				lat = response[n].lat;
				lng = response[n].lng;
				//event = response[n].event;
				var marker = L.marker([lat, lng]).addTo(map);
				//markers.push(marker);
			}   
		}

  </script>
</body>
</html>