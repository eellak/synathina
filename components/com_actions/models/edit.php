<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modelitem');

/**
 * Actions Model
 */
class ActionsModelEdit extends JModelItem
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

	public function getUrlslug($str, $options = array()) {
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

	public function donations_valid($text = false)
	{
		$db = JFactory::getDBO();

		$query = "SELECT id, name FROM #__team_donation_types WHERE published = 1";
		$db->setQuery($query);
		$donations = $db->loadObjectList();
		$donations_array = [];
		foreach ($donations as $donation) {
			$query = "SELECT id FROM #__team_donation_types WHERE parent_id='".$donation->id."' LIMIT 1";
			$db->setQuery($query);
			$has_children = $db->loadResult();
			if (!$has_children) {
				if ($text) {
					$donations_array[] = $donation->name;
				} else {
					$donations_array[] = $donation->id;
				}
			}
		}

		return $donations_array;
	}

	//GET Teams Ids from remote site with Comma Delimiter
	public function getUserIdsCommaDelRemote($donations_id)
	{
		//remote connections
		$dbRemoteClass = new RemotedbConnection();
		$db_remote = $dbRemoteClass->remoteConnect();

		$query = "SELECT user_id FROM #__teams
					WHERE published=1 AND support_actions = 1 AND FIND_IN_SET(".$donations_id.",`org_donation`) ";
		$db_remote->setQuery($query);
		$team_user_ids = $db_remote->loadAssocList();

		return implode(', ', array_map(function ($entry) {
		  return $entry['user_id'];
		}, $team_user_ids));
	}

	public function getTeam()
	{
		$user = JFactory::getUser();
		$isroot = $user->authorise('core.admin');
		$db = JFactory::getDBO();

		if ($isroot == 1) {
			$action = $this->getAction();
			$query = "SELECT t.*, u.email AS user_email FROM #__teams AS t
				INNER JOIN #__users AS u
				ON u.id=t.user_id
				WHERE t.id='".$action->team_id."' LIMIT 1";
		} else {
			$query = "SELECT t.*, u.email AS user_email FROM #__teams AS t
				INNER JOIN #__users AS u
				ON u.id=t.user_id
				WHERE t.user_id='".$user->id."' LIMIT 1";
		}
		$db->setQuery( $query );

		return $db->loadObject();
	}

	public function getActivities()
	{
		$db = JFactory::getDBO();

		$query = "SELECT * FROM #__team_activities AS a
					WHERE a.published = 1 ";
		$db->setQuery( $query );

		return $db->loadObjectList();
	}

	public function getTeams()
	{
		$db = JFactory::getDBO();

		$query = "SELECT id, name, logo FROM #__teams
					WHERE create_actions = 1 AND published = 1 AND `hidden` = 0 ORDER BY name ASC";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function getSupporters()
	{
		$db = JFactory::getDBO();

		$query = "SELECT id, name, logo FROM #__teams
					WHERE support_actions = 1 AND published = 1 AND `hidden` = 0 ORDER BY name ASC";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	//GET Teams Ids with Comma Delimiter
	public function getUserIdsCommaDel($donations_id, $team_id)
	{
		$db = JFactory::getDBO();

		$query = "SELECT user_id FROM #__teams
					WHERE published=1 AND support_actions = 1 AND id!='".$team_id."' AND FIND_IN_SET(".$donations_id.",`org_donation`) ";
		$db->setQuery($query);
		$team_user_ids = $db->loadAssocList();

		return implode(', ', array_map(function ($entry) {
		  return $entry['user_id'];
		}, $team_user_ids));
	}

	public function getServices()
	{
		$db = JFactory::getDBO();

		$query = "SELECT a.id, a.name
							FROM #__municipality_services AS a
							WHERE a.published = 1
							ORDER BY a.id ASC ";
		$db->setQuery( $query );
		$services = $db->loadObjectList();

		return $services;
	}

	public function getAction()
	{
		$user = JFactory::getUser();
		$isroot = $user->authorise('core.admin');
		$activityClass = new RemotedbActivity();

		$fields = [];
		$limit = 1;
		if ($isroot == 1) {
			$where = "id='".@$_REQUEST['id']."'";
		} else {
			$where = "id='".@$_REQUEST['id']."'";
		}
		$activity = $activityClass->getActivity($fields, $where, 1);

		if ($activity) {
			return $activity;
		} else {
			header('Location:'.JURI::root());
			exit();
		}
	}

	public function getSubactions()
	{
		$user = JFactory::getUser();
		$isroot = $user->authorise('core.admin');
		$activityClass = new RemotedbActivity();

		$fields = [];
		$order_by = "id ASC";
		if ($isroot == 1) {
			$where = "action_id='".@$_REQUEST['id']."'";
		} else {
			$where = "action_id='".@$_REQUEST['id']."'";
		}

		return $activityClass->getActivities($fields, $where, $order_by);
	}

	public function getTeamDonationTypes($only_parents = true) {
		$db = JFactory::getDBO();

		$where = " AND parent_id=0";
		if (!$only_parents) {
			$where = "";
		}
		$query = "SELECT id, parent_id, name FROM #__team_donation_types
					WHERE published = 1 ".$where." ORDER BY id ASC";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function save()
	{
		$session = JFactory::getSession();
		$editform_session = $session->get( 'editform' );
		if ($editform_session != @$_REQUEST['editform']) {
			header('Location:'.@$_REQUEST['return']);
			exit();
		}

		$config = JFactory::getConfig();
		$app = JFactory::getApplication();
		$templateDir = JURI::base() . 'templates/' . $app->getTemplate();
		$user = JFactory::getUser();
		$isroot = $user->authorise('core.admin');

		$db = JFactory::getDBO();

		//remote db
		$dbRemoteClass = new RemotedbConnection();
		$db_remote = $dbRemoteClass->connect();

		//get request data
		$action_id = @$_REQUEST['action_id'];

		$activityClass = new RemotedbActivity();
		$where = "id='".$action_id."'";
		$limit = 1;
		$action = $activityClass->getActivity([], $where, $limit);

		$activities_send = $action->activities_send;
		$municipality_send = $action->municipality_send;
		$published = $action->published;
		$published_old = $published;

		$team_info = $this->getTeam();
		$team_id = $team_info->id;

		$remote = 0;
		$remote_pre = $action->remote;
		if (@$_REQUEST['remote'] == 'on') {
			$remote = 1;
		}

		$best_practice = 0;

		if ($isroot == 1) {
			if (@$_REQUEST['best_practice'] == 'on') {
				$best_practice = 1;
			}
			if (@$_REQUEST['published'] == 'on') {
				$published = 1;
			} else {
				$published = 0;
			}
		}
		$name = addslashes(@$_REQUEST['name']);
		$alias = '';
		$short_description = addslashes(@$_REQUEST['short_description']);
		$description = addslashes(@$_REQUEST['activity_description']);
		$web_link = addslashes(@$_REQUEST['web_link']);
		$partners_db = '';
		if (@$_REQUEST['teams'] != '') {
			foreach (@$_REQUEST['teams'] as $p) {
				$partners_db .= $p.',';
			}
		}
		$supporters_db = '';
		if (@$_REQUEST['supporters'] != '') {
			foreach (@$_REQUEST['supporters'] as $p) {
				$supporters_db .= $p.',';
			}
		}
		$municipality_services = '';
		$municipality_message = '';

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
			if (preg_match('/^support_message-*/',$key)) {
				$support_request_id = explode('-',$key);
				$support_request_id = $support_request_id[count($support_request_id) - 1];
				$supports_message_array[$support_request_id] = $requestParam;
			}
		}

		$supporters_message = base64_encode(serialize($supports_message_array));

		//donations
		$donations = $this->getTeamDonationTypes(false);
		$donations_ids = '';
		$donation_other_1 = addslashes(@$_REQUEST['donation-1-other']);
		$donation_other_16 = addslashes(@$_REQUEST['donation-16-other']);
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

		//proper timezone
		date_default_timezone_set('Europe/Athens');

		//update parent action
		$this->lockRemoteTable('actions');
		$actions_query = "UPDATE #__actions SET
			published='".$published."',
			".($isroot ? "remote = '".$remote."'," : "")."
			best_practice='".$best_practice."',
			team_id='".$team_id."',
			name='".$name."',
			org_donation='".$donations_ids."',
			donation_eidos='".$donation_other_1."',
			donation_technology='".$donation_other_16."',
			short_description='".$short_description."',
			description='".$description."',
			web_link='".$web_link."',
			partners='".$partners_db."',
			supporters='".$supporters_db."',
			municipality_services='".$municipality_services."',
			municipality_message='".$municipality_message."',
			supporters_message='".$supporters_message."',
			timestamp='".time()."',
			modified='".date('Y-m-d H:i:s')."',
			modified_by=".$user->id."
			WHERE id='".$action_id."' ";

		$db_remote->setQuery($actions_query);
		$db_remote->execute();
		$parent_id = $action_id;
		$this->unlockRemoteTable();

		if ($parent_id > 0) {

			//main image
			if ($_FILES['image']['error'] == 0) {
				$image_array = explode('.', $_FILES['image']['name']);
				$ext = end($image_array);
				if (move_uploaded_file($_FILES["image"]["tmp_name"], $config->get( 'abs_path' ).'/images/actions/main_images/'.$parent_id.'.'.$ext)) {
					$main_image = $parent_id.'.'.$ext;
					//update parent
					$this->lockRemoteTable('actions');
					$query_action_update = "UPDATE #__actions SET image='".$main_image."' WHERE id='".$parent_id."' LIMIT 1";
					$db_remote->setQuery($query_action_update);
					$db_remote->execute();
					$this->unlockRemoteTable();
				}
			}

			//gallery
			if (@count($_FILES['photos']['name']) > 0) {
				if (!file_exists($config->get( 'abs_path' ).'/images/actions/'.$parent_id)) {
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

			require_once JPATH_CONFIGURATION.'/global_functions.php';

			//delete all subactions
			$this->lockRemoteTable('actions');
			$query = "DELETE FROM #__actions WHERE action_id='".$parent_id."' ";
			$db_remote->setQuery($query);
			$db_remote->execute();
			$this->unlockRemoteTable();

			//stegi
			$this->lockTable('stegihours');
			$query = "DELETE FROM #__stegihours WHERE action_id='".$parent_id."' ";
			$db->setQuery($query);
			$db->execute();
			$this->unlockTable();

			//get all activities
			$activities = $this->getActivities();
			$stegi_exists_in_general = 0;

			//check if refugees exist in activities
			$refugees_activity = 0;
			//insert subactions
			for ($f = 0; $f < 11; $f++) {
				if (@$_REQUEST['ypotitlos_drashs_'.$f] != '' && @$_REQUEST['date_start_'.$f] != '' && @$_REQUEST['date_end_'.$f] != '') {
					$subtitle = addslashes(@$_REQUEST['ypotitlos_drashs_'.$f]);
					$start_array = explode(' ',@$_REQUEST['date_start_'.$f]);
					$start_array1 = explode('/',$start_array[0]);
					$action_date_start = $start_array1[2].'-'.$start_array1[1].'-'.$start_array1[0].' '.$start_array[1].':00';
					$end_array = explode(' ',@$_REQUEST['date_end_'.$f]);
					$end_array1 = explode('/',$end_array[0]);
					$action_date_end = $end_array1[2].'-'.$end_array1[1].'-'.$end_array1[0].' '.$end_array[1].':00';
					if (@$_REQUEST['stegi_'.$f] == 'on') {
						$stegi = 1;
						$address = '';
						$lat = 37.980522;
						$lng = 23.726839;
						$area = 1;
						$stegi_exists_in_general = 1;

						//insert into stegi
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
							'".$published."',
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

						if ($published_old == 0) {
							//email to admin
							$emails = [];
							$s_array = array($team_info->name, $subtitle, $start_array1[0].'-'.$start_array1[1].'-'.$start_array1[2], $start_array[1], $end_array[1]);
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
							for ($i = 1; $i<8; $i++) {
								if ($area == 0) {
									$pointloc = $pointLocation->pointInPolygon($point, ${'polygon'.$i});
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
							//check if refugees exist
							if ($activity->id == 12) {
								$refugees_activity = 1;
							}
							//check if refugees is selected and update only if root
							if ($isroot) {
								if ($activity->id == 12 && $remote == 1) {
									$sub_remote = 1;
								}
							} else {
								if ($activity->id == 12 && $remote_pre == 1) {
									$sub_remote = 1;
								}
							}
						}
					}

					//insert subaction
					$this->lockRemoteTable('actions');
					$subactions_query = "INSERT INTO #__actions VALUES (
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
						'".$published."',
						'','',
						'*',
						'".date('Y-m-d H:i:s')."',
						'',
						".$user->id.",
						'',
						'".date('Y-m-d H:i:s')."',
						'',''
					)";
					$db_remote->setQuery($subactions_query);
					$db_remote->execute();
					$this->unlockRemoteTable();
				}
			}
		}

		//create new action emails
		if ($published == 1 && $isroot == 1) {
			//email to municipalities
			if ($municipality_services != '' && $municipality_send == 0) {
				//email to municipalities pending code
				$emails = array('tep@athens.gr');
				//municipality_services
				$municipality_array = explode(',', $municipality_services);
				array_filter($municipality_array);
				$municipality_services_text = '';
				for ($m = 0; $m < count($municipality_array); $m++) {
					$query = "SELECT email_name FROM #__municipality_services WHERE id='".$municipality_array[$m]."' AND published = 1";
					$db->setQuery($query);
					$municipality_services_text .= '- '.$db->loadResult().'<br />';
				}
				$team_link = 'http://www.synathina.gr'.JRoute::_('index.php?option=com_teams&view=team&id='.$team_id.'&Itemid=140');
				$team_municipality_text = $team_info->contact_1_name.'<br />'.$team_info->contact_1_email.'<br />'.$team_info->contact_1_phone;
				$drasi_url = $config->get( 'main_url' ).JRoute::_('index.php?option=com_actions&view=action&id='.$parent_id.'&Itemid=138');
				$s_array = array($team_link, $team_info->name, str_replace('- <br />', '', $municipality_services_text), $municipality_message, $drasi_url, $team_municipality_text);
				synathina_email('action_created_municipality', $s_array, $emails, '');

				//update database
				$query_municipality_update = "UPDATE #__actions SET municipality_send = 1 WHERE id='".$action_id."' LIMIT 1";
				$db_remote->setQuery($query_municipality_update);
				$db_remote->execute();
			}

			//email to supporters
			if ($donations_ids != '' && $activities_send == 0) {
				$supporters_exist = 0;
				$emails = [];
				$supporters_emails = [];
				$donations_array = explode(',',$donations_ids);
				array_filter($donations_array);
				$donations_valid = $this->donations_valid();
				$donations_valid_text = $this->donations_valid(true);
				$db_accmr = $dbRemoteClass->remoteConnect();
				for ($d = 0; $d < count($donations_array); $d++) {
					if (in_array($donations_array[$d], $donations_valid)) {
						$team_user_ids_text = $this->getUserIdsCommaDel($donations_array[$d], $team_id);
						if ($team_user_ids_text) {
							$query = "SELECT email FROM #__users
											WHERE id IN (".$team_user_ids_text.") ";
							$db->setQuery($query);
							$emails_results = $db->loadObjectList();
							foreach ($emails_results as $emails_result) {
								$emails[] = $emails_result->email;
								$supporters_emails[$donations_array[$d]][] = $emails_result->email;
								$supporters_donation_titles[$donations_array[$d]] = $donations_valid_text[array_search($donations_array[$d], $donations_valid)];
							}
							$supporters_exist = 1;
						}
						//get remote supporters
						if ($refugees_activity == 1) {
							$team_user_ids_text_remote = $this->getUserIdsCommaDelRemote($donations_array[$d]);
							if ($team_user_ids_text_remote) {
								$query = "SELECT email FROM #__users
												WHERE id IN (".$team_user_ids_text_remote.") ";
								$db_accmr->setQuery($query);
								$emails_results_remote = $db_accmr->loadObjectList();
								foreach ($emails_results_remote as $emails_result) {
									//$emails[] = $emails_result->email;
									$supporters_emails[$donations_array[$d]][] = $emails_result->email;
									$supporters_donation_titles[$donations_array[$d]] = $donations_valid_text[array_search($donations_array[$d], $donations_valid)];
								}
								$supporters_exist = 1;
							}
						}
					}
				}

				$query_activities_update = "UPDATE #__actions SET activities_send = 1 WHERE id='".$action_id."' LIMIT 1";
				$db_remote->setQuery($query_activities_update);
				$db_remote->execute();

				if (!empty($supporters_emails)) {
					$team_link = 'http://www.synathina.gr'.JRoute::_('index.php?option=com_teams&view=team&id='.$team_id.'&Itemid=140');
					$drasi_url = $config->get( 'main_url' ).JRoute::_('index.php?option=com_actions&view=action&id='.$parent_id.'&Itemid=138');
					if (@unserialize($supporters_message)) {
						$supporters_message = unserialize($supporters_message);
					} else {
						$supporters_message = unserialize(base64_decode($supporters_message));
					}
					foreach ($supporters_emails as $key => $emails) {
						$s_array = array($team_link, $team_info->name, $supporters_donation_titles[$key], '"'.$supporters_message[$key].'"', $drasi_url, $name, $team_info->contact_1_name, $team_info->contact_1_email, $team_info->contact_1_phone);
						$emails_unique = array_unique($emails);
						//count emails per donations
						$query_donations_counter = "INSERT INTO #__donations_counter
													VALUES ('', '".date('Y-m-d H:i:s')."', '".count($emails_unique)."', '".$key."' )";
						$db->setQuery($query_donations_counter);
						$db->execute();
						foreach ($emails_unique as $email) {
							synathina_email('action_created_supporters', $s_array, [$email], '');
						}
					}
				}
			}

			//email to user
			if ($published_old == 0) {
				if ($team_info->user_email != '') {
					$emails = [$team_info->user_email];
					$att = '';
					$s_array = array(0 => ' ', ' ', ' ');
					if ($stegi_exists_in_general == 1) {
						$s_array[0] = '<p>Στα συνημμένα μπορείτε να διαβάσετε τους όρους χρήσης της στέγης τους οποίους δηλώνετε ότι αποδέχεστε ανεπιφύλακτα.</p>';
						$att='stegi_terms_conditions.pdf';
					}
					if ($municipality_services != '') {
						$s_array[1] = '<p>Το αίτημά σας προς το δήμο Αθηναίων έχει διαβιβαστεί στην αρμόδια υπηρεσία. Σε περίπτωση καθυστέρησης παρακαλούμε καλέστε στη γραμμή εξυπηρέτησης του Δημότη 1595 ή επικοινωνήστε με την ομάδα του συνΑθηνά στο synathina@athens.gr ή τηλεφωνικά, στο 2105277521.</p>';
					}
					if ($supporters_exist == 1) {
						$s_array[2] = '<p>Το αίτημά σας για υποστήριξη έχει διαβιβαστεί στους αντίστοιχους υποστηρικτές. Οι υποστηρικτές που θα ενδιαφερθούν να ενισχύσουν τη δράση σας θα επικοινωνήσουν μαζί σας στα στοιχεία τα οποία έχετε καταχωρίσει.</p>';
					}
					synathina_email('action_created_user_confirmed', $s_array, $emails, $att);
				}
			}
		}

		//email to user if activity is cancelled by the admin
		if ($published == 0 && $published_old == 1 && $isroot == 1) {
			if ($team_info->user_email != '') {
				$emails[] = $team_info->user_email;
				$s_array = array($name);
				synathina_email('action_cancelled_user', $s_array, $emails, '');
			}
		}

		if ($isroot == 1) {
			header('Location:'.JRoute::_('index.php?option=com_actions&view=action&id='.$action_id.'&Itemid=138'));
		} else {
			header('Location:'.JRoute::_('index.php?option=com_actions&view=myactions&Itemid=143'));
		}
		exit();

		return true;
	}

	public function getData()
	{
		if ($this->data === null)
		{
			$this->data = new stdClass;
			$app = JFactory::getApplication();
		}

		return $this->data;
	}
}
