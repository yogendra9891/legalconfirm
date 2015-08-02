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
/**
 * Rest model class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_legalconfirmusers
 * @since		1.6
 */
class LegalconfirmusersModelLogin extends JModelForm
{
	/**
	 * Method to get the login form.
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
		$form = $this->loadForm('com_users.login', 'login', array('load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	array	The default data is an empty array.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered login form data.
		$app	= JFactory::getApplication();
		$data	= $app->getUserState('users.login.form.data', array());

		// check for return URL from the request first
		if ($return = JRequest::getVar('return', '', 'method', 'base64')) {
			$data['return'] = base64_decode($return);
			if (!JURI::isInternal($data['return'])) {
				$data['return'] = '';
			}
		}
      $data['return'] = 'index.php?option=com_legalconfirmusers&view=profile';
		// Set the return URL if empty.
		if (!isset($data['return']) || empty($data['return'])) {
			$data['return'] = 'index.php?option=com_legalconfirmusers&view=profile';
		}
		$app->setUserState('users.login.form.data', $data);

		return $data;
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
		$params	= JFactory::getApplication()->getParams('com_users');

		// Load the parameters.
		$this->setState('params', $params);
	}

	/**
	 * Method to allow derived classes to preprocess the form.
	 *
	 * @param	object	A form object.
	 * @param	mixed	The data expected for the form.
	 * @param	string	The name of the plugin group to import (defaults to "content").
	 * @throws	Exception if there is an error in the form event.
	 * @since	1.6
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'user')
	{
		// Import the approriate plugin group.
		JPluginHelper::importPlugin($group);

		// Get the dispatcher.
		$dispatcher	= JDispatcher::getInstance();

		// Trigger the form preparation event.
		$results = $dispatcher->trigger('onContentPrepareForm', array($form, $data));

		// Check for errors encountered while preparing the form.
		if (count($results) && in_array(false, $results, true)) {
			// Get the last error.
			$error = $dispatcher->getError();

			// Convert to a JException if necessary.
			if (!($error instanceof Exception)) {
				throw new Exception($error);
			}
		}
	}
	//code start edited by yogendra....
   /*
    * checking the user is using the correct login form for Auditor related group..
    */
	public function checkauditorUsergroup($postdata){
	 $db = JFactory::getDbo();
	 $query = $db->getQuery(true);
	 $query->select('a.id, b.group_id');
	 $query->from('#__users as a');
	 $query->join('INNER', '#__user_usergroup_map as b ON a.id = b.user_id');
	 $query->where('a.username = "'.$postdata['username'].'"');
     $db->setQuery($query);
     $db->query(); 
     $resultid = $db->loadObject();
     return $resultid;
	}
   /*
    * checking the user is using the correct login form for Laywer related group..
    */
	public function checklawyerUsergroup($postdata){
	 $db = JFactory::getDbo();
	 $query = $db->getQuery(true);
	 $query->select('a.id, b.group_id');
	 $query->from('#__users as a');
	 $query->join('INNER', '#__user_usergroup_map as b ON a.id = b.user_id');
	 $query->where('a.username = "'.$postdata['username'].'"');
     $db->setQuery($query);
     $db->query();
     $resultid = $db->loadObject();
     return $resultid;
	}
	//code end edit by yogendra....
	
	/**
	 * @Author Abhishek
	 * Method to add login count of each user
	 */
	public function addlogincount(){
		 $db = JFactory::getDbo();
		 //get login user id
		 $user = JFactory::getUser();
		 $userId = $user->id;
		 //check for user login has added
		 $query = "select count(a.id) as logincount from #__lawfirm_login_count as a WHERE a.lid = ".$db->Quote($userId);
		 $db->setQuery($query);
		 $db->query();
		 $result = $db->loadResult();
		 if($result == 0){
		 	$query = "INSERT INTO #__lawfirm_login_count (lid,login_count) values(".$db->Quote($userId).",'1')";
		 }else{
		 	$query = "UPDATE #__lawfirm_login_count SET login_count = '2' WHERE lid = ".$db->Quote($userId);
		 }
		 $db->setQuery($query);
		 $db->query();
	}
}
