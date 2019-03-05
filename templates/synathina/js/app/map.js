var Map = (function(global) {
    function containsLocation(event, polygon) {
        var isWithinPolygon = google.maps.geometry.poly.containsLocation(event.latLng, polygon);
        return isWithinPolygon;
    }
    function initMap() {
        //$(document).ready(function(){
        var bw = window.innerWidth;
        var zoom = 13;
        if (bw > 480) {
            zoom = 13;
        } else {
            zoom = 12;
        }
        map = new google.maps.Map(document.getElementById('map'), {
            center: {
                lat: 37.9908372,
                lng: 23.7383394
            },
            zoom: zoom,
            minZoom: 11,
            maxZoom: 17,
            mapTypeControl: true,
            styles: styledArray
        });

        map.controls[google.maps.ControlPosition.TOP_CENTER];

        var ctaLayer = new google.maps.KmlLayer({
            url: 'js-collections/masks/aLayer01.kml',
            map: map
        });

        // creating and setting only one infowindow available to the map scope
        infoWindow = Info;
        map.infoWindow = infoWindow.createWindow();
        //map.panTo({lat: pt.lat(), lng: pt.lng() });
        //});
    }

    var infoFun, c_filter, point;
    var styledArray = [
        {
            'featureType': 'water',
            'elementType': 'geometry',
            'stylers': [{
                'color': '#e9e9e9'
            }, {
                'lightness': 17
            }]
        }, {
            'featureType': 'landscape',
            'elementType': 'geometry',
            'stylers': [{
                'color': '#f5f5f5'
            }, {
                'lightness': 20
            }]
        }, {
            'featureType': 'road.highway',
            'elementType': 'geometry.fill',
            'stylers': [{
                'color': '#ffffff'
            }, {
                'lightness': 17
            }]
        }, {
            'featureType': 'road.highway',
            'elementType': 'geometry.stroke',
            'stylers': [{
                'color': '#ffffff'
            }, {
                'lightness': 29
            }, {
                'weight': 0.2
            }]
        }, {
            'featureType': 'road.arterial',
            'elementType': 'geometry',
            'stylers': [{
                'color': '#ffffff'
            }, {
                'lightness': 18
            }]
        }, {
            'featureType': 'road.local',
            'elementType': 'geometry',
            'stylers': [{
                'color': '#ffffff'
            }, {
                'lightness': 16
            }]
        }, {
            'featureType': 'poi',
            'elementType': 'geometry',
            'stylers': [{
                'color': '#f5f5f5'
            }, {
                'lightness': 21
            }]
        }, {
            'featureType': 'poi.park',
            'elementType': 'geometry',
            'stylers': [{
                'color': '#dedede'
            }, {
                'lightness': 21
            }]
        }, {
            'elementType': 'labels.text.stroke',
            'stylers': [{
                'visibility': 'on'
            }, {
                'color': '#ffffff'
            }, {
                'lightness': 16
            }]
        }, {
            'elementType': 'labels.text.fill',
            'stylers': [{
                'saturation': 36
            }, {
                'color': '#333333'
            }, {
                'lightness': 40
            }]
        }, {
            'elementType': 'labels.icon',
            'stylers': [{
                'visibility': 'off'
            }]
        }, {
            'featureType': 'transit',
            'elementType': 'geometry',
            'stylers': [{
                'color': '#f2f2f2'
            }, {
                'lightness': 19
            }]
        }, {
            'featureType': 'administrative',
            'elementType': 'geometry.fill',
            'stylers': [{
                'color': '#fefefe'
            }, {
                'lightness': 20
            }]
        }, {
            'featureType': 'administrative',
            'elementType': 'geometry.stroke',
            'stylers': [{
                'color': '#fefefe'
            }, {
                'lightness': 17
            }, {
                'weight': 1.2
            }]
        }
    ];

    global.map = map;
    global.initMap = initMap;

    if (typeof google === 'object' && typeof google.maps === 'object') {
        point = new google.maps.LatLng(37.9908372, 23.7383394);
    }

    return {
        containsLocation: containsLocation
    };

})(window);