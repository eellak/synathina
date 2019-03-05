<?php
defined('_JEXEC') or die;

//language
$doc = JFactory::getDocument();
$lang_code_array=explode('-',$doc->language);
$lang_code=$lang_code_array[0];

//connect to db
$db = JFactory::getDBO();

$actions_date_array1=array();

date_default_timezone_set('Europe/Athens');
$time_diff=3600*3;

function nb_mois($date1, $date2){
    $begin = new DateTime( $date1 );
    $end = new DateTime( $date2 );
    $end = $end->modify( '+1 month' );
    $interval = DateInterval::createFromDateString('1 month');

    $period = new DatePeriod($begin, $interval, $end);
    $counter = 0;
    foreach($period as $dt) {
        $counter++;
    }
    return $counter;
}


function get_distinct_teams($date, $db)
{
	$query = "SELECT team_id FROM #__stegihours
				WHERE published=1 AND (date_start LIKE '".$date."%' OR date_end LIKE '".$date."%' OR (date_start<'".$date." 23:59:59' AND date_end>'".$date." 00:00:00') ) GROUP BY team_id";
	$db->setQuery($query);
	$db->execute();
// echo $db->getNumRows();
// die;
	return $db->getNumRows();
}

//get activities
/*$query = "SELECT t.name AS tname, a.id, a.subtitle, a.action_date_start, a.action_date_end FROM #__actions AS a INNER JOIN #__teams AS t ON t.id=a.team_id WHERE a.published=1 AND a.stegi_use=1 AND a.action_id>0 ORDER BY a.action_date_start ASC ";
$db->setQuery($query);
$actions = $db->loadObjectList();
//$actions_array=array();
//$actions_date_array=array();

foreach($actions as $action){
	$start=$action->action_date_start;
	$start_array=explode(':',$start);
	$new_start=$start_array[0]; //2016-05-27 12
	$new_start_array=explode(' ',$new_start);
	$new_start_time=$new_start_array[1]; //12
	$end=$action->action_date_end;
	$end_array=explode(':',$end);
	$new_end=$end_array[0]; //2016-05-27 12
	$new_end_array=explode(' ',$new_end);
	$new_end_time=$new_end_array[1]; //12
	if($new_start_time<$new_end_time){
		for($i=$new_start_time; $i<$new_end_time; $i++){
			//$actions_array[]=array($action->id, $action->subtitle, $new_start_array[0].' '.$i, $action->tname);
			//$actions_date_array[]=$new_start_array[0].' '.$i;
			$actions_date_array1[]=$new_start_array[0];
		}
	}
}*/
//get stegi teams
$query = "SELECT t.name AS tname, a.id, a.name, a.date_start, a.date_end FROM #__stegihours AS a
			INNER JOIN #__teams AS t
			ON t.id=a.team_id
			WHERE a.published=1 ORDER BY a.date_start ASC";
$db->setQuery($query);
$actions = $db->loadObjectList();

foreach($actions as $action){
	$start=$action->date_start;
	$start_array=explode(':',$start);
	$new_start=$start_array[0]; //2016-05-27 12
	$new_start_array=explode(' ',$new_start);
	$new_start_time=intval($new_start_array[1]); //12
	$end=$action->date_end;
	$end_array=explode(':',$end);
	$new_end=$end_array[0]; //2016-05-27 12
	$new_end_array=explode(' ',$new_end);
	$new_end_time=intval($new_end_array[1]); //12
	if($new_end_array[0]>@$_REQUEST['stegi_date']){
		$new_end_time=24;
	}

	if($new_start_array[0]==$new_end_array[0]){
		$actions_date_array1[]=$new_start_array[0];
	}else{
		$date_start=new DateTime($new_start_array[0]);
		$time_start=$date_start->getTimestamp()+$time_diff;
		$date_end=new DateTime($new_end_array[0]);
		$time_end=$date_end->getTimestamp()+$time_diff;
		$days=($time_end-$time_start)/(60*60*24)+1;
		$counter_time=$time_start;
		//echo $days.'<br />';
		for($d=1; $d<($days+1); $d++){
			$actions_date_array1[]=date('Y-m-d',$counter_time);
			//echo date('Y-m-d',$counter_time).'<br />';
			$counter_time=$time_start+60*60*24*$d;
		}
	}
	//if($new_start_time<$new_end_time){
		//for($i=$new_start_time; $i<$new_end_time; $i++){
			//$actions_array[]=array($action->id, $action->description, $new_start_array[0].' '.$i, $action->tname);
			//$actions_date_array[]=$new_start_array[0].' '.$i;
			//$actions_date_array1[]=$new_start_array[0];
		//}
	//}
}

$user = JFactory::getUser();

//get team
$query = "SELECT published FROM #__teams
			WHERE user_id='".$user->id."' LIMIT 1";
$db->setQuery($query);
$team = $db->loadObject();

function draw_calendar($month,$year,$actions_date_array1,$lang, $db){
	/* draw table */
	//$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';
	$calendar='';
	/* table headings */
	if($lang=='en'){
		$headings = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	}else{
		$headings = array('Κυριακή','Δευτέρα','Τρίτη','Τετάρτη','Πέμπτη','Παρασκευή','Σάββατο');
	}

	//$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';
	$calendar.= '<ul class="weekdays"><li>'.implode('</li><li>',$headings).'</li></ul>';


	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* row for week one */
	//$calendar.= '<tr class="calendar-row">';
	$calendar.='<ul class="days">';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
		//$calendar.= '<td class="calendar-day-np"> </td>';
		$calendar.= '<li>&nbsp;</li>';
		$days_in_this_week++;
	endfor;

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):
		//$calendar.= '<td class="calendar-day">';
		$calendar.= '';
			/* add in the day number */
			//$calendar.= '<div class="day-number">'.$list_day.'</div>';
			$new_time1=$year.'-'.($month<10?0:'').$month.'-'.($list_day<10?0:'').$list_day;
			$found=array_keys($actions_date_array1,$new_time1);
			if(!empty($found)){
				$s=@get_distinct_teams($new_time1, $db);
				$calendar.='<li href="'.JURI::base().'" rel="'.$new_time1.'" class="stegi_use_exists stegi_'.($s>4?4:$s).'">
											<a class="active" href="'.JURI::base().'" rel="'.$new_time1.'">'.$list_day.'</a>
											<a class="stegi_count" href="'.JURI::base().'" rel="'.$new_time1.'"><span>'.$s.'</span></a>
										</li>';
			}else{
			 	$calendar.= '<li>'.$list_day.'</li>';
			}

			/** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
			//$calendar.= str_repeat('<p> </p>',2);

		//$calendar.= '</td>';
		$calendar.= '';
		if($running_day == 6):
			//$calendar.= '</tr>';
			if(($day_counter+1) != $days_in_month):
				//$calendar.= '<tr class="calendar-row">';
			endif;
			$running_day = -1;
			$days_in_this_week = 0;
		endif;
		$days_in_this_week++; $running_day++; $day_counter++;
	endfor;

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
		for($x = 1; $x <= (8 - $days_in_this_week); $x++):
			//$calendar.= '<td class="calendar-day-np"> </td>';
			$calendar.= '<li>&nbsp;</li>';
		endfor;
	endif;

	/* final row */
	//$calendar.= '</tr>';
	$calendar.= '</ul>';

	/* end the table */
	//$calendar.= '</table>';

	/* all done, return result */
	return $calendar;
}
?>
<div class="module module--synathina mfp-hide" id="stegi_hours_popup" style="background-color:#ebebeb;">
</div>

<div class="module module--synathina mfp-hide" id="activate_to_book">
   <div class="module-skewed">
      <!-- Content Module -->
      <div class="book-stegh">
         <h3 class="book-stegh-title">Κλείσε τη στέγη για την ομάδα σου</h3>
         Ο λογαριασμός σας δεν έχει ενεργοποιηθεί ακόμα από το συνΑθηνά.<br /><br /><br /><br />
      </div>

   </div>
</div>

<div class="module module--synathina mfp-hide" id="login_to_book">
   <div class="module-skewed">
      <!-- Content Module -->
      <div class="book-stegh">
         <h3 class="book-stegh-title">Κλείσε τη στέγη για την ομάδα σου</h3>
         Παρακαλώ <a href="<?php echo JRoute::_('index.php?option=com_users&view=login&Itemid=120'); ?>">συνδεθείτε</a> για να καταχωρίσετε το αίτημά σας<br /><br /><br /><br />
      </div>

   </div>
</div>
<div class="module module--synathina mfp-hide" id="book_stegi">
   <div class="module-skewed">
      <!-- Content Module -->
      <div class="book-stegh">
         <h3 class="book-stegh-title">Κλείσε τη στέγη για την ομάδα σου</h3>
         <form action="" class="form form-inline" method="post" id="request_stegi">

            <div class="form-group">
               <label for="from_date">Από</label>
               <input type="text" class="from_date" id="from_date" name="date_from" required="">
            </div>

            <div class="form-group">
               <label for="to_date">Έως</label>
               <input type="text" class="to_date" name="date_to" id="to_date" required="">
            </div>
            <div class="form-group is-block">
               <label for="activity_title">Τίτλος δραστηριότητας*:</label>
               <input style="min-width: 270px;" type="text" class="form-control" id="activity_title" name="activity_title" required="" value="Εσωτερική συνάντηση ομάδας" />
            </div>
            <div class="form-group is-block">
               <label for="activity_description">Περιγραφή δραστηριότητας*:</label>
               <textarea class="form-control" id="activity_description" name="activity_description" rows="8" required=""></textarea>
               <span class="is-block is-italic">(πχ. Εσωτερική συνάντηση ομάδας ή κλειστό meeting μελών ομάδας κλπ)</span>
            </div>

            <div class="form-group is-block clearfix">
               <span class="pull-left"><em>*Υποχρεωτικά πεδία</em></span>
               <button type="submit" class="pull-right btn btn--coral btn--bold">Καταχώριση</button>
            </div>
						<input type="hidden" name="abspath" id="abspath" value="<?php echo JUri::base(); ?>" />

         </form>
      </div>

   </div>
</div>

<?php
	date_default_timezone_set('Europe/Athens');
	if($lang_code=='en'){
		setlocale(LC_TIME, "en_GB.UTF8");
	}else{
		setlocale(LC_TIME, "el_GR.UTF8");
	}

	$time_diff=3600*3;
	$next_month=mktime(0, 0, 0, (date('n')+1), 1, date('Y'));
	if(date('n')==12){
		$next_month_var=1;
		$next_year_var=date('Y')+1;
	}else{
		$next_month_var=date('n')+1;
		$next_year_var=date('Y');
	}
	//echo strftime("%B %Y",(time()+$time_diff));
	$start=mktime(0, 0, 0, 06, 01, 2016);
	$end=mktime(0, 0, 0, (date('n')+12), 01, date('Y'));
	$months=nb_mois(date('Y-m-d H:i:s',$start), date('Y-m-d H:i:s',$end));
?>
	<a href="SinathinaSTEGH.pdf" target="_blank" class="btn btn--skewed btn--coral btn--bold" download="SinathinaSTEGH.pdf"><strong><?php echo JText::_('COM_STEGI_FLOORPLAN'); ?></strong></a>
	<div class="c-diary">
		<h2><?php echo JText::_('COM_STEGI_CALENDAR'); ?></h2>
		<div class="diary-switcher">
				<button rel="js-left"><i class="fa fa-angle-left"></i></button>
				<div class="is-inline-block diary-labels">
<?php
	$current_month=mktime(0, 0, 0, (date('n')), 1, date('Y'));
	for($m=0; $m<$months; $m++){
		$next_month=mktime(0, 0, 0, (6+$m), 1, 2016);
		echo '<span '.($next_month==$current_month?'class="active"':'').'>'.strftime("%B %Y",($next_month+$time_diff)).'</span>';
	}
?>
				</div>
				<button rel="js-right" class="fa fa-angle-right"></button>
		</div>
		<div class="module module--synathina">
      		<div class="module-skewed module-skewed--gray" rel="js-container">
<?php
	$current=0;
	$current_month=gmmktime(0, 0, 0, (date('n')), 1, date('Y'));
	for($m=0; $m<$months; $m++){
		$next_month=gmmktime(0, 0, 0, (6+$m), 1, 2016);
		echo '<div id="tab-'.($m+1).'" class="tab '.($current_month==$next_month?'active':'').'">
						<div class="diary diary--month">
						'.draw_calendar(date('n',$next_month),date('Y',$next_month),$actions_date_array1,$lang_code, $db).'
						</div>
					</div>';
		if($current_month==$next_month){
			$current=$m;
		}
	}
?>
			</div>
		</div>

	<div id="tab-counter" style="visibility:hidden"><?=$current?></div>
	</div>



	<div class="btn-group">
			<a href="<?php echo JRoute::_('index.php?option=com_actions&view=form&Itemid=139');?>" class="btn btn--skewed btn--skewed--bottom btn--coral btn--bold">
					<strong><?php echo JText::_('MOD_STEGI_BOOK_ACTIVITY');?></strong>
			</a>
			<a href="<?=($user->id>0?($team->published==1?'#book_stegi':'#activate_to_book'):'#login_to_book')?>" class="btn btn--skewed btn--skewed--bottom btn--coral btn--bold book-stegi">
					<strong><?php echo JText::_('MOD_STEGI_BOOK_TEAM');?></strong>
			</a>
	</div>
