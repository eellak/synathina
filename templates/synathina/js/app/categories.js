var Categories = (function(global){

   function init(){
      //data = arguments[0];
      var lang = document.getElementsByTagName('html')[0].getAttribute('lang');
      categoriesHTMLparent = document.querySelector("[rel*=js-create-categories]");
      AjaxCall.get('/categories_filters.php?lang='+lang, func);
   }
   function func(data){
      try {
         dat = JSON.parse(data.response);
      } catch(e) {
          //JSON parse error, this is not json
          console.log(e)
          var test = eval( "(" + JSON.parse(data.response) + ")" );
          return false;
      }

      collection = JSON.parse(data.response);

      createCategory(collection);

   }
   function createCategory(data){

      if(categories_created) {
         return false;
      }
      var exports = {}
      /**
      for( var i = 0; i < data.features.length; i += 1) {

         categories.push({
            category_id : parseInt(data.features[i].category_id),
            category_name : data.features[i].category_name,
         });
      }
      */

      for (var i = 0; i < data.length; i += 1) {
         categories.push({
            category_id : parseInt(data[i].category.id),
            category_name : data[i].category.name
         });
      }
      // append categories in DOM
      elements = renderCategory(uniqueCategory(categories));
      categories = uniqueCategory(categories);
      exports.category_NodeList = elements;
      exports.category_entity = categories;
      elements.length = 0;
      Filter.category_filters = exports;

   }

   function renderCategory(cat){
			//categoriesHTMLfilters.querySelector('form').insertAdjacentHTML('beforeend','<div class="filters_home">Φίλτρα</div>');
      categoriesHTMLparent.querySelector('form').insertAdjacentHTML('beforeend',
         '<div class="form-group" style="display:none" > <input id="catAll" data-id="catAll" type="checkbox"> <label for="catAll" class="label-horizontal">Check All</label> </div>'
      )
      for( var i = 0; i < cat.length; i ++){
         var categoriesTPL = '<div class="form-group filters-open"> <input id="cat'+cat[i].category_id+'" data-id="'+cat[i].category_id+'" type="checkbox"> <label for="cat'+cat[i].category_id+'" class="label-horizontal">'+cat[i].category_name+'</label> </div>';
         categoriesHTMLparent.querySelector('form').insertAdjacentHTML('beforeend', categoriesTPL)
      }
      setTimeout(function(){
         classie.addClass(categoriesHTMLparent, 'is-visible');
         categories_created = true;
      }, 200);
      //eventBinder();
      inputs = categoriesHTMLparent.querySelectorAll('input');

      return  inputs;
   }

  function uniqueCategory(cat) {
      var catNew = [];
      var counter=0;

      for(var i=0; i<cat.length; i++){
         var found=false;
         for(var j=0; j<catNew.length; j++){
            if(cat[i].category_id==catNew[j].category_id){
               found=true;
               break;
            }
         }
         if(!found){
            catNew[counter++]=cat[i];
         }

      }
      return catNew.sort(function(a, b){
         return parseInt(a.category_id) - parseInt(b.category_id);
      });
  }

   var data, categories = [], categoriesHTMLparent, categories_created = false;

   return {
      init: init,
      createCategory :createCategory
   }

})(window);
