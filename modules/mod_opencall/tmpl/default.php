<?php
defined('_JEXEC') or die;

$config = JFactory::getConfig();
$user = JFactory::getUser();
$abspath = $config->get( 'abs_path' );

//local db
$db = JFactory::getDbo();

//get team state
$query = "SELECT * FROM #__teams
			WHERE user_id='".$user->id."' LIMIT 1 ";
$db->setQuery($query);
$team = $db->loadObject();

//language
$doc = JFactory::getDocument();
$lang_code_array = explode('-', $doc->language);
$lang_code = $lang_code_array[0];

function url_slug1($str, $options = array()) {
	// Make sure string is in UTF-8 and strip invalid UTF-8 characters
	$str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());

	$defaults = array(
		'delimiter' => '-',
		'limit' => null,
		'lowercase' => true,
		'replacements' => array(),
		'transliterate' => false,
	);

	// Merge options
	$options = array_merge($defaults, $options);

	$char_map = array(
		// Latin
		'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
		'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
		'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
		'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
		'ß' => 'ss',
		'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
		'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
		'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
		'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
		'ÿ' => 'y',
		// Latin symbols
		'©' => '(c)',
		// Greek
		'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
		'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
		'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
		'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
		'Ϋ' => 'Y',
		'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
		'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
		'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
		'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
		'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
		// Turkish
		'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
		'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',
		// Russian
		'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
		'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
		'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
		'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
		'Я' => 'Ya',
		'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
		'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
		'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
		'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
		'я' => 'ya',
		// Ukrainian
		'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
		'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
		// Czech
		'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
		'Ž' => 'Z',
		'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
		'ž' => 'z',
		// Polish
		'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z',
		'Ż' => 'Z',
		'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
		'ż' => 'z',
		// Latvian
		'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N',
		'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
		'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
		'š' => 's', 'ū' => 'u', 'ž' => 'z'
	);

	// Make custom replacements
	$str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);

	// Transliterate characters to ASCII
	if ($options['transliterate']) {
		$str = str_replace(array_keys($char_map), $char_map, $str);
	}

	// Replace non-alphanumeric characters with our delimiter
	$str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);

	// Remove duplicate delimiters
	$str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

	// Truncate slug to max. characters
	$str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');

	// Remove delimiter from ends
	$str = trim($str, $options['delimiter']);

	return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
}

if (@$_REQUEST['open_call_title']!='' && @$_REQUEST['open_call_description'] != '' && $user->id > 0) {
	require_once(JPATH_SITE.'/components/com_content/helpers/route.php');
	$title = addslashes(@$_REQUEST['open_call_title']);
	$introtext = '<p>'.addslashes( nl2br(@$_REQUEST['open_call_description']) ).'</p>';
	$alias = url_slug1($title);
	$user_id = $user->id;
	date_default_timezone_set('Europe/Athens');
	$created = date('Y-m-d');
	$end_date = '';
	if (@$_REQUEST['opencall_date'] != '') {
		$end_date_array = explode('/', @$_REQUEST['opencall_date']);
		$end_date = trim(@$end_date_array[2]).'-'.@$end_date_array[1].'-'.@$end_date_array[0];
	}
	$activities_ids = '';
	$activities_keys = @array_keys(@$_REQUEST['activities']);
	$activities_ids = '[';
	for ($a = 0; $a < count($activities_keys); $a++) {
		$activities_ids .= '"'.$activities_keys[$a].'",';
	}
	$activities_ids = rtrim($activities_ids,',');
	$activities_ids .= ']';

	//insert to content - assets
	$query_article = "INSERT INTO #__content VALUES ('','','".$title."','".$alias."','".$introtext."','".$introtext."',1,15,'".$created."','".$user_id."','','".$created."',0,0,'0000-00-00 00:00:00','".$created."','0000-00-00 00:00:00',
	'{\"image_intro\":\"\",\"float_intro\":\"\",\"image_intro_alt\":\"\",\"image_intro_caption\":\"\",\"image_fulltext\":\"\",\"float_fulltext\":\"\",\"image_fulltext_alt\":\"\",\"image_fulltext_caption\":\"\"}',
	'{\"urla\":false,\"urlatext\":\"\",\"targeta\":\"\",\"urlb\":false,\"urlbtext\":\"\",\"targetb\":\"\",\"urlc\":false,\"urlctext\":\"\",\"targetc\":\"\"}',
	'{\"opencall_date\":\"".@$end_date."\",\"opencall_activities\":".$activities_ids.",\"news_subtitle\":\"\",\"article_video\":\"\",\"show_title\":\"\",\"link_titles\":\"\",\"show_tags\":\"\",\"show_intro\":\"\",\"info_block_position\":\"\",\"show_category\":\"\",\"link_category\":\"\",\"show_parent_category\":\"\",\"link_parent_category\":\"\",\"show_author\":\"\",\"link_author\":\"\",\"show_create_date\":\"\",\"show_modify_date\":\"\",\"show_publish_date\":\"\",\"show_item_navigation\":\"\",\"show_icons\":\"\",\"show_print_icon\":\"\",\"show_email_icon\":\"\",\"show_vote\":\"\",\"show_hits\":\"\",\"show_noauth\":\"\",\"urls_position\":\"\",\"alternative_readmore\":\"\",\"article_layout\":\"\",\"show_publishing_options\":\"\",\"show_article_options\":\"\",\"show_urls_images_backend\":\"\",\"show_urls_images_frontend\":\"\"}',
	1,0,'','',1,0,'{\"robots\":\"\",\"author\":\"\",\"rights\":\"\",\"xreference\":\"\"}',0,'*',''); ";

	$db->setQuery($query_article);
	$db->execute();
	$article_id = $db->insertid();
	if ($article_id > 0) {

		//assets
		$query_asset = "SELECT rgt FROM #__assets WHERE name LIKE 'com_content.article.%' ORDER BY id DESC LIMIT 1";
		$db->setQuery($query_asset);
		$assoc_rgt = $db->loadResult()+1;
		$query_asset = "INSERT INTO #__assets VALUES ('',80,'".$assoc_rgt."','".($assoc_rgt+1)."',3,'com_content.article.".$article_id."','".$title."','{\"core.admin\":{\"7\":1},\"core.manage\":{\"6\":1},\"core.create\":{\"3\":1},\"core.delete\":[],\"core.edit\":{\"4\":1},\"core.edit.state\":{\"5\":1},\"core.edit.own\":[]}') ";
		$db->setQuery($query_asset);
		$db->execute();
		$asset_id = $db->insertid();
		$query_article_update = "UPDATE #__content SET asset_id='".$asset_id."' WHERE id='".$article_id."' LIMIT 1";
		$db->setQuery($query_article_update);
		$db->execute();

		//files
		$images_types = array('image/bmp','image/gif','image/jpeg','image/jpeg','image/png');
		$images_types_mime = array('bmp','gif','jpeg','jpg','png');
		$file_types = array('application/msword', 'application/pdf', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
		$file_types_mime = array('doc', 'pdf', 'pptx', 'docx');
		if (!empty($_FILES['files'])) {
			$img_ordering = 1;
			for ($f = 0; $f < count($_FILES['files']['name']); $f++) {
				if ($_FILES['files']['error'][$f] == 0) {
					//proceed to upload
					$ext_array = @explode('.', $_FILES['files']['name'][$f]);
					$ext = strtolower(end($ext_array));
					$rand = md5($_FILES['files']['name'][$f]);
					//image case
					if (in_array($_FILES['files']['type'][$f], $images_types)) {
						//insert to di images
						$query_di = "INSERT INTO #__di_images VALUES ('','".$article_id."',1,'".$rand.'.'.$ext."','','',0,'".date('Y-m-d H:i:s')."','".$img_ordering."','','')";
						$db->setQuery($query_di);
						$db->execute();
						$di_id = $db->insertid();
						$img_ordering++;
						//move image to images/di
						move_uploaded_file($_FILES['files']['tmp_name'][$f], 'images/di/'.$article_id.'_'.$di_id.'_'.$rand.'.'.$ext);
						copy('images/di/'.$article_id.'_'.$di_id.'_'.$rand.'.'.$ext,'images/di/'.$article_id.'_'.$di_id.'_regular_'.$rand.'.'.$ext);
						copy('images/di/'.$article_id.'_'.$di_id.'_'.$rand.'.'.$ext,'images/di/'.$article_id.'_'.$di_id.'_thumb_'.$rand.'.'.$ext);
						copy('images/di/'.$article_id.'_'.$di_id.'_'.$rand.'.'.$ext,'images/di/'.$article_id.'_'.$di_id.'_zoomed_'.$rand.'.'.$ext);
					}
					//file case
					if (in_array($_FILES['files']['type'][$f],$file_types)) {
						//insert to attachments
						$key = array_search($ext, $file_types_mime);
						$query_att = "INSERT INTO #__attachments VALUES ('','".$rand.".".$ext."','".$abspath."/attachments/article/".$article_id."/".$rand.".".$ext."','".$file_types[$key]."','".$_FILES['files']['size'][$f]."',
							'attachments/article/".$article_id."/".$rand.".".$ext."','file',0,0,0,'".addslashes($_FILES['files']['name'][$f])."','','".$ext.".gif',1,1,'','','','com_content','article','".$article_id."','".date('Y-m-d- H:i:s')."','".$user->id."','".date('Y-m-d- H:i:s')."',
							'".$user->id."',0) ";
						$db->setQuery($query_att);
						$db->execute();
						//mkdir attachments/content_id/ and move file there
						if (!file_exists(JURI::root( true ).'/attachments/article/'.$article_id)) {
							mkdir('attachments/article/'.$article_id);
						}
						move_uploaded_file($_FILES['files']['tmp_name'][$f], 'attachments/article/'.$article_id.'/'.$rand.'.'.$ext);
					}
				}
			}
		}
		//email to admin
		$config = JFactory::getConfig();
		$emails = [];
		$opencall_url = JRoute::_(ContentHelperRoute::getArticleRoute($article_id.':'.$alias, '15'));
		$s_array = array($config->get('live_site').$opencall_url,$config->get( 'live_site' ).'/administrator/index.php?core&option=com_content&view=articles');
		synathina_email('open_call_admin', $s_array, $emails, '');
		//email to user
		$s_array = array($config->get('live_site').$opencall_url);
		if ($user->email != '') {
			$emails = array($user->email);
		}
		synathina_email('open_call_user', $s_array, $emails, '');

		echo '<script>alert(\''.($lang_code=='en'?'Success':'Επιτυχής καταχώριση').'\')</script>';
	} else {
		echo '<script>alert(\''.($lang_code=='en'?'Something went wrong, please try again':'Παρουσιάστηκε πρόβλημα, παρακαλώ προσπαθήστε ξανά').'\')</script>';
	}
}
?>

<div class="module module--synathina module--popup mfp-hide" id="opencall-message" style="margin: 0px auto;">
   <div class="module-skewed">
      <!-- Content Module -->
      <div class="open-call">
         <h3 class="popup-title">Open Call</h3>
<?php
	if ($user->id > 0 && $team->published == 1) {
?>
         <form action="<?php echo JURI::current();?>" class="form form-inline" method="post" enctype="multipart/form-data">
            <div class="form-group">
               <label class="is-block"for="">Τίτλος*:</label>
               <input type="text" class="input--large" name="open_call_title" id="open_call_title" required>
               <span class="is-block is-italic">(πχ. Ανοιχτό Κάλεσμα για Εθελοντές από την ομάδα Άλφα)</span>
            </div>
            <div class="form-group">
               <label for="open_call_description" class="is-block">Περιγραφή*:</label>
               <textarea name="open_call_description" id="open_call_description"  rows="10" required></textarea>
               <span class="is-block is-italic">(πχ. Αναγράφετε όλα τα στοιχεία του δελτίου τύπου)</span>
            </div>
            <div class="form-inline filters">
							<label for="" class="is-block">Θεματική ενότητα*:</label>
<?php
	$query = "SELECT * FROM #__team_activities WHERE published=1 ORDER BY id ASC";
	$db->setQuery( $query );
	$activities = $db->loadObjectList();
	foreach ($activities as $activity) {
    echo '<div class="form-group">
						<input id="box_activity_'.$activity->id.'" name="activities['.$activity->id.']" type="checkbox" />
						<label for="box_activity_'.$activity->id.'" class="label-horizontal">'.($lang_code=='en'?$activity->name_en:$activity->name).'</label>
					</div>';
	}
?>
            </div>
            <div class="form-inline" style="vertical-align: top">
               <div class="form-group" style="vertical-align: top">
                  <label for="date_end" class="is-block">Ημερομηνία λήξης:</label>
                  <input type="text" id="opencall_date" name="opencall_date" required>
               </div>
               <div class="form-group form-group--upload" style="width:50%;vertical-align: top">
                  <label for="upload" class="is-block">Σχετικά αρχεία:</label>
									<input type="file" name="files[]" id="upload" placeholder="Παρακαλώ επιλέξτε αρχείο" multiple="multilple" class="file-browser">
									<span class="is-block is-italic">(Μπορείτε να επισυνάψετε το δελτίο τύπου  του  καλέσματός σας ή την αφίσα ή κάποιες σχετικές φωτογραφίες.)</span>
               </div>
            </div>
            <div class="form-group form-group--tail is-block clearfix">
               <span class="pull-left"><em>*Υποχρεωτικά πεδία</em></span>
               <button type="submit" class="pull-right btn btn--coral btn--bold">Καταχώριση</button>
            </div>
			<div class="form-group form-group--tail is-block clearfix">
				<span class="pull-left"><em>Εάν θέλετε να επεξεργαστείτε το open call για να προσθέσετε ενεργούς συνδέσμους ή να αλλάξετε τα στοιχεία του, μπορείτε να κάνετε κλικ στο “Επεξεργασία open calls” που βρίσκεται στο “O λογαριασμός μου.</em></span>
			</div>
		 </form>
<?php
	} elseif (isset($team) && $team->published == 0 && $user->id > 0) {
?>
	Ο λογαριασμός σας δεν έχει ενεργοποιηθεί ακόμα από το συνΑθηνά.
<?php
	} else {
?>
			Παρακαλώ <a href="<?php echo JRoute::_('index.php?option=com_users&view=login&Itemid=120'); ?>">συνδεθείτε</a> για να καταχωρίσετε open call<br /><br /><br /><br />
<?php
	}
?>
      </div>
   </div>
</div>