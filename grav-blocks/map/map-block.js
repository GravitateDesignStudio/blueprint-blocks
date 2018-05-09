jQuery(document).ready(function($){

    // Setup map_blocks array
    var block_index = $('.block-map.block-container').attr('data-block-index');

    $('#' + block_index + '_map').css('padding-bottom', '75%');

    initMap(block_index);

    function initMap(block_index) {
        var bounds = new google.maps.LatLngBounds();
        var mapOptions = {
            zoomControl: true,
            zoom: 8,
            zoomControlOptions: {
                position: google.maps.ControlPosition.RIGHT_CENTER
            },
            mapTypeControl: false,
            mapTypeId: 'roadmap',
            draggable: true,
            panControl: true,
            panControlOptions: {
                position: google.maps.ControlPosition.RIGHT_TOP
            },
            scrollwheel: false,
            streetViewControl: false,

        styles: [
            {
                "featureType": "administrative",
                "elementType": "all",
                "stylers": [
                    {
                        "saturation": "-100"
                    }
                ]
            },
            {
                "featureType": "administrative.province",
                "elementType": "all",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "landscape",
                "elementType": "all",
                "stylers": [
                    {
                        "saturation": -100
                    },
                    {
                        "lightness": 65
                    },
                    {
                        "visibility": "on"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "all",
                "stylers": [
                    {
                        "saturation": -100
                    },
                    {
                        "lightness": "50"
                    },
                    {
                        "visibility": "simplified"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "all",
                "stylers": [
                    {
                        "saturation": "-100"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "all",
                "stylers": [
                    {
                        "visibility": "simplified"
                    }
                ]
            },
            {
                "featureType": "road.arterial",
                "elementType": "all",
                "stylers": [
                    {
                        "lightness": "30"
                    }
                ]
            },
            {
                "featureType": "road.local",
                "elementType": "all",
                "stylers": [
                    {
                        "lightness": "40"
                    }
                ]
            },
            {
                "featureType": "transit",
                "elementType": "all",
                "stylers": [
                    {
                        "saturation": -100
                    },
                    {
                        "visibility": "simplified"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [
                    {
                        "hue": "#ffff00"
                    },
                    {
                        "lightness": -25
                    },
                    {
                        "saturation": -97
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "labels",
                "stylers": [
                    {
                        "lightness": -25
                    },
                    {
                        "saturation": -100
                    }
                ]
            }
        ]



        };

        // Display a map on the page
        map = new google.maps.Map(document.getElementById(block_index + "_map"), mapOptions);
        map.setTilt(45);

        //google map custom marker icon - .png fallback for IE11
        // TODO: Add in this IE11 check
        // var is_internetExplorer11= navigator.userAgent.toLowerCase().indexOf('trident') > -1;
        // var marker_url = ( is_internetExplorer11 ) ? '/library/images/icon.png' : '/library/images/icon.svg';

        var gravMarker = {
            url: marker_url,
        }

        // var locations = $('#' + block_index + '_map').data('locations');
        // console.log(locations);
        locations = locations.replace(/'/g, '"');

        gravMarkerLocations = JSON.parse(locations);

        //Array for infoWindow
        // var infoWindows = $('#' + block_index + '_map').data('infowindows');
        infoWindows = infoWindows.replace(/'/g, '"').replace('<br />', '');

        var InfoWindowContent = JSON.parse(infoWindows);
        // Display multiple markers on a map
        var infoWindow = new google.maps.InfoWindow(), marker, i;

         // Loop through our array of markers & place each one on the map
         for( i = 0; i < gravMarkerLocations.length; i++ ) {
             console.log(gravMarkerLocations[i]);
             var position = new google.maps.LatLng(
                 gravMarkerLocations[i][1],
                 gravMarkerLocations[i][2]
             );
             bounds.extend(position);
             marker = new google.maps.Marker({
                 icon: gravMarker,
                 position: position,
                 map: map,
                 title: gravMarkerLocations[i][0]
             });

             // Allow each marker to have an info window
             google.maps.event.addListener(marker, 'click', (function(marker, i) {
                 return function() {
                     infoWindow.setContent('<div class="info_content">' + InfoWindowContent[i]['marker_name'] +  InfoWindowContent[i]['marker_text'] + '<p><a href="https://www.google.com/maps/dir/Current+Location/' + gravMarkerLocations[i][1] +',' +gravMarkerLocations[i][2] +'" target="_blank">Get Directions</a></p></div>');
                     infoWindow.open(map, marker);
                 }
             })(marker, i));

             // Automatically center the map fitting all markers on the screen
             map.fitBounds(bounds);
         }

         // Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
         var zoomOffest = $('#' + block_index + '_map').data('zoom');

         var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {

            var theZoom = this.getZoom();

            this.setZoom(theZoom - zoomOffest);

            google.maps.event.removeListener(boundsListener);

        });

         google.maps.event.addDomListener(window, 'resize', function() {
              map.fitBounds(bounds);
             //function to get current zoom and set map to zoom - 1
             var theZoom = map.getZoom();
             map.setZoom(theZoom - zoomOffest);

         });
      }

});
