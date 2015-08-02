<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');
require_once JPATH_COMPONENT.'/tables/legaluserdetail.php';
require_once JPATH_COMPONENT.'/tables/legaluseroffice.php';
require_once JPATH_COMPONENT.'/tables/legalempoffice.php';
require_once JPATH_COMPONENT.'/tables/legaluserpayment.php';
/**
 * Registration model class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @since		1.6
 */
class LegalconfirmusersModelRegistration extends JModelForm
{
	/**
	 * @var		object	The user registration data.
	 * @since	1.6
	 */
	protected $data;

	/**
	 * Method to activate a user account.
	 *
	 * @param	string		The activation token.
	 * @return	mixed		False on failure, user object on success.
	 * @since	1.6
	 */
	public function activate($token)
	{
		$config	= JFactory::getConfig();
		$userParams	= JComponentHelper::getParams('com_users');
		$db		= $this->getDbo();

		// Get the user id based on the token.
		$db->setQuery(
			'SELECT '.$db->quoteName('id').' FROM '.$db->quoteName('#__users') .
			' WHERE '.$db->quoteName('activation').' = '.$db->Quote($token) .
			' AND '.$db->quoteName('block').' = 1' .
			' AND '.$db->quoteName('lastvisitDate').' = '.$db->Quote($db->getNullDate())
		);
		$userId = (int) $db->loadResult();

		// Check for a valid user id.
		if (!$userId) {
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
			$uri = JURI::getInstance();

			// Compile the admin notification mail values.
			$data = $user->getProperties();
			$data['activation'] = JApplication::getHash(JUserHelper::genRandomPassword());
			$user->set('activation', $data['activation']);
			$data['siteurl']	= JUri::base();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base.JRoute::_('index.php?option=com_users&task=registration.activate&token='.$data['activation'], false);
			$data['fromname'] = $config->get('fromname');
			$data['mailfrom'] = $config->get('mailfrom');
			$data['sitename'] = $config->get('sitename');
			$user->setParam('activate', 1);
			$emailSubject	= JText::sprintf(
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
			$data['siteurl'].'index.php?option=com_users&task=registration.activate&token='.$data['activation']
			);

			// get all admin users
			$query = 'SELECT name, email, sendEmail, id' .
						' FROM #__users' .
						' WHERE sendEmail=1';

			$db->setQuery( $query );
			$rows = $db->loadObjectList();

			// Send mail to all users with users creating permissions and receiving system emails
			foreach( $rows as $row )
			{
				$usercreator = JFactory::getUser($id = $row->id);
				if ($usercreator->authorise('core.create', 'com_users'))
				{
					$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $emailSubject, $emailBody);

					// Check for an error.
					if ($return !== true) {
						$this->setError(JText::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'));
						return false;
					}
				}
			}
		}

		//Admin activation is on and admin is activating the account
		elseif (($userParams->get('useractivation') == 2) && $user->getParam('activate', 0))
		{
			$user->set('activation', '');
			$user->set('block', '0');

			$uri = JURI::getInstance();

			// Compile the user activated notification mail values.
			$data = $user->getProperties();
			$user->setParam('activate', 0);
			$data['fromname'] = $config->get('fromname');
			$data['mailfrom'] = $config->get('mailfrom');
			$data['sitename'] = $config->get('sitename');
			$data['siteurl']	= JUri::base();
			$emailSubject	= JText::sprintf(
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

			$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);

			// Check for an error.
			if ($return !== true) {
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
		if (!$user->save()) {
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
	 * @return	mixed		Data object on success, false on failure.
	 * @since	1.6
	 */
	public function getData()
	{
		if ($this->data === null) {

			$this->data	= new stdClass();
			$app	= JFactory::getApplication();
			$params	= JComponentHelper::getParams('com_users');

			// Override the base user data with any data in the session.
			$temp = (array)$app->getUserState('com_users.registration.data', array());
			foreach ($temp as $k => $v) {
				$this->data->$k = $v;
			}

			// Get the groups the user should be added to after registration.
			$this->data->groups = array();

			// Get the default new user group, Registered if not specified.
			$system	= $params->get('new_usertype', 2);

			//$this->data->groups[] = $system;

			// Unset the passwords.
			unset($this->data->password1);
			unset($this->data->password2);

			// Get the dispatcher and load the users plugins.
			$dispatcher	= JDispatcher::getInstance();
			JPluginHelper::importPlugin('user');

			// Trigger the data preparation event.
			$results = $dispatcher->trigger('onContentPrepareData', array('com_users.registration', $this->data));

			// Check for errors encountered while preparing the data.
			if (count($results) && in_array(false, $results, true)) {
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
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_users.registration', 'registration', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		return $this->getData();
	}

	/**
	 * Override preprocessForm to load the user plugin group instead of content.
	 *
	 * @param	object	A form object.
	 * @param	mixed	The data expected for the form.
	 * @throws	Exception if there is an error in the form event.
	 * @since	1.6
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'user')
	{
		$userParams	= JComponentHelper::getParams('com_users');

		//Add the choice for site language at registration time
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
	 * @since	1.6
	 */
	protected function populateState()
	{
		// Get the application object.
		$app	= JFactory::getApplication();
		$params	= $app->getParams('com_users');

		// Load the parameters.
		$this->setState('params', $params);
	}

	/**
	 * Method to save the form data.
	 *
	 * @param	array		The form data.
	 * @return	mixed		The user id on success, false on failure.
	 * @since	1.6
	 */
	public function register($temp)
	{
		$config = JFactory::getConfig();
		$db		= $this->getDbo();
		$params = JComponentHelper::getParams('com_users');

		// Initialise the table with JUser.
		$user = new JUser;
		$data = (array)$temp;
		$data['groups'][]=$data['gid'];
		 
		// Merge in the registration data.
		foreach ($temp as $k => $v) {
			$data[$k] = $v;
		}

		// Prepare the data for the user object.
		$data['email']		= $data['email1'];
		$data['password']	= $data['password1'];
		$useractivation = $params->get('useractivation');
		$sendpassword = $params->get('sendpassword', 1);
                $office_activation = $this->getOfficeKey();
		// Check if the user needs to activate their account.
		if (($useractivation == 1) || ($useractivation == 2)) {
			$data['activation'] = JApplication::getHash(JUserHelper::genRandomPassword());
			$data['block'] = 1;
		}

		// Bind the data.
		 
		if (!$user->bind($data)) {
			$this->setError(JText::sprintf('COM_USERS_REGISTRATION_BIND_FAILED', $user->getError()));
			return false;
		}

		// Load the users plugin group.
		JPluginHelper::importPlugin('user');

		// Store the data.
		if (!$user->save()) {
			$this->setError(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()));
			return false;
		}else{
			//check for user type
			$user_parent = $this->checkUserParent($data['email']);
			$data['parent'] = $user_parent;
			//store the data in detail profile table
			//get table object
		 $userdetailtable = &JTable::getInstance('legaluser', 'LegaluserdetailTable');
		 	
		 //bind the data

		 $data['lid'] = $user->id;
		 $userdetailtable->bind($data);
		 //store the data

		 if (!$userdetailtable->save($data)) {
		 	$this->setError(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()));
		 	return false;
		 }
		 //store the data in users_office table
		 $allow_admin_group = array(9,11);
		 if(in_array($data['gid'], $allow_admin_group)){
		  foreach($data['office_details'] as $office_detail){
		 	$data['status']='1';
		    $data['activation_key'] = '';
		 	$data['lid']=$user->id;
		 	$data['office_title']=$office_detail['office'];
		 	$data['gid']=$data['gid'];
		 	$data['address'] = $office_detail['address'];
		 	$data['city'] = $office_detail['city'];
		 	$data['state'] = $office_detail['state'];
		 	$data['country'] = $office_detail['country'];
		 	$data['zip'] = $office_detail['zip'];
		 	//store the data in detail profile table
		 	//get table object
		 	$userofficetable = &JTable::getInstance('legaluser', 'LegaluserofficeTable');
		 	$userofficetable->bind($data);
		 	if(!$userofficetable->save($data)){
		 		$this->setError(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()));
		 		return false;
		 	}
		 	
		 	//add the office relation
		 	//$query = ""
  
		 }
		 	
		 }else{
		 	//if employee has registered
		  foreach($data['office_details'] as $office_detail){
		  	
             
             $data['parent_id']=$user_parent;
		 	//check if the user is parent. If the user is parent then the office will be active.
		 	if($user_parent == $user->id){
		 		$data['status']='1';
		 		$data['activation_key'] = '';
		 	}else{
		 		//check if office is new
		 		if($data['check_office']==1){
		 			$data['status']='0';
		 			$data['activation_key'] = $office_activation;
		 		}else{
		 			$data['status']='1';
		 			$data['activation_key'] = '';
		 		}
		 	}
		 	if($data['check_office']==1){
		 	$data['lid']=$user_parent;
		 	$data['empid']=$user->id;
		 	$data['office_title']=$office_detail['office'];
		 	$data['gid']=$data['gid'];
		 	$data['address'] = $office_detail['address'];
		 	$data['city'] = $office_detail['city'];
		 	$data['state'] = $office_detail['state'];
		 	$data['country'] = $office_detail['country'];
		 	$data['zip'] = $office_detail['zip'];
		 	//store the data in detail profile table
		 	//get table object
		 	$userofficetable = &JTable::getInstance('legaluser', 'LegaluserofficeTable');
		 	$userofficetable->bind($data);
		 	if(!$userofficetable->save($data)){
		 		$this->setError(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()));
		 		return false;
		 	}
		 	$data['office_id']=$userofficetable->id;
		 	$empofficetable = &JTable::getInstance('legaluser', 'LegalempofficeTable');
		 	$empofficetable->bind($data);
		 	if(!$empofficetable->save($data)){
		 		$this->setError(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()));
		 		return false;
		 	}
		 	}else{
		 		$data['empid']=$user->id;
		 		$data['office_id']=$office_detail['ofc_val'];
		 		
		 		$empofficetable = &JTable::getInstance('legaluser', 'LegalempofficeTable');
			 	$empofficetable->bind($data);
			 	
			 	if(!$empofficetable->save($data)){
			 		$this->setError(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()));
			 		return false;
			 	}
		 	}
		 	//add the office relation
		 	//$query = ""
  
		 }
		 }
		
		 //get cofig
		 $config =& JFactory::getConfig();
		 $auditor = $config->getValue('auditor');
		 $auditor_emp = $config->getValue('auditor_emp');
		 if($data['gid'] == $auditor){
		 	//get payment info
		 	$app	= JFactory::getApplication();
		 	$payment_info = $app->getUserState('com_legalconfirmusers.payment.data');
		 	$payment_info['lid'] = $user->id;
		 	$payment_info['gid'] = $data['gid'];
		 	$this->addpaymentdetail($payment_info);
		 }

		}
		// Compile the notification mail values.
		$data = $user->getProperties();
		$data['fromname']	= $config->get('fromname');
		$data['mailfrom']	= $config->get('mailfrom');
		$data['sitename']	= $config->get('sitename');
		$data['siteurl']	= JUri::root();

		// Handle account activation/confirmation emails.
		if ($useractivation == 2)
		{
			// Set the link to confirm the user email.
			$uri = JURI::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base.JRoute::_('index.php?option=com_legalconfirmusers&task=registration.activate&token='.$data['activation'], false);

			$emailSubject	= JText::sprintf(
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
				$data['siteurl'].'index.php?option=com_legalconfirmusers&task=registration.activate&token='.$data['activation'],
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
				$data['siteurl'].'index.php?option=com_legalconfirmusers&task=registration.activate&token='.$data['activation'],
				$data['siteurl'],
				$data['username']
				);
			}
		}
		elseif ($useractivation == 1)
		{
			// Set the link to activate the user account.
			$uri = JURI::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base.JRoute::_('index.php?option=com_legalconfirmusers&task=registration.activate&token='.$data['activation'], false);

			$emailSubject	= JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
			$data['name'],
			$data['sitename']
			);

			if ($sendpassword)
			{
				$emailBody = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
				$data['name'],
				$data['sitename'],
				$data['siteurl'].'index.php?option=com_legalconfirmusers&task=registration.activate&token='.$data['activation'],
				$data['siteurl'],
				$data['username'],
				$data['password_clear']
				);
			}
			else
			{
				$emailBody = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY_NOPW',
				$data['name'],
				$data['sitename'],
				$data['siteurl'].'index.php?option=com_legalconfirmusers&task=registration.activate&token='.$data['activation'],
				$data['siteurl'],
				$data['username']
				);
			}
		}
		else
		{

			$emailSubject	= JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
			$data['name'],
			$data['sitename']
			);

			$emailBody = JText::sprintf(
				'COM_USERS_EMAIL_REGISTERED_BODY',
			$data['name'],
			$data['sitename'],
			$data['siteurl']
			);
		}

		// Send the registration email .
		$lawyer = $config->getValue('lawfirm');

		if($data['gid'] == $auditor){
			$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);
		}
		//
		//
		//
		//
		//send mail to auditor employee and auditor admin
		if($data['gid']==$auditor_emp){
			$auditor_emp_email = $data['email'] ;
			//get firm admin id
			$audior_firm_admin_id = $this->getAuditorFirmAdminEmailId($auditor_emp_email);
			$auditor_admin = JUser::getInstance($audior_firm_admin_id);
			$auditor_admin_email = $auditor_admin->email;
			$auditor_admin_name = $auditor_admin->name;
				
			//mail to auditor employee when he get registerd
			$emailSubject_auditor_emp	= JText::sprintf(
					'COM_USERS_EMAIL_ACCOUNT_DETAILS',
			$data['name'],
			$data['sitename']
			);
			$emailBody_auditor_emp = JText::sprintf(
						'COM_LEGALEMP_PARTNER_EMAIL_REGISTERED_APPROVAL',
			$data['name'],
			$data['sitename'],
			$data['siteurl'],
			$data['username'],
			$data['password_clear']
			);
			$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject_auditor_emp, $emailBody_auditor_emp);
			 
			// mail to lawfirm admin
			 
			$emailSubjectFirmAdmin	= JText::sprintf(
					'COM_USERS_EMAIL_ACCOUNT_DETAILS',
			$data['name'],
			$data['sitename']
			);
			$emailBodyFirmAdmin = JText::sprintf(
						'COM_LEGALFIRM_ADMIN_EMAIL_SEND_FOR_APPROVAL',
			$auditor_admin_name,
			$emp_type,
			$data['username'],
			$data['sitename'],
			$data['siteurl'].'index.php?option=com_legalconfirmusers&task=registration.activate&token='.$data['activation'],
			$data['username'],
			$data['siteurl'],
			$data['password_clear']
			);
			$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $auditor_admin_email, $emailSubjectFirmAdmin, $emailBodyFirmAdmin);
			 
				
		}
		//
		//
		//
		//Send Notification mail to site administrators if user type is lawfirm admin
		if($data['gid'] == $lawyer){
				
			//send mail to lawfirm admin
			$emailSubjectLawfirm = JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
			$data['name'],
			$data['sitename']
			);

			$emailBodyAdminLawfirm = JText::sprintf(
					'COM_LEGALCONFIRM_LAWFIRM_ACCOUNT_MESSAGE_BODY',
			$data['name']
			);
				
			$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubjectLawfirm, $emailBodyAdminLawfirm);

			$emailSubject = JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
			$data['name'],
			$data['sitename']
			);

			$emailBodyAdmin = JText::sprintf(
					'COM_LEGALCONFIRM_ADMIN_LAWFIRM_APPROVE_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
				
			$data['sitename'],
			$data['name'],
			$data['username'],
			$data['username'],
			$data['siteurl'].'index.php?option=com_legalconfirmusers&task=registration.activate&token='.$data['activation'],
			$data['password_clear']
			);

			// get all admin users
			$query = 'SELECT name, email, sendEmail' .
					' FROM #__users' .
					' WHERE sendEmail=1';

			$db->setQuery( $query );
			$rows = $db->loadObjectList();

			// Send mail to all superadministrators id
			foreach( $rows as $row )
			{
				$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $emailSubject, $emailBodyAdmin);

				// Check for an error.
				if ($return !== true) {
					$this->setError(JText::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'));
					return false;
				}
			}
		}
		//send email notification to the firm admin
		//get lawfirm group value
		$lawfirm_emp = $config->getValue('lawfirm_emp');
		$lawfirm_partner = $config->getValue('lawfirm_partner');

		//check for lawfirm employee or partner to set value in mail

		if($data['gid'] == $lawfirm_emp || $data['gid'] == $lawfirm_partner){
			if($data['gid'] == $lawfirm_emp){
				$emp_type="Employee";
			}else{
				$emp_type="Partner";
			}
			//get firm admin email id
			$law_emp_email = $data['email'] ;
			//get lawfirm admin email id
			$lawfirm_admin_id = $this->getLawFirmAdminEmailId($law_emp_email);
			$lawfirm_admin = JUser::getInstance($lawfirm_admin_id);
			$lawfirm_admin_email = $lawfirm_admin->email;
			$lawfirm_admin_name = $lawfirm_admin->name;
			//mail to user when he get registerd
			$emailSubject	= JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
			$data['name'],
			$data['sitename']
			);
			$emailBody = JText::sprintf(
					'COM_LEGALEMP_PARTNER_EMAIL_REGISTERED_APPROVAL',
			$data['name'],
			$data['sitename'],
			$data['siteurl'],
			$data['username'],
			$data['password_clear']
			);
			$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);

			// mail to lawfirm admin
			 
			//$emailSubjectFirmAdmin	= JText::sprintf(
			//	'COM_USERS_EMAIL_ACCOUNT_DETAILS',
			//$data['name'],
			//$data['sitename']
			//);
			//$emailBodyFirmAdmin = JText::sprintf(
			//		'COM_LEGALFIRM_ADMIN_EMAIL_SEND_FOR_APPROVAL',
			//$lawfirm_admin_name,
			//$emp_type,
			//$data['username'],
			//$data['sitename'],
			//$data['siteurl'].'index.php?option=com_legalconfirmusers&task=registration.activate&token='.$data['activation'],
			//$data['username'],
			//$data['siteurl'],
			//$data['password_clear']
			//);
			//$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $lawfirm_admin_email, $emailSubjectFirmAdmin, $emailBodyFirmAdmin);


			//send the mail to lawfirm admin when the new employee get registered.
			//mail will contain user activation link and new office detail.
			//send the mail code
			$activation_url = $data['siteurl'].'index.php?option=com_legalconfirmusers&task=registration.activate&token='.$data['activation'];
$office_activation_url = $data['siteurl'].'index.php?option=com_lawfirm&task=lawfirmadmin.activateoffice&token='.$office_activation;
			
			$subject = "Account detail for ".$data['name']." at ".$data['sitename'];
			$to_email = $lawfirm_admin_email;
			$mailfrom = $data['mailfrom'];
			$fromname = $data['fromname'];
			$text = "Hello ".$lawfirm_admin_name.",<br /><br />New ".$emp_type." ".$data['username']." registered at LegalConfirm. Account must be activated before they can use it.
			           <br />To activate the account click on the following link or copy-paste it in your browser: ".$activation_url;

$office_text = "<br /><br /><b>New office is added by this employee</b>.Details are:<br/>
			                Office Title: ".$office_detail['office']."<br />
			                Address: ".$office_detail['address']."<br />
			                City: ".$office_detail['city']."<br />
			                State: ".$office_detail['state']."<br />
			                Country: ".$office_detail['country']."<br />
			                Zip: ".$office_detail['zip']."<br /><br />
			                You can activate the office clicking the following link
			                ".$office_activation_url."<br />";
			
			
			if($user_parent != $user->id && $data['check_office']==1){
				
				$text = $text.$office_text;
			}

			$mail = &JFactory::getMailer();
			$app  = JFactory::getApplication();

			$mail->setSubject($subject);
			 
			$mail->IsHTML= true;
			$mail->ContentType = 'text/html';
			$joomla_config = new JConfig();
			$mail->addRecipient($to_email);
			$mail->setSender($mailfrom, $fromname);
			$mail->setBody($text);
			$return = $mail->Send();

		}
		// Check for an error.
		if ($return !== true) {
			$this->setError(JText::_('COM_USERS_REGISTRATION_SEND_MAIL_FAILED'));

			// Send a system message to administrators receiving system mails
			$db = JFactory::getDBO();
			$q = "SELECT id
				FROM #__users
				WHERE block = 0
				AND sendEmail = 1";
			$db->setQuery($q);
			$sendEmail = $db->loadColumn();
			if (count($sendEmail) > 0) {
				$jdate = new JDate();
				// Build the query to add the messages
				$q = "INSERT INTO ".$db->quoteName('#__messages')." (".$db->quoteName('user_id_from').
				", ".$db->quoteName('user_id_to').", ".$db->quoteName('date_time').
				", ".$db->quoteName('subject').", ".$db->quoteName('message').") VALUES ";
				$messages = array();

				foreach ($sendEmail as $userid) {
					$messages[] = "(".$userid.", ".$userid.", '".$jdate->toSql()."', '".JText::_('COM_USERS_MAIL_SEND_FAILURE_SUBJECT')."', '".JText::sprintf('COM_USERS_MAIL_SEND_FAILURE_BODY', $return, $data['username'])."')";
				}
				$q .= implode(',', $messages);
				$db->setQuery($q);
				$db->query();
			}
			return false;
		}

		if ($useractivation == 1){
			//check for group id to send message when user get registerd
			if($data['gid'] == $auditor){
				return "useractivate";
			}
			if($data['gid'] == $auditor_emp){
				return "useractivate3";
			}
			if($data['gid'] == $lawyer){
				return "useractivate1";
			}
			if($data['gid'] == $lawfirm_emp || $data['gid'] == $lawfirm_partner){
				return "useractivate2";
			}
		}

		elseif ($useractivation == 2)
		return "adminactivate";
		else
		return $user->id;
	}

	/**
	 * @Author::Abhishek
	 * Method to check the register in step1
	 * @param:: Email,Group type
	 * @return::
	 */
	public function register1($data){
		$email = $data['email1'];
		$group = $data['user_type'];

		//check for parent firm
		$chk_parent_firm = $this->checkParentFirm($email,$group);
		return $chk_parent_firm;
	}

	/**
	 * @Author::Abhishek
	 * Method to check the parent firm
	 * We will explode the email and check for parent firm if exist
	 * @param:: Email
	 */
	public function checkParentFirm($email,$group){
		//get db object
		$db = JFactory::getDbo();
		$firm_array = explode('@',$email);
		$firm_name = $firm_array['1'];
			
		//check for firm if exist
		$query = "SELECT count('id') as firmcount FROM #__users where email like '%@".$firm_name."'";
		$db->setQuery($query);
		$db->query();
		$result = $db->loadResult();

		//if parent user
		if($result==0){
			$group_id = $group;
		}
		//if child user
		else{
			$group_id = $group+1;
		}
		return $group_id;
	}

	/**
	 *
	 */
	public function checkemail($email){
		//get db object
		$db = JFactory::getDbo();

		//check for email
		$query = "SELECT count('email') as emailcount FROM #__users WHERE email = ".$db->Quote($email);
		$db->setQuery($query);
		$db->query();
		$result = $db->loadResult();

		if($result == 0){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * @Author::Abhishek
	 * Method to submit the payment detail by user.
	 * @param::array user payment data
	 */
	public function addpaymentdetail($data){
		//get table object
		$userpaymenttable = &JTable::getInstance('legaluser', 'LegaluserpaymentTable');
		$userpaymenttable->bind($data);
		$userpaymenttable->save($data);
	}

	/**
	 * @Author::Abhishek
	 * Method to get office detail
	 * @param::office id
	 * return array office data
	 */
	public function getOfcDetail($data){
		//get database object
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__users_office where id = ".$db->Quote($data);
		$db->setQuery($query);
		$db->query();
		$results = $db->loadObject();
		return $results;

	}
	/**
	 * @Author::Abhishek
	 * Method to get usergroup on the basis of token
	 */
	public function getusergroup($data){
		//get database object
		$db = JFactory::getDBO();
		$query = "SELECT a.group_id,b.id FROM #__user_usergroup_map a,#__users b where b.activation = ".$db->Quote($data)." AND a.user_id=b.id";
		$db->setQuery($query);
		$db->query();
		$results = $db->loadObject();
		return $results;
	}
	/**
	 *Method to send email to lawfirm admin when site admin activate the account
	 */
	public function lawfirmadminaccount($id){
		// Get the user object.
		$user = JUser::getInstance($id);

		// Generate the new password hash.
		$salt		= JUserHelper::genRandomPassword(32);
		//get random password
		$pass = $this->rand_string(8);
		$crypted	= JUserHelper::getCryptedPassword($pass, $salt);
		$password	= $crypted.':'.$salt;

		// Update the user object.
		$user->password			= $password;
		$user->activation		= '';
		$user->password_clear	= $pass;
		// Save the user to the database.

		if (!$user->save(true)) {
				
			return new JException(JText::sprintf('COM_USERS_USER_SAVE_FAILED', $user->getError()), 500);
		}
		else{
			$config	= JFactory::getConfig();
			$data['fromname']	= $config->get('fromname');
			$data['mailfrom']	= $config->get('mailfrom');
			$data['sitename']	= $config->get('sitename');
			$data['siteurl']	= JUri::root();
			//send mail to firm admin containing user name and password.
			$emailSubject	= JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
			$user->name,
			$data['sitename']
			);
			$emailBody = JText::sprintf(
					'COM_LAWFIRM_ADMIN_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
			$user->name,
			$data['sitename'],
			$data['siteurl'],
			$user->username,
			$user->password_clear
			);
			$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $user->email, $emailSubject, $emailBody);
			return $return;
		}

	}
	/**
	 * Method to get firm admin email id
	 * @param employee $email
	 * @return mixed
	 */
	public function getLawFirmAdminEmailId($email){
		$firm_array = explode('@',$email);
		$firm_name = $firm_array['1'];
		$db = JFactory::getDbo();
		$query = "SELECT MIN(id) as ms FROM #__users as a where a.email like '%@".$firm_name."'";
		$db->setQuery($query);
		$db->query();
		$result = $db->loadResult();
		return $result;
			
	}
	/**
	 * Method to get Auditor firm admin id
	 * @param employee $email
	 * @return mixed
	 */
	public function getAuditorFirmAdminEmailId($email){
		$firm_array = explode('@',$email);
		$firm_name = $firm_array['1'];
		$db = JFactory::getDbo();
		$query = "SELECT MIN(id) as ms FROM #__users as a where a.email like '%@".$firm_name."'";
		$db->setQuery($query);
		$db->query();
		$result = $db->loadResult();
			
		return $result;
			
	}
	/**
	 * Method to check user group
	 */
	public function checkgroup($email){
		//get firm admin id
		$id = $this->getLawFirmAdminEmailId($email);
		//get db object
		$db = JFactory::getDbo();
		$query = "SELECT group_id FROM #__user_usergroup_map as a where a.user_id =".$db->Quote($id);
		$db->setQuery($query);
		$db->query();
		$result = $db->loadResult();
		return $result;
	}
	/**
	 * Method to send mail to employee on activation of account
	 */
	public function sendMailToEmp($id){
		// Get the user object.
		$user = JUser::getInstance($id);
		$config	= JFactory::getConfig();
		$data['fromname']	= $config->get('fromname');
		$data['mailfrom']	= $config->get('mailfrom');
		$data['sitename']	= $config->get('sitename');
		$data['siteurl']	= JUri::root();
		//send mail to firm employee containing user name and site url.
		$emailSubject	= JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
		$user->name,
		$data['sitename']
		);
		$emailBody = JText::sprintf(
					'COM_LEGALFIRM_EMPLOYEE_EMAIL_REGISTERED',
		$user->name,
		$data['sitename'],
		$data['siteurl'],
		$user->username,
		$user->password_clear
		);
		$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $user->email, $emailSubject, $emailBody);
        return $return;
	}
	/**
	 * Method to check user parent
	 * 
	 */
	public function checkUserParent($data){
		 $db = JFactory::getDbo();
		 $firm_array = explode('@',$data);
		 $firm_name = $firm_array['1'];
		 $query = "SELECT MIN(id) as ms FROM #__users as a where a.email like '%@".$firm_name."'";
		 $db->setQuery($query);
		 $db->query();
		 $result = $db->loadResult();
		 return $result;
	}
	
	/**
	 * Method to get random password
	 */
	public function rand_string( $length ) {
	
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	return substr(str_shuffle($chars),0,$length);
	
	}

/**
	 * Method to get activatio key for office
	 */
	public function getOfficeKey(){
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$rand_num = substr(str_shuffle($chars),0,'4');
		$encrypted_key = md5($rand_num);
		return $encrypted_key;
	}
}
