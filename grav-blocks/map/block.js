(function ($) {
    var defaultStyles = [{
            "featureType": "water",
            "elementType": "geometry",
            "stylers": [{
                    "color": "#e9e9e9"
                },
                {
                    "lightness": 17
                }
            ]
        },
        {
            "featureType": "landscape",
            "elementType": "geometry",
            "stylers": [{
                    "color": "#f5f5f5"
                },
                {
                    "lightness": 20
                }
            ]
        },
        {
            "featureType": "road.highway",
            "elementType": "geometry.fill",
            "stylers": [{
                    "color": "#ffffff"
                },
                {
                    "lightness": 17
                }
            ]
        },
        {
            "featureType": "road.highway",
            "elementType": "geometry.stroke",
            "stylers": [{
                    "color": "#ffffff"
                },
                {
                    "lightness": 29
                },
                {
                    "weight": 0.2
                }
            ]
        },
        {
            "featureType": "road.arterial",
            "elementType": "geometry",
            "stylers": [{
                    "color": "#ffffff"
                },
                {
                    "lightness": 18
                }
            ]
        },
        {
            "featureType": "road.local",
            "elementType": "geometry",
            "stylers": [{
                    "color": "#ffffff"
                },
                {
                    "lightness": 16
                }
            ]
        },
        {
            "featureType": "poi",
            "elementType": "geometry",
            "stylers": [{
                    "color": "#f5f5f5"
                },
                {
                    "lightness": 21
                }
            ]
        },
        {
            "featureType": "poi.park",
            "elementType": "geometry",
            "stylers": [{
                    "color": "#dedede"
                },
                {
                    "lightness": 21
                }
            ]
        },
        {
            "elementType": "labels.text.stroke",
            "stylers": [{
                    "visibility": "on"
                },
                {
                    "color": "#ffffff"
                },
                {
                    "lightness": 16
                }
            ]
        },
        {
            "elementType": "labels.text.fill",
            "stylers": [{
                    "saturation": 36
                },
                {
                    "color": "#333333"
                },
                {
                    "lightness": 40
                }
            ]
        },
        {
            "elementType": "labels.icon",
            "stylers": [{
                "visibility": "off"
            }]
        },
        {
            "featureType": "transit",
            "elementType": "geometry",
            "stylers": [{
                    "color": "#f2f2f2"
                },
                {
                    "lightness": 19
                }
            ]
        },
        {
            "featureType": "administrative",
            "elementType": "geometry.fill",
            "stylers": [{
                    "color": "#fefefe"
                },
                {
                    "lightness": 20
                }
            ]
        },
        {
            "featureType": "administrative",
            "elementType": "geometry.stroke",
            "stylers": [{
                    "color": "#fefefe"
                },
                {
                    "lightness": 17
                },
                {
                    "weight": 1.2
                }
            ]
        }
    ];

    function initMap($block) {
        var blockIndex = $block.attr('data-block-index');
        var mapConfig = window['mapBlockConfig' + blockIndex] || {};
        var mapStyles = (typeof mapConfig.customMapStyles !== 'undefined' && mapConfig.customMapStyles.length > 1) ? JSON.parse(mapConfig.customMapStyles.replace(/\r?\n|\r/g, '')) : defaultStyles;
        var $map = $block.find('.block-map__google-map');

        $map.css('padding-bottom', '75%');

        var bounds = new google.maps.LatLngBounds();
        var mapOptions = {
            zoomControl: true,
            zoom: 8,
            zoomControlOptions: {
                position: google.maps.ControlPosition.LEFT_BOTTOM
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
            styles: mapStyles
        };

        // Display a map on the page
        var map = new google.maps.Map(document.getElementById(blockIndex + '_map'), mapOptions);
        map.setTilt(45);

        // google map custom marker icon - .png fallback for IE11
        var is_internetExplorer11 = navigator.userAgent.toLowerCase().indexOf('trident') > -1;

        var marker_url = (is_internetExplorer11) ? mapConfig.markerUrl : mapConfig.markerUrlSvg;
        var markerClose = (is_internetExplorer11) ? mapConfig.markerClose : mapConfig.markerCloseSvg;
        var gravMarker = {
            url: marker_url
        };

        var locations = mapConfig.locations.replace(/'/g, '"');
        var gravMarkerLocations = JSON.parse(locations);

        // Array for infoWindow
        var infoWindows = mapConfig.infoWindows.replace(/'/g, '"').replace('<br />', '');

        var InfoWindowContent = JSON.parse(infoWindows);
        // Display multiple markers on a map
        
        var infoBubbleParams = Object.assign({
            closeSrc: markerClose
        }, mapConfig.infoBubbleParams || {});

        var infoWindow = new InfoBubble(infoBubbleParams)
        var marker = null;

        // Loop through our array of markers & place each one on the map
        for (var i = 0; i < gravMarkerLocations.length; i++) {
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
            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                return function () {
                    var link = (InfoWindowContent[i]['marker_link']) ? '<p><a href="' + InfoWindowContent[i]['marker_link'] + '" target="_blank">' + InfoWindowContent[i]['marker_link_text'] + '</a></p>' : '';
                    infoWindow.setContent('<div class="info-bubble-content">' + InfoWindowContent[i]['marker_name'] + InfoWindowContent[i]['marker_text'] + link + '</div>');
                    infoWindow.open(map, marker);
                }
            })(marker, i));

            // Automatically center the map fitting all markers on the screen
            map.fitBounds(bounds);
        }

        // Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
        var zoomOffest = $map.data('zoom');

        var boundsListener = google.maps.event.addListener(map, 'bounds_changed', function () {
            var theZoom = this.getZoom();

            this.setZoom(theZoom - zoomOffest);

            google.maps.event.removeListener(boundsListener);
        });

        google.maps.event.addDomListener(window, 'resize', function () {
            map.fitBounds(bounds);
            // get current zoom and set map to zoom - 1
            map.setZoom(map.getZoom() - zoomOffest);
        });
    }

    $(document).ready(function () {
        var $blocks = $('.block-map');

        if (!$blocks.length) {
            return;
        }

        $blocks.each(function () {
            initMap($(this));
        });
    });
})(jQuery);
