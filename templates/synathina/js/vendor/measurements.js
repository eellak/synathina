/**
* MEASUREMENTS
*/
initMeasurements = function(){
	var MEASUREMENTS_ID = 'measurements';

	$("body").append('<div id="'+MEASUREMENTS_ID+'"></div>');
	$("#"+MEASUREMENTS_ID).css({
		'position': 'fixed',
		'bottom': '0',
		'right': '0',
		'background-color': 'black',
		'color': 'white',
		'padding': '5px',
		'font-size': '12px',
		'font-family' : 'Courier New',
		'font-weight' : 'bold',
		'opacity': '0.4',
		'z-index' : 99999
	});

	$("#"+MEASUREMENTS_ID).text(getDimensions());
	$(window).on("resize", function(){
		$("#"+MEASUREMENTS_ID).text(getDimensions());
	});
	(function(){
		$("#"+MEASUREMENTS_ID).text(getDimensions());
	});
}

function getDimensions(){
	var view_w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0)
	var view_h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0)
	return view_w+' x  '+view_h;
}
function getViewportHeight(){
	var view_h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0)
	return view_h;
}

initMeasurements();
