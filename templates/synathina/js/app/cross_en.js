var Cross = ( function(global){



   function showCross() {

      cross = document.querySelector("[rel='js-cross']");

      classie.addClass(cross, 'shown');

   }

   function hideCross() {

      cross = document.querySelector("[rel='js-cross']");

      classie.removeClass(cross, 'shown');

   }

   function getData() {

      details = arguments[0];

      renderData(details);

   }

   function renderData(data) {

      content_population = document.querySelector("[rel='js-population']");

      content_teams = document.querySelector("[rel='js-teams']");

      content_activities = document.querySelector("[rel='js-activities']");

      cross_left = document.querySelector('.cross-side.cross-side--left');

      cross_bottom = document.querySelector('.cross-side.cross-side--bottom');

			var fillColor1='#FFFFFF';

      content_population.style.background = data.fillColor;

      content_teams.style.background = data.fillColor1;

      //content_activities.style.background = data.fillColor;

      //cross_left.style.background = data.fillColor;

      //cross_bottom.style.background = data.fillColor;

      content_population.innerHTML = data.details.population;

      cross_left.innerHTML = '<div class="cross-content"><div class="is-tablecell">'+data.details.teams+'</div></div>';

			content_teams.innerHTML = 'Total from 2013';

      content_activities.innerHTML = data.details.activities;



   }

   var cross, details, content_population, content_teams, content_activities



   EVT.on( 'show-cross', showCross );

   EVT.on( 'hide-cross', hideCross );

   EVT.on('send-polygon-details', getData);



   return {

      showCross : showCross,

      hideCross : hideCross,

      getData : getData

   }



})(window)

