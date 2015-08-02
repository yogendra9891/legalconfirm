<?php

/**
 * @version     1.0.0
 * @package     com_legalconfirm
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Abhishek Gupta <abhishek.gupta@daffodilsw.com> - http://
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_legalconfirmusers' . DS . 'tables' . DS . 'legaluserdetail.php' );
/**
 * Methods supporting a list of Legalconfirm clients.
 */
class LegalconfirmModelUserprofile extends JModelList {

	/**
	 * Constructor.
	 *
	 * @param    array    An optional associative array of configuration settings.
	 * @see        JController
	 * @since    1.6
	 */
	public function __construct($config = array()) {
		parent::__construct($config);
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
		$app = JFactory::getApplication('com_legalconfirm');

		// Load state from the request userState on edit or from the passed variable on default
		if (JFactory::getApplication()->input->get('layout') == 'edit') {
			$id = JFactory::getApplication()->getUserState('com_legalconfirm.edit.lawfirm.id');
		} else {
			$id = JFactory::getApplication()->input->get('id');
			JFactory::getApplication()->setUserState('com_legalconfirm.edit.lawfirm.id', $id);
		}
		$this->setState('lawfirm.id', $id);

		// Load the parameters.
		$params = $app->getParams();
		$params_array = $params->toArray();
		if(isset($params_array['item_id'])){
			$this->setState('lawfirm.id', $params_array['item_id']);
		}
		$this->setState('params', $params);

	}


	/**
	 * Method to get an ojbect.
	 *
	 * @param	integer	The id of the object to get.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function &getData($id = null)
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		//get logged in userid
		$user = JFactory::getUser();
		$userId = $user->id;

		$table = $this->getTable();
		//get profile
		$query = "SELECT a.*,"
		." c.lid,c.accounting_firm,c.emp_title,c.phone"
		." FROM #__users as a ,"
		." #__user_usergroup_map as b,"
		." #__users_profile_detail as c"
		." WHERE a.id = b.user_id AND c.lid=a.id AND a.id =".$db->Quote($userId);

		$db->setQuery($query);
		$db->query();
		$result = $db->loadObject();


		//get offices
		$query_office = "SELECT a.* FROM #__users_office as a JOIN #__employee_office as b ON b.office_id = a.id where b.empid = ".$db->Quote($userId);

		$db->setQuery($query_office);
		$db->query();
		$offices = $db->loadObject();

		//get payment detail
		$payment_data = $this->getpaymentinfo($userId);

		//get groupid
		$query_group = "SELECT group_id FROM #__user_usergroup_map where user_id = ".$db->Quote($userId);
		$db->setQuery($query_group);
		$db->query();
		$group_id = $db->loadObject();
		$user_group = $group_id->group_id;


		$data = array('personalinfo'=>$result,'offices'=>$offices,'paymentInfo'=>$payment_data,'groupId'=>$user_group);


		return $data;


	}


	public function getTable($type = 'User', $prefix = 'JTable', $config = array())
	{
		$table = JTable::getInstance($type, $prefix, $config);

		return $table;
	}

	/**
	 * Method to get payment info
	 *
	 */
	public function getpaymentinfo($id){
		$db = $this->getDbo();
		$query = "SELECT a.cc_number,a.name_on_cc,a.address,a.city,a.state,a.country,a.zip,a.esn FROM #__users_payment_detail as a where a.lid = ".$db->Quote($id);
			
		$db->setQuery($query);
		$db->query();
		$result = $db->loadObject();

		return $result;
	}

	/**
	 *Method to update user profile
	 */
	public function save($data){
		$config =& JFactory::getConfig();
		$auditor = $config->getValue( 'auditor');
		//get db object
		$db = $this->getDbo();
		//get current user id
		$cuser = JFactory::getUser();
		$userId = $cuser->id;
		$data['id'] = $userId;
		$data['name'] = $data['personal']['emp_name'];
		$data['accounting_firm']=$data['personal']['firm'];
		$data['emp_title']=$data['personal']['title'];
		$data['phone']=$data['personal']['phone'];
		$gid = $data['personal']['gid'];

		//$user_userdata['name'] = $data['personal']['emp_name'];
		//get #__user table object
		$table = $this->getTable();
		$table->load($userId);
		//save the data in table
		if(!$table->save($data)){
			$this->setError(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()));
			return false;
		}
		
                //update the user object
		$user = JFactory::getUser();
		$session = JFactory::getSession();
		$session->set('user', new JUser($user->id));
		//new data for your user after the update
		$user = JFactory::getUser();
	
		//update #__users_profile_detail
		$query = "Update #__users_profile_detail
    			 set `accounting_firm` = ".$db->Quote($data['accounting_firm']).",
    			  `emp_title` = ".$db->Quote($data['emp_title']).",
    			   `phone` = ".$db->Quote($data['phone'])."
    			    WHERE lid = ".$db->Quote($userId);
		$db->setQuery($query);
		if(!$db->query()){
			return false;
		}

		if($auditor == $gid){
			$query = "Update #__users_profile_detail
    			     set `accounting_firm` = ".$db->Quote($data['accounting_firm'])."
    			     WHERE `parent` = ".$db->Quote($userId);
			
			$db->setQuery($query);
		if(!$db->query()){
			return false;
		}
		}
		//update #__users_payment_detail
		$query = "Update #__users_payment_detail
    			  set `cc_number` = ".$db->Quote($data['cc_number']).",
    			    `name_on_cc` = ".$db->Quote($data['name_on_cc']).",
    			    `address` = ".$db->Quote($data['address']).", 
    			    `city` = ".$db->Quote($data['city']).", 
    			    `country` = ".$db->Quote($data['country']).", 
    			    `zip` = ".$db->Quote($data['zip']).", 
    			    `esn` = ".$db->Quote($data['esn']).", 
    			    `state` = ".$db->Quote($data['state'])."
    			    WHERE lid = ".$db->Quote($userId);
		$db->setQuery($query);
		if(!$db->query()){
			return false;
		}

		//update office table
		///get offices
		$file_ary = array();
		$file_count = count($data['ofc_detail']['office']);
		$file_keys = array_keys($data['ofc_detail']);

		for ($i=0; $i<$file_count; $i++) {
			foreach ($file_keys as $key) {
				$file_ary[$i][$key] = $data['ofc_detail'][$key][$i];
			}
		}

		$office_array = $file_ary;

		foreach($office_array as $single){
			$query = "UPDATE #__users_office
					  SET `office_title` = ".$db->Quote($single['office']).",
					  `address`=".$db->Quote($single['address']).",
					  `city`=".$db->Quote($single['city']).",
					  `state`=".$db->Quote($single['state']).",
					  `country`=".$db->Quote($single['country']).",
					  `zip`=".$db->Quote($single['zip'])."
					  WHERE id=".$db->Quote($single['id']);
			$db->setQuery($query);
			if(!$db->query()){
				return false;
			}
		}

		//check for password
		$newpassword = $data['personal']['password'];
		if($newpassword != ""){
			//update password
			//get random password
			$pass = $newpassword;
			$salt		= JUserHelper::genRandomPassword(32);
			$crypted	= JUserHelper::getCryptedPassword($pass, $salt);
			$password	= $crypted.':'.$salt;
			$query = "UPDATE #__users
					  SET `password` = ".$db->Quote($password)."
					  WHERE id = ".$db->Quote($userId);
			$db->setQuery($query);
			if(!$db->query()){
				return false;
			}
		}

	}
	/**
	 * Method to add office
	 */
	public function addoffice($data){
		//get db object
		$db = JFactory::getDBO();
		$query = "insert into #__users_office
		        (`lid`,`office_title`,`gid`,`address`,`city`,`state`,`country`,`zip`) 
		        values(".$db->quote($data['lid']).","
		        .$db->quote($data['ofc_detail']['office']).","
		        .$db->quote($data['gid']).","
		        .$db->quote($data['ofc_detail']['address']).","
		        .$db->quote($data['ofc_detail']['city']).","
		        .$db->quote($data['ofc_detail']['state']).","
		        .$db->quote($data['ofc_detail']['country']).","
		        .$db->quote($data['ofc_detail']['zip']).")";
		        $db->setQuery($query);
		        if(!$db->query()){
		        	return false;
		        }
	}

	/**
	 * Method to get usa states
	 */

	public function getUsaStates(){
		$db = $this->getDBO();
		$query = "SELECT * FROM #__legalconfirm_usastates";
		$db->setQuery($query);
		$db->query();
		$result = $db->loadObjectlist();
		return $result;
	}
	

	/**
	 * Method to check email 
	 * @param email
	 */
	public function checkAdminEmail($data){
		//get db object
		$db = JFactory::getDBO();
		$email_name = $data['email'];
		$name = $data['name'];
		$email_domain = $data['email_domain'];
		//complete email
		$email = $email_name.$email_domain;
		//check for valid email
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			//check for if email already exist
		$query = "select count(id) as emailcount FROM #__users where email = ".$db->Quote($email);
		$db->setQuery($query);
		$db->query();
		$result = $db->loadResult();
		if($result>0){
			return "email_exist";
		}else{
			
			//get current login user info
			$user = JFactory::getUser();
			$user_email = $user->email;
			$user_name = $user->name;
			
			$message = "<b>Hello Admin</b>,<br /><br />We want to change the admin of our Firm.<br /><br />
			            <b>Our Current Firm Details are</b> : <br />
			            FirmAdmin Email: ".$user_email."<br />FirmAdmin Name: ".$user_name."<br /><br />
			            <b>New Admin details are</b> :<br />
			            New FirmAdmin Email: ".$email."<br />New FrimAdmin Name: ".$name;
			
			
			//get superadmin email
			$query = "select a.email FROM #__users as a where a.id = 124";
			$db->setQuery($query);
			$db->query();
			
			$to_email_object = $db->loadObject();
			$to_email = $to_email_object->email;
			//send the mail to site admin
			   $mail = &JFactory::getMailer();
               $app                = JFactory::getApplication();
               $mailfrom        = $user_email;
               $fromname        = $user_name;
               $mail->setSubject(JText::_('COM_LEGALCONFIRM_CHANGE_ADMIN'));
               $text = $message;
               $mail->IsHTML= true;
               $mail->ContentType = 'text/html';
               $joomla_config = new JConfig();
               $mail->addRecipient($to_email);
               $mail->setSender($mailfrom, $fromname);
               $mail->setBody($text);
               if($mail->Send()){
               	return "sent";
               }else{
               	return "not_sent";
               }
               }
		}
		else{
			return "invalid_email";
		}
		
		//check valid email
	}


}
