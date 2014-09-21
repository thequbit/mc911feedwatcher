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

    </style>

</head>
<body>

    <!--<div class="off-canvas-wrape" data-offcanvas="">-->
        <!-- Top nav bar -->
        <nav id="top-nav-bar" class="top-bar" data-topbar role="navigation">

            <ul class="title-area">
                <li class="name">
                    <h1><a href="/">mcsafetyfeed.org</a></h1>
                </li>
            </ul>

            <section class="top-bar-section">
                <!-- Right Nav Section -->
                <ul class="right">
                    <li class="divider"></li>
                    <li class="has-dropdown">
                        <a href="#">Menu</a>
                        <ul class="dropdown">
                            <li class="menu-item">
                                <a href="/">Home</a>
                            </li>
                            <li class="menu-item">
                                <a href="feed">911 Feed</a>
                            </li>
                            <li class="menu-item">
                                <a href="accidents">Accidents</a>
                            </li>
                            <li class="menu-item">
                                <a href="search">Search</a>
                            </li>
                            <li class="menu-item">
                                <a href="browse">Browse</a>
                            </li>
                            <li class="menu-item">
                                <a href="status">Status</a>
                            </li>
                            <li class="menu-item">
                                <a href="about">About</a>
                            </li>
                        </ul>
                    </li>
                    <li class="divider"></li>
                </ul>
            </section>

        </nav>
    <!--</div>-->

    ${self.body()}

    <footer class="row">
        <div class="large-12 columns">
            <hr/>
            <div class="row">
                <div class="large-6 columns">
                    <p>&copy; Copyright Timothy Duffy, 2014</p>
                </div>
                <div class="large-6 columns">
                    <ul class="inline-list right">
                        <li><a href="/">Home</a></li>
                        <li><a href="/feed">Feed</a></li>
                        <li><a href="/accidents">Accidents</a></li>  
                        <li><a href="/search">Search</a></li>
                        <li><a href="/browse">Browse</a></li>
                        <li><a href="/status">Status</a></li>
                        <li><a href="/about">About</a></li>
                    </ul>
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
