var Menu = (function (global) {

    function init() {

        body = document.querySelector('body');

        menu = document.querySelector("[rel=js-nav]");

        categories = document.querySelector("[rel*=js-toggle-categories]");
    };



    var menu, button, body, categories;



    EVT.on('init', init);



    return {

        init: init,

        initMenu: init

    }



})(window);
