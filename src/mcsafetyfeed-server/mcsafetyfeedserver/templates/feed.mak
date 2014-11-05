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
            margin-right: 4px !important;
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
                % if dispatch_count > count:
                    <a href="#" id="next-link">Next</a> >
                % endif
            </div>
        </div>
        <hr/>
        <div class="large-12 columns">
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
        var popups = [];
 
        var dispatch_data = [];

        var dispatch_type_ids = [];

        var start = ${start};
        var count = ${count};

        $(document).ready(function() {

            var main = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'Map data © OpenStreetMap contributors',
                minZoom: 10,
                maxZoom: 18,
            });

            window.map = L.map('map-canvas', {
                center: [43.16412, -77.60124], //[42.6501, -76.3659],
                zoom: 10,
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

            
            $('#check-box-container :input').each(function() {
                var checkbox = $(this);
                checkbox.attr('checked', false)
            });

            $('#button-select-all').on('click', function(e) {
                $('#check-box-container :input').each(function() {
                    var checkbox = $(this);
                    checkbox.attr('checked', true)
                });
                display_markers();
            });

            $('#button-clear-map').on('click', function(e) {
                
                // clear markers
                markers.forEach( function(marker) {
                    map.removeLayer(marker);
                });

                // clear popups
                markers.forEach( function(popup) {
                    map.removeLayer(popup);
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

            update_page();

        });

        function update_page() {
            get_dispatch_types(get_dispatches)
        }

        function update_dispatch_type_ids() {
            dispatch_type_ids = [];
            $('#check-box-container :input').each(function(i, data) {
                var checkbox = $(data);
                console.log(checkbox.context.checked);
                if ( checkbox.context.checked == true ) {
                    dispatch_type_ids.push(parseInt(checkbox.context.value));
                } else {
                }
            });
        }

        function get_dispatch_types(callback) {
            url = '/dispatch_types.json';
            $.getJSON(url, function( data ) {
                
                //dispatch_type_ids = [];
                
                var html = '';
                data.dispatch_types.forEach(function(dispatch_type) {
                    html += '<input checked="checked" class="checkbox" name="' + dispatch_type.text + '" value="' + dispatch_type.id + '" type="checkbox">' + dispatch_type.text + '</input></br>';
                    //dispatch_type_ids.push(dispatch_type.id);
                });
                
                $('#check-box-container').html(html);

                update_dispatch_type_ids();

                $('input[type="checkbox"]').change( function() {

                    update_dispatch_type_ids();

                    display_markers();
                    
                });

                callback();

            });
            
        }

        function get_dispatches() {
            // load feed items

            dispatch_data = [];

            url = '/dispatches.json?start=' + start + '&count=' + count;
            $.getJSON(url, function( data ) {
                dispatch_data = data;
                display_dispatches();
                display_markers();
            });
        }
        
        function display_dispatches() {
            html = '';
            html += '<table>';
            html += '<thead><tr>';
            html += '<th width="45">Geo</th>';
            html += '<th width="101">Time</th>';
            html += '<th width="300">Dispatch</th>';
            html += '<th width="200">Address</th>';
            html += '<th width="200">Agency</th>';
            html += '<th width="150">Event ID</th>';
            html += '</tr></thead>';
            dispatch_data.dispatches.forEach( function( dispatch ) {

                html += '<tr>';
                if ( dispatch.geocode_successful == false ) {
                    html += '<td><center><b style="color: red; font-size: 120%;">~</b></center></td>';
                } else {
                    html += '<td><center><b style="color: green;">&#x2713;</b></center></td>';
                }

                html += '<td>' + dispatch.dispatch_datetime.split(' ')[1].split('.')[0] + '</td>';
                html += '<td>' + dispatch.dispatch_text + '</td>';
                html += '<td>' + '<a href="#" id="' + dispatch.guid + '">' + dispatch.short_address + '</a>' + '</td>';
                html += '<td>' + dispatch.agency_name + '</td>';
                html += '<td>' + dispatch.guid + '</td>';
                html += '</tr>';

            });

            html += '</tbody></table>';

            $('#feed-items').html(html);
            var display_count = start+count;
            if ( display_count > dispatch_data.dispatch_count ) {
                display_count = dispatch_data.dispatch_count;
            }
            $('#dispatch-counts').html('Displaying Dispatches ' + (start+1) + ' - ' + display_count + ' of ' + dispatch_data.dispatch_count);
        }
        
        function display_markers() {
            markers.forEach( function(marker) {
                map.removeLayer(marker);
            });

            markers = [];

            dispatch_data.dispatches.forEach( function( dispatch ) {
                if ( dispatch_type_ids.indexOf(dispatch.dispatch_type_id) != -1 ) {
                    if ( dispatch.geocode_lat != null && dispatch.geocode_lng != null && dispatch.geocode_lat != 0 && dispatch.geocode_lng != 0) {

                        var marker = L.marker([dispatch.geocode_lat, dispatch.geocode_lng]).addTo(map);

                        var contents = '';
                        contents += '<div>' + dispatch.dispatch_datetime.split(' ')[1].split('.')[0] + '<br/>';
                        contents +=           dispatch.dispatch_text + '<br/>';
                        contents +=           dispatch.short_address + '<br/>';
                        contents +=           dispatch.agency_name + '<br/>';
                        contents +=           dispatch.guid + '<br/>';
                        contents += '</div>';

                        marker.bindPopup(contents).onOpen;

                        $('#' + dispatch.guid).on('click', function(e) {
                            
                            marker.openPopup();
                        });

                        markers.push(marker);

                    } else {
                        //console.log([dispatch.geocode_lat, dispatch.geocode_lng]);
                    }
                }
            });

        }
    

    </script>


