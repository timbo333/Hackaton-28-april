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

        var tweetMarkers = [];

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
                        }).done((function (selected) {
                            return function(dataCords) {
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

                            // Create a radius on location
                            var populationOptions = {
                              strokeColor: '#FF0000',
                              strokeOpacity: 0.5,
                              strokeWeight: 2,
                              fillColor: '#FF0000',
                              fillOpacity: 0.1,
                              map: map,
                              center: cords,
                              radius: 16093.44
                            };
                            cityCircle = new google.maps.Circle(populationOptions);

                            google.maps.event.addListener(delictMarker, 'click', function() {
                                
                                var contentString = 

                                    '<table>'+
                                        '<tr>'+
                                            '<th style="text-align: left;">Beschrijving</th>'+
                                            '<td>' + selected.description + '</td>'+
                                        '</tr>'+
                                        '<tr>'+
                                            '<th style="text-align: left;">Datum</th>'+
                                            '<td>' + selected.date + '</td>'+
                                        '</tr>'+
                                        '<tr>'+
                                            '<th style="text-align: left;">Tijd</th>'+
                                            '<td>' + selected.time + '</td>'+
                                        '</tr>'+
                                        '<tr>'+
                                            '<th style="text-align: left;">Stad</th>'+
                                            '<td>' + selected.city + '</td>'+
                                        '</tr>'+
                                        '<tr>'+
                                            '<th style="text-align: left;">Postcode</th>'+
                                            '<td>' + selected.zip + '</td>'+
                                        '</tr>'+
                                        '<tr>'+
                                            '<th style="text-align: left;">Adres</th>'+
                                            '<td>' + selected.adress + '</td>'+
                                        '</tr>'+
                                    '</table>';

                                var infoWindow = new google.maps.InfoWindow({
                                    content: contentString
                                });

                                infoWindow.open(map, delictMarker);

                                getTweets(delictMarker.position.k.toString() + "," + delictMarker.position.D.toString() + ",10mi", selected.date);
                            });

                        };
                    })(selected));

                    }
                });

                // Load all delicts from location
                // add click event for every delict
                // On click load twitter messages
            }

            function getTweets(curLocation, date) {
                date = date.split('-');
                date = date[2] + '-' + date[1] + '-' + date[0];
                $.ajax({
                    url: "twitter.php",
                    data: {
                        q: "",
                        geocode: curLocation,
                        count: 10,
                        since: date
                    },
                    method: "POST"
                }).done(function(dataArray) {

                    for(var i = 0; i < tweetMarkers.length; i++) {
                        tweetMarkers[i].setMap(null);
                    }
                    tweetMarkers = [];

                    var tweetArray = JSON.parse(dataArray).statuses;

                    for(var i = 0; i < tweetArray.length; i++) {
                        var selected = tweetArray[i];
                        
                        var cords = { lat: selected.coordinates.coordinates[1], lng: selected.coordinates.coordinates[0] };

                        // Create marker on location
                        var twitterMarker = new google.maps.Marker({
                            position: cords,
                            map: map,
                            icon: 'images/twitter.png'
                        });

                        tweetMarkers.push(twitterMarker);

                        listenMarker(twitterMarker, selected);
                    }

                    // for every tweet, place marker
                })
            }

            function listenMarker (marker, tweet)
            {
                var contentString = '<div id="content">'+
                    '<div id="siteNotice">'+
                    '</div>'+
                    '<h1 id="firstHeading" class="firstHeading"><img src="' + tweet.user.profile_image_url + '">' +
                    '<h3>"' + tweet.user.name + '"</h3><h6>"' + tweet.user.description + '"</h6></h1>'+
                    '<div id="bodyContent">'+
                    '<p>' + tweet.text + '</p>'+
                    '<p>' + tweet.created_at + ' on: ' + tweet.source + '</p>'+
                    '</div>'+
                    '</div>';

                var infowindow = new google.maps.InfoWindow({
                    content: contentString
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