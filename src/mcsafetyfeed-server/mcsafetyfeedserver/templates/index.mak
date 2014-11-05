<%inherit file="base.mak"/>

    <style>

        div.info-box {
            padding: 15px;
            min-height: 290px;
        }

        div.button-wrapper {
            margin: auto;
            text-align: center;
        }

    </style>
    
    <div class="row" id="top-orbit">
        <center>
        <div class="large-12 columns">
            <ul class="main-top-orbit" data-orbit>
                <!--<li><img src="/static/media/top.png"></li>-->
                <!--<li><img src="http://placehold.it/1000x400&amp;text=[%20img%201%20]"></li>-->
                <!--<li><img src="http://placehold.it/1000x400&amp;text=[%20img%202%20]"></li>
                <li><img src="http://placehold.it/1000x400&amp;text=[%20img%203%20]"></li>-->
            </ul>
        </div>
        <!--
        <p style="font-size: 50%;">"RochesterCollage4" by EastOfWest - Own work. Licensed under Creative Commons Attribution-Share Alike 3.0 via Wikimedia Commons - http://commons.wikimedia.org/wiki/File:RochesterCollage4.jpg#mediaviewer/File:RochesterCollage4.jpg</p>
        -->
        </center>
    </div>
    <!--<hr/>-->

    <div class="row">
        <center><p style="color: red;">MCSafetyFeed.org has no officiation with Monroe County, NY.</p></center>
    </row>
    
    <div class="row">
        <div class="large-4 columns">
            <div class="info-box">
            <h3>911 Feed</h3>
            <p>
                Monroe County, NY is equiped with a e911 system which allows for certain 911 calls to be available to the 
                public in near-real-time.  A live list of these calls, as well as their current status can be viewed
                in the Live 911 Feed section.
            </p>
            </div>
            <div class="button-wrapper">
                <a class="button" id="button-live-911-feed" href="/feed">Live 911 Feed</a>
            </div>
            <hr/>
        </div>
        <div class="large-4 columns ">
            <div class="info-box">
            <h3>Accidents</h3>
            <p>
                View a live map of accidents that have been called into 911 in Monroe County, NY.  The type of accident,
                the agencies being dispatched, and it's location can be seen on the map.
            </p>
            </div>
            <div class="button-wrapper">
            <a class="button" id="button-view-accidents" href="/accidents">View Accidents</a>
            </div>
            <hr/>
        </div>
        <div class="large-4 columns">
            <div class="info-box">
            <h3>Search Incidents</h3>
            <p>
                A database of observed 911 calls is available for searching.  Search by location, time, incident type, 
                and/or dispatched agencies.  Note all 911 calls will be available, as only non-violent calls are available
                via the Monroe County, NY e911 system.
            </p>
            </div>
            <div class="button-wrapper">
            <a class="button" id="search-incidents" href="/search">Search Incidents</a>
            </div>
            <hr/>
        </div>
    </div>
    
