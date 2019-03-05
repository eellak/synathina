<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modelitem');

/**
 * Actions Model
 */
class ActionsModelForm extends JModelItem
{
	/**
	 * @var object item
	 */
	protected $item;

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
	protected function populateState()
	{
		$app = JFactory::getApplication();
		// Get the message catid
		$catid = JRequest::getInt('catid');
		$this->setState('message.catid', $catid);
		// Get the pagination
		$page = JRequest::getInt('page');
		$this->setState('message.page', $page);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		parent::populateState();
	}

	public function getUrlslug($str, $options = array())
	{
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
		$str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . ') {2,}/', '$1', $str);

		// Truncate slug to max. characters
		$str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');

		// Remove delimiter from ends
		$str = trim($str, $options['delimiter']);

		return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
	}


	public function donations_valid()
	{
		$db = JFactory::getDBO();
		$query = "SELECT id,parent_id FROM #__team_donation_types WHERE published=1";
		$db->setQuery($query);
		$donations = $db->loadObjectList();
		$donations_array = [];
		foreach ($donations as $donation) {
			$query = "SELECT id FROM #__team_donation_types WHERE parent_id='".$donation->id."' LIMIT 1";
			$db->setQuery($query);
			$has_children = $db->loadResult();
			if ($has_children) {
				//do nothing
			} else {
				$donations_array[] = $donation->id;
			}
		}

		return $donations_array;
	}

	public function donations_valid_text()
	{
		$db = JFactory::getDBO();
		$query = "SELECT id,parent_id,name FROM #__team_donation_types WHERE published=1";
		$db->setQuery($query);
		$donations = $db->loadObjectList();
		$donations_array = [];
		foreach ($donations as $donation) {
			$query = "SELECT id FROM #__team_donation_types WHERE parent_id='".$donation->id."' LIMIT 1";
			$db->setQuery($query);
			$has_children = $db->loadResult();
			if ($has_children) {
				//do nothing
			} else {
				$donations_array[] = $donation->name;
			}
		}

		return $donations_array;
	}


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Actions', $prefix = 'Actions', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

    /**
     * Load an JSON string into the registry into the given namespace [or default if a namespace is not given]
     *
     * @param    string    JSON formatted string to load into the registry
     * @return    boolean True on success
     * @since    1.5
     * @deprecated 1.6 - Oct 25, 2010
     */
    public function loadJSON($data)
    {
        return $this->loadString($data, 'JSON');
    }

	public function lockRemoteTable ($table)
	{
		$dbRemoteClass = new RemotedbConnection();
		$db_remote = $dbRemoteClass->connect();

		$query = "LOCK TABLES #__".$table." WRITE";
		$db_remote->setQuery($query);
		$db_remote->execute();
	}

	public function unlockRemoteTable ()
	{
		$dbRemoteClass = new RemotedbConnection();
		$db_remote = $dbRemoteClass->connect();

		$query = "UNLOCK TABLES";
		$db_remote->setQuery($query);
		$db_remote->execute();
	}

	public function lockTable ($table)
	{
		$db = JFactory::getDBO();

		$query = "LOCK TABLES #__".$table." WRITE";
		$db->setQuery($query);
		$db->execute();
	}

	public function unlockTable ()
	{
		$db = JFactory::getDBO();

		$query = "UNLOCK TABLES";
		$db->setQuery($query);
		$db->execute();
	}

	public function getTeam($user_id = false)
	{
		$db = JFactory::getDBO();

		if (!$user_id) {
			$user = JFactory::getUser();
			$user_id = $user->id;
		}
		$query = "SELECT * FROM #__teams WHERE user_id='".$user_id."' ";
		$db->setQuery($query);
		$db->lodObject();

		return $db->loadObject();
	}

	public function getTeamActivities()
	{
		$db = JFactory::getDBO();

		$query = "SELECT *
						FROM #__team_activities
						WHERE published=1 ";
		$db->setQuery($query);
		$activities = $db->loadObjectList();

		return $activities;
	}

	public function getTeams()
	{
		$user = JFactory::getUser();
		$user_id = $user->id;
		$db = JFactory::getDBO();

		$query = "SELECT id, name, logo FROM #__teams
					WHERE user_id!='".$user_id."' AND `hidden`=0 AND published=1 ORDER BY name ASC ";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function getTeamsUsers()
	{
		$db = JFactory::getDBO();

		$query = "SELECT u.id, u.name
					FROM #__users AS u
					INNER JOIN #__teams AS t ON t.user_Id=u.id
					WHERE u.block=0 AND u.activation='' AND t.published=1 ";
		$db->setQuery($query);

		return  $db->loadObjectList();
	}

	public function getServices()
	{
		$db = JFactory::getDBO();

		$query = "SELECT a.id, a.name
						FROM #__municipality_services AS a
						WHERE a.published=1
						ORDER BY a.id ASC ";
		$db->setQuery($query);
		$services = $db->loadObjectList();

		return $services;
	}

	public function save()
	{
		$session = JFactory::getSession();

		//validate form integrity
		$newform_session = $session->get( 'newform' );
		if ($newform_session != @$_REQUEST['newform']) {
			header('Location:'.@$_REQUEST['return_false']);
			exit();
		}

		//requirements
		//local db
		$db = JFactory::getDBO();

		//remote db
		$dbRemoteClass = new RemotedbConnection();
		$db_remote = $dbRemoteClass->connect();

		$params = JComponentHelper::getParams('com_users');
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();
		$templateDir = JURI::base() . 'templates/' . $app->getTemplate();
		$user = JFactory::getUser();
		$isroot = $user->authorise('core.admin');

		$root_insert = 0;
		//check if root inserts an action and change user credentials
		if ($isroot) {
			$root_insert = 1;
			$user->id = @$_REQUEST['user_id'];
			$user->email = '';
		}

		//get team info
		$team_info = $this->getTeam($user->id);

		//get request data
		$team_id = $team_info->id;

		$remote = 0;
		if (@$_REQUEST['remote'] == 'on') {
			$remote = 1;
		}
		$name = addslashes(@$_REQUEST['name']);
		$alias = $this->getUrlslug(@$_REQUEST['name']);
		$short_description = addslashes(@$_REQUEST['short_description']);
		$description = addslashes(@$_REQUEST['activity_description']);
		$web_link = addslashes(@$_REQUEST['web_link']);
		$partners_db = '';
		$teams_request = [];
		if (@$_REQUEST['teams']) {
			$teams_request = @$_REQUEST['teams'];
		}
		foreach ($teams_request as $p) {
			$partners_db .= $p.',';
		}
		$municipality_services = '';
		$municipality_message = '';
		$municipality_send = 0;
		if (@$_REQUEST['services'] == 'on') {
			for ($s = 1; $s < 20; $s++) {
				if (@$_REQUEST['service_'.$s] == 'on') {
					$municipality_services .= $s.',';
				}
			}
			$municipality_message = addslashes(nl2br(@$_REQUEST['services_message']));
		}

		$supports_message_array = [];
		foreach (@$_REQUEST as $key => $requestParam) {
			if (preg_match('/^support_message-*/', $key)) {
		    	$support_request_id = explode('-', $key);
        		$support_request_id = $support_request_id[count($support_request_id) - 1];
        		$supports_message_array[$support_request_id] = $requestParam;
      		}
    	}
	    $supporters_message = base64_encode(serialize($supports_message_array));

		//donations
		$query = "SELECT id,parent_id FROM #__team_donation_types WHERE published=1";
		$db->setQuery($query);
		$donations = $db->loadObjectList();
		$donations_ids = '';
		$activities_send = 0;
		$donation_other_1 = addslashes(@$_REQUEST['donation-1-other']);
		$donation_other_16 = addslashes(@$_REQUEST['donation-16-other']);
		$d = 1;
		foreach ($donations as $donation) {
			if ($donation->parent_id == 0) {
				if (@$_REQUEST['donation-'.$donation->id] == 'show') {
					$donations_ids .= $donation->id.',';
				}
			} else {
				if (@$_REQUEST['donation-'.$donation->parent_id.'-'.$donation->id] == 'on') {
					$donations_ids .= $donation->id.',';
				}
			}
		}

		//insert into actions

		//set timezone
		date_default_timezone_set('Europe/Athens');

		//insert parent action
		$this->lockRemoteTable('actions');

		$actions_query="INSERT INTO #__actions VALUES (
			'','',
			0,
			'".$team_id."',
			0,
			0,
			1,
			'".$remote."',
			0,
			'".$name."',
			'".$alias."',
			'', '', '',
			'".$donations_ids."',
			'".$donation_other_1."',
			'".$donation_other_16."',
			'".$short_description."',
			'".$description."',
			'',
			'".$web_link."',
			'".$partners_db."',
			'', '', '', '', '', '', '', '', '', '', '',
			'".$municipality_services."',
			'".$municipality_message."',
			'".$municipality_send."',
			'".$activities_send."',
			'".$supporters_message."',
			'".time()."',
			'".$root_insert."',
			'', '',
			'*',
			'".date('Y-m-d H:i:s')."',
			'',
			'".$user->id."','',
			'".date('Y-m-d H:i:s')."',
			'',
			''
		) ";

		$db_remote->setQuery($actions_query);
		$db_remote->execute();
		$parent_id = $db_remote->insertid();

		$this->unlockRemoteTable();

		require_once JPATH_CONFIGURATION.'/global_functions.php';

		$action_ok = 0;

		if ($parent_id > 0) {
			$action_ok = 1;

			//main image
			$main_image = '';
			if ($_FILES['image']['error'] == 0) {
				$image_array = explode('.', $_FILES['image']['name']);
				$ext = end($image_array);
				if (move_uploaded_file($_FILES["image"]["tmp_name"], $config->get( 'abs_path' ).'/images/actions/main_images/'.$parent_id.'.'.$ext)) {
					$main_image = $parent_id.'.'.$ext;
				}
			}

			//gallery
			if (@count($_FILES['photos']['name']) > 0) {
				if (!file_exists($config->get('abs_path').'/images/actions/'.$parent_id)) {
					mkdir($config->get( 'abs_path' ).'/images/actions/'.$parent_id, 0777);
				}
				for ($p = 0; $p < count($_FILES['photos']['name']); $p++) {
					if ($_FILES['photos']['error'][$p] == 0) {
						$image_array = explode('.',$_FILES['photos']['name'][$p]);
						$ext = end($image_array);
						move_uploaded_file($_FILES["photos"]["tmp_name"][$p], $config->get( 'abs_path' ).'/images/actions/'.$parent_id.'/'.$_FILES['photos']['name'][$p]);
					}
				}
			}

			$this->lockRemoteTable('actions');
			//update parent activity
			$query_action_update = "UPDATE #__actions SET image='".$main_image."' WHERE id='".$parent_id."' LIMIT 1";
			$db_remote->setQuery($query_action_update);
			$db_remote->execute();
			$this->unlockRemoteTable();

			//insert subactivities

			//get all activities
			$activities = $this->getTeamActivities();

			$stegi_exists_in_general = 0;
			for ($f = 0; $f < 11; $f++) {
				if ( trim(@$_REQUEST['ypotitlos_drashs_'.$f]) != '' && @$_REQUEST['date_start_'.$f] != '' && @$_REQUEST['date_end_'.$f] != '' ) {
					$subtitle = addslashes(@$_REQUEST['ypotitlos_drashs_'.$f]);
					$start_array = explode(' ', @$_REQUEST['date_start_'.$f]);
					$start_array1 = explode('/', $start_array[0]);
					$action_date_start = $start_array1[2].'-'.$start_array1[1].'-'.$start_array1[0].' '.$start_array[1].':00';
					$end_array = explode(' ', @$_REQUEST['date_end_'.$f]);
					$end_array1 = explode('/', $end_array[0]);
					$action_date_end = $end_array1[2].'-'.$end_array1[1].'-'.$end_array1[0].' '.$end_array[1].':00';

					//insert into stegi
					if (@$_REQUEST['stegi_'.$f] == 'on') {
						$stegi = 1;
						$address = '';
						$lat = 37.980522;
						$lng = 23.726839;
						$area = 1;
						$stegi_exists_in_general = 1;

						$this->lockTable('stegihours');
						$query_stegi = "INSERT INTO #__stegihours VALUES (
							'','',
							0,
							'".$team_id."',
							'".$subtitle."',
							'".$this->getUrlslug($subtitle)."',
							'',
							'".$subtitle."',
							'".$action_date_start."',
							'".$action_date_end."',
							'".$parent_id."',
							'".$root_insert."',
							1,
							1,
							'*',
							'".date('Y-m-d H:i:s')."',
							'".date('Y-m-d H:i:s')."',
							'".$user->id."',
							'".$user->id."',
							'','',''
						)";
						$db->setQuery($query_stegi);
						$db->execute();
						$this->unlockTable();

						//email to admin
						$emails = [];
						$att = 'stegi_terms_conditions.pdf';
						$s_array = array($team_info->name, $subtitle, $start_array1[0].'-'.$start_array1[1].'-'.$start_array1[2], $start_array[1], $end_array[1]);
						if (!$isroot) {
							synathina_email('stegi_action_created_admin', $s_array, $emails, '');
						}
					} else {
						$stegi = 0;
						$address = addslashes(@$_REQUEST['address_'.$f]);
						$lat = @$_REQUEST['lat_'.$f];
						$lng = @$_REQUEST['lng_'.$f];
						require_once JPATH_CONFIGURATION.'/get_map.php';
						$pointLocation = new pointLocation();
						$points = array($lat." ".$lng);
						for ($i = 1; $i < 8; $i++) {
							${'polygon'.$i} = [];
							$xml = simplexml_load_file($templateDir.'/js_collections/maps/'.$i.'o_Diamerisma.kml');
							$placemarks = $xml->Document->Placemark;
							foreach ($placemarks as $placemark)
							{
								$array_xy = [];
								$coordinates = '';
								$coordinates_array = [];
								$array_xy = $placemark->Point->coordinates;
								$coordinates = (string)$array_xy;
								$coordinates_array = explode(',', $coordinates);
								${'polygon'.$i}[] = $coordinates_array[1].' '.$coordinates_array[0];
							}
						}
						$area = 0;
						foreach ($points as $key => $point) {
								for ($i = 1; $i <8; $i++) {
									if ($area == 0) {
										$pointloc=$pointLocation->pointInPolygon($point, ${'polygon'.$i});
										if ($pointloc == 'inside') {
											$area = $i;
										}
									}
								}
						}
					}

					//activities
					$activities_ids = '';
					$sub_remote = 0;
					foreach ($activities as $activity) {
						if (@$_REQUEST['activity_'.$activity->id.'_'.$f] == 'on') {
							$activities_ids .= $activity->id.',';
							//check if refugees is selected
							if ($activity->id == 12 && $remote == 1 && $isroot) {
								$sub_remote = 1;
							}
						}
					}

					//insert subaction
					$this->lockRemoteTable('actions');
					$subactions_query="INSERT INTO #__actions VALUES (
						'','',
						0,
						'".$team_id."',
						0,
						'".$parent_id."',
						1,
						'".$sub_remote."',
						0,
						'','','',
						'".$subtitle."',
						'".$activities_ids."',
						'','','','','','','','','',
						'".$lat."',
						'".$lng."',
						'".$address."',
						'',
						'".$area."',
						'".$action_date_start."',
						'".$action_date_end."',
						'".$stegi."',
						'','','','','','','',
						'".time()."',
						'".$root_insert."',
						'','',
						'*',
						'".date('Y-m-d H:i:s')."',
						'',
						".$user->id.",
						'',
						'".date('Y-m-d H:i:s')."',
						'',
						''
					)";
					$db_remote->setQuery($subactions_query);
					$db_remote->execute();
					$this->unlockRemoteTable();
				}
			}
		}

		//send new action emails
		if ($action_ok == 1) {
			//email to user
			if ($user->email != '' && !$isroot) {
				$emails = array($user->email);
				$att = '';
				$s_array = [];
				synathina_email('action_created_user_pending', $s_array, $emails, $att);
			}

			//email to user if root has added an action on behalf of him
			if ($user->email == '' && $isroot) {
				$query = "SELECT email
								FROM #__users
								WHERE block=0 AND activation='' AND id='".$user->id."'
								LIMIT 1 ";
				$db->setQuery($query);
				$team_email = $db->loadResult();
				$emails = array($team_email);
				$att = '';
				$s_array = array($config->get('live_site').'/'.JRoute::_('index.php?option=com_actions&view=action&id='.$parent_id.'&Itemid=138'), $name);
				synathina_email('action_created_user_from_root', $s_array, $emails, $att);
			}

			//email to admin
			if (!$isroot) {
				$emails = [];
				$s_array = array($team_info->name, $config->get('live_site').'/'.JRoute::_('index.php?option=com_actions&view=edit&id='.$parent_id.'&Itemid=144'), $name, $config->get('live_site').'/'.JRoute::_('index.php?option=com_actions&view=edit&id='.$parent_id.'&Itemid=144'));
				synathina_email('action_created_admin', $s_array, $emails, '');
			}

			//redirect
			if ($isroot) {
				header('Location:'.JRoute::_('index.php?option=com_actions&view=actions&Itemid=138'));
			} else {
				header('Location:'.JRoute::_('index.php?option=com_actions&view=myactions&Itemid=143&action_save=1'));
			}
			exit();
		} else {
			$s_array = array( $name, $team_info->name );
			$emails = [];
			synathina_email('action_fail_admin', $s_array, $emails, '');
			header('Location:'.JRoute::_('index.php?option=com_actions&view=myactions&Itemid=143&action_error=1'));
			exit();
		}

		return true;
	}

	public function getData()
	{
		if ($this->data === null) {
			$this->data = new stdClass;
			$app = JFactory::getApplication();
		}

		return $this->data;
	}

}
