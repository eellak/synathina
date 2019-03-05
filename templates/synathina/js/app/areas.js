var Areas = (function(global) {

    function createCoordsArray() {
			var arrayCoords = [];
			InputObj = arguments[0];

			for (var i = 0; i < InputObj.features.length; i += 1) {
				arrayCoords.push({
					lng: InputObj.features[i].geometry.coordinates[0],
					lat: InputObj.features[i].geometry.coordinates[1],
				});
			}
			return arrayCoords;
    }

    function initPolygons(areaObj, styles) {
        AjaxCall.kmlToJson(function(jsonData) {
            createPaths(map, jsonData, styles, areaObj.details)
        }, areaObj.kmlPath);
    }

    function createPaths() {
            var inputObj = arguments[1];
            var styles = arguments[2];
            var polygonDetails = arguments[3]
            var polygonCoords = [];

            // if data is geoJson standard
            if (inputObj.type === 'FeatureCollection') {
                polygonCoords = createCoordsArray(inputObj);
                makePolygons = constructPolygon();
                makePolygons.init(polygonCoords, styles, polygonDetails);
            }
            // else do something else
    }

    function constructPolygon(polygonCoords, styles, polygonDetails) {

        function init(polygonCoords, styles, polygonDetails) {
            var myPolygon = new google.maps.Polygon({
                paths: polygonCoords,
                strokeColor: styles.strokeColor,
                strokeOpacity: styles.strokeOpacity,
                strokeWeight: styles.strokeWeight,
                fillColor: styles.fillColor,
                fillOpacity: styles.fillOpacity,
                details: polygonDetails,
                zIndex: global_zindex
            });

            polygonsArray.push(myPolygon);
            // cache polygon's options
            cachedStyles.push(styles);

            myPolygon.setMap(map);

            myPolygon.addListener('click', function(e) {
                // reset clusterer
                $('.categories input[type=checkbox]').each(function() {
                    this.checked = false;
                });

                var pt = myPolygon.getCenter();
                map.setZoom(14);
                map.panTo({
                    lat: pt.lat(),
                    lng: pt.lng()
                });

                // threading with activity object
                //EVT.emit('show-activities', this);

                Activities.setActivitiesVisibility(this);

                for (i in polygonsArray) {
                    if (polygonsArray[i] !== this) {
                        polygonsArray[i].setOptions({
                            fillColor: '#ccc',
                            strokeWeight: 2,
                            is_focused: false,
                            zIndex: global_zindex -= 1
                        });
                    } else {
                        this.setOptions({
                            fillColor: cachedStyles[i].fillColor,
                            zIndex: global_zindex += 1,
                            is_focused: true,
                            zIndex: global_zindex += 1
                        });
                    }
                }
                // triggers Cross Object event binded functions
                EVT.emit('show-cross');
                EVT.emit('send-polygon-details', this)
            });

            myPolygon.addListener('mouseover', function(evt) {
                this.setOptions({
                    strokeWeight: 3
                });
                for (i in polygonsArray) {
                    if (polygonsArray[i] !== this) {
                        polygonsArray[i].setOptions({
                            strokeWeight: 2,
                        });
                        if (polygonsArray[i].is_focused === true) {
                            polygonsArray[i].setOptions({
                                fillColor: cachedStyles[i].fillColor
                            })
                        } else {
                            polygonsArray[i].setOptions({
                                fillColor: '#ccc',
                            });
                        }

                    } else {
                        this.setOptions({
                            fillColor: cachedStyles[i].fillColor,
                            zIndex: global_zindex += 1
                        });
                    }
                }
                // triggers Cross Object event binded functions
                //EVT.emit('show-cross');
                EVT.emit('send-polygon-details', this)
            });

            myPolygon.addListener('mouseout', function(evt) {
                this.setOptions({
                    strokeWeight: 0
                });
                for (i in polygonsArray) {
					var isWithinPolygon = google.maps.geometry.poly.containsLocation(evt.latLng, this);
					if (isWithinPolygon === false) {
					//if (Map.containsLocation(evt, this) === false) {
                        polygonsArray[i].setOptions({
                            fillColor: cachedStyles[i].fillColor,
                            strokeWeight: 0
                        });
                    }
                    if (polygonsArray[i] == this) {
                        this.setOptions({
                            fillColor: '#ccc',
                            zIndex: global_zindex -= 1
                        })
                        if (this.is_focused === true) {
                            this.setOptions({
                                fillColor: cachedStyles[i].fillColor
                            })
                        }
                    }
                }
                // threading with Cross
                //EVT.emit('hide-cross');
            });

            // PROTOTYPES
            // ==========
            google.maps.Polygon.prototype.getCenter = function() {
                var PI = 22 / 7
                var X = 0;
                var Y = 0;
                var Z = 0;

                this.getPath().forEach(function(vertex, inex) {
                    lat1 = vertex.lat();
                    lon1 = vertex.lng();
                    lat1 = lat1 * PI / 180
                    lon1 = lon1 * PI / 180
                    X += Math.cos(lat1) * Math.cos(lon1)
                    Y += Math.cos(lat1) * Math.sin(lon1)
                    Z += Math.sin(lat1)
                });

                Lon = Math.atan2(Y, X)
                Hyp = Math.sqrt(X * X + Y * Y)
                Lat = Math.atan2(Z, Hyp)
                Lat = Lat * 180 / PI
                Lon = Lon * 180 / PI

                return new google.maps.LatLng(Lat, Lon);
            }

            google.maps.Polygon.prototype.Contains = function(point) {
                var crossings = 0,
                    path = this.getPath();
                // for each edge
                for (var i = 0; i < path.getLength(); i += 1) {
                    var a = path.getAt(i),
                        j = i + 1;
                    if (j >= path.getLength()) {
                        j = 0;
                    }
                    var b = path.getAt(j);
                    if (rayCrossesSegment(point, a, b)) {
                        crossings += 1;
                    }
                }

                // odd number of crossings?
                return (crossings % 2 == 1);

                function rayCrossesSegment(point, a, b) {
                    var px = point.lng(),
                        py = point.lat(),
                        ax = a.lng(),
                        ay = a.lat(),
                        bx = b.lng(),
                        by = b.lat();
                    if (ay > by) {
                        ax = b.lng();
                        ay = b.lat();
                        bx = a.lng();
                        by = a.lat();
                    }
                    // alter longitude to cater for 180 degree crossings
                    if (px < 0) {
                        px += 360
                    };
                    if (ax < 0) {
                        ax += 360
                    };
                    if (bx < 0) {
                        bx += 360
                    };

                    if (py == ay || py == by) py += 0.00000001;
                    if ((py > by || py < ay) || (px > Math.max(ax, bx))) return false;
                    if (px < Math.min(ax, bx)) return true;

                    var red = (ax != bx) ? ((by - ay) / (bx - ax)) : Infinity;
                    var blue = (ax != px) ? ((py - ay) / (px - ax)) : Infinity;

                    return (blue >= red);

                }

            };
        }

        var global_zindex = 1;

        return {
            init: init
        }
    }

    function populateAreas(data) {

        try {
            dat = JSON.parse(data.response);
        } catch (e) {
            //JSON parse error, this is not json
            console.log(e)
            return false;
        }

        collection = JSON.parse(data.response);

        for (var i = 0; i < collection.length; i += 1) {
            initPolygons(collection[i], collection[i].styles)
        }
    }

    function initAreas() {
        var lang = document.getElementsByTagName('html')[0].getAttribute('lang');
        AjaxCall.get('/areas.php?lang=' + lang, populateAreas);
        /**
        for( var i = 0; i < Athens.length; i += 1) {
          initPolygons(Athens[i], Athens[i].styles)
        }
        */

    }

    /**
     * BACKENDINFO: This JSON array describes overview details about each city's sector,
     * on production this must be retuned from the webservice
     */
    var Athens = [{
            details: {
                name: 'a_diamerisma',
                id: '1',
                title: '1o Διαμέρισμα',
                population: '<strong class=\"cross-side-title\">1<sup>o</sup></strong> <br> πληθυσμός',
                teams: '8 ομάδες',
                activities: '20'
            },
            kmlPath: 'js_collections/maps/1o_Diamerisma.kml',
            styles: {
                strokeColor: '#fbee66',
                strokeOpacity: 1,
                strokeWeight: 0,
                fillColor: '#fbee66',
                fillOpacity: 0.35
            }
        },
        {
            details: {
                name: 'b_diamerisma',
                id: '2',
                title: '2o Διαμέρισμα',
                population: '<strong class=\"cross-side-title\">2<sup>o</sup></strong> <br> πληθυσμός',
                teams: '12 ομάδες',
                activities: '80'
            },
            kmlPath: 'js_collections/maps/2o_Diamerisma.kml',
            styles: {
                strokeColor: '#00ffca',
                strokeOpacity: 1,
                strokeWeight: 0,
                fillColor: '#00ffca',
                fillOpacity: 0.35
            }
        },
        {
            details: {
                name: 'f_diamerisma',
                id: '3',
                title: '<strong>6o</strong> Διαμέρισμα',
                population: '<strong class=\"cross-side-title\">6<sup>oς</sup></strong> <br> πληθυσμός',
                teams: '49 ομάδες',
                activities: '1000'
            },
            kmlPath: 'js_collections/maps/6o_Diamerisma.kml',
            styles: {
                strokeColor: '#e55229',
                strokeOpacity: 1,
                strokeWeight: 0,
                fillColor: '#e55229',
                fillOpacity: 0.5
            }
        },
        {
            details: {
                name: 'g_diamerisma',
                id: '4',
                title: '<strong>7o</strong> Διαμέρισμα',
                population: '<strong class=\"cross-side-title\">7<sup>oς</sup></strong> <br> πληθυσμός',
                teams: '49 ομάδες',
                activities: '1122'
            },
            kmlPath: 'js_collections/maps/7o_Diamerisma.kml',
            styles: {
                strokeColor: '#cf93ff',
                strokeOpacity: 1,
                strokeWeight: 0,
                fillColor: '#cf93ff',
                fillOpacity: 0.5
            }
        },
        {
            details: {
                name: 'c_diamerisma',
                id: '5',
                title: '<strong>3o</strong> Διαμέρισμα',
                population: '<strong class=\"cross-side-title\">3<sup>oς</sup></strong> <br> πληθυσμός',
                teams: '49 ομάδες',
                activities: '1122'
            },
            kmlPath: 'js_collections/maps/3o_Diamerisma.kml',
            styles: {
                strokeColor: '#dbacb9',
                strokeOpacity: 1,
                strokeWeight: 0,
                fillColor: '#dbacb9',
                fillOpacity: 0.5
            }
        },

        {
            details: {
                name: 'e_diamerisma',
                id: '6',
                title: '<strong>5o</strong> Διαμέρισμα',
                population: '<strong class=\"cross-side-title\">5<sup>oς</sup></strong> <br> πληθυσμός',
                teams: '49 ομάδες',
                activities: '1122'
            },
            kmlPath: 'js_collections/maps/5o_Diamerisma.kml',
            styles: {
                strokeColor: '#dd9e58',
                strokeOpacity: 1,
                strokeWeight: 0,
                fillColor: '#dd9e58',
                fillOpacity: 0.5
            }
        },

        {
            details: {
                name: 'd_diamerisma',
                id: '7',
                title: '<strong>4o</strong> Διαμέρισμα',
                population: '<strong class=\"cross-side-title\">4<sup>oς</sup></strong> <br> πληθυσμός',
                teams: '49 ομάδες',
                activities: '1122'
            },
            kmlPath: 'js_collections/maps/4o_Diamerisma.kml',
            styles: {
                strokeColor: '#24c2e9',
                strokeOpacity: 1,
                strokeWeight: 0,
                fillColor: '#24c2e9',
                fillOpacity: 0.5
            }
        }
    ];

    var a_area, b_area, f_area, g_area, polygonsArray = [],
        cachedStyles = [];

    window.EVT.on('init', initAreas);

    return {
        initPolygons: initPolygons,
        Athens: ''
    }

})(window);