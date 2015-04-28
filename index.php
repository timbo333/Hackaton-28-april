<!DOCTYPE html>
<html>
<head>
    <style type="text/css">
        html, body, #map-canvas { height: 100%; margin: 0; padding: 0;}
    </style>
    <script type="text/javascript"
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAjWEJGC9ozZSYrtCgXk8SUf6orgbPAFcM">
    </script>
    <script type="text/javascript">
        function initialize() {
            var mapOptions = {
                zoom: 12,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

            // Try W3C Geolocation (Preferred)
            if(navigator.geolocation) {
                browserSupportFlag = true;
                navigator.geolocation.getCurrentPosition(function(position) {
                    initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
                    map.setCenter(initialLocation);

                    var image = 'images/gpsMarker.png';

                    var marker = new google.maps.Marker({
                        position: initialLocation,
                        map: map,
                        icon: image
                    });


                }, function() {
                    handleNoGeolocation(browserSupportFlag);
                });
            }
            // Browser doesn't support Geolocation
            else {
                browserSupportFlag = false;
                handleNoGeolocation(browserSupportFlag);
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