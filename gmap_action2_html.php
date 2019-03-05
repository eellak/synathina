<?php
	$f=1;
	$ff=2;
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Καταχώριση διεύθυνσης</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
<script>
function openChild(file,window,id,lat,lng) {
	file=file+'?address_name='+id+'&lng_name='+lng+'&lat_name='+lat+'&lat='+document.getElementById(lat).value+'&lng='+document.getElementById(lng).value+'&address='+document.getElementById(id).value;
  var left = (screen.width/2)-(750/2);
  var top = (screen.height/2)-(550/2);	
	childWindow=open(file,window,'resizable=no,width=750,height=550, top='+top+', left='+left);
	if (childWindow.opener == null) childWindow.opener = self;
} 
</script>		
	</head>
	<body>
	<form action="#" class="form" method="post" name="create_action" enctype="multipart/form-data">
		<input type="text" name="address_<?php echo $f; ?>" id="address_<?php echo $f; ?>" onclick="openChild('gmap_action2.php','win<?php echo $f; ?>',this.id,'lat_<?php echo $f; ?>','lng_<?php echo $f; ?>')" style="width:80%" />
		<input type="hidden" name="lat_<?php echo $f; ?>" id="lat_<?php echo $f; ?>" value="" />
		<input type="hidden" name="lng_<?php echo $f; ?>" id="lng_<?php echo $f; ?>" value="" />	
		<input type="text" name="address_<?php echo $ff; ?>" id="address_<?php echo $ff; ?>" onclick="openChild('gmap_action2.php','win<?php echo $ff; ?>',this.id,'lat_<?php echo $ff; ?>','lng_<?php echo $ff; ?>')" style="width:80%" />
		<input type="hidden" name="lat_<?php echo $ff; ?>" id="lat_<?php echo $ff; ?>" value="" />
		<input type="hidden" name="lng_<?php echo $ff; ?>" id="lng_<?php echo $ff; ?>" value="" />
	</form>
	</body>
</html>
	