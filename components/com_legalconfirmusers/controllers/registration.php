<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Registration controller class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @since		1.6
 */
class LegalconfirmusersControllerRegistration extends LegalconfirmusersController
{
	/**
	 * Method to activate a user.
	 *
	 * @return	boolean		True on success, false on failure.
	 * @since	1.6
	 */
	public function activate()
	{
		$user		= JFactory::getUser();
		$uParams	= JComponentHelper::getParams('com_users');

		// If the user is logged in, return them back to the homepage.
		if ($user->get('id')) {
			$this->setRedirect('index.php');
			return true;
		}

		// If user registration or account activation is disabled, throw a 403.
		if ($uParams->get('useractivation') == 0 || $uParams->get('allowUserRegistration') == 0) {
			JError::raiseError(403, JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
			return false;
		}

		$model = $this->getModel('Registration', 'LegalconfirmusersModel');
		$token = JRequest::getVar('token', null, 'request', 'alnum');

		// Check that the token is in a valid format.
		if ($token === null || strlen($token) !== 32) {
			JError::raiseError(403, JText::_('JINVALID_TOKEN'));
			return false;
		}
		//get user group
         $return1 = $model->getusergroup($token);
        
         $group_id = $return1->group_id;	
         $uid = $return1->id;
		// Attempt to activate the user.
		$return = $model->activate($token);

		// Check for errors.
		if ($return === false) {
			// Redirect back to the homepage.
			$this->setMessage(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect('index.php');
			return false;
		}
		//check for group id;
		$config =& JFactory::getConfig();
		$auditor = $config->getValue( 'auditor');
		$lawfirm = $config->getValue( 'lawfirm');
		$auditor_emp = $config->getValue( 'auditor_emp');
		$lawfirm_emp = $config->getValue( 'lawfirm_emp');
		$lawfirm_partner = $config->getValue( 'lawfirm_partner');
		
		if($group_id == $lawfirm){
			$model->lawfirmadminaccount($uid);
		}
		elseif($group_id == $auditor_emp || $group_id == $lawfirm_emp || $group_id == $lawfirm_partner){
			$model->sendMailToEmp($uid);
		}
	   
			
		

		$useractivation = $uParams->get('useractivation');

		// Redirect to the login screen.
		if ($useractivation == 0)
		{
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_SAVE_SUCCESS1'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
		}
		elseif ($useractivation == 1)
		{
			//show the message according to user type
			if($group_id==$auditor_emp){
				
				$this->setMessage(JText::_('COM_USERS_REGISTRATION_ACTIVATE_SUCCESS_AUDITOR'));
			 	$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=complete', false));
			  //  die;
			}
			elseif($group_id==$lawfirm_emp || $group_id==$lawfirm_partner){
				$this->setMessage(JText::_('COM_USERS_REGISTRATION_ACTIVATE_SUCCESS_LAWFIRM'));
				$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=complete', false));
			}
			elseif($group_id==$lawfirm){
				$this->setMessage(JText::_('COM_USERS_REGISTRATION_ACTIVATE_SUCCESS_LAWFIRM'));
				$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=complete', false));
			}
			else{
				
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_ACTIVATE_SUCCESS'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
			}
		}
		elseif ($return->getParam('activate'))
		{
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_VERIFY_SUCCESS'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=complete', false));
		}
		else
		{
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_ADMINACTIVATE_SUCCESS'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=complete', false));
		}
		return true;
	}

	/**
	 * Method to register a user.
	 *
	 * @return	boolean		True on success, false on failure.
	 * @since	1.6
	 */
	public function register()
	{
		
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
     
		// If registration is disabled - Redirect to login page.
		if(JComponentHelper::getParams('com_users')->get('allowUserRegistration') == 0) {
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
			return false;
		}

		// Initialise variables.
		$app	= JFactory::getApplication();
		//get all users data
		$app	= JFactory::getApplication();
		$allinfo = $app->getUserState('com_legalconfirmusers.register2.data');
		
		
		$pay_nfo = $app->getUserState('com_legalconfirmusers.payment.data');
		
		$model	= $this->getModel('Registration', 'LegalconfirmusersModel');

		// Get the user data.
		
		$data['name'] = $allinfo['personal_detail']['emp_name'];
		$data['username'] = $allinfo['personal_detail']['email'];
		//check for lawfirm admin
		//form law firm we will generate auto password.
		//get cofig
		$config =& JFactory::getConfig();
		$lawfirm = $config->getValue( 'lawfirm');
		
		if($allinfo['personal_detail']['gid'] == $lawfirm){
		$data['password1'] = 'password';
		$data['password2'] = 'password';
		}else{
		$data['password1'] = $allinfo['personal_detail']['password'];
		$data['password2'] = $allinfo['personal_detail']['password'];
		}
		$data['email1'] = $allinfo['personal_detail']['email'];
		$data['email2'] = $allinfo['personal_detail']['email'];
		$data['accounting_firm']=$allinfo['personal_detail']['firm'];
		$data['emp_title']=$allinfo['personal_detail']['title'];
		$data['phone']=$allinfo['personal_detail']['phone'];
		//check for lawfirm emp or partner
//		echo "<pre>";
//		print_r($allinfo);
//		die;
		$data['gid'] = $allinfo['personal_detail']['gid'];
		
		//office data 
		//if admin
		$data['office_details'] = $allinfo['office_detail'];
		$data['check_office'] = $allinfo['check_office_type'];
		
		// Attempt to save the data.
		$return	= $model->register($data);

		// Check for errors.
		if ($return === false) {
			// Save the data in the session.
			$app->setUserState('com_users.registration.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=register1', false));
			return false;
		}
        
		// Flush the data from the session.
		$app->setUserState('com_users.registration.data', null);
		$app->setUserState('com_legalconfirmusers.email.data', null);
		$app->setUserState('com_legalconfirmusers.register2.data', null);
		$app->setUserState('com_legalconfirmusers.payment.data', null);

		// Redirect to the profile screen.
		if ($return === 'adminactivate'){
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_VERIFY'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=complete', false));
		} elseif ($return === 'useractivate') {
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=complete', false));
		} elseif ($return === 'useractivate1') {
			$this->setMessage(JText::_('COM_LEGALCONFIRM_USERS_REGISTRATION_COMPLETE_LAW_ADMIN'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=complete', false));
		} elseif ($return === 'useractivate2') {
			$this->setMessage(JText::_('COM_LEGALCONFIRM_USERS_REGISTRATION_COMPLETE_LAW_EMPLOYEE'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=complete', false));
		} elseif ($return === 'useractivate3') {
			$this->setMessage(JText::_('COM_LEGALCONFIRM_USERS_REGISTRATION_COMPLETE_AUDITOR_EMPLOYEE'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=complete', false));
		} else {
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_SAVE_SUCCESS5'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers2&view=login', false));
		}


		return true;
	}
	
	/**
	 * @Author::Abhishek
	 * Method to check the register in step1
	 * @param:: Email,Group type
	 */
	public function register1(){
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$app	= JFactory::getApplication();
		
		//get form data
		$data=JRequest::get('POST');
		
		//get model object 
		$model	= $this->getModel('Registration', 'LegalconfirmusersModel');
		
	   //check for email id
		$valid_email = $model->checkemail($data['email1']);
		if($valid_email==false){
			$this->setMessage(JText::sprintf('COM_USERS_REGISTRATION_EMAIL_IN_USE', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=register1', false));
			return false;
		}
		$valid_group = $model->checkgroup($data['email1']);
		
		if($valid_group != $data['user_type'] && $valid_group != ''){
			$this->setMessage(JText::sprintf('COM_USERS_REGISTRATION_CORRECT_USER_TYPE', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=register1', false));
			return false;
		}
		
		//check for step register1
		$result = $model->register1($data);
		//get cofig
		$config =& JFactory::getConfig();
		$auditor = $config->getValue( 'auditor');
		$lawfirm = $config->getValue( 'lawfirm');
		$app	= JFactory::getApplication();
		$app->setUserState('com_legalconfirmusers.email.data', $data['email1']);
		//check for user group
		if($result == $auditor || $result == $lawfirm){
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=firmadminregister&gid='.$result, false));
		}else{
	    $this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=firmempregister&gid='.$result, false));	
		}
	}
	
	/**
	 * @Author::Abhishek
	 * Method to check the register in step2
	 * @param:: Email,Group type
	 */
	public function register2(){
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		//get form data
		$data = JRequest::get('POST');
		
		//get model object 
		$model	= $this->getModel('Registration', 'LegalconfirmusersModel');
		//check for email id
		$valid_email = $model->checkemail($data['personal']['email']);
		if($valid_email==false){
			$this->setMessage(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=firmadminregister&gid='.$data['gid'], false));
			return false;
		}
		///
		
		if($data['check_office'] == 1){
		unset($file_ary);	
		$file_ary = array();
		$file_count = count($data['ofc_detail_new']['office']);
		$file_keys = array_keys($data['ofc_detail_new']);

		for ($i=0; $i<$file_count; $i++) {
			foreach ($file_keys as $key) {
				$file_ary[$i][$key] = $data['ofc_detail_new'][$key][$i];
			}
		}
		}else{
		$file_ary = array();
		$file_count = count($data['ofc_detail']['office']);
		$file_keys = array_keys($data['ofc_detail']);

		for ($i=0; $i<$file_count; $i++) {
			foreach ($file_keys as $key) {
				$file_ary[$i][$key] = $data['ofc_detail'][$key][$i];
			}
		}
		
	}
		$office_array = $file_ary;
		
		//get personal data
		$personal_info = $data['personal'];
		
		$all_info = array('office_detail'=>$office_array,'personal_detail'=>$personal_info,'check_office_type'=>$data['check_office']);
		
		
		//store the data in session
		$app	= JFactory::getApplication();
		$app->setUserState('com_legalconfirmusers.register2.data', $all_info);
		//get auditor employee value
		$config =& JFactory::getConfig();
		$auditor_emp = $config->getValue( 'auditor_emp');
		$lawfirm = $config->getValue('lawfirm');
		$lawfirm_emp = $config->getValue('lawfirm_emp');
		$lawfirm_partner = $config->getValue('lawfirm_partner');
		//check for usergroup
		if($personal_info['gid'] == $auditor_emp || $personal_info['gid'] == $lawfirm || $personal_info['gid'] == $lawfirm_emp || $personal_info['gid'] == $lawfirm_partner){
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=terms', false));
		}else{
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=payment', false));
		}
	}
	
	/**
	 * @Author::Abhishek
	 * Method for payment information
	 * @param:: payment array
	 */
	public function payment(){
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$data = JRequest::get('POST');
		//store the data in session
		$app	= JFactory::getApplication();
		$app->setUserState('com_legalconfirmusers.payment.data', $data);
		//check for previous form
		$prev_data = $app->getUserState('com_legalconfirmusers.register2.data');
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=terms', false));
	}
	
	/**
	 * Method to check email
	 * isValid,isAlreadyRegisterd,isCorrect
	 */
	public function checkemail(){
		$email = JRequest::getVar('email');
		if(filter_var($email, FILTER_VALIDATE_EMAIL)){
        //get model object 
		$model	= $this->getModel('Registration', 'LegalconfirmusersModel');
		//check for email id
		$valid_email = $model->checkemail($email);
		if($valid_email==false){
			echo "Email in use";
		}
		else{
			echo "Correct";
		}
		exit;
        }else{
              echo "invalid";
              exit;
          } 
		
	}
	
	/**
	 * @Author::Abhishek
	 * Method to get officedetail
	 */
	public function getofficedetail(){
		  //get office id
		  $id = JRequest::getVar('ofcid');
		 //get model object 
		$model	= $this->getModel('Registration', 'LegalconfirmusersModel');
		$ofc_data = $model->getOfcDetail($id);
		$json_data = json_encode($ofc_data);
		echo $json_data;
		exit;
	}

        /**
	 * Method to check user type from step1
	 * @param email
	 */
	public function checkUser(){
		 $email = JRequest::getVar('email');
		 $group = JRequest::getVar('group');
		
		//get model object
		$model	= $this->getModel('Registration', 'LegalconfirmusersModel');
		$user_type = $model->checkParentFirm($email,$group);
		//get cofig
		$config =& JFactory::getConfig();
		$auditor = $config->getValue( 'auditor');
		$lawfirm = $config->getValue( 'lawfirm');
		if($user_type == $auditor || $user_type == $lawfirm){
			echo "admin";
		}else{
			echo "emp";
		}
		exit;
	}
}
