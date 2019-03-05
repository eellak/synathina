window.EVT = new EventEmitter2();

var App = (function(global){
    // init App on window load
    $(global).load(function(){
        EVT.emit('init');
    });
})(window);
