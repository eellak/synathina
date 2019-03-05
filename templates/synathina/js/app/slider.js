var Slider = (function(global) {

   function init(){
      slider = document.querySelector("[rel='js-range-slider']");
   }

   var slider;

   return {
      init : init
   }

})(window)