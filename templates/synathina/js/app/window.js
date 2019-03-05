var Info = (function(global) {

    function createWindow(args) {
        info_window = new google.maps.InfoWindow({
            pixelOffset: new google.maps.Size(0, 0),
            zIndex : 99999
        });

        google.maps.event.addListener(info_window, 'domready', function() {

            // Reference to the DIV that wraps the bottom of infowindow
            var iwOuter = $('.gm-style-iw');

            $('.gm-style-iw').each(function(){
                $(this).css({
                  //width: window.getComputedStyle(this,':before').width+'px'
                  width: $(this).find('.info').width()
                })
            });

            // imedieate child
            iwOuter.find(' > div').css({
            })

            var iwBackground = iwOuter.prev();

            // Removes background shadow DIV
            iwBackground.children(':nth-child(2)').css({'display' : 'none'});

            // white background div
            iwBackground.children(':nth-child(4)').css({'display' : 'none'});

            // move info
            iwOuter.parent().parent().css({top: '0px'});

            // Arrow
            iwBackground.children(':nth-child(1)').attr('style', function(i,s){ return s + 'left: 76px !important; width: 0px; border-right-width: 10px; border-right-style: solid; border-right-color: transparent; border-left-width: 10px; border-left-style: solid; border-left-color: transparent; border-top-width: 24px; border-top-style: solid; border-top-color: rgba(255, 255, 255, 1); position: absolute; -ms-transform: translateY(10px) rotate(6.5deg) !important; -webkit-transform: translateY(10px) rotate(6.5deg)!important; transform: translateY(10px) rotate(6.5deg) !important; opacity: 1 !important;' });
            //Arrow
            iwBackground.children(':nth-child(3)').attr('style', function(i,s){ return s + 'left: 76px !important; display: none !important'});

            // Changes the desired tail shadow color.
            iwBackground.children(':nth-child(3)').find('div').children().css({'box-shadow': 'rgba(72, 181, 233, 0.6) 0px 1px 6px', 'z-index' : '1' });

            var iwCloseBtn = iwOuter.next();
            iwCloseBtn.css({opacity: '1', right: '48px', top: '23px', 'font-size': '15px', 'line-height': '15px', 'color': '#000000', 'font-weight': 'bold'});
        	iwCloseBtn.append( "X" );
            //old styles:  border: '7px solid #48b5e9', 'border-radius': '13px', 'box-shadow': '0 0 5px #3990B9'

            // If the content of infowindow not exceed the set maximum height, then the gradient is removed.
            if($('.iw-content').height() < 140){
            //$('.iw-bottom-gradient').css({display: 'none'});
            }

            // The API automatically applies 0.7 opacity to the button after the mouseout event. This function reverses this event to the desired value.
            iwCloseBtn.mouseout(function(){
            $(this).css({opacity: '1'});
            });

            // et : added : 08/07/2016
            // show elements when info window is closed
            $('img[src="https://maps.gstatic.com/mapfiles/transparent.png"]').click(function() {
            	if($( window ).width()>480){
            		$('.cross').css({display: 'block'});
            	}
            	$('.categories').css({display: 'block'});
            	$('.gm-style-iw').css({display: 'none'});
            	$('.logo-container').css({display: 'block'});
            	$('.hamburger').css({display: 'block'});
            });

            // et : added : 07/07/2016
            // show elements when info window is closed
            iwCloseBtn.click(function(){
            	if($( window ).width()>480){
            		$('.cross').css({display: 'block'});
            	}
            	$('.categories').css({display: 'block'});
            	$('.gm-style-iw').css({display: 'none'});
            	$('.logo-container').css({display: 'block'});
            	$('.hamburger').css({display: 'block'});
            });
        });

        return info_window
    };

    //var contentString = '<div class="info"> <div class="info-title info-title--address">'+data.title+'</div> <div class="info-meta"><span class="info-address">'+data.address+'</span>, <span class="info-date">'+data.date+'</span></div> <div class="info-source">'+data.team_name+'</div> <div class="info-img"><img src="'+data.source+'></div> <div class="info-description"> <a href="">/περισσότερα</a> </div> <div class="info-badge"> <div class="info-badge-item info-badge-item--sponsor-logo"> <i class="fill"></i> </div> <div class="info-badge-item info-badge-item--team-logo"> <i class="fill"></i> </div> <div class="info-badge-item info-badge-item--team-power"> <i class="fill"></i> </div> </div> </div>';
    var info_window;

    return {
        createWindow : createWindow
    }

})(window);