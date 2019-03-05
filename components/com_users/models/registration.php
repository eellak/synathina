<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Registration model class for Users.
 *
 * @since  1.6
 */
class UsersModelRegistration extends JModelForm
{
	/**
	 * @var    object  The user registration data.
	 * @since  1.6
	 */
	protected $data;

	/**
	 * Method to activate a user account.
	 *
	 * @param   string  $token  The activation token.
	 *
	 * @return  mixed    False on failure, user object on success.
	 *
	 * @since   1.6
	 */
	public function activate($token)
	{
		$config = JFactory::getConfig();
		$userParams = JComponentHelper::getParams('com_users');
		$db = $this->getDbo();

		// Get the user id based on the token.
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id'))
			->from($db->quoteName('#__users'))
			->where($db->quoteName('activation') . ' = ' . $db->quote($token))
			->where($db->quoteName('block') . ' = ' . 1)
			->where($db->quoteName('lastvisitDate') . ' = ' . $db->quote($db->getNullDate()));
		$db->setQuery($query);

		try
		{
			$userId = (int) $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			$this->setError(JText::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);

			return false;
		}

		// Check for a valid user id.
		if (!$userId)
		{
			$this->setError(JText::_('COM_USERS_ACTIVATION_TOKEN_NOT_FOUND'));

			return false;
		}

		// Load the users plugin group.
		JPluginHelper::importPlugin('user');

		// Activate the user.
		$user = JFactory::getUser($userId);

		// Admin activation is on and user is verifying their email
		if (($userParams->get('useractivation') == 2) && !$user->getParam('activate', 0))
		{
			$uri = JUri::getInstance();

			// Compile the admin notification mail values.
			$data = $user->getProperties();
			$data['activation'] = JApplicationHelper::getHash(JUserHelper::genRandomPassword());
			$user->set('activation', $data['activation']);
			$data['siteurl'] = JUri::base();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base . JRoute::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'], false);

			// Remove administrator/ from activate url in case this method is called from admin
			if (JFactory::getApplication()->isAdmin())
			{
				$adminPos         = strrpos($data['activate'], 'administrator/');
				$data['activate'] = substr_replace($data['activate'], '', $adminPos, 14);
			}

			$data['fromname'] = $config->get('fromname');
			$data['mailfrom'] = $config->get('mailfrom');
			$data['sitename'] = $config->get('sitename');
			$user->setParam('activate', 1);
			$emailSubject = JText::sprintf(
				'COM_USERS_EMAIL_ACTIVATE_WITH_ADMIN_ACTIVATION_SUBJECT',
				$data['name'],
				$data['sitename']
			);

			$emailBody = JText::sprintf(
				'COM_USERS_EMAIL_ACTIVATE_WITH_ADMIN_ACTIVATION_BODY',
				$data['sitename'],
				$data['name'],
				$data['email'],
				$data['username'],
				$data['activate']
			);

			// Get all admin users
			$query->clear()
				->select($db->quoteName(array('name', 'email', 'sendEmail', 'id')))
				->from($db->quoteName('#__users'))
				->where($db->quoteName('sendEmail') . ' = ' . 1);

			$db->setQuery($query);

			try
			{
				$rows = $db->loadObjectList();
			}
			catch (RuntimeException $e)
			{
				$this->setError(JText::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);

				return false;
			}

			// Send mail to all users with users creating permissions and receiving system emails
			foreach ($rows as $row)
			{
				$usercreator = JFactory::getUser($row->id);

				if ($usercreator->authorise('core.create', 'com_users'))
				{
					if ($config->get('dev_mode') == 1) {
						$row->email = $config->get('dev_email');
					}
					$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $emailSubject, $emailBody);

					// Check for an error.
					if ($return !== true)
					{
						$this->setError(JText::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'));

						return false;
					}
				}
			}
		}
		// Admin activation is on and admin is activating the account
		elseif (($userParams->get('useractivation') == 2) && $user->getParam('activate', 0))
		{
			$user->set('activation', '');
			$user->set('block', '0');

			// Compile the user activated notification mail values.
			$data = $user->getProperties();
			$user->setParam('activate', 0);
			$data['fromname'] = $config->get('fromname');
			$data['mailfrom'] = $config->get('mailfrom');
			$data['sitename'] = $config->get('sitename');
			$data['siteurl'] = JUri::base();
			$emailSubject = JText::sprintf(
				'COM_USERS_EMAIL_ACTIVATED_BY_ADMIN_ACTIVATION_SUBJECT',
				$data['name'],
				$data['sitename']
			);

			$emailBody = JText::sprintf(
				'COM_USERS_EMAIL_ACTIVATED_BY_ADMIN_ACTIVATION_BODY',
				$data['name'],
				$data['siteurl'],
				$data['username']
			);

			if ($config->get('dev_mode') == 1) {
				$data['email'] = $config->get('dev_email');
			}
			$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);

			// Check for an error.
			if ($return !== true)
			{
				$this->setError(JText::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'));

				return false;
			}
		}
		else
		{
			$user->set('activation', '');
			$user->set('block', '0');
		}

		// Store the user object.
		if (!$user->save())
		{
			$this->setError(JText::sprintf('COM_USERS_REGISTRATION_ACTIVATION_SAVE_FAILED', $user->getError()));

			return false;
		}

		return $user;
	}

	/**
	 * Method to get the registration form data.
	 *
	 * The base form data is loaded and then an event is fired
	 * for users plugins to extend the data.
	 *
	 * @return  mixed  Data object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getData()
	{
		if ($this->data === null)
		{
			$this->data = new stdClass;
			$app = JFactory::getApplication();
			$params = JComponentHelper::getParams('com_users');

			// Override the base user data with any data in the session.
			$temp = (array) $app->getUserState('com_users.registration.data', array());

			foreach ($temp as $k => $v)
			{
				$this->data->$k = $v;
			}

			// Get the groups the user should be added to after registration.
			$this->data->groups = array();

			// Get the default new user group, Registered if not specified.
			$system = $params->get('new_usertype', 2);

			//dennis change of groups
			$system=@$_REQUEST['jform']['team_or_org'];

			$this->data->groups[] = $system;

			// Unset the passwords.
			unset($this->data->password1);
			unset($this->data->password2);

			// Get the dispatcher and load the users plugins.
			$dispatcher = JEventDispatcher::getInstance();
			JPluginHelper::importPlugin('user');

			// Trigger the data preparation event.
			$results = $dispatcher->trigger('onContentPrepareData', array('com_users.registration', $this->data));

			// Check for errors encountered while preparing the data.
			if (count($results) && in_array(false, $results, true))
			{
				$this->setError($dispatcher->getError());
				$this->data = false;
			}
		}

		return $this->data;
	}

	/**
	 * Method to get the registration form.
	 *
	 * The base form is loaded from XML and then an event is fired
	 * for users plugins to extend the form with extra fields.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_users.registration', 'registration', array('control' => 'jform', 'load_data' => $loadData));

		// When multilanguage is set, a user's default site language should also be a Content Language
		if (JLanguageMultilang::isEnabled())
		{
			$form->setFieldAttribute('language', 'type', 'frontend_language', 'params');
		}

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		$data = $this->getData();

		$this->preprocessData('com_users.registration', $data);

		return $data;
	}

	/**
	 * Override preprocessForm to load the user plugin group instead of content.
	 *
	 * @param   JForm   $form   A JForm object.
	 * @param   mixed   $data   The data expected for the form.
	 * @param   string  $group  The name of the plugin group to import (defaults to "content").
	 *
	 * @return  void
	 *
	 * @since   1.6
	 * @throws  Exception if there is an error in the form event.
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'user')
	{
		$userParams = JComponentHelper::getParams('com_users');

		// Add the choice for site language at registration time
		if ($userParams->get('site_language') == 1 && $userParams->get('frontend_userparams') == 1)
		{
			$form->loadFile('sitelang', false);
		}

		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState()
	{
		// Get the application object.
		$app = JFactory::getApplication();
		$params = $app->getParams('com_users');

		// Load the parameters.
		$this->setState('params', $params);
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $temp  The form data.
	 *
	 * @return  mixed  The user id on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function register($temp)
	{
		$params = JComponentHelper::getParams('com_users');

		// Initialise the table with JUser.
		$user = new JUser;
		$data = (array) $this->getData();

		// Merge in the registration data.
		foreach ($temp as $k => $v)
		{
			$data[$k] = $v;
		}

		// Prepare the data for the user object.
		$data['email'] = JStringPunycode::emailToPunycode($data['email1']);
		$data['password'] = $data['password1'];
		$useractivation = $params->get('useractivation');
		$sendpassword = $params->get('sendpassword', 1);

		// Check if the user needs to activate their account.
		if (($useractivation == 1) || ($useractivation == 2))
		{
			$data['activation'] = JApplicationHelper::getHash(JUserHelper::genRandomPassword());
			$data['block'] = 1;
		}

		// Bind the data.
		if (!$user->bind($data))
		{
			$this->setError(JText::sprintf('COM_USERS_REGISTRATION_BIND_FAILED', $user->getError()));

			return false;
		}

		// Load the users plugin group.
		JPluginHelper::importPlugin('user');

		//dennis code started

		//general
		$config = JFactory::getConfig();
		//local db
		$db = JFactory::getDbo();

		//requests
		$team_name=addslashes(strip_tags(htmlspecialchars($_REQUEST['jform']['name'])));
		//$alias=JApplication::stringURLSafe($_REQUEST['jform']['name']);
		$alias=JFilterOutput::stringURLSafe($team_name);
		$query = "SELECT id FROM #__teams WHERE alias='".$alias."' LIMIT 1";
		$db->setQuery($query);
		$same_alias = $db->loadResult();
		if(@$_REQUEST['jform']['create_actions']=='organizer'){
			$create_actions=1;
		}else{
			$create_actions=0;
		}
		if(@$_REQUEST['jform']['support_actions']=='supporter'){
			$support_actions=1;
		}else{
			$support_actions=0;
		}
		$hidden_team=0;
		if(@$_REQUEST['jform']['hidden_team']=='hidden_team' && @$_REQUEST['jform']['support_actions']=='supporter' && @$_REQUEST['jform']['create_actions']!='organizer'){
			$hidden_team=1;
		}
		$team_or_org=@$_REQUEST['jform']['team_or_org'];
		if(@$_REQUEST['has_legal_type']=='yes'){
			$legal_form=1;
		}else{
			$legal_form=0;
		}
		if(@$_REQUEST['organization_type']=='yes'){
			$profit=0;
		}else{
			$profit=1;
		}
		//cheeeeeckkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk profits
		$profit_custom=addslashes(strip_tags(htmlspecialchars(@$_REQUEST['profit_custom'])));
		if($profit_custom!=''){
			$profit_id=0;
		}else{
			$profit_id=@$_REQUEST['profit_id'];
		}
		$team_description=addslashes($_REQUEST['jform']['description']);
		//activities
		$query = "SELECT id FROM #__team_activities WHERE published=1";
		$db->setQuery($query);
		$activities = $db->loadObjectList();
		$activities_ids='';
		foreach($activities as $activity){
			if(@$_REQUEST['jform']['activity_'.$activity->id]=='on'){
				$activities_ids.=$activity->id.',';
			}
		}
		$web_link=addslashes(htmlspecialchars($_REQUEST['jform']['web_link']));
		$fb_link=addslashes(htmlspecialchars($_REQUEST['jform']['fb_link']));
		$tw_link=addslashes(htmlspecialchars($_REQUEST['jform']['tw_link']));
		$pn_link='';
		$in_link=addslashes(htmlspecialchars($_REQUEST['jform']['in_link']));
		$go_link='';
		$li_link=addslashes(htmlspecialchars($_REQUEST['jform']['li_link']));
		$yt_link=addslashes(htmlspecialchars($_REQUEST['jform']['yt_link']));
		for($i=1; $i<4; $i++){
			${'contact_'.$i.'_name'}=addslashes(strip_tags(htmlspecialchars($_REQUEST['jform']['contact_'.$i.'_name'])));
			${'contact_'.$i.'_email'}=addslashes(strip_tags(htmlspecialchars($_REQUEST['jform']['contact_'.$i.'_email'])));
			${'contact_'.$i.'_phone'}=addslashes(strip_tags(htmlspecialchars($_REQUEST['jform']['contact_'.$i.'_phone'])));
		}
		//donations
		$query = "SELECT id,parent_id FROM #__team_donation_types WHERE published=1";
		$db->setQuery($query);
		$donations = $db->loadObjectList();
		$donations_ids='';
		$donation_other_1=addslashes(@$_REQUEST['donation-1-other']);
		$donation_other_16=addslashes(@$_REQUEST['donation-16-other']);
		foreach($donations as $donation){
			if($donation->parent_id==0){
				if(@$_REQUEST['donation-'.$donation->id]=='show'){
					$donations_ids.=$donation->id.',';
				}
			}else{
				if(@$_REQUEST['donation-'.$donation->parent_id.'-'.$donation->id]=='on'){
					$donations_ids.=$donation->id.',';
				}
			}
		}
		if(@$_REQUEST['jform']['newsletter']=='yes'){
			$newsletter=1;
		}else{
			$newsletter=0;
		}

		// Store the data.
		if (!$user->save())
		{
			$this->setError(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()));

			return false;
		}

		//check alias
		$userid=$user->id;
		if($same_alias>0 || !$alias){
			$alias=$alias.'_'.$userid;
		}

		//team management
		$query = "SELECT MAX(ordering) FROM #__teams ";
		$db->setQuery($query);
		$max_ordering = $db->loadResult() + 1;

		$query = "LOCK TABLES #__teams WRITE";
		$db->setQuery($query);
		$db->execute();

		$query = "INSERT INTO #__teams VALUES
							('', '', 0, ".$userid.", '".$team_name."', '".$alias."', '', '".$create_actions."', '".$support_actions."', '".$legal_form."', '".$profit."', '".$profit_id."' , '".$profit_custom."', '".$team_or_org."', '".$activities_ids."',
							'".$donations_ids."', '".$donation_other_1."','".$donation_other_16."', '".$team_description."', '', '".$web_link."', '".$fb_link."', '".$tw_link."', '', '".$in_link."', '', '".$li_link."', '".$yt_link."',
							'".$contact_1_name."','".$contact_1_email."','".$contact_1_phone."','".$contact_2_name."','".$contact_2_email."','".$contact_2_phone."','".$contact_3_name."','".$contact_3_email."','".$contact_3_phone."',
							'".$newsletter."','".$hidden_team."','".time()."', 0, 1, '".$max_ordering."', '*', '".date('Y-m-d H:i:s',time())."', '', ".$userid.", '', '', '', '')";
		$db->setQuery($query);
		$db->execute();
		$teamid = $db->insertid();

		$query = "UNLOCK TABLES";
		$db->setQuery($query);
		$db->execute();

		//assets
		// $query = "SELECT rgt FROM #__assets WHERE name LIKE '#__teams.%' ORDER BY id DESC LIMIT 1";
		// $db->setQuery($query);
		// $assoc_rgt = $db->loadResult()+1;
		// $query = "INSERT INTO #__assets VALUES ('',1,'".$assoc_rgt."','".($assoc_rgt+1)."',1,'#__teams.".$teamid."','#__teams.".$teamid."','{}') ";
		// $db->setQuery($query);
		// $db->execute();
		// $asset_id=$db->insertid();

		//create directories
		if (!file_exists($config->get( 'abs_path' ).'/images/team_photos/'.$teamid)) {
			mkdir($config->get( 'abs_path' ).'/images/team_photos/'.$teamid, 0777);
		}
		//if (!file_exists($config->get( 'abs_path' ).'/images/team_logos/'.$teamid)) {
		//	mkdir($config->get( 'abs_path' ).'/images/team_logos/'.$teamid, 0777);
		//}
		if (!file_exists($config->get( 'abs_path' ).'/images/team_files/'.$teamid)) {
			mkdir($config->get( 'abs_path' ).'/images/team_files/'.$teamid, 0777);
		}

		//logo management
		$logo_path='';
		if(@$_FILES['jform']['error']['logo']==0){
			if(@$_FILES['jform']['size']['logo']<1000000){
				$path_parts = pathinfo($_FILES["jform"]["name"]["logo"]);
				$ext = $path_parts['extension'];
				if(move_uploaded_file($_FILES["jform"]["tmp_name"]['logo'], $config->get( 'abs_path' ).'/images/team_logos/'.$teamid.'.'.$ext)){
					$logo_path='images/team_logos/'.$teamid.'.'.$ext;
				}
			}else{
				//logo max size
				$this->setError(JText::sprintf('Το λογότυπο υπερβαίνει το μέγιστο επιτροπόμενο όριο μεγέθους.', $user->getError()));
			}
		}else{
			//logo issue
			$this->setError(JText::sprintf('Παρουσιάστηκε σφάλμα με το λογότυπο.', $user->getError()));
		}

		//files_upload management
		if(@$_FILES['jform']['error']['files_upload']==0){
			if(@$_FILES['jform']['size']['files_upload']<1000000){
				$path_parts = pathinfo($_FILES["jform"]["name"]["files_upload"]);
				$ext = $path_parts['extension'];
				$new_file_name=JFilterOutput::stringURLSafe($path_parts['filename']).'.'.$ext;
				if(move_uploaded_file($_FILES["jform"]["tmp_name"]['files_upload'], $config->get( 'abs_path' ).'/images/team_files/'.$teamid.'/'.$teamid.'.'.$ext)){
					//file uploaded
					$query="INSERT INTO #__team_files VALUES ('','".$teamid."','".$new_file_name."','')";
					$db->setQuery($query);
					$db->execute();
				}
			}else{
				//files_upload max size
				$this->setError(JText::sprintf('Το αρχείο υπερβαίνει το μέγιστο επιτροπόμενο όριο μεγέθους.', $user->getError()));
			}
		}else{
			//files_upload issue
			$this->setError(JText::sprintf('Παρουσιάστηκε σφάλμα με το αρχείο.', $user->getError()));
		}

		//gallery management
		$imgs_count=count(@$_FILES['gallery_upload']['name']);
		if($imgs_count>0){
			for($i=0; $i<$imgs_count; $i++){
				if(@$_FILES['gallery_upload']['error'][$i]==0){
					if(@$_FILES['gallery_upload']['size'][$i]<1000000){
						$path_parts = pathinfo($_FILES["gallery_upload"]["name"][$i]);
						$ext = $path_parts['extension'];
						$new_img_name=JFilterOutput::stringURLSafe($path_parts['filename']).'.'.$ext;
						if(move_uploaded_file($_FILES["gallery_upload"]["tmp_name"][$i], $config->get( 'abs_path' ).'/images/team_photos/'.$teamid.'/'.$new_img_name)){
							//file uploaded - insert to db
							$query="INSERT INTO #__team_photos VALUES ('','".$teamid."','".$new_img_name."','".($i+1)."')";
							$db->setQuery($query);
							$db->execute();
						}
					}else{
						//image max size
						$this->setError(JText::sprintf('Η εικόνα υπερβαίνει το μέγιστο επιτροπόμενο όριο μεγέθους.', $user->getError()));
					}
				}else{
					//image issue
					$this->setError(JText::sprintf('Παρουσιάστηκε σφάλμα με την εικόνα: '.$_FILES["gallery_upload"]["name"][$i].'.', $user->getError()));
				}
			}
		}


		//update teams with: asset, logo
		//$query = "UPDATE #__teams SET asset_id='".$asset_id."', logo='".$logo_path."' WHERE id='".$teamid."' LIMIT 1";
		//$db->setQuery($query);
		//$db->execute();

		//dennis code ended

		// $config = JFactory::getConfig();
		// $db = $this->getDbo();
		// $query = $db->getQuery(true);

		// Compile the notification mail values.
		$data = $user->getProperties();
		$data['fromname'] = $config->get('fromname');
		$data['mailfrom'] = $config->get('mailfrom');
		$data['sitename'] = $config->get('sitename');
		$data['siteurl'] = JUri::root();

		// Handle account activation/confirmation emails.
		if ($useractivation == 2)
		{
			// Set the link to confirm the user email.
			$uri = JUri::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base . JRoute::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'], false);

			// Remove administrator/ from activate url in case this method is called from admin
			if (JFactory::getApplication()->isAdmin())
			{
				$adminPos         = strrpos($data['activate'], 'administrator/');
				$data['activate'] = substr_replace($data['activate'], '', $adminPos, 14);
			}

			$emailSubject = JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			if ($sendpassword)
			{
				$emailBody = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY',
					$data['name'],
					$data['sitename'],
					$data['activate'],
					$data['siteurl'],
					$data['username'],
					$data['password_clear']
				);
			}
			else
			{
				$emailBody = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY_NOPW',
					$data['name'],
					$data['sitename'],
					$data['activate'],
					$data['siteurl'],
					$data['username']
				);
			}
		}
		elseif ($useractivation == 1)
		{
			// Set the link to activate the user account.
			$uri = JUri::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base . JRoute::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'], false);

			// Remove administrator/ from activate url in case this method is called from admin
			if (JFactory::getApplication()->isAdmin())
			{
				$adminPos         = strrpos($data['activate'], 'administrator/');
				$data['activate'] = substr_replace($data['activate'], '', $adminPos, 14);
			}

			$emailSubject = JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			if ($sendpassword)
			{
				$emailBody = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
					/*$data['name'],*/
					$data['sitename'],
					$data['activate'],
					$data['activate'],
					$data['siteurl'],
					$data['username'],
					$data['password_clear']
				);
			}
			else
			{
				$emailBody = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY_NOPW',
					/*$data['name'],*/
					$data['sitename'],
					$data['activate'],
					$data['activate'],
					$data['siteurl'],
					$data['username']
				);
			}
		}
		else
		{
			$emailSubject = JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			if ($sendpassword)
			{
				$emailBody = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_BODY',
					/*$data['name'],*/
					$data['sitename'],
					$data['siteurl'],
					$data['username'],
					$data['password_clear']
				);
			}
			else
			{
				$emailBody = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_BODY_NOPW',
					/*$data['name'],*/
					$data['sitename'],
					$data['siteurl']
				);
			}
		}

		if ($config->get('dev_mode')) {
			$data['email'] = array($config->get('dev_email'));
		}
		// Send the registration email.
		$emailBody_new = '<body style="margin:0px auto; padding:0px; background-color:#FFFFFF; color:#5d5d5d; font-family:Arial; outline:none; font-size:12px;" bgcolor="#FFFFFF">
									<div style="background-color:#FFFFFF;margin:0px auto; font-family:Arial;color:#5d5d5d;">
										<div style="margin:0px auto; width:640px; text-align:left; background-color:#ebebeb; font-family:Arial; padding:20px;color:#5d5d5d;">
										<div style="text-align:right;"><img src="'.$config->get( 'live_site' ).'/images/template/synathina_logo.jpg" alt="συνΑθηνά" /></div>
										<div style="font-size: 18px;font-weight:bold; color:#05c0de;padding-bottom: 10px;">'.JText::_('COM_USERS_ACTIVATION_TITLE').'</div>'.$emailBody;
		$emailBody_new .= JText::_('COM_EMAIL_NOTE');
		$emailBody_new .= '</div></div></body>';
		if ($config->get('dev_mode') == 1) {
			$data['email'] = $config->get('dev_email');
		}
		$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody_new,true);

		// Send Notification mail to administrators
		if (($params->get('useractivation') < 2) && ($params->get('mail_to_admin') == 1))
		{
			$emailSubject = JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBodyAdmin = JText::sprintf(
				'COM_USERS_EMAIL_REGISTERED_NOTIFICATION_TO_ADMIN_BODY',
				$data['name'],
				$data['username'],
				$data['siteurl']
			);

			// Get all admin users
			$query->clear()
				->select($db->quoteName(array('name', 'email', 'sendEmail')))
				->from($db->quoteName('#__users'))
				->where($db->quoteName('sendEmail') . ' = ' . 1);

			$db->setQuery($query);

			try
			{
				$rows = $db->loadObjectList();
			}
			catch (RuntimeException $e)
			{
				$this->setError(JText::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);

				return false;
			}

			// Send mail to all superadministrators id
			foreach ($rows as $row)
			{
				$emailBodyAdmin_new = '<body style="margin:0px auto; padding:0px; background-color:#FFFFFF; color:#5d5d5d; font-family:Arial; outline:none; font-size:12px;" bgcolor="#FFFFFF">
											<div style="background-color:#FFFFFF;margin:0px auto; font-family:Arial;color:#5d5d5d;">
												<div style="margin:0px auto; width:640px; text-align:left; background-color:#ebebeb; font-family:Arial; padding:20px;color:#5d5d5d;">
												<div style="text-align:right;"><img src="'.$config->get( 'live_site' ).'/images/template/synathina_logo.jpg" alt="συνΑθηνά" /></div>
												<div style="font-size: 18px;font-weight:bold; color:#05c0de;padding-bottom: 10px;">'.JText::_('COM_USERS_REGISTRATION').'</div>'.$emailBodyAdmin.'</div></div></body>';
				if ($config->get('dev_mode') == 1) {
					$row->email = $config->get('dev_email');
				}
				$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $emailSubject, $emailBodyAdmin_new,true);

				// Check for an error.
				if ($return !== true)
				{
					$this->setError(JText::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'));

					return false;
				}
			}
		}

		// Check for an error.
		if ($return !== true)
		{
			$this->setError(JText::_('COM_USERS_REGISTRATION_SEND_MAIL_FAILED'));

			// Send a system message to administrators receiving system mails
			$db = $this->getDbo();
			$query->clear()
				->select($db->quoteName(array('name', 'email', 'sendEmail', 'id')))
				->from($db->quoteName('#__users'))
				->where($db->quoteName('block') . ' = ' . (int) 0)
				->where($db->quoteName('sendEmail') . ' = ' . (int) 1);
			$db->setQuery($query);

			try
			{
				$sendEmail = $db->loadColumn();
			}
			catch (RuntimeException $e)
			{
				$this->setError(JText::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);

				return false;
			}

			if (count($sendEmail) > 0)
			{
				$jdate = new JDate;

				// Build the query to add the messages
				foreach ($sendEmail as $userid)
				{
					$values = array(
						$db->quote($userid),
						$db->quote($userid),
						$db->quote($jdate->toSql()),
						$db->quote(JText::_('COM_USERS_MAIL_SEND_FAILURE_SUBJECT')),
						$db->quote(JText::sprintf('COM_USERS_MAIL_SEND_FAILURE_BODY', $return, $data['username']))
					);
					$query->clear()
						->insert($db->quoteName('#__messages'))
						->columns($db->quoteName(array('user_id_from', 'user_id_to', 'date_time', 'subject', 'message')))
						->values(implode(',', $values));
					$db->setQuery($query);

					try
					{
						$db->execute();
					}
					catch (RuntimeException $e)
					{
						$this->setError(JText::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);

						return false;
					}
				}
			}

			return false;
		}

		if ($useractivation == 1)
		{
			return "useractivate";
		}
		elseif ($useractivation == 2)
		{
			return "adminactivate";
		}
		else
		{
			return $user->id;
		}
	}
}
