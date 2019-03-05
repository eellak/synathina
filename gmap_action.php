<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
      	<title>Ανεύρεση διεύθυνσης μέσω Google Maps</title>
    <script src="https://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAA5QlJAN4wTR7ks9Dj0SrJ_BRKMDyeZRKrH0o9eDKdySZmTUMBShRTI86FKhH5PoDkiyaj9skKpjVn2A" type="text/javascript"></script>
    <script type="text/javascript">

 function load() {
      if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById("map"));
        map.addControl(new GSmallMapControl());
        map.addControl(new GMapTypeControl());
        var center = new GLatLng(<?=(@$_REQUEST['lat']!=''?$_REQUEST['lat']:'37.980522')?>,      <?=(@$_REQUEST['lng']!=''?$_REQUEST['lng']:'23.726839')?>);
        map.setCenter(center, 15);
        geocoder = new GClientGeocoder();
        var marker = new GMarker(center, {draggable: true});  
        map.addOverlay(marker);
        document.getElementById("lat").value = center.lat().toFixed(5);
        document.getElementById("lng").value = center.lng().toFixed(5);

	  GEvent.addListener(marker, "dragend", function() {
       var point = marker.getPoint();
	      map.panTo(point);
       document.getElementById("lat").value = point.lat().toFixed(5);
       document.getElementById("lng").value = point.lng().toFixed(5);

        });


	 GEvent.addListener(map, "moveend", function() {
		  map.clearOverlays();
    var center = map.getCenter();
		  var marker = new GMarker(center, {draggable: true});
		  map.addOverlay(marker);
		  document.getElementById("lat").value = center.lat().toFixed(5);
	   document.getElementById("lng").value = center.lng().toFixed(5);


	 GEvent.addListener(marker, "dragend", function() {
      var point =marker.getPoint();
	     map.panTo(point);
      document.getElementById("lat").value = point.lat().toFixed(5);
	     document.getElementById("lng").value = point.lng().toFixed(5);

        });
 
        });

      }
    }

	   function showAddress(address) {
	   var map = new GMap2(document.getElementById("map"));
       map.addControl(new GSmallMapControl());
       map.addControl(new GMapTypeControl());
       if (geocoder) {
        geocoder.getLatLng(
          address,
          function(point) {
            if (!point) {
              alert(address + " not found");
            } else {
		  document.getElementById("lat").value = point.lat().toFixed(5);
	   document.getElementById("lng").value = point.lng().toFixed(5);
		 map.clearOverlays()
			map.setCenter(point, 14);
   var marker = new GMarker(point, {draggable: true});  
		 map.addOverlay(marker);

		GEvent.addListener(marker, "dragend", function() {
      var pt = marker.getPoint();
	     map.panTo(pt);
      document.getElementById("lat").value = pt.lat().toFixed(5);
	     document.getElementById("lng").value = pt.lng().toFixed(5);
        });


	 GEvent.addListener(map, "moveend", function() {
		  map.clearOverlays();
    var center = map.getCenter();
		  var marker = new GMarker(center, {draggable: true});
		  map.addOverlay(marker);
		  document.getElementById("lat").value = center.lat().toFixed(5);
	   document.getElementById("lng").value = center.lng().toFixed(5);

	 GEvent.addListener(marker, "dragend", function() {
     var pt = marker.getPoint();
	    map.panTo(pt);
    document.getElementById("lat").value = pt.lat().toFixed(5);
	   document.getElementById("lng").value = pt.lng().toFixed(5);
        });
 
        });

            }
          }
        );
      }
    }
		
   function urlEncode( s )
   {
      return encodeURIComponent( s ).replace( /\%20/g, '+' ).replace( /!/g, '%21' ).replace( /'/g, '%27' ).replace( /\(/g, '%28' ).replace( /\)/g, '%29' ).replace( /\*/g, '%2A' ).replace( /\~/g, '%7E' );
   }		
		
	function updateParent() {
			//var frame1;
			//frame1='<iframe width="300" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?f=q&source=s_d&hl=en&geocode=&q='+urlEncode(document.getElementById('address').value)+'&aq=0&ie=UTF8&hq=&hnear='+document.getElementById('address').value+'&t=m&ll='+document.getElementById('lat').value+','+document.getElementById('lng').value+'&z=15&sll=38.007119,23.715363&iwloc=near&output=embed"></iframe>';
			opener.document.create_action.<?php echo @$_REQUEST['lng_name']; ?>.value = document.search.lng.value;
			opener.document.create_action.<?php echo @$_REQUEST['lat_name']; ?>.value = document.search.lat.value;
			opener.document.create_action.<?php echo @$_REQUEST['address_name']; ?>.value = document.form_address.address.value;
			if(document.form_address.address.value==''){
				alert('Παρακαλώ πληκτρολογήστε μία διεύθυνση');
				return false;
			}
			//opener.document.getElementById('show_frame').innerHTML = frame1;
			self.close();
			return false;
	}		
    </script>
  </head>

  
<body onload="load()" onunload="GUnload()" >

  <form action="#" onsubmit="showAddress(this.address.value); return false" name="form_address">
     <p>        
      <input type="text" size="60" name="address" id="address" value="<?=@$_REQUEST['address']?>" />
      <input type="submit" value="Αναζήτηση" />
      </p>
    </form>

 <p align="left">

 
 <table  bgcolor="#FFFFCC" width="100%">
  <tr>
    <td><b>Latitude</b></td>
    <td><b>Longitude</b></td>
  </tr>
  <tr>
		<form name="search" onsubmit="return updateParent();">
			<input name="lat" id="lat" value=""  type="text" />
			<input id="lng" name="lng" value=""  type="text" />
			<input type="submit" name="submit" value="Αποθήκευση" />
		</form>
  </tr>
</table>
 </p>
  <p>
	<div id="frame">
  <div align="center" id="map" style="width: 100%; min-height:300px"><br/></div>
	</div>
   </p>
  </div>
  </body>

</html>

