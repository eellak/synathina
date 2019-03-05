$(window).mousemove(function(e) {
    var mouseY = e.pageY - $(window).scrollTop(); // mouse y coordinate relative to window
    if (mouseY < 500) {
        $('.debug .dev_menu').slideUp(50);
    } else {
        $('.debug .dev_menu').slideDown(50);
    }
});