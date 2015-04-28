<!DOCTYPE html>
<html>
<head>
    <style type="text/css">
        html, body, #map-canvas { height: 100%; margin: 0; padding: 0;}
    </style>
    <script type="text/javascript"
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAjWEJGC9ozZSYrtCgXk8SUf6orgbPAFcM">
    </script>
    <script src="jquery-1.11.2.js"></script>
    <script type="text/javascript">
        function initialize() {
            loadDelicts(1);
            // Set map options
            var mapOptions = {
                zoom: 12,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            // Create map
            var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

            // Try geolocation (WC3)
            if(navigator.geolocation) {
                browserSupportFlag = true;
                navigator.geolocation.getCurrentPosition(function(position) {
                    // Get current location
                    initialLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    map.setCenter(initialLocation);

                    // Select postion marker image
                    var image = 'images/gpsMarker.png';

                    // Create marker on location
                    var marker = new google.maps.Marker({
                        position: initialLocation,
                        map: map,
                        icon: image
                    });

                    // Load delicts on location


                }, function() {
                    handleNoGeolocation(browserSupportFlag);
                });
            }
            // Browser doesn't support Geolocation
            else {
                browserSupportFlag = false;
                handleNoGeolocation(browserSupportFlag);
            }

            function loadDelicts(curLocation) {
                var delictsArr = [];

                $.ajax({
                    url: "meldingen.php"
                }).done(function(dataDelicts) {
                    delictsArr = JSON.parse(dataDelicts);

                    for(var i = 0; i < delictsArr.length; i++) {
                        var selected = delictsArr[i];
                        // AJAX request to https://maps.googleapis.com/maps/api/geocode/json?address=1600+Amphitheatre+Parkway,+Mountain+View,+CA&key=AIzaSyAjWEJGC9ozZSYrtCgXk8SUf6orgbPAFc
                        $.ajax( {
                            url: "https://maps.googleapis.com/maps/api/geocode/json?address=" + selected.adress + "," + selected.city + ",&key=AIzaSyAjWEJGC9ozZSYrtCgXk8SUf6orgbPAFcM"
                        }).done(function(dataCords) {
                            var cords = dataCords.results[0].geometry.location;

                            var type = selected.type;

                            if(type == null)
                                type = "politie";

                            // Create marker on location
                            var delictMarker = new google.maps.Marker({
                                position: cords,
                                map: map,
                                icon: 'images/' + type + '.png'
                            });

                            google.maps.event.addListener(delictMarker, 'click', function() {
                                getTweets(delictMarker.position.k.toString() + "," + delictMarker.position.D.toString() + ",10mi");
                            });

                        });

                    }
                });

                // Load all delicts from location
                // add click event for every delict
                // On click load twitter messages
            }

            function getTweets(curLocation) {
                $.ajax({
                    url: "twitter.php?q=hallo&geocode=" + curLocation + "&count=10",
                    type: "GET"
                }).done(function(dataArray) {
                    var tweetArray = JSON.parse(dataArray).statuses;

                    for(var i = 0; i < tweetArray.length; i++) {
                        var selected = tweetArray[i];
                        
                        var time = selected.createdAt;
                        var cords = { lat: selected.coordinates.coordinates[1], lng: selected.coordinates.coordinates[0] };

                        var source = selected.source;
                        var text = selected.text;

                        console.log(cords);

                        // Create marker on location
                        var twitterMarker = new google.maps.Marker({
                            position: cords,
                            map: map,
                            icon: 'images/twitter.png'
                        });

                        listenMarker(twitterMarker, selected.user.profile_image_url);
                    }

                    // for every tweet, place marker
                })
            }

            function listenMarker (marker, imgURL)
            {

                var infowindow = new google.maps.InfoWindow({
                content: "<img src=" + imgURL + "></img>"
                });

                // so marker is associated with the closure created for the listenMarker function call
                google.maps.event.addListener(marker, 'click', function() {
                    infowindow.open(map, marker);
                });
            }

            function handleNoGeolocation(errorFlag) {
                if (errorFlag == true)
                    alert("Geolocation service failed.");
                else
                    alert("Your browser doesn't support geolocation.");
            }
        }
        google.maps.event.addDomListener(window, 'load', initialize);
    </script>
</head>
<body>
<div id="map-canvas"></div>
</body>
</html>