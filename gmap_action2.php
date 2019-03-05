<?php
/**
 * @package    Core.Site
 *
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Define the application's minimum supported PHP version as a constant so it can be referenced within the application.
 */
define('JOOMLA_MINIMUM_PHP', '5.3.10');

if (version_compare(PHP_VERSION, JOOMLA_MINIMUM_PHP, '<'))
{
    die('Your host needs to use PHP ' . JOOMLA_MINIMUM_PHP . ' or higher to run this version of Core');
}

// Saves the start time and memory usage.
$startTime = microtime(1);
$startMem  = memory_get_usage();

/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
define('_JEXEC', 1);

if (file_exists(__DIR__ . '/defines.php'))
{
    include_once __DIR__ . '/defines.php';
}

if (!defined('_JDEFINES'))
{
    define('JPATH_BASE', __DIR__);
    require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_BASE . '/includes/framework.php';

// Set profiler start time and memory usage and mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->setStart($startTime, $startMem)->mark('afterLoad') : null;
date_default_timezone_set('Europe/Athens');

// Instantiate the application.
$app = JFactory::getApplication('site');
$config = JFactory::getConfig();

$google_api_key = $config->get('google_api');
if ( $config->get('dev_mode') == 1) {
    $google_api_key = $config->get('dev_google_api');
}

//get lang variables
$lang = JFactory::getLanguage();
$lang_code_array = explode('-', $lang->getTag());
$lang_code = $lang_code_array[0];

?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php echo ($lang_code == 'en' ? 'Submit address' : 'Καταχώριση διεύθυνσης'); ?></title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
.gm-style-mtc {
  display: none;
}
      #map {
        height: 100%;
      }
      .controls {
        margin-top: 10px;
        border: 1px solid transparent;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        height: 32px;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
      }

      #pac-input {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 300px;
      }

      #pac-input:focus {
        border-color: #4d90fe;
      }

      .pac-container {
        font-family: Roboto;
      }

      #type-selector {
        color: #fff;
        background-color: #4d90fe;
        padding: 5px 11px 0px 11px;
      }

      #type-selector label {
        font-family: Roboto;
        font-size: 13px;
        font-weight: 300;
      }
            .searchbox-searchbutton {
                    content: '';
                    display: block;
                    width: 32px;
                    height: 32px;
                    background: url(//maps.gstatic.com/tactile/omnibox/quantum_search_button-20150825-1x.png);
                    background-size: 100px 32px;
                    background-color:#FFF;
            }
    </style>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script type="text/javascript">
            function updateParent() {
                if(document.getElementById('pac-input').value=='') {
                    alert('<?php echo ($lang_code == 'en' ? 'Please type an address' : 'Παρακαλώ πληκτρολογήστε μία διεύθυνση'); ?>');
                    return false;
                }
                if(document.getElementById('pac-input').value!='' && document.getElementById('lat').value=='37.980510' && document.getElementById('lng').value=='23.726836'){
                    alert('<?php echo ($lang_code == 'en' ? 'Please type a valid address' : 'Παρακαλώ πληκτρολογήστε μία έγκυρη διεύθυνση'); ?>');
                    return false;
                }
                      opener.document.create_action.<?php echo @$_REQUEST['lng_name']; ?>.value = document.search.lng.value;
                    opener.document.create_action.<?php echo @$_REQUEST['lat_name']; ?>.value = document.search.lat.value;
                    opener.document.create_action.<?php echo @$_REQUEST['address_name']; ?>.value = document.getElementById('pac-input').value;
                    self.close();
                    return false;
            }
        </script>
  </head>
  <body>
    <input id="pac-input" class="controls" type="text"  placeholder="<?php echo ($lang_code == 'en' ? 'Search an address' : 'Αναζήτηση διεύθυνσης'); ?>" value="<?=(@$_REQUEST['address']!=''?@$_REQUEST['address']:'')?>" style="width:50%;" >
        <!--<input id="submit" type="submit" aria-label="Search" class="controls searchbox-searchbutton" value="" />-->
        <form id="search" name="search" onsubmit="return updateParent();">
            <input id="lat" name="lat" type="hidden" value="<?=(@$_REQUEST['lat']!=''?@$_REQUEST['lat']:'37.980510')?>" />
            <input id="lng" name="lng" type="hidden" value="<?=(@$_REQUEST['lng']!=''?@$_REQUEST['lng']:'23.726836')?>" />
            <input type="submit" name="submit" value="<?php echo ($lang_code == 'en' ? 'Save' : 'Αποθήκευση'); ?>" style="margin-top:8px; padding:8px; cursor:pointer;" />
        </form>
    <!--<div id="type-selector" class="controls">
      <input type="radio" name="type" id="changetype-all" checked="checked">
      <label for="changetype-all">All</label>

      <input type="radio" name="type" id="changetype-establishment">
      <label for="changetype-establishment">Establishments</label>

      <input type="radio" name="type" id="changetype-address">
      <label for="changetype-address">Addresses</label>

      <input type="radio" name="type" id="changetype-geocode">
      <label for="changetype-geocode">Geocodes</label>
    </div>-->
    <div id="map"></div>

    <script>
      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

      function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: <?=(@$_REQUEST['lat']!=''?@$_REQUEST['lat']:'37.980510')?>, lng: <?=(@$_REQUEST['lng']!=''?@$_REQUEST['lng']:'23.726836')?>},
          zoom: <?=(@$_REQUEST['address']!=''?15:14)?>
        });
        var input = /** @type {!HTMLInputElement} */(
            document.getElementById('pac-input'));
                var form = /** @type {!HTMLInputElement} */(
            document.getElementById('search'));

        var types = document.getElementById('type-selector');
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(form);

        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);

        var infowindow = new google.maps.InfoWindow();
        var marker = new google.maps.Marker({
          map: map,
                    position: {lat: <?=(@$_REQUEST['lat']!=''?@$_REQUEST['lat']:'37.980510')?>, lng: <?=(@$_REQUEST['lng']!=''?@$_REQUEST['lng']:'23.726836')?>},
                    draggable: true,
          anchorPoint: new google.maps.Point(0, -29)
        });
                markerCoords(marker);

                /*document.getElementById('submit').onclick = function () {
                        var input = document.getElementById('pac-input');
                        google.maps.event.trigger(input, 'focus')
                        google.maps.event.trigger(input, 'keydown', {
                                keyCode: 13
                        });
                };*/

        autocomplete.addListener('place_changed', function() {
          infowindow.close();
          marker.setVisible(false);
          var place = autocomplete.getPlace();
          if (!place.geometry) {
            window.alert("Παρακαλώ επιλέξτε μία από τις διαθέσιμες διευθύνσεις");
            return;
          }

          // If the place has a geometry, then present it on a map.
          if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
                        map.setCenter(place.geometry.location);
                        document.getElementById('lat').value=place.geometry.location.lat();
                        document.getElementById('lng').value=place.geometry.location.lng();
          } else {
            map.setCenter(place.geometry.location);
                        //console.log(place.geometry.location.lat());
                        //document.getElementById('latlng').value=place.geometry.location.lat()+','+place.geometry.location.lng();
                        document.getElementById('lat').value=place.geometry.location.lat();
                        document.getElementById('lng').value=place.geometry.location.lng();
            map.setZoom(15);  // Why 17? Because it looks good.
          }
          marker.setIcon(/** @type {google.maps.Icon} */({
            url: place.icon,
            size: new google.maps.Size(71, 71),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(17, 34),
            scaledSize: new google.maps.Size(35, 35)
          }));
          marker.setPosition(place.geometry.location);
          marker.setVisible(true);

          var address = '';
          if (place.address_components) {
            address = [
              (place.address_components[0] && place.address_components[0].short_name || ''),
              (place.address_components[1] && place.address_components[1].short_name || ''),
              (place.address_components[2] && place.address_components[2].short_name || '')
            ].join(' ');
          }

          infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
          infowindow.open(map, marker);
        });

        // Sets a listener on a radio button to change the filter type on Places
        // Autocomplete.
        function setupClickListener(id, types) {
          var radioButton = document.getElementById(id);
          radioButton.addEventListener('click', function() {
            autocomplete.setTypes(types);
          });
        }

        setupClickListener('changetype-all', []);
        //setupClickListener('changetype-address', ['address']);
        //setupClickListener('changetype-establishment', ['establishment']);
        //setupClickListener('changetype-geocode', ['geocode']);
      }
            function markerCoords(markerobject){
                    google.maps.event.addListener(markerobject, 'dragend', function(evt){
                        //console.log(evt.latLng.lat().toFixed(3));
                        //console.log(evt.latLng.lng().toFixed(3));
                        //console.log(document.getElementById('pac-input').value);
                        //document.getElementById('latlng').value=evt.latLng.lat().toFixed(6)+','+evt.latLng.lng().toFixed(6);
                        document.getElementById('lat').value=evt.latLng.lat().toFixed(6);
                        document.getElementById('lng').value=evt.latLng.lng().toFixed(6);
                            //map.setOptions({
                                    //content: '<p>Marker dropped: Current Lat: ' + evt.latLng.lat().toFixed(3) + ' Current Lng: ' + evt.latLng.lng().toFixed(3) + '</p>'
                                //alert(evt.latLng.lat().toFixed(3)+' '+evt.latLng.lng().toFixed(3))
                            //});
                            //map.open(map, markerobject);
                    });

                    google.maps.event.addListener(markerobject, 'drag', function(evt){
                            //console.log("marker is being dragged");
                    });
            }

    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_api_key; ?>&libraries=places&callback=initMap"
        async defer></script>
  </body>
</html>


