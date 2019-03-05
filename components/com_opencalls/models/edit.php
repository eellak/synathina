<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modelitem');

/**
 * Actions Model
 */
class OpencallsModelEdit extends JModelItem
{
	/**
	 * @var object item
	 */
	protected $items;

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */

	/**
	 * Get the message
	 * @return object The message to be displayed to the user
	 */
	public function getOpencalls()
	{
		//db connection
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$query = "SELECT * FROM #__content WHERE id='".@$_REQUEST['id']."' AND created_by='".$user->id."' AND state=1 LIMIT 1 ";
		$db->setQuery($query);
		$items = $db->loadObjectList();
		return $items;
	}

	function url_slug_opencall($str, $options = array()) {
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

	public function save()
	{
		//echo '<pre>';
		//print_r($_REQUEST);
		//print_r($_FILES);
		//echo '</pre>';
		//die;
		$session = JFactory::getSession();
		$editform_session=$session->get( 'editform' );
		if($editform_session==@$_REQUEST['editform']){
			//ok
		}else{
			header('Location:'.@$_REQUEST['return']);
			exit();
		}
		$params = JComponentHelper::getParams('com_users');
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();
		$templateDir = JURI::base() . 'templates/' . $app->getTemplate();
		$abspath=$config->get( 'abs_path' );
		// Initialise the table with JUser.
		$user = JFactory::getUser();
		$isroot = $user->authorise('core.admin');
		$db = JFactory::getDBO();

		date_default_timezone_set('Europe/Athens');

		//requests
		$title=addslashes(@$_REQUEST['open_call_title']);
		$introtext=addslashes(str_replace('\n','',@$_REQUEST['open_call_description']));
		$alias=$this->url_slug_opencall($title);
		$user_id=$user->id;
		date_default_timezone_set('Europe/Athens');
		$created=date('Y-m-d');
		$end_date='';
		if(@$_REQUEST['open_call_date']!=''){
			$end_date_array=explode('/',@$_REQUEST['open_call_date']);
			$end_date=trim(@$end_date_array[2]).'-'.@$end_date_array[1].'-'.@$end_date_array[0];
		}
		$activities_ids='';
		$activities_keys=@array_keys(@$_REQUEST['activities']);
		$activities_ids='[';
		for($a=0; $a<count($activities_keys); $a++){
			$activities_ids.='"'.$activities_keys[$a].'",';
		}
		$activities_ids=rtrim($activities_ids,',');
		$activities_ids.=']';

		//update open call
		$query_update="UPDATE #__content
						SET alias='".$alias."',title='".$title."', introtext='".$introtext."', `fulltext`='".$introtext."',
						attribs='{\"opencall_date\":\"".@$end_date."\",\"opencall_activities\":".$activities_ids.",\"news_subtitle\":\"\",\"article_video\":\"\",\"show_title\":\"\",\"link_titles\":\"\",\"show_tags\":\"\",\"show_intro\":\"\",\"info_block_position\":\"\",\"show_category\":\"\",\"link_category\":\"\",\"show_parent_category\":\"\",\"link_parent_category\":\"\",\"show_author\":\"\",\"link_author\":\"\",\"show_create_date\":\"\",\"show_modify_date\":\"\",\"show_publish_date\":\"\",\"show_item_navigation\":\"\",\"show_icons\":\"\",\"show_print_icon\":\"\",\"show_email_icon\":\"\",\"show_vote\":\"\",\"show_hits\":\"\",\"show_noauth\":\"\",\"urls_position\":\"\",\"alternative_readmore\":\"\",\"article_layout\":\"\",\"show_publishing_options\":\"\",\"show_article_options\":\"\",\"show_urls_images_backend\":\"\",\"show_urls_images_frontend\":\"\"}',
						modified='".date('Y-m-d H:i:s')."',modified_by=".$user->id."
						WHERE id='".@$_REQUEST['id']."' AND created_by='".$user->id."' AND state=1 ";
		$db->setQuery($query_update);
		$db->execute();

		//files
		$images_types=array('image/bmp','image/gif','image/jpeg','image/jpeg','image/png');
		$images_types_mime=array('bmp','gif','jpeg','jpg','png');
		$file_types=array('application/msword','application/pdf','application/vnd.openxmlformats-officedocument.presentationml.presentation','application/vnd.openxmlformats-officedocument.wordprocessingml.document');
		$file_types_mime=array('doc','pdf','pptx','docx');
		$article_id=@$_REQUEST['id'];
		if(!empty($_FILES['files'])){
			$img_ordering=1;
			for($f=0; $f<count($_FILES['files']['name']); $f++){
				if($_FILES['files']['error'][$f]==0){
					//proceed to upload
					$ext_array = @explode('.',$_FILES['files']['name'][$f]);
					$ext=strtolower(end($ext_array));
					$rand=md5($_FILES['files']['name'][$f]);
					//image case
					if(in_array($_FILES['files']['type'][$f],$images_types)){
						//insert to di images
						$query_di = "INSERT INTO #__di_images VALUES ('','".$article_id."',1,'".$rand.'.'.$ext."','','',0,'".date('Y-m-d H:i:s')."','".$img_ordering."','','')";
						$db->setQuery($query_di);
						$db->execute();
						$di_id=$db->insertid();
						$img_ordering++;
						//move image to images/di
						move_uploaded_file($_FILES['files']['tmp_name'][$f], 'images/di/'.$article_id.'_'.$di_id.'_'.$rand.'.'.$ext);
						copy('/images/di/'.$article_id.'_'.$di_id.'_'.$rand.'.'.$ext,'images/di/'.$article_id.'_'.$di_id.'_regular_'.$rand.'.'.$ext);
						copy('/images/di/'.$article_id.'_'.$di_id.'_'.$rand.'.'.$ext,'images/di/'.$article_id.'_'.$di_id.'_thumb_'.$rand.'.'.$ext);
						copy('/images/di/'.$article_id.'_'.$di_id.'_'.$rand.'.'.$ext,'images/di/'.$article_id.'_'.$di_id.'_zoomed_'.$rand.'.'.$ext);
					}
					//file case
					if(in_array($_FILES['files']['type'][$f],$file_types)){
						//insert to attachments
						$key = array_search($ext, $file_types_mime);
						$query_att = "INSERT INTO #__attachments VALUES ('','".$rand.".".$ext."','".$abspath."/attachments/article/".$article_id."/".$rand.".".$ext."','".$file_types[$key]."','".$_FILES['files']['size'][$f]."',
							'attachments/article/".$article_id."/".$rand.".".$ext."','file',0,0,0,'".addslashes($_FILES['files']['name'][$f])."','','".$ext.".gif',1,1,'','','','com_content','article','".$article_id."','".date('Y-m-d- H:i:s')."','".$user->id."','".date('Y-m-d- H:i:s')."',
							'".$user->id."',0) ";
						$db->setQuery($query_att);
						$db->execute();
						//mkdir attachments/content_id/ and move file there
						if (!file_exists(JURI::root( true ).'/attachments/article/'.$article_id)) {
							mkdir('/attachments/article/'.$article_id);
						}
						move_uploaded_file($_FILES['files']['tmp_name'][$f], '/attachments/article/'.$article_id.'/'.$rand.'.'.$ext);
					}
				}
			}
		}

		header('Location:'.JRoute::_(@$_REQUEST['return']));
		exit();
		//return true;
	}

	public function getActivities()
	{
		//db connection
		$db = JFactory::getDBO();
		$query="SELECT * FROM #__team_activities WHERE published=1 ORDER BY name ASC";
		$db->setQuery( $query );
		$activities = $db->loadObjectList();
		return $activities;
	}

	public function getImages()
	{
		//db connection
		$db = JFactory::getDBO();
		$query="SELECT * FROM #__di_images WHERE state=1 AND object_id='".@$_REQUEST['id']."' ORDER BY ordering ASC";
		$db->setQuery( $query );
		$images = $db->loadObjectList();
		return $images;
	}

	public function getFiles()
	{
		//db connection
		$db = JFactory::getDBO();
		$query="SELECT * FROM #__attachments WHERE state=1 AND parent_type='com_content' AND parent_entity='article' AND parent_id='".@$_REQUEST['id']."' ORDER BY id DESC";
		$db->setQuery( $query );
		$files = $db->loadObjectList();
		return $files;
	}
}
