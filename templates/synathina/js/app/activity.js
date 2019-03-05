var Activities = (function(global) {

    function uniqueCategory(cat) {
        var catNew = [];
        var counter = 0;

        for (var i = 0; i < cat.length; i++) {
            var found = false;
            for (var j = 0; j < catNew.length; j++) {
                if (cat[i].category_id == catNew[j].category_id) {
                    found = true;
                    break;
                }
            }
            if (!found) {
                catNew[counter++] = cat[i];
            }

        }

        return catNew.sort(function(a, b) {
            return parseInt(a.category_id) - parseInt(b.category_id);
        });

    }

    function contains2(needle) {
        //INFO EDW FILTRARW
        // Per spec, the way to identify NaN is that it is not equal to itself
        var findNaN = needle !== needle;
        var indexOf;

        if (!findNaN && typeof Array.prototype.indexOf === 'function') {
            indexOf = Array.prototype.indexOf;
        } else {
            indexOf = function(needle) {
                var i = -1,
                    index = -1;

                for (i = 0; i < this.length; i++) {
                    var item = this[i];

                    if ((findNaN && item !== item) || item === needle) {
                        index = i;
                        break;
                    }
                }

                return index;
            };
        }

        return indexOf.call(this, needle) > -1;
    };

    function findIn() {

        filter_array = arguments[0];

        obj_categories = arguments[1];

        for (x = 0; x < filter_array.length; x += 1) {

            for (y = 0; y < obj_categories.length; y += 1) {
                if (obj_categories[y] === filter_array[x]) {
                    return true;
                }
            }
        }

        var filter_array, obj_categories
    }

    function checkActivityDate(a_date) {
        var today = new Date();
        var a_day = new Date(a_date);
        var state;
        if (today.getFullYear() > a_day.getFullYear()) {
            state = 'past';
        } else if (today.getFullYear() <= a_day.getFullYear()) {
            state = 'future';
        }

        return state;
    }

    function synathina(url) {
        synathina_text = [];
        $.ajax({
            type: 'GET',
            url: url,
            async: false,
            dataType: 'json',
            success: function(data) {}
        }).done(function(data, statusText, resObject) {
            synathina_text = stegiText(data);
        });
        return synathina_text;
    }

    function stegiText(myArr) {
        titlesArray = createTitlesArray(myArr);
        var synathina_text = '<div style="overflow:auto; background-color:#FFF"><div class="info info-stegi-container"><h3>Δράσεις που πραγματοποιούνται στη στέγη του συνΑθηνά</h3><img src="/templates/synathina/images/stegi-pin-image.jpg"/>';
        for (var i = 0; i < titlesArray.length; i += 1) {
            var synathina_text = synathina_text + '<div class="info-title-stegi info-title--address"><a href="' + titlesArray[i].url + '">' + titlesArray[i].title + '</a><div class="info-meta"> <span class="info-date">' + titlesArray[i].dates + '</span></div><div class="info-source"><a href="' + titlesArray[i].team_url + '">' + titlesArray[i].team_name + '</a></div></div>';
        }
        var synathina_text = synathina_text + '</div></div>';
        return synathina_text;
    }

    function Activity(map, myLatLng, data) {
        /**
         * [marker description]
         * @type {google}
         * @param {obj} [db_content] [data records that came from server's database]
         */
        var state = null;
        var src = icons.future.icon;
        state = checkActivityDate(data.date_end);

        if (state === 'future') {
            src = icons.future.icon;
        }
        if (state === 'past') {
            src = icons.past.icon;
        }
        var marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            db_data: {
                title: data.title,
                date: new Date(data.date),
                address: data.address,
                team_name: data.team_name,
                id: data.id,
                is_featured: data.is_featured,
                slug: data.slug,
                url: data.url,
                team_url: data.team_url,
                category_id: data.category_id,
                category_name: data.category_name,
                team_id: data.team_id,
                action_id: data.action_id,
                team_name: data.team_name,
                team_members: data.team_members,
                sponsor_title: data.sponsor_title,
                date: data.date,
                date_end: data.date_end,
                dates: data.dates,
                title: data.title,
                content: data.content,
                content_img: data.content_img,
                logo: data.log,
                logo_sponsor: data.logo_sponsor,
                logo_team: data.logo_team,
                coordinates: data.coordinates
            },
            icon: src
        });

        // TEMPLATE  INFO WINDOW
        var newurl = '/index.php?option=com_actions&view=action&id=' + data.action_id + '&Itemid=138';
        if (data.coordinates[0] == '37.980522' && data.coordinates[1] == '23.726839') {
            var contentString = synathina_var;
        } else {
            if (data.logo_sponsor != '') {
                var contentString = '<div class="info"> <div class="info-title info-title--address"><a href="' + data.url + '">' + data.title + '</a></div> <div class="info-meta"><span class="info-address">' + data.address + '</span>, <span class="info-date">' + data.dates + '</span></div> <div class="info-source">' + data.team_name + '</div> <div class="info-img"><img src="' + data.content_img + '""></div> <div class="info-description">' + data.content + ' <a href="' + data.url + '">περισσότερα</a> </div> <div class="info-badge"> <div class="info-badge-item info-badge-item--sponsor-logo"> <i class="fill" style="background-image:url(' + data.logo_team + ')"></i> </div> <div class="info-badge-item info-badge-item--team-logo" id="sponsor_' + data.action_id + '"> <i class="fill" style="background-image:url(' + data.logo_sponsor + ')"></i> </div> <div class="info-badge-item info-badge-item--team-power"> <div class="fill1">' + data.team_members + '</div> </div> </div> </div>';
            } else {
                var contentString = '<div class="info"> <div class="info-title info-title--address"><a href="' + data.url + '">' + data.title + '</a></div> <div class="info-meta"><span class="info-address">' + data.address + '</span>, <span class="info-date">' + data.dates + '</span></div> <div class="info-source">' + data.team_name + '</div> <div class="info-img"><img src="' + data.content_img + '""></div> <div class="info-description">' + data.content + ' <a href="' + data.url + '">περισσότερα</a> </div> <div class="info-badge"> <div class="info-badge-item info-badge-item--sponsor-logo"> <i class="fill" style="background-image:url(' + data.logo_team + ')"></i> </div> <div class="info-badge-item info-badge-item--team-power"> <div class="fill1">' + data.team_members + '</div> </div> </div> </div>';
            }
        }

        this.marker = marker;
        // et : added : 07/07/2016
        // get the width of the viewport/window [size]
        var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);

        this.marker.addListener('click', function() {
            map.infoWindow.setContent(contentString)
            map.infoWindow.open(map, this);
            // hide cross on click-window open, if browser width is less than 420
            if (w < '420') {
                $('.cross').css({
                    display: 'none'
                });
                $('.categories').css({
                    display: 'none'
                });
                $('.logo-container').css({
                    display: 'none'
                });
                $('.hamburger').css({
                    display: 'none'
                });
            }
        });
    }

    function populateActivities(data) {
        try {
            dat = JSON.parse(data.response);
        } catch (e) {
            //JSON parse error, this is not json
            console.log(e)
            var test = eval("(" + JSON.parse(data.response) + ")");
            return false;
        }

        collection = JSON.parse(data.response);

        coordinatesArray = createMarkersArray(collection);

        //Check for activities that have identical coordinates and slighly alter them
        markersArrayAltered = [];
        //put first element in altered array
        if (coordinatesArray.length > 0) {
            markersArrayAltered.push(coordinatesArray[0]);
        }

        //iterate through all elements starting from the 2nd one
        for (var i = 1; i < coordinatesArray.length; i++) {
            //if coordinates do not take place at stegi and are identical with previous ones alter them
            if (coordinatesArray[i]['lat'] == coordinatesArray[i - 1]['lat']
                && coordinatesArray[i]['lng'] == coordinatesArray[i - 1]['lng']
                && coordinatesArray[i]['lat']
                && coordinatesArray[i]['lng']
                && coordinatesArray[i]['lat'] != '37.980522'
                && coordinatesArray[i]['lng'] != '23.726839') {
                //create a temp array for storing altered values
                coords_identical = [];
                coords_identical.lat = coordinatesArray[i]['lat'] +  (Math.random() - .5) / 1500;
                coords_identical.lng = coordinatesArray[i]['lng'] + (Math.random() - .5) / 1500;
                markersArrayAltered.push(coords_identical);
            } else {
                markersArrayAltered.push(coordinatesArray[i]);
            }
        }
        coordinatesArray = markersArrayAltered;

        for (var i = 0; i < coordinatesArray.length; i += 1) {
            var point = new google.maps.LatLng(coordinatesArray[i].lat, coordinatesArray[i].lng);
            // create activities with marker object as property
            activities[i] = new Activity(map, coordinatesArray[i], collection.features[i]);
            activities[i].marker.setVisible(false);
        }

        // INIT CATEGORIES
        Categories.init(collection);
        showActivitiesOnInit();
    }

    function showActivitiesOnInit() {

        var markers = [];

        for (var i = 0; i < coordinatesArray.length; i += 1) {
            activities[i].marker.setVisible(true);
            markers.push(activities[i].marker);
        }

        window.markerCluster = new MarkerClusterer(map, markers, {
            gridSize: 50,
            maxZoom: 14,
            imagePath: '/images/template/m'
        });

    }

    function setActivitiesVisibility() {
        var allMarkers = activities;
        Activities.current_polygon = arguments[0];
        polygon = arguments[0];
        cat_filter = arguments[1];

        //clear all markers from init markerCluster created from the above function
        if (window.markerCluster !== undefined) {
            window.markerCluster.clearMarkers();
        }

        //Clusterer
        if (window.clusterer !== undefined) {
            window.category.length = 0;
            window.clusterer.clearMarkers();
        }

        window.category = [];

        window.clusterer = new MarkerClusterer(map, [], {
            gridSize: 50,
            maxZoom: 14,
            imagePath: '/images/template/m'
        });

        /**
         * [if user clicks on polygon and already have used filtering, use the latter year ranges also for the next polygon]
         * @param  {[boolean]} Activities.is_filtered []
         */

        if (Activities.is_filtered_bySlider == true && Activities.yearRangeBuffer !== null && cat_filter === undefined) {
            for (var i = 0; i < coordinatesArray.length; i += 1) {
                activities[i].marker.setVisible(false)
            }
            filterActivities(Activities.yearRangeBuffer);

        } else {
            var thematic_check_exists = 0;
            $('.categories input[type=checkbox]').each(function() {
                if (this.checked) {
                    thematic_check_exists = 1;
                }
            });

            if (cat_filter !== undefined && thematic_check_exists == 1) {
                Filter.run = true;
                var allMarkers = [];
                for (var i = 0; i < coordinatesArray.length; i += 1) {

                    var point = new google.maps.LatLng(coordinatesArray[i].lat, coordinatesArray[i].lng);
                    if (polygon.Contains(point)) {
                        index = findIn(cat_filter, activities[i].marker.db_data.category_id);
                        if (index) {
                            activities[i].marker.setVisible(true);
                            category.push(activities[i].marker);
                        } else {
                            activities[i].marker.setVisible(false);
                        }
                    }
                }
                //console.log(category.length);
                // pushing markers to cluster
                clusterer.clearMarkers();
                clusterer.addMarkers(category);
                category.length = 0;

            } else {

                for (var i = 0; i < coordinatesArray.length; i += 1) {

                    var point = new google.maps.LatLng(coordinatesArray[i].lat, coordinatesArray[i].lng);

                    if (polygon.Contains(point)) {
                        activities[i].marker.setVisible(true);
                        checkSameLocation([coordinatesArray[i].lat, coordinatesArray[i].lng])
                        category.push(activities[i].marker);
                    } else {
                        activities[i].marker.setVisible(false);
                    }
                }

                clusterer.clearMarkers();
                clusterer.addMarkers(category);
                category.length = 0;
            }

            // threading with Filter
            saFilter = Filter(activities);
            //saFilter.initSliderFilter();
            saFilter.initCategoryFilters();


        }
    }

    function checkSameLocation(a) {
    }

    function createLocationSpace(a) {
    }

    function filterActivities(dates, filter) {
        Filter.run = true;
        var date_from = Math.floor(dates[0]);
        var date_to = Math.floor(dates[1]);

        if (window.clusterer !== undefined) {
            window.category.length = 0;
            window.clusterer.clearMarkers();
        }

        window.category = [];
        window.clusterer = new MarkerClusterer(map, [], {
            gridSize: 50,
            maxZoom: 14,
            imagePath: '/images/template/m'
        });

        for (var i = 0; i < coordinatesArray.length; i += 1) {
            var point = new google.maps.LatLng(coordinatesArray[i].lat, coordinatesArray[i].lng);
            if (Activities.current_polygon.Contains(point)) {
                if (activities[i].year >= date_from && activities[i].year <= date_to) {
                    activities[i].marker.setVisible(true);
                    category.push(activities[i].marker);
                } else {
                    activities[i].marker.setVisible(false);
                }
            }
        }
        clusterer.clearMarkers();
        clusterer.addMarkers(category);
        category.length = 0;

    }

    function createMarkersArray() {
        InputObj = arguments[0];

        for (var i = 0; i < InputObj.features.length; i += 1) {
            if (InputObj.features[i].coordinates[0]){
                markersArray.push({
                    lat: InputObj.features[i].coordinates[0],
                    lng: InputObj.features[i].coordinates[1],
                });
            }
        }

        return markersArray;
    }

    function createTitlesArray() {
        InputObj = arguments[0];

        for (var i = 0; i < InputObj.features.length; i += 1) {
            if (InputObj.features[i].coordinates[0] == '37.980522' && InputObj.features[i].coordinates[1] == '23.726839') {
                titlesArray.push({
                    url: InputObj.features[i].url,
                    team_url: InputObj.features[i].team_url,
                    title: InputObj.features[i].title,
                    action_id: InputObj.features[i].action_id,
                    team_id: InputObj.features[i].team_id,
                    team_name: InputObj.features[i].team_name,
                    dates: InputObj.features[i].dates,
                    address: InputObj.features[i].address,
                });
            }
        }
        return titlesArray;
    }



    function init() {
        var lang = document.getElementsByTagName('html')[0].getAttribute('lang');
        AjaxCall.get('/actions.php?lang=' + lang, populateActivities);
        window.synathina_var = synathina('/actions_stegi.php?lang=' + lang);
    }


    var res, collection, activities = [],
        markersArray = [],
        titlesArray = [],
        coordinatesArray, polygon, contentString, saFilter, saSlider, current_polygon, cachedMarkers = [],
        markerClusterer, categories;

    var iconBase = '/templates/synathina/img/markers/';
    var icons = {
        past: {
            icon: iconBase + 'marker_grey.png'
        },
        future: {
            icon: iconBase + 'marker_orange.png'
        }
    };

    global.EVT.on('init', init);

    return {
        init: init,
        filterActivities: filterActivities,
        setActivitiesVisibility: setActivitiesVisibility,
        current_polygon: current_polygon,
        is_filtered_bySlider: false,
        yearRangeBuffer: null,
    }

})(window);
