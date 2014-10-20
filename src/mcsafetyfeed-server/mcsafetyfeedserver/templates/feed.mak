<%inherit file="base.mak"/>

    <style>

        #check-box-container {
            overflow: auto;
            /*overflow-y: hidden;*/
            white-space: nowrap;
            padding: 3px;
            border: 1px solid #DDD;
        }

        #check-box-container input {
            margin: 0px !important;
        }

    </style>

    <div class="row" id="feed-stop-stuff">
        <div class="large-8 columns">
            <!-- map here -->
            <div id="map-canvas"></div>

        </div>
        <div class="large-4 columns">
            <!-- control here -->
            <div id="map-control">
                
                <h5>Dispatches Types:</h5>
                <div id="check-box-container">
                    <input checked="checked" class="checkbox" name="Parking complaint" value="5" type="checkbox">Parking complaint</input></br>
                    <input checked="checked" class="checkbox" name="report of something burning inside not involving the structure" value="6" type="checkbox">report of something burning inside not involving the structure</input></br>
                    <input checked="checked" class="checkbox" name="Hit and Run, no injury and no blocking" value="7" type="checkbox">Hit and Run, no injury and no blocking</input></br>
                    <input checked="checked" class="checkbox" name="Dangerous condition" value="8" type="checkbox">Dangerous condition</input></br>
                    <input checked="checked" class="checkbox" name="Human life endangered by animal" value="10" type="checkbox">Human life endangered by animal</input></br>
                    <input checked="checked" class="checkbox" name="Any dumpster, grass or rubbish fire not posing an exposure problem" value="11" type="checkbox">Any dumpster, grass or rubbish fire not posing an exposure problem</input></br>
                    <input checked="checked" class="checkbox" name="Accident of motor vehicles involving unknown injury" value="12" type="checkbox">Accident of motor vehicles involving unknown injury</input></br>
                    <input checked="checked" class="checkbox" name="Barking dogs" value="13" type="checkbox">Barking dogs</input></br><input checked="checked" class="checkbox" name="Odor of smoke" value="15" type="checkbox">Odor of smoke</input></br>
                    <input checked="checked" class="checkbox" name="Dangerous condition - no immediate danger to life or property" value="16" type="checkbox">Dangerous condition - no immediate danger to life or property</input></br>
                    <input checked="checked" class="checkbox" name="MVA rollover" value="18" type="checkbox">MVA rollover</input></br><input checked="checked" class="checkbox" name="MVA with injuries" value="18" type="checkbox">MVA with injuries</input></br>
                    <input checked="checked" class="checkbox" name="Accident of motor vehicles involving known injury" value="19" type="checkbox">Accident of motor vehicles involving known injury</input></br>
                    <input checked="checked" class="checkbox" name="report of  a structure fire" value="20" type="checkbox">report of  a structure fire</input></br>
                    <input checked="checked" class="checkbox" name="Traffic light problems" value="21" type="checkbox">Traffic light problems</input></br>
                    <input checked="checked" class="checkbox" name="Wires down, wires arcing, wires blocking roadway" value="22" type="checkbox">Wires down, wires arcing, wires blocking roadway</input></br>
                    <input checked="checked" class="checkbox" name="MVA auto - pedestrian" value="23" type="checkbox">MVA auto - pedestrian</input></br>
                    <input checked="checked" class="checkbox" name="MVA auto - bicycle/motorcycle" value="25" type="checkbox">MVA auto - bicycle/motorcycle</input></br>
                    <input checked="checked" class="checkbox" name="Wires burning in a tree, object on wire" value="26" type="checkbox">Wires burning in a tree, object on wire</input></br>
                    <input checked="checked" class="checkbox" name="MVA person not alert" value="29" type="checkbox">MVA person not alert</input></br>
                    <input checked="checked" class="checkbox" name="MVA ATV" value="33" type="checkbox">MVA ATV</input></br></input></br>
                </div>

                <a class="small" href="#" id="button-select-all">Select All</a>
                <div class="right">
                    <a class="small" href="#" id="button-clear-map">Clear Map</a>
                </div>  
            </div>
        </div>
    </div>

    <br/>

    <div class="row">
        <div class="large-12 columns"> 
            <div id="dispatch-counts" class="left"></div>
            <div class="right">
                % if start != 0:
                    <!-- < <a href="/feed?start=0&count=${count}">Previous</a> | -->
                    < <a href="#" id="previous-link">Previous</a> | 
                    <!-- < <a href="/feed?start=${start-count}&count=${count}">Previous</a> | -->
                % endif
                <!-- <a href="/feed?start=${start+count}&count=${count}">Next</a> > -->
                <a href="#" id="next-link">Next</a> >
            </div>
        </div>
        <hr/>
        <div class="large-12 columns">
            <div class="row">
                <div class="large-1 columns">
                    <h4>Time</h4>
                </div>
                <div class="large-4 columns">
                    <h4>Dispatch</h4>
                </div>
                <div class="large-3 columns">
                    <h4>Address</h4>
                </div>
                <div class="large-2 columns">
                    <h4>Agency</h4>
                </div>
                <div class="large-2 columns">
                    <h4>Event ID</h4>
                </div>
            </div>

            <!-- feed here -->
            <div id="feed-items">
            </div>
        </div
    </div>

    <script src="static/js/vendor/jquery.js"></script>
    <script src="static/js/foundation.min.js"></script>
    <script>

        // init foundation
        $(document).foundation();

    </script>


    <script src="http://cdn.leafletjs.com/leaflet-0.7/leaflet.js"></script>
    <script>

        var map_container = document.getElementById('map-canvas');

        var markers = [];
 
        var start = ${start};
        var count = ${count};

        $(document).ready(function() {

            var main = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'Map data © OpenStreetMap contributors',
                minZoom: 10,
                maxZoom: 16,
            });

            window.map = L.map('map-canvas', {
                center: [43.16412, -77.60124], //[42.6501, -76.3659],
                zoom: 11,
                layers: [
                    main
                ]
            });

            /*
            L.control.scale({
                position: 'bottomright',
                metric: true,
                imperial: true
            }).addTo(map);
            */

            /*
            $(".checkbox").change(function() {
                if(this.checked == true) {
                    $(":checked").each(
                        function(i,data){
                            //popmarkers(data);
                        }
                    );
                } else {
                    // clear map of check box type
                    if (markerArray) {
                        for (i in markerArray) {
                            if( markerArray[i].title == this.name ) {
                                markerArray[i].setMap(null);
                                
                                // TODO: remove item from the array ... becaues this is an empic memory leak
                            }
                        }
                        //markerArray.length = 0;
                    }
                }
            });
            */

            $('#check-box-container :input').each(function() {
                var checkbox = $(this);
                checkbox.attr('checked', false)
            });

            $('#button-clear-map').on('click', function(e) {
                
                //markers.clearMarkers();
                markers.forEach( function(marker) {
                    map.removeLayer(marker);
                });

                markers = [];

                $('#check-box-container :input').each(function() {
                    var checkbox = $(this);
                    checkbox.attr('checked', false)
                });
                
            });

            $('#next-link').on('click', function(e) {

                start += count;            

                get_dispatches();
            
            });

            $('#previous-link').on('click', function(e) {

                if ( start - count < 0 ) {
                    start = 0;
                } else {
                    start -= count;
                }

                get_dispatches();

            });

            get_dispatches();

        });
 
        function get_dispatches() {

            // load feed items
            url = '/dispatches.json?start=' + start + '&count=' + count;
            $.getJSON(url, function( data ) {

                html = '';
                data.dispatches.forEach( function( dispatch ) {

                        html += '<div class="row feed-item">';
                        html += '<div class="large-1 columns">';
                        //html += data.dispatches[i].dispatch_datetime.split(' ')[1];
                        html += dispatch.dispatch_datetime.split(' ')[1].split('.')[0];
                        html += '</div>';
                        html += '<div class="large-4 columns">';
                        //html += data.dispatches[i].dispatch_text;
                        html += dispatch.dispatch_text;
                        html += '</div>';
                        html += '<div class="large-3 columns">';
                        //html += data.dispatches[i].short_address;
                        html += dispatch.short_address;
                        html += '</div>';
                        html += '<div class="large-2 columns">';
                        //html += data.dispatches[i].agency_name;
                        html += dispatch.agency_name;
                        html += '</div>';
                        html += '<div class="large-2 columns">';
                        //html += data.dispatches[i].guid;
                        html += dispatch.guid;
                        html += '</div>';
                        html += '</div>';

                        if ( dispatch.geocode_lat != null && dispatch.geocode_lng != null && dispatch.geocode_lat != 0 && dispatch.geocode_lng != 0) {
                            marker = L.marker([dispatch.geocode_lat, dispatch.geocode_lng]).addTo(map);
                            markers.push(marker);
                        }
                        else {
                            console.log([dispatch.geocode_lat, dispatch.geocode_lng]);
                        }

                    });

                    $('#feed-items').html(html);
                 
                    $('#dispatch-counts').html('Displaying Dispatches ' + (start+1) + ' - ' + (start+count) + ' of ' + data.dispatch_count);
                });
        }
        
    

    </script>


