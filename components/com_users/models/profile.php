<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * Profile model class for Users.
 *
 * @since  1.6
 */
class UsersModelProfile extends JModelForm
{
	/**
	 * @var		object	The user profile data.
	 * @since   1.6
	 */
	protected $data;

	/**
	 * Constructor
	 *
	 * @param   array  $config  An array of configuration options (name, state, dbo, table_path, ignore_request).
	 *
	 * @since   3.2
	 *
	 * @throws  Exception
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Load the Core RAD layer
		if (!defined('FOF_INCLUDED'))
		{
			include_once JPATH_LIBRARIES . '/fof/include.php';
		}

		// Load the helper and model used for two factor authentication
		require_once JPATH_ADMINISTRATOR . '/components/com_users/models/user.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_users/helpers/users.php';
	}

	/**
	 * Method to check in a user.
	 *
	 * @param   integer  $userId  The id of the row to check out.
	 *
	 * @return  boolean  True on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function checkin($userId = null)
	{
		// Get the user id.
		$userId = (!empty($userId)) ? $userId : (int) $this->getState('user.id');

		if ($userId)
		{
			// Initialise the table with JUser.
			$table = JTable::getInstance('User');

			// Attempt to check the row in.
			if (!$table->checkin($userId))
			{
				$this->setError($table->getError());

				return false;
			}
		}

		return true;
	}

	/**
	 * Method to check out a user for editing.
	 *
	 * @param   integer  $userId  The id of the row to check out.
	 *
	 * @return  boolean  True on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function checkout($userId = null)
	{
		// Get the user id.
		$userId = (!empty($userId)) ? $userId : (int) $this->getState('user.id');

		if ($userId)
		{
			// Initialise the table with JUser.
			$table = JTable::getInstance('User');

			// Get the current user object.
			$user = JFactory::getUser();

			// Attempt to check the row out.
			if (!$table->checkout($user->get('id'), $userId))
			{
				$this->setError($table->getError());

				return false;
			}
		}

		return true;
	}

	/**
	 * Method to get the profile form data.
	 *
	 * The base form data is loaded and then an event is fired
	 * for users plugins to extend the data.
	 *
	 * @return  mixed  	Data object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getData()
	{
		if ($this->data === null)
		{
			$userId = $this->getState('user.id');

			// Initialise the table with JUser.
			$this->data = new JUser($userId);

			// Set the base user data.
			$this->data->email1 = $this->data->get('email');
			$this->data->email2 = $this->data->get('email');

			// Override the base user data with any data in the session.
			$temp = (array) JFactory::getApplication()->getUserState('com_users.edit.profile.data', array());

			foreach ($temp as $k => $v)
			{
				$this->data->$k = $v;
			}

			// Unset the passwords.
			unset($this->data->password1);
			unset($this->data->password2);

			$registry           = new Registry($this->data->params);
			$this->data->params = $registry->toArray();

			// Get the dispatcher and load the users plugins.
			$dispatcher = JEventDispatcher::getInstance();
			JPluginHelper::importPlugin('user');

			// Trigger the data preparation event.
			$results = $dispatcher->trigger('onContentPrepareData', array('com_users.profile', $this->data));

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
	 * Method to get the profile form.
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
		$form = $this->loadForm('com_users.profile', 'profile', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		// Check for username compliance and parameter set
		$isUsernameCompliant = true;

		if ($this->loadFormData()->username)
		{
			$username = $this->loadFormData()->username;
			$isUsernameCompliant  = !(preg_match('#[<>"\'%;()&\\\\]|\\.\\./#', $username) || strlen(utf8_decode($username)) < 2
				|| trim($username) != $username);
		}

		$this->setState('user.username.compliant', $isUsernameCompliant);

		if (!JComponentHelper::getParams('com_users')->get('change_login_name') && $isUsernameCompliant)
		{
			$form->setFieldAttribute('username', 'class', '');
			$form->setFieldAttribute('username', 'filter', '');
			$form->setFieldAttribute('username', 'description', 'COM_USERS_PROFILE_NOCHANGE_USERNAME_DESC');
			$form->setFieldAttribute('username', 'validate', '');
			$form->setFieldAttribute('username', 'message', '');
			$form->setFieldAttribute('username', 'readonly', 'true');
			$form->setFieldAttribute('username', 'required', 'false');
		}

		// When multilanguage is set, a user's default site language should also be a Content Language
		if (JLanguageMultilang::isEnabled())
		{
			$form->setFieldAttribute('language', 'type', 'frontend_language', 'params');
		}

		// If the user needs to change their password, mark the password fields as required
		if (JFactory::getUser()->requireReset)
		{
			$form->setFieldAttribute('password1', 'required', 'true');
			$form->setFieldAttribute('password2', 'required', 'true');
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

		$this->preprocessData('com_users.profile', $data);

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
	 * @throws	Exception if there is an error in the form event.
	 *
	 * @since   1.6
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'user')
	{
		if (JComponentHelper::getParams('com_users')->get('frontend_userparams'))
		{
			$form->loadFile('frontend', false);

			if (JFactory::getUser()->authorise('core.login.admin'))
			{
				$form->loadFile('frontend_admin', false);
			}
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
		$params = JFactory::getApplication()->getParams('com_users');

		// Get the user id.
		$userId = JFactory::getApplication()->getUserState('com_users.edit.profile.id');
		$userId = !empty($userId) ? $userId : (int) JFactory::getUser()->get('id');

		// Set the user id.
		$this->setState('user.id', $userId);

		// Load the parameters.
		$this->setState('params', $params);
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  mixed  The user id on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function save($data)
	{
		$userId = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('user.id');

		$user = new JUser($userId);

		// Prepare the data for the user object.
		$data['email']    = JStringPunycode::emailToPunycode($data['email1']);
		$data['password'] = $data['password1'];

		// Unset the username if it should not be overwritten
		$username            = $data['username'];
		$isUsernameCompliant = $this->getState('user.username.compliant');

		if (!JComponentHelper::getParams('com_users')->get('change_login_name') && $isUsernameCompliant)
		{
			unset($data['username']);
		}

		// Unset the block so it does not get overwritten
		unset($data['block']);

		// Unset the sendEmail so it does not get overwritten
		unset($data['sendEmail']);

		// Handle the two factor authentication setup
		if (array_key_exists('twofactor', $data))
		{
			$model = new UsersModelUser;

			$twoFactorMethod = $data['twofactor']['method'];

			// Get the current One Time Password (two factor auth) configuration
			$otpConfig = $model->getOtpConfig($userId);

			if ($twoFactorMethod != 'none')
			{
				// Run the plugins
				FOFPlatform::getInstance()->importPlugin('twofactorauth');
				$otpConfigReplies = FOFPlatform::getInstance()->runPlugins('onUserTwofactorApplyConfiguration', array($twoFactorMethod));

				// Look for a valid reply
				foreach ($otpConfigReplies as $reply)
				{
					if (!is_object($reply) || empty($reply->method) || ($reply->method != $twoFactorMethod))
					{
						continue;
					}

					$otpConfig->method = $reply->method;
					$otpConfig->config = $reply->config;

					break;
				}

				// Save OTP configuration.
				$model->setOtpConfig($userId, $otpConfig);

				// Generate one time emergency passwords if required (depleted or not set)
				if (empty($otpConfig->otep))
				{
					$oteps = $model->generateOteps($userId);
				}
			}
			else
			{
				$otpConfig->method = 'none';
				$otpConfig->config = array();
				$model->setOtpConfig($userId, $otpConfig);
			}

			// Unset the raw data
			unset($data['twofactor']);

			// Reload the user record with the updated OTP configuration
			$user->load($userId);
		}

		// Bind the data.
		if (!$user->bind($data))
		{
			$this->setError(JText::sprintf('COM_USERS_PROFILE_BIND_FAILED', $user->getError()));

			return false;
		}

		// Load the users plugin group.
		JPluginHelper::importPlugin('user');

		// Null the user groups so they don't get overwritten
		$user->groups = null;

		//dennis code started

		//general
		$config = JFactory::getConfig();

		//local db
		$db = JFactory::getDbo();

		//requests
		$team_id=@$_REQUEST['team_id'];
		$team_name=addslashes(strip_tags(htmlspecialchars($_REQUEST['jform']['name'])));
		$user->name = $team_name;
		//$alias=JApplication::stringURLSafe($_REQUEST['jform']['name']);
		$alias=JFilterOutput::stringURLSafe($team_name);
		$query = "SELECT id FROM #__teams WHERE alias='".$alias."' AND id!='".$team_id."' LIMIT 1";
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
			$this->setError($user->getError());

			return false;
		}

		//check alias
		$userid=$user->id;
		if($same_alias>0){
			$alias=$alias.'_'.$userid;
		}

		//team management
		$query = "SELECT MAX(ordering) FROM #__teams ";
		$db->setQuery($query);
		$max_ordering = $db->loadResult() + 1;

		$query = "LOCK TABLES #__teams WRITE";
		$db->setQuery($query);
		$db->execute();

		$query = "UPDATE #__teams SET  hidden='".$hidden_team."',name='".$team_name."', alias='".$alias."', create_actions='".$create_actions."', support_actions='".$support_actions."', legal_form='".$legal_form."', profit='".$profit."', profit_id='".$profit_id."' , profit_custom='".$profit_custom."',
							team_or_org='".$team_or_org."', activities='".$activities_ids."', org_donation='".$donations_ids."',donation_eidos='".$donation_other_1."',donation_technology='".$donation_other_16."', description='".$team_description."', web_link='".$web_link."', fb_link='".$fb_link."', tw_link='".$tw_link."', in_link='".$in_link."',
							li_link='".$li_link."', yt_link='".$yt_link."',
							contact_1_name='".$contact_1_name."',contact_1_email='".$contact_1_email."',contact_1_phone='".$contact_1_phone."',contact_2_name='".$contact_2_name."',contact_2_email='".$contact_2_email."',contact_2_phone='".$contact_2_phone."',
							contact_3_name='".$contact_3_name."',contact_3_email='".$contact_3_email."',contact_3_phone='".$contact_3_phone."',	newsletter='".$newsletter."', modified='".date('Y-m-d H:i:s',time())."'  WHERE id='".$team_id."' LIMIT 1 ";
		$db->setQuery($query);
		$db->execute();

		$query = "UNLOCK TABLES";
		$db->setQuery($query);
		$db->execute();

		//logo management
		$logo_path='';
		if(@$_FILES['jform']['error']['logo']==0){
			if(@$_FILES['jform']['size']['logo']<1000000){
				$path_parts = pathinfo($_FILES["jform"]["name"]["logo"]);
				$ext = $path_parts['extension'];
				if(move_uploaded_file($_FILES["jform"]["tmp_name"]['logo'], $config->get( 'abs_path' ).'/images/team_logos/'.$team_id.'.'.$ext)){
					$query = "SELECT logo FROM #__teams WHERE id='".$team_id."' LIMIT 1 ";
					$db->setQuery($query);
					$logo_path = $db->loadResult();
					if($logo_path!=''){
						unlink($config->get( 'abs_path' ).'/'.$logo_path);
					}
					$new_logo_path='images/team_logos/'.$team_id.'.'.$ext;
					$query = "UPDATE #__teams SET logo='".$new_logo_path."' WHERE id='".$team_id."' LIMIT 1";
					$db->setQuery($query);
					$db->execute();

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
				if(move_uploaded_file($_FILES["jform"]["tmp_name"]['files_upload'], $config->get( 'abs_path' ).'/images/team_files/'.$team_id.'/'.$team_id.'.'.$ext)){
					//remove file
					$query = "SELECT path FROM #__team_files WHERE team_id='".$team_id."' LIMIT 1 ";
					$db->setQuery($query);
					$file_path = $db->loadResult();
					if($file_path!=''){
						unlink($config->get( 'abs_path' ).'/images/team_files/'.$team_id.'/'.$file_path);
						$query="DELETE FROM #__team_files WHERE team_id='".$team_id."' LIMIT 1";
						$db->setQuery($query);
						$db->execute();
					}
					//file uploaded
					$query="INSERT INTO #__team_files VALUES ('','".$team_id."','".$new_file_name."','')";
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
			$query="SELECT MAX(ordering) FROM #__team_photos WHERE team_id='".$team_id."' LIMIT 1";
			$db->setQuery($query);
			$max_photo_ordering = $db->loadResult()+1;
			for($i=0; $i<$imgs_count; $i++){
				if(@$_FILES['gallery_upload']['error'][$i]==0){
					if(@$_FILES['gallery_upload']['size'][$i]<1000000){
						$path_parts = pathinfo($_FILES["gallery_upload"]["name"][$i]);
						$ext = $path_parts['extension'];
						$new_img_name=JFilterOutput::stringURLSafe($path_parts['filename']).'.'.$ext;
						if(move_uploaded_file($_FILES["gallery_upload"]["tmp_name"][$i], $config->get( 'abs_path' ).'/images/team_photos/'.$team_id.'/'.$new_img_name)){
							//file uploaded - insert to db
							$query="INSERT INTO #__team_photos VALUES ('','".$team_id."','".$new_img_name."','".$max_photo_ordering."')";
							$db->setQuery($query);
							$db->execute();
							$max_photo_ordering++;
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

		//dennis code ended



		$user->tags = new JHelperTags;
		$user->tags->getTagIds($user->id, 'com_users.user');

		return $user->id;
	}

	/**
	 * Gets the configuration forms for all two-factor authentication methods
	 * in an array.
	 *
	 * @param   integer  $user_id  The user ID to load the forms for (optional)
	 *
	 * @return  array
	 *
	 * @since   3.2
	 */
	public function getTwofactorform($user_id = null)
	{
		$user_id = (!empty($user_id)) ? $user_id : (int) $this->getState('user.id');

		$model = new UsersModelUser;

		$otpConfig = $model->getOtpConfig($user_id);

		FOFPlatform::getInstance()->importPlugin('twofactorauth');

		return FOFPlatform::getInstance()->runPlugins('onUserTwofactorShowConfiguration', array($otpConfig, $user_id));
	}

	/**
	 * Returns the one time password (OTP) – a.k.a. two factor authentication –
	 * configuration for a particular user.
	 *
	 * @param   integer  $user_id  The numeric ID of the user
	 *
	 * @return  stdClass  An object holding the OTP configuration for this user
	 *
	 * @since   3.2
	 */
	public function getOtpConfig($user_id = null)
	{
		$user_id = (!empty($user_id)) ? $user_id : (int) $this->getState('user.id');

		$model = new UsersModelUser;

		return $model->getOtpConfig($user_id);
	}
}
