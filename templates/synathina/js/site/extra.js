//$( window ).load(function() {
//$('.teams_slider_2').attr({'width':'600','height':'400'})
//});
//


$('#filters-closed').click(function() {
    $(".filters-open").toggle();
    $("#filters-closed-x").toggle();
});

$( "#request_stegi" ).submit(function( event ) {
    $.ajax({
        url: $( "#abspath" ).val()+'request_stegi.php',
        type: 'post',
        data: {'date_from':$( "#from_date" ).val(),'date_to':$( "#to_date" ).val(),'activity_description':$( "#activity_description" ).val(),'activity_title':$( "#activity_title" ).val()},
        success: function(data, status) {
            if(data == 1) {
                //alert('Η αίτηση σας καταχωρήθηκε, εντός 24 ωρών θα λάβετε απάντηση για το αίτημά σας');
                //alert('Η αίτηση σας καταχωρήθηκε.');
                if(!alert('Η αίτησή σας καταχωρίστηκε.')){location.reload();}
                //$.magnificPopup.close();
            }else if(data == 2) {
                alert('Παρακαλώ συμπληρώστε τα υποχρεωτικά πεδία');
            }else if(data == 3) {
                alert('Η ημερομηνία έναρξης πρέπει να είναι μικρότερη από την ημερομηνία λήξης');
            }
        },
        error: function(xhr, desc, err) {
        }
    });
    event.preventDefault();
    //abspath
});

$( ".stegi_use_exists" ).click(function( event ) {
    event.preventDefault();
    $.ajax({
        url: $( this ).attr('href')+'stegi_hours.php',
        type: 'post',
        data: {'stegi_date':$(this).attr('rel')},
        success: function(data, status) {
            if(data != '') {
                $( '#stegi_hours_popup' ).html(data);
                //$('#stegi_hours').magnificPopup({
                $.magnificPopup.open({
                    tClose: '',
                    items: {
                        src: '#stegi_hours_popup'
                    },
                    type: 'inline'
                });
                //alert('Η αίτηση σας καταχωρήθηκε, εντός 24 ωρών θα λάβετε απάντηση για το αίτημά σας');
                //$.magnificPopup.close();
            }else if(data == 2) {
                //alert('Παρακαλώ συμπληρώστε τα υποχρεωτικά πεδία');
            }
        },
        error: function(xhr, desc, err) {
        }
    });
    //event.preventDefault();
});

$('.stegi_use input:checkbox').click(function() {
    var tmp=$(this).attr("data-href");
    $("#address_fields_"+tmp).toggle();
    $(".show_stegi_hours_"+tmp).toggle();
});

$('#services_choice input:checkbox').click(function(e) {
    $("#service_list").toggle(); // simplifies to this
    if ($('#services_choice input:checkbox:checked').length > 0) {
        $('#services_message').attr('required', 'required');
    } else {
        $('#services_message').removeAttr('required');
    }
});

$('.support_fields input:checkbox').click(function() {
    var parent_id=this.id;
    var tmp=$(this).attr("data-href");
    $(tmp).toggle(); // simplifies to this

});

//$('#tokenize').tokenize({displayDropdownOnFocus:true, nbDropdownElements:1000});

var token_elem = $('#tokenize');
var func = function(a, b, c, d){
    this.onAddToken = smth;
    function smth(){
        var res = arguments[1].split('"').join('\'')
        $( "#logos_select" ).append('<img id="team_logo_img_'+arguments[0]+'" width="32" height="32" style="margin-right:5px;" title="'+res+'" src="'+$('#team_logo_'+arguments[0]).attr('rel')+'" alt="" />');
        //console.log(arguments);
    }
}
var func1 = function(a, b){
    $('#team_logo_img_'+arguments[0]).remove();
    //console.log(arguments);
}
token_elem.tokenize({
    displayDropdownOnFocus:true,
    nbDropdownElements:1000,
    onDropdownAddItem : func,
    onRemoveToken : func1
})

var token_elem = $('#tokenize1');
var func2 = function(a, b, c, d){
    this.onAddToken = smth;
    function smth(){
        var res = arguments[1].split('"').join('\'')
        $( "#logos_select1" ).append('<img id="team_logo_img1_'+arguments[0]+'" width="32" height="32" style="margin-right:5px;" title="'+res+'" src="'+$('#team_logo_'+arguments[0]).attr('rel')+'" alt="" />');
        //console.log(arguments);
    }
}
var func3 = function(a, b){
    $('#team_logo_img1_'+arguments[0]).remove();
    //console.log(arguments);
}
token_elem.tokenize({
    displayDropdownOnFocus:true,
    nbDropdownElements:1000,
    onDropdownAddItem : func2,
    onRemoveToken : func3
})


$('#opencall_date').datetimepicker({
    dateFormat: 'dd/mm/yy',
    timeFormat: '',
    timeInput: false,
    minDate: new Date(),
    closeText: 'Αποθήκευση',
    showHour: false,
    showMinute: false,
    showTime:false
    /*hourMin: 8,
    hourMax: 16*/
});

$('#opencall_date_edit').datetimepicker({
    dateFormat: 'dd/mm/yy',
    timeFormat: '',
    timeInput: false,
    minDate: new Date(),
    closeText: 'Αποθήκευση',
    showHour: false,
    showMinute: false,
    showTime:false
    /*hourMin: 8,
    hourMax: 16*/
});

$('.from_date').datetimepicker({
    dateFormat: 'dd/mm/yy',
    timeFormat: 'HH:mm',
    stepMinute: 30,
    minuteGrid: 30,
    timeInput: false,
    /*minDate: new Date(),*/
    closeText: 'Αποθήκευση',
    currentText: 'Τώρα',
    timeText: 'Από',
    hourText: 'Ώρα',
    minuteText: 'Λεπτά'
    /*hourMin: 8,
    hourMax: 16*/
});

$('.to_date').datetimepicker({
    dateFormat: 'dd/mm/yy',
    timeFormat: 'HH:mm',
    stepMinute: 30,
    minuteGrid: 30,
    timeInput: false,
    /*minDate: new Date(),*/
    closeText: 'Αποθήκευση',
    currentText: 'Τώρα',
    timeText: 'Έως',
    hourText: 'Ώρα',
    minuteText: 'Λεπτά'
    /*hourMin: 8,
    hourMax: 16*/
});

$('.from_date_edit').datetimepicker({
    dateFormat: 'dd/mm/yy',
    timeFormat: 'HH:mm',
    stepMinute: 30,
    minuteGrid: 30,
    timeInput: true,
    closeText: 'Αποθήκευση',
    currentText: 'Τώρα',
    timeText: 'Από',
    hourText: 'Ώρα',
    minuteText: 'Λεπτά'
    /*hourMin: 8,
    hourMax: 16*/
});

$('.to_date_edit').datetimepicker({
    dateFormat: 'dd/mm/yy',
    timeFormat: 'HH:mm',
    stepMinute: 30,
    minuteGrid: 30,
    timeInput: true,
    closeText: 'Αποθήκευση',
    currentText: 'Τώρα',
    timeText: 'Έως',
    hourText: 'Ώρα',
    minuteText: 'Λεπτά'
    /*hourMin: 8,
    hourMax: 16*/
});

$(function () {
    $(document).tooltip({
        content: function () {
            return $(this).prop('title');
        }
    });
});
$('.form-tooltip-jquery').click(function(e){
    e.preventDefault();
});

$('.book-stegi').magnificPopup({
    type: 'inline',
    preloader: false,
    tClose: ''
})

$('.form-tooltip').magnificPopup({
    type: 'inline',
    preloader: false,
    tClose: ''
});

$('.newsletter-tooltip').magnificPopup({
    type: 'inline',
    preloader: false,
    tClose: ''
});

$('.opencall-tooltip').magnificPopup({
    type: 'inline',
    preloader: false,
    tClose: ''
});

$('.magnifying').magnificPopup({
    type: 'image',
    tClose: ''
});
$('.magnifying-gallery').magnificPopup({
    type: 'image',
    tClose: '',
    gallery: {
        enabled: true,
        navigateByImgClick: true,
        preload: [0,1] // Will preload 0 - before current, and 1 after the current image
    },
    image: {
        tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
        titleSrc: ''
    }
});
$('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
    disableOn: 700,
    tClose: '',
    type: 'iframe',
    mainClass: 'mfp-fade',
    removalDelay: 160,
    preloader: false,

    fixedContentPos: false
});

//registration form validations
$('#box2').change(function(){
    if($('#box2').is(":checked")){
        $('.hidden-team').show();
    }else{
        $("#hidden_team").attr('checked', false);
        $('.hidden-team').hide();
    }
});
$('#box1').change(function(){
    if($('#box1').is(":checked")){
        $("#hidden_team").attr('checked', false);
        $('.hidden-team').hide();
    }
});
$(document).ready(function (){
    $( "#member-registration" ).on('submit', function(e) {
        e.preventDefault();
        //photos validation
        if (window.File && window.FileReader && window.FileList && window.Blob)
        {
            var ValidImageTypes = ["image/gif", "image/jpeg", "image/png"];

            //logo validation
            if($('#box5').val()!=''){
                var fsize = $('#box5')[0].files[0].size;
                if(fsize>1048576){
                    //do something if file size more than 1 mb (1048576){
                    alert("Παρακαλώ εισάγετε φωτογραφία λογότυπου μικρότερου μεγέθους");
                    return false;
                }
                var fileType = $('#box5')[0].files[0].type;
                if ($.inArray(fileType, ValidImageTypes) < 0) {
                    //do something if file is not a valid image
                    alert("Παρακαλώ εισάγετε αρχείο jpg, png ή gif");
                    return false;
                }
            }
            //gallery validation one by one
            if($('#gallery_upload').val()!=''){
                //get the file size and file type from file input field
                var galleryInput = document.getElementById("gallery_upload");
                var galleryInputFiles = galleryInput.files;
                for(p=0; p<galleryInputFiles.length; p++){
                    var fsize = galleryInputFiles[p].size;
                    if(fsize>1048576){
                        //do something if file size more than 1 mb (1048576){
                        alert("Παρακαλώ εισάγετε φωτογραφία μικρότερου μεγέθους");
                        return false;
                    }
                    var fileType = galleryInputFiles[p].type;
                    if ($.inArray(fileType, ValidImageTypes) < 0) {
                        //do something if file is not a valid image
                        alert("Παρακαλώ εισάγετε μόνο αρχεία φωτογραφίας");
                        return false;
                    }
                }
            }
            //files validation one by one
            if($('#files_upload').val()!=''){
                //get the file size and file type from file input field
                var filesInput = document.getElementById("files_upload");
                var filesInputFiles = filesInput.files;
                for(p=0; p<filesInputFiles.length; p++){
                    var fsize = filesInputFiles[p].size;
                    if(fsize>1048576){
                        //do something if file size more than 1 mb (1048576){
                        alert("Παρακαλώ εισάγετε αρχείο μικρότερου μεγέθους");
                        return false;
                    }
                }
            }
        }

        //alert( "Handler for .submit() called." );
        $('#jform_username').val($('#jform_email1').val());
        if($('#box1').is(':checked')||$('#box2').is(':checked')){
        }else{
            alert('Συμπληρώστε εάν θέλετε να διοργανώσετε ή/και να υποστηρίξετε δράσεις');
            return false;
        }
        if($('#jform_password1').val()!=$('#jform_password2').val()){
            alert('Δε συμπίπτουν οι δύο κωδικοί');
            return false;
        }
        if($('#team_id').val()==''){
            if( ($('#jform_password1').val().length<4 || $('#jform_password2').val().length<4) ){
                alert('Παρακαλώ εισάγετε έναν κωδικό με τουλάχιστον 4 χαρακτήρες');
                return false;
            }
        }
        if($('#box3').is(':checked')||$('#box4').is(':checked')||$('#box41').is(':checked')||$('#box42').is(':checked')){
        }else{
            alert('Συμπληρώστε τύπο ομάδας');
            return false;
        }
        /*if($('#box2').is(':checked')){
            var donations=0;
            var sub_donations=0;
            $message = 'Επιλέξτε τουλαχιστον μία υποκατηγορία για την προσφορά σας ως υποστηρικτής';
            $('input', $('.registration-donations')).each(function () {
                //console.log($(this)); //log every element found to console output
                if($(this).is(':checked')){

                    donations=1;

                    if( $('#donation-1').is(':checked') || $('#donation-16').is(':checked') ) {

                        if ( $('#donation-1').is(':checked') ) {

                            $message = 'Παρακαλούμε επιλέξτε τουλάχιστον μια υποκατηγορία σε Προσφορά σε είδος';

                            $('input', $('#subcat1')).each(function () {
                                if($(this).is(':checked')){
                                    sub_donations=1;
                                }
                            });

                        }

                        if  ( $('#donation-16').is(':checked') && sub_donations==1 ) {

                            sub_donations=0;

                            $message = 'Παρακαλούμε επιλέξτε τουλάχιστον μια υποκατηγορία σε Προσφορά σε τεχνογνωσία';

                            $('input', $('#subcat2')).each(function () {
                                if($(this).is(':checked')){
                                    sub_donations=1;
                                }
                            });

                        }

                    } else if ( !$('#donation-1').is(':checked') && !$('#donation-16').is(':checked') ) {
                        sub_donations=1;
                    }
                }
            });

            if( donations==0 || sub_donations==0 ){
                if(donations==0) {
                    alert('Συμπληρώστε τουλαχιστον μία προσφορά');
                    return false;
                }

                if(sub_donations==0){
                    alert($message);
                    return false;
                }
            }
        }*/
        if($('#box2').is(':checked')){
            var donations=0;
            var sub_donations=1;
            message = 'Παρακαλούμε επιλέξτε τουλάχιστον μια κατηγορία υποστήριξης';
            $('.registration-donations-parent').each(function () {

                if($(this).is(':checked')){

                    donations=1;

                    if( $('#donation-1').is(':checked') || $('#donation-16').is(':checked') ) {

                        if ( $('#donation-1').is(':checked') ) {

                            sub_donations=0;

                            message = 'Παρακαλούμε επιλέξτε τουλάχιστον μια υποκατηγορία σε Προσφορά σε είδος';

                            $('input', $('#subcat1')).each(function () {
                                if($(this).is(':checked')){
                                    sub_donations=1;
                                }
                            });

                        }

                        if  ( $('#donation-16').is(':checked') && sub_donations==1 ) {

                            sub_donations=0;

                            message = 'Παρακαλούμε επιλέξτε τουλάχιστον μια υποκατηγορία σε Προσφορά σε τεχνογνωσία';

                            $('input', $('#subcat2')).each(function () {
                                if($(this).is(':checked')){
                                    sub_donations=1;
                                }
                            });

                        }

                    } else {
                        if(!($('#donation-27').is(':checked') || $('#donation-28').is(':checked') || $('#donation-35').is(':checked'))) {
                            message = 'Επιλέξτε τουλαχιστον μία υποκατηγορία για την προσφορά σας ως υποστηρικτής';
                        } else {
                            donations = 1;
                        }
                    }
                }
            });

            if( donations==0 || sub_donations==0 ){
                if(donations==0) {
                    alert(message);
                    return false;
                }

                if(sub_donations==0){
                    alert(message);
                    return false;
                }
            }
        }
        if($('#box8').is(':checked')||$('#box9').is(':checked')){
        }else{
            alert('Συμπληρώστε εάν έχετε νομική μορφή');
            return false;
        }
        if($('#box8').is(':checked')){
            if($('#box10').is(':checked')||$('#box11').is(':checked')){
                if($('#box10').is(':checked')){
                    if($('#box-type-12').is(':checked')||$('#box-type-13').is(':checked')||$('#box-type-14').is(':checked')||$('#box150').val()!=''){
                    }else{
                        alert('Συμπληρώστε τη μη κερδοσκοπική νομική μορφή');
                        return false;
                    }
                }
            }else{
                alert('Συμπληρώστε εάν έχετε κερδοσκοπική νομική μορφή');
                return false;
            }
        }
        var activities=0;
        $('input', $('.registration-activities')).each(function () {
            //console.log($(this)); //log every element found to console output
            if($(this).is(':checked')){
                activities=1;
            }
        });

        if(activities==0){
            alert('Συμπληρώστε τουλαχιστον μία δραστηριότητα');
            return false;
        }
        //return false;
        this.submit();
        //event.preventDefault();
    });
});

function createFromMysql(mysql_string)
{
    var t, result = null;

    if( typeof mysql_string === 'string' )
    {
        t = mysql_string.split(/[- :]/);

        //when t[3], t[4] and t[5] are missing they defaults to zero
        result = new Date(t[0], t[1] - 1, t[2], t[3] || 0, t[4] || 0, t[5] || 0);
    }

    return result;
}

//check if refugees checkbox is on and display the remote checkbox
function showRemote()
{
    remote = 0;
    $('#create_action .remote-option').each(function () {
        if ($(this).is(":checked")) {
            remote = 1;
        }
    });

    if (remote) {
        $('#remote-checkbox input')[0].checked = true;
        $('#remote-checkbox').removeClass('hidden');
    } else {
        $('#remote-checkbox input')[0].checked = false;
        $('#remote-checkbox').addClass('hidden');
    }
}

//actions form validations
$(document).ready(function (){
    if($('#team_root_id').length > 0){
        $('#team_root_id').change(function(){
            $('#user_id').val($('#team_root_id').val());
        });
    }
    $( "#create_action" ).on('submit', function(e) {
        e.preventDefault();
        if($('#team_root_id').length > 0){
            if($('#user_id').val()==''){
                alert("Παρακαλώ εισάγετε ομάδα");
                return false;
            }
        }
        for(f=0; f<11; f++){
            if($('#form-block-'+f).css('display')!='none'){
                if($('#ypotitlos_drashs_'+f).val()==''){
                    alert('Παρακαλώ συμπληρώστε τίτλο στην υποδράση');
                    return false;
                }
                var start = $('#from_date_'+f).val();
                var start_array = start.split("/");
                var year_start_array = start_array[2].split(" ");
                var day_start=start_array[0];
                var month_start=start_array[1];
                var year_start=year_start_array[0];
                var time_start=year_start_array[1]+':00';
                var start_js = createFromMysql(year_start+'-'+month_start+'-'+day_start+' '+time_start);

                var end = $('#to_date_'+f).val();
                var end_array = end.split("/");
                var year_end_array = end_array[2].split(" ");
                var day_end=end_array[0];
                var month_end=end_array[1];
                var year_end=year_end_array[0];
                var time_end=year_end_array[1]+':00';
                var end_js = createFromMysql(year_end+'-'+month_end+'-'+day_end+' '+time_end);

                if (start_js >= end_js) {
                    alert("Η ημερομηνία έναρξης πρέπει να είναι μικρότερη από την ημερομηνία λήξης");
                    return false;
                }

                if($('#stegi_'+f).is(":checked")){
                }else{
                }
            }
        }
        if(!$('#image').val()){
            alert('Προσθέστε μία κεντρική φωτογραφία');
            return false;
        }
        if (window.File && window.FileReader && window.FileList && window.Blob)
        {
            var ValidImageTypes = ["image/gif", "image/jpeg", "image/png"];
            //get the file size and file type from file input field
            var fsize = $('#image')[0].files[0].size;
            if(fsize>1048576){
                //do something if file size more than 1 mb (1048576){
                alert("Παρακαλώ εισάγετε φωτογραφία μικρότερου μεγέθους");
                return false;
            }
            var fileType = $('#image')[0].files[0].type;
            if ($.inArray(fileType, ValidImageTypes) < 0) {
                //do something if file is not a valid image
                alert("Παρακαλώ εισάγετε αρχείο jpg, png ή gif");
                return false;
            }
            //photos validation one by one
            if($('#box70').val()!=''){
                //get the file size and file type from file input field
                var galleryInput = document.getElementById("box70");
                var galleryInputFiles = galleryInput.files;
                for(p=0; p<galleryInputFiles.length; p++){
                    //console.log(galleryInputFiles[p]);
                    var fsize = galleryInputFiles[p].size;
                    if(fsize>1048576){
                        //do something if file size more than 1 mb (1048576){
                        alert("Παρακαλώ εισάγετε φωτογραφία μικρότερου μεγέθους");
                        return false;
                    }
                    var fileType = galleryInputFiles[p].type;
                    if ($.inArray(fileType, ValidImageTypes) < 0) {
                        //do something if file is not a valid image
                        alert("Παρακαλώ εισάγετε μόνο αρχεία φωτογραφίας");
                        return false;
                    }
                }
            }
        }
        this.submit();
        //return;
    });

    $('#create_action').find('#name').change(function() {
        var curValue = $(this).val();

        $('#ypotitlos_drashs_0').val(curValue);
    });
    //event.preventDefault();
});


//edit actions form validations
$(document).ready(function (){
    $( "#edit_action" ).on('submit', function(e) {
        e.preventDefault();
        for(f=0; f<11; f++){
            if($('#form-block-'+f).css('display')!='none' && $('#from_date_'+f)){
                if($('#ypotitlos_drashs_'+f).val()==''){
                    alert('Παρακαλώ συμπληρώστε τίτλο στην επιμέρους δράση');
                    return false;
                }
                if($('#from_date_'+f).val()=='' || $('#to_date_'+f).val()==''){
                    alert('Παρακαλώ συμπληρώστε ημερομηνία στην επιμέρους δράση');
                    return false;
                }
                var start = $('#from_date_'+f).val();
                var start_array = start.split("/");
                var year_start_array = start_array[2].split(" ");
                var day_start=start_array[0];
                var month_start=start_array[1];
                var year_start=year_start_array[0];
                var time_start=year_start_array[1]+':00';
                var start_js = createFromMysql(year_start+'-'+month_start+'-'+day_start+' '+time_start);

                var end = $('#to_date_'+f).val();
                var end_array = end.split("/");
                var year_end_array = end_array[2].split(" ");
                var day_end=end_array[0];
                var month_end=end_array[1];
                var year_end=year_end_array[0];
                var time_end=year_end_array[1]+':00';
                var end_js = createFromMysql(year_end+'-'+month_end+'-'+day_end+' '+time_end);

                if (start_js >= end_js) {
                    alert("Η ημερομηνία έναρξης πρέπει να είναι μικρότερη από την ημερομηνία λήξης");
                    return false;
                }

                if($('#stegi_'+f).is(":checked")){
                }else{
                }
            }else{
                //$('#form-block-'+f).remove();
            }
        }
        if (window.File && window.FileReader && window.FileList && window.Blob)
        {
            var ValidImageTypes = ["image/gif", "image/jpeg", "image/png"];

            //get the file size and file type from file input field
            //console.log($('#image')[0].files.length);
            if($('#image').val()){
                var fsize = $('#image')[0].files[0].size;
                if(fsize>1048576){
                    //do something if file size more than 1 mb (1048576){
                    alert("Παρακαλώ εισάγετε φωτογραφία μικρότερου μεγέθους");
                    return false;
                }
                var fileType = $('#image')[0].files[0].type;
                if ($.inArray(fileType, ValidImageTypes) < 0) {
                    //do something if file is not a valid image
                    alert("Παρακαλώ εισάγετε αρχείο jpg, png ή gif");
                    return false;
                }
            }
            //photos validation one by one
            if($('#box70').val()!=''){
                //get the file size and file type from file input field
                var galleryInput = document.getElementById("box70");
                var galleryInputFiles = galleryInput.files;
                for(p=0; p<galleryInputFiles.length; p++){
                    var fsize = galleryInputFiles[p].size;
                    if(fsize>1048576){
                        //do something if file size more than 1 mb (1048576){
                        alert("Παρακαλώ εισάγετε φωτογραφία μικρότερου μεγέθους");
                        return false;
                    }
                    var fileType = galleryInputFiles[p].type;
                    if ($.inArray(fileType, ValidImageTypes) < 0) {
                        //do something if file is not a valid image
                        alert("Παρακαλώ εισάγετε μόνο αρχεία φωτογραφίας");
                        return false;
                    }
                }
            }
        }
        this.submit();
    });
});

$( "#toolkit_tabs" ).tabs();

$('.donation-change').click(function() {
    var tmp=$(this).attr("data-href");
    if($(this).is(":checked")){
        $(tmp).removeClass('form-block--hidden');
        $(tmp).addClass('is-shown');
    }else{
        $(tmp).removeClass('is-shown');
        $(tmp).addClass('form-block--hidden');
    }
    //$(".show_stegi_hours_"+tmp).toggle();
});

$('.filters div>input:checkbox').change(function (e) {
    if (typeof $(this).attr('data-href') !== 'undefined') {
        return false;
    }

    var currId = $(this).attr('id');

    if (document.getElementById(currId).checked) {
        $(".textarea-" + currId).css('display', 'block');
        $(".textarea-" + currId).find('[name^="support_message"]').attr('required', 'required');
    } else {
        $("input[id*='"+currId+"']").attr('checked', false);
        $("div[class*='textarea-" + currId + "']").css('display', 'none');
        $("div[class*='textarea-" + currId + "']").find('[name^="support_message"]').removeAttr('required');
        $("div[class*='textarea-" + currId + "']").find('[name^="support_message"]').val('');
    }
    /*if(check_donation==1){
     //console.log(check_donation);
     $(".donation-message").css('display','block');
     $('#support_message').attr('required', 'required');
     }else{
     $(".donation-message").css('display','none');
     $('#support_message').removeAttr('required');
     }*/
});

function delete_di_confirmation(opencall_id,di_id,di_filename,image_id,abspath) {
    var answer = confirm("Είστε σίγουροι πως θέλετε να διαγράψετέ αυτή την εικόνα;");
    if (answer){
        $.ajax({
            url: abspath+'delete_di_photo_opencall.php',
            type: 'post',
            data: {'opencall_id':opencall_id,'di_id':di_id,'di_filename':di_filename},
            success: function(data, status) {
                if(data == 1) {
                    $('#photo_edit_'+image_id).remove();
                    alert('Η φωτογραφία διαγράφηκε επιτυχώς');
                }
            },
            error: function(xhr, desc, err) {
            }
        });
    }else{
        return false;
    }
}

function delete_file_confirmation(user_id,file_id,file_path,abspath) {
    var answer = confirm("Είστε σίγουροι πως θέλετε να διαγράψετέ αυτό το αρχείο;");
    if (answer){
        $.ajax({
            url: abspath+'delete_file_opencall.php',
            type: 'post',
            data: {'user_id':user_id,'file_id':file_id,'file_path':file_path},
            success: function(data, status) {
                if(data == 1) {
                    $('#attachment_'+file_id).remove();
                    alert('Το αρχείο διαγράφηκε επιτυχώς');
                }
            },
            error: function(xhr, desc, err) {
            }
        });
    }else{
        return false;
    }
}

$('.mfp-close').click(function(){
    $('.overlay').css('display','none');
});