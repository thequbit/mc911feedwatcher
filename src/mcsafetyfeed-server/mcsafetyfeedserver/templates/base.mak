<!doctype html>
<html class="no-js" lang="en">
<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MCSafetyFeed.org | Feed</title>

    <link rel="stylesheet" href="static/css/site.css">
    <link rel="stylesheet" href="static/css/foundation.css" />
    <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.css" />

    <script src="static/js/vendor/modernizr.js"></script>

    <style>

        #top-orbit {
            margin-top: 1%;
        }

        div.title-bar {
            color: black;
            background-color: #DDD;
        }

        div.title-bar a {
            color: #333;
        }

        div.top-links {
            margin-top: 20px !important;
            margin-bottom: 20px !important;
            border-bottom: 1px solid #DDD;
        }

    </style>

</head>
<body>

     <div class="title-bar">
        <div class="row">
            <div class="large-12 columns">
                <h3><a href="/">MCSafetyFeed</a></h3>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="large-12 columns">
            <div class="row top-links">
                <div class="large-8 columns">
                    <ul class="inline-list">
                        <li><a href="/">Home</a></li>
                        <li><a href="/feed">Feed</a></li>
                        <li><a href="/accidents">Accidents</a></li>
                        <li><a href="/agencies">Agencies</a></li>
                        <li><a href="/search">Search</a></li>
                        <li><a href="/browse">Browse</a></li>
                        <li><a href="/status">Status</a></li>
                        <li><a href="/about">About</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    ${self.body()}

    <footer class="row">
        <div class="large-12 columns">
            <!-- <hr/> -->
            <div class="row">
                <div class="large-6 columns">
                    <p>&copy; Copyright Timothy Duffy, 2014</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="static/js/vendor/jquery.js"></script>
    <script src="static/js/foundation.min.js"></script>
    <script>

        // init foundation
        $(document).foundation();

    </script>

</body>
</html>
