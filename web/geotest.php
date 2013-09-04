 <html> 
<head> 
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
  <title>Google Maps Multiple Markers</title> 
  <script src="http://maps.google.com/maps/api/js?sensor=false" 
          type="text/javascript"></script>
</head> 
<body>
  </br></br>
  <div id="map" style="margin: auto; width: 640px; height: 480px;"></div>

	<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
	<script type="text/javascript">

		var map = new google.maps.Map(document.getElementById('map'), {
			zoom: 10,
			center: new google.maps.LatLng(43.1547, -77.6158),
			mapTypeId: google.maps.MapTypeId.ROADMAP
		});

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



	</script>
</body>
</html>