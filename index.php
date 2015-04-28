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
                center: { lat: 52.500035, lng: 6.07942300000002 },
                zoom: 12
            };
            var map = new google.maps.Map(document.getElementById('map-canvas'),
                mapOptions);

            var marker = new google.maps.Marker({
                position: { lat: 52.500035, lng: 6.07942300000002 },
                map: map,
                title: 'Windesheim, Zwolle'
            });
        }
        google.maps.event.addDomListener(window, 'load', initialize);
    </script>
</head>
<body>
<div id="map-canvas"></div>
</body>
</html>