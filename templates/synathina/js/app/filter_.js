  var Filter = (function(){
    data = arguments[0];
    activities = arguments[0];

    function initSliderFilter() {
			var cusid_ele = document.getElementsByClassName('time');
			for (var i = 0; i < cusid_ele.length; ++i) {
					var item = cusid_ele[i];
					item.style.display='block';
			}

      $('div#slider3').html('');
      dateFormat = d3.time.format('%Y-%m-%d %H:%M:%S');
      numberFormat = d3.format('0');

      activities.forEach(function (d) {
        d.dd = dateFormat.parse(d.marker.db_data.date);
        d.year = d3.time.year(d.dd).getFullYear();
        d.month = d3.time.month(d.dd).getMonth()+1
      });

      dispatch = d3.dispatch('filter');
      accessor = function(d){return d.year};
      range = d3.extent(activities, accessor);
      if(Activities.yearRangeBuffer === null) {

        var today = new Date()
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0 WTF!!!
        var yyyy = today.getFullYear();

        //range[0] = today
        // + 1 year to make slider more usable
        range[1] += 1;
        d3.select("div#slider3")
          .call(d3.slider().axis(true).min(range[0]).max(range[1]).value([yyyy, range[1]])
          .on("slide",function(evt, value, activities){
              dispatch.filter(value);
              //hasRun = 1; //!!!!!
        }));
      } else {

        d3.select("div#slider3")
          .call(d3.slider().axis(true).min(range[0]).max(range[1]).value(range)
          .on("slide",function(evt, value, activities){
              dispatch.filter(value);
              hasRun = 1;
        }));
      }
      dispatch.on('filter',function(value){
        Activities.filterActivities(value);
        Activities.is_filtered_bySlider = true;
        Activities.yearRangeBuffer = value;

      });
countFacts();
	console.log('a');
      dc.renderAll()
    }

    // How many activities are in my app?
    function countFacts() {
      num = facts.groupAll().reduceCount().value();
	console.log('aaa'+num);
      return num;
    }

    function contains(needle) {

        var findNaN = needle !== needle;
        var indexOf;

        if(!findNaN && typeof Array.prototype.indexOf === 'function') {
            indexOf = Array.prototype.indexOf;
        } else {
            indexOf = function(needle) {
                var i = -1, index = -1;

                for(i = 0; i < this.length; i++) {
                    var item = this[i];

                    if((findNaN && item !== item) || item === needle) {
                        index = i;
                        break;
                    }
                }

                return index;
            };
        }

        return indexOf.call(this, needle) > -1;
    };

    function initCategoryFilters(){

        checkboxes = Filter.category_filters.category_NodeList;
        RECORDS = Filter.category_filters.category_entity;

        var filters = [];

        for ( var i = 1; i < checkboxes.length; i++ ){
            checkboxes[i].addEventListener('click', function(evt) {
              //hasRun = 1;
              //run = 'true';
              evt.stopPropagation();
              evt.stopImmediatePropagation();
                if(this.checked === false) {

                    filters.pop(parseInt(this.dataset.id));
                    Activities.setActivitiesVisibility(Activities.current_polygon, filters)
                } else if(this.checked === true) {
                    filters.push(parseInt(this.dataset.id));
                    Activities.setActivitiesVisibility(Activities.current_polygon, filters)
                    //evt.target.checked =false;
                }
            });
        }
    }


    var accesor, range, activities, category_filters, input_filters = [], hasRun = 0;
    //EVT.on('init-filter', setupFilter);

    return {
      initSliderFilter :initSliderFilter,
      initCategoryFilters :initCategoryFilters,
      count : countFacts,
      //filterByCategory : filterByCategory,
      activities,
      category_filters,
      input_filters,
      hasRun
    }

})
