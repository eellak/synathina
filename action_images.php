<?php

if (@$_REQUEST['action_id']) {
	$images = [];
	foreach (glob('images/actions/'.@$_REQUEST['action_id'].'/*.*') as $filename) {
		$images[] = $filename;
	}

	echo json_encode($images);
}

?>