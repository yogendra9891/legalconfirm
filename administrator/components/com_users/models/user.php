<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
jimport('joomla.access.access');

/**
 * User model.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       1.6
 */
class UsersModelUser extends JModelAdmin
{
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A database object
	 *
	 * @since   1.6
	*/
	public function getTable($type = 'User', $prefix = 'JTable', $config = array())
	{
		$table = JTable::getInstance($type, $prefix, $config);

		return $table;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed	Object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		$result = parent::getItem($pk);

		// Get the dispatcher and load the users plugins.
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('user');

		// Trigger the data preparation event.
		$results = $dispatcher->trigger('onContentPrepareData', array('com_users.user', $result));

		return $result;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_users.user', 'user', array('control' => 'jform', 'load_data' => $loadData));
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
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_users.edit.user.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		// TODO: Maybe this can go into the parent model somehow?
		// Get the dispatcher and load the users plugins.
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('user');

		// Trigger the data preparation event.
		$results = $dispatcher->trigger('onContentPrepareData', array('com_users.profile', $data));

		// Check for errors encountered while preparing the data.
		if (count($results) && in_array(false, $results, true))
		{
			$this->setError($dispatcher->getError());
		}

		return $data;
	}

	/**
	 * Override JModelAdmin::preprocessForm to ensure the correct plugin group is loaded.
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
		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function save($data)
	{
		
		//code written by abhishek
		//get edited userid
		$edit_uid = $data['id'];

		//get edited user email
		$edit_user = JFactory::getUser($edit_uid);
		$old_email = $edit_user->email;
              
                //only for auditor and lawfirm 
                //get allow group id
                $config =& JFactory::getConfig();
		$lawfirm = $config->getValue('lawfirm');
                $auditor = $config->getValue('auditor');
                $allowgroup = array($lawfirm,$auditor);
                $user_group = $this->getUserGroup($edit_uid);

                if(in_array($user_group,$allowgroup,true)){
                //check if email is changed
                if($old_email != $data['email']){
                //check if password is changed
                if($data['password'] != ""){
		$send_password = $data['password'];
                }else{
                //update the password with random number
                $pass = $this->rand_string(8);
                
		//$crypted	= JUserHelper::getCryptedPassword($pass, $salt);
		$data['password']	= $pass;
                $data['password2']	= $pass;
                $send_password = $pass;

		}
                }
                }
		//make the username same as email
		$data['username'] = $data['email'];
		// Initialise variables;
		$pk			= (!empty($data['id'])) ? $data['id'] : (int) $this->getState('user.id');
		$user		= JUser::getInstance($pk);

		$my = JFactory::getUser();

		if ($data['block'] && $pk == $my->id && !$my->block)
		{
			$this->setError(JText::_('COM_USERS_USERS_ERROR_CANNOT_BLOCK_SELF'));
			return false;
		}

		// Make sure that we are not removing ourself from Super Admin group
		$iAmSuperAdmin = $my->authorise('core.admin');
		if ($iAmSuperAdmin && $my->get('id') == $pk)
		{
			// Check that at least one of our new groups is Super Admin
			$stillSuperAdmin = false;
			$myNewGroups = $data['groups'];
			foreach ($myNewGroups as $group)
			{
				$stillSuperAdmin = ($stillSuperAdmin) ? ($stillSuperAdmin) : JAccess::checkGroup($group, 'core.admin');
			}
			if (!$stillSuperAdmin)
			{
				$this->setError(JText::_('COM_USERS_USERS_ERROR_CANNOT_DEMOTE_SELF'));
				return false;
			}
		}

		// Bind the data.
		if (!$user->bind($data))
		{
			$this->setError($user->getError());
			return false;
		}

		// Store the data.
		if (!$user->save())
		{
			$this->setError($user->getError());
			return false;
		}
        if(in_array($user_group,$allowgroup,true)){

        if($old_email != $data['email'] ){
		$config	= JFactory::getConfig();
        	$mailfrom = $config->get('mailfrom');
        	$fromname = $config->get('fromname');
                $password = $send_password;
        	$text = "Hello ".$data['name']."<br /><br />Login credential has changed for the firm.<br /><br />
        	         New login detail is: <br />UserName: ".$data['email']."<br />Email: ".$data['email']."<br />Password: ".$password;
        	
        	$to_email = $data['email'];
          //send the mail to site admin
		       $mail = &JFactory::getMailer();
               $app             = JFactory::getApplication();
              
               $mail->setSubject(JText::_('COM_LAWFIRM_CHANGE_ADMIN'));
             
               $mail->IsHTML= true;
               $mail->ContentType = 'text/html';
               $joomla_config = new JConfig();
               $mail->addRecipient($to_email);
               $mail->setSender($mailfrom, $fromname);
               $mail->setBody($text);
               $mail->Send();
        
               //logout the user
               $this->logoutUser($edit_uid);
        }
        }
		$this->setState('user.id', $user->id);

		return true;
	}

	/**
	 * Method to delete rows.
	 *
	 * @param   array  &$pks  An array of item ids.
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function delete(&$pks)
	{
		// Initialise variables.
		$user	= JFactory::getUser();
		$table	= $this->getTable();
		$pks	= (array) $pks;

		// Check if I am a Super Admin
		$iAmSuperAdmin	= $user->authorise('core.admin');

		// Trigger the onUserBeforeSave event.
		JPluginHelper::importPlugin('user');
		$dispatcher = JDispatcher::getInstance();

		if (in_array($user->id, $pks))
		{
			$this->setError(JText::_('COM_USERS_USERS_ERROR_CANNOT_DELETE_SELF'));
			return false;
		}

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk))
			{
				// Access checks.
				$allow = $user->authorise('core.delete', 'com_users');
				// Don't allow non-super-admin to delete a super admin
				$allow = (!$iAmSuperAdmin && JAccess::check($pk, 'core.admin')) ? false : $allow;

				if ($allow)
				{
					// Get users data for the users to delete.
					$user_to_delete = JFactory::getUser($pk);

					// Fire the onUserBeforeDelete event.
					$dispatcher->trigger('onUserBeforeDelete', array($table->getProperties()));

					if (!$table->delete($pk))
					{
						$this->setError($table->getError());
						return false;
					}
					else
					{
						// Trigger the onUserAfterDelete event.
						$dispatcher->trigger('onUserAfterDelete', array($user_to_delete->getProperties(), true, $this->getError()));
					}
				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					JError::raiseWarning(403, JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'));
				}
			}
			else
			{
				$this->setError($table->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to block user records.
	 *
	 * @param   array    &$pks   The ids of the items to publish.
	 * @param   integer  $value  The value of the published state
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	function block(&$pks, $value = 1)
	{
		// Initialise variables.
		$app		= JFactory::getApplication();
		$dispatcher	= JDispatcher::getInstance();
		$user		= JFactory::getUser();
		// Check if I am a Super Admin
		$iAmSuperAdmin	= $user->authorise('core.admin');
		$table		= $this->getTable();
		$pks		= (array) $pks;

		JPluginHelper::importPlugin('user');

		// Access checks.
		foreach ($pks as $i => $pk)
		{
			if ($value == 1 && $pk == $user->get('id'))
			{
				// Cannot block yourself.
				unset($pks[$i]);
				JError::raiseWarning(403, JText::_('COM_USERS_USERS_ERROR_CANNOT_BLOCK_SELF'));

			}
			elseif ($table->load($pk))
			{
				$old	= $table->getProperties();
				$allow	= $user->authorise('core.edit.state', 'com_users');
				// Don't allow non-super-admin to delete a super admin
				$allow = (!$iAmSuperAdmin && JAccess::check($pk, 'core.admin')) ? false : $allow;

				// Prepare the logout options.
				$options = array(
					'clientid' => array(0, 1)
				);

				if ($allow)
				{
					// Skip changing of same state
					if ($table->block == $value)
					{
						unset($pks[$i]);
						continue;
					}

					$table->block = (int) $value;
				// If unblocking, also change password reset count to zero to unblock reset
					if ($table->block === 0)
					{
						$table->resetCount = 0;
					}
					// Allow an exception to be thrown.
					try
					{
						if (!$table->check())
						{
							$this->setError($table->getError());
							return false;
						}

						// Trigger the onUserBeforeSave event.
						$result = $dispatcher->trigger('onUserBeforeSave', array($old, false, $table->getProperties()));
						if (in_array(false, $result, true))
						{
							// Plugin will have to raise it's own error or throw an exception.
							return false;
						}

						// Store the table.
						if (!$table->store())
						{
							$this->setError($table->getError());
							return false;
						}

						// Trigger the onAftereStoreUser event
						$dispatcher->trigger('onUserAfterSave', array($table->getProperties(), false, true, null));
					}
					catch (Exception $e)
					{
						$this->setError($e->getMessage());

						return false;
					}

					// Log the user out.
					if ($value)
					{
						$app->logout($table->id, $options);
					}
				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
				}
			}
		}

		return true;
	}

	/**
	 * Method to activate user records.
	 *
	 * @param   array  &$pks  The ids of the items to activate.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	function activate(&$pks)
	{
		
		// Initialise variables.
		$dispatcher	= JDispatcher::getInstance();
		$user		= JFactory::getUser();
		// Check if I am a Super Admin
		$iAmSuperAdmin	= $user->authorise('core.admin');
		$table		= $this->getTable();
		$pks		= (array) $pks;

		JPluginHelper::importPlugin('user');

		// Access checks.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk))
			{
				$old	= $table->getProperties();
				$allow	= $user->authorise('core.edit.state', 'com_users');
				// Don't allow non-super-admin to delete a super admin
				$allow = (!$iAmSuperAdmin && JAccess::check($pk, 'core.admin')) ? false : $allow;

				if (empty($table->activation))
				{
					// Ignore activated accounts.
					unset($pks[$i]);
				}
				elseif ($allow)
				{
					$table->block		= 0;
					$table->activation	= '';

					// Allow an exception to be thrown.
					try
					{
						if (!$table->check())
						{
							$this->setError($table->getError());
							return false;
						}

						// Trigger the onUserBeforeSave event.
						$result = $dispatcher->trigger('onUserBeforeSave', array($old, false, $table->getProperties()));
						if (in_array(false, $result, true))
						{
							// Plugin will have to raise it's own error or throw an exception.
							return false;
						}

						// Store the table.
						if (!$table->store())
						{
							$this->setError($table->getError());
							return false;
						}
						//get user group
						$user_group = $this->getUserGroup($pk);
						$config =& JFactory::getConfig();
						$lawfirm = $config->getValue('lawfirm');
                        if($user_group == $lawfirm){
                        	$return = $this->sendMailtoLawFirmAdmin($pk);
                        }
						// Fire the onAftereStoreUser event
						$dispatcher->trigger('onUserAfterSave', array($table->getProperties(), false, true, null));
					}
					catch (Exception $e)
					{
						$this->setError($e->getMessage());

						return false;
					}
				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
				}
			}
		}

		return true;
	}

	/**
	 * Method to perform batch operations on an item or a set of items.
	 *
	 * @param   array  $commands  An array of commands to perform.
	 * @param   array  $pks       An array of item ids.
	 * @param   array  $contexts  An array of item contexts.
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 *
	 * @since   2.5
	 */
	public function batch($commands, $pks, $contexts)
	{
		// Sanitize user ids.
		$pks = array_unique($pks);
		JArrayHelper::toInteger($pks);

		// Remove any values of zero.
		if (array_search(0, $pks, true))
		{
			unset($pks[array_search(0, $pks, true)]);
		}

		if (empty($pks))
		{
			$this->setError(JText::_('COM_USERS_USERS_NO_ITEM_SELECTED'));
			return false;
		}

		$done = false;

		if (!empty($commands['group_id']))
		{
			$cmd = JArrayHelper::getValue($commands, 'group_action', 'add');

			if (!$this->batchUser((int) $commands['group_id'], $pks, $cmd))
			{
				return false;
			}
			$done = true;
		}

		if (!$done)
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));
			return false;
		}

		// Clear the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Perform batch operations
	 *
	 * @param   integer  $group_id  The group ID which assignments are being edited
	 * @param   array    $user_ids  An array of user IDs on which to operate
	 * @param   string   $action    The action to perform
	 *
	 * @return  boolean  True on success, false on failure
	 *
	 * @since	1.6
	 */
	public function batchUser($group_id, $user_ids, $action)
	{
		// Get the DB object
		$db = $this->getDbo();

		JArrayHelper::toInteger($user_ids);

		// Non-super admin cannot work with super-admin group
		if ((!JFactory::getUser()->get('isRoot') && JAccess::checkGroup($group_id, 'core.admin')) || $group_id < 1)
		{
			$this->setError(JText::_('COM_USERS_ERROR_INVALID_GROUP'));
			return false;
		}

		switch ($action)
		{
			// Sets users to a selected group
			case 'set':
				$doDelete	= 'all';
				$doAssign	= true;
				break;

			// Remove users from a selected group
			case 'del':
				$doDelete	= 'group';
				break;

			// Add users to a selected group
			case 'add':
			default:
				$doAssign	= true;
				break;
		}

		// Remove the users from the group if requested.
		if (isset($doDelete))
		{
			$query = $db->getQuery(true);

			// Remove users from the group
			$query->delete($db->quoteName('#__user_usergroup_map'));
			$query->where($db->quoteName('user_id') . ' IN (' . implode(',', $user_ids) . ')');

			// Only remove users from selected group
			if ($doDelete == 'group')
			{
				$query->where($db->quoteName('group_id') . ' = ' . (int) $group_id);
			}

			$db->setQuery($query);

			// Check for database errors.
			if (!$db->query())
			{
				$this->setError($db->getErrorMsg());
				return false;
			}
		}

		// Assign the users to the group if requested.
		if (isset($doAssign))
		{
			$query = $db->getQuery(true);

			// First, we need to check if the user is already assigned to a group
			$query->select($db->quoteName('user_id'));
			$query->from($db->quoteName('#__user_usergroup_map'));
			$query->where($db->quoteName('group_id') . ' = ' . (int) $group_id);
			$db->setQuery($query);
			$users = $db->loadColumn();

			// Build the values clause for the assignment query.
			$query->clear();
			$groups = false;
			foreach ($user_ids as $id)
			{
				if (!in_array($id, $users))
				{
					$query->values($id . ',' . $group_id);
					$groups = true;
				}
			}

			// If we have no users to process, throw an error to notify the user
			if (!$groups)
			{
				$this->setError(JText::_('COM_USERS_ERROR_NO_ADDITIONS'));
				return false;
			}

			$query->insert($db->quoteName('#__user_usergroup_map'));
			$query->columns(array($db->quoteName('user_id'), $db->quoteName('group_id')));
			$db->setQuery($query);

			// Check for database errors.
			if (!$db->query())
			{
				$this->setError($db->getErrorMsg());
				return false;
			}
		}

		return true;
	}

	/**
	 * Gets the available groups.
	 *
	 * @return  array  An array of groups
	 *
	 * @since   1.6
	 */
	public function getGroups()
	{
		$user = JFactory::getUser();
		if ($user->authorise('core.edit', 'com_users') && $user->authorise('core.manage', 'com_users'))
		{
			$model = JModelLegacy::getInstance('Groups', 'UsersModel', array('ignore_request' => true));
			return $model->getItems();
		}
		else
		{
			return null;
		}
	}

	/**
	 * Gets the groups this object is assigned to
	 *
	 * @param   integer  $userId  The user ID to retrieve the groups for
	 *
	 * @return  array  An array of assigned groups
	 *
	 * @since   1.6
	 */
	public function getAssignedGroups($userId = null)
	{
		// Initialise variables.
		$userId = (!empty($userId)) ? $userId : (int)$this->getState('user.id');

		if (empty($userId))
		{
			$result = array();
			$config = JComponentHelper::getParams('com_users');
			if ($groupId = $config->get('new_usertype'))
			{
				$result[] = $groupId;
			}
		}
		else
		{
			$result = JUserHelper::getUserGroups($userId);
		}

		return $result;
	}
	
	/**
	 * Get Group id
	 * @Author:Abhishek
	 * @param user id
	 * @return group id.
	 */
	public function getUserGroup($id){
		//get db object
		 $db = JFactory::getDbo();
		 $query = "SELECT group_id FROM #__user_usergroup_map as a where a.user_id =".$db->Quote($id);
		 $db->setQuery($query);
		 $db->query();
		 $result = $db->loadResult();
		 return $result;
	}
	
	/**
	 * Send mail to firm admin
	 * @param user id
	 * @return boolean
	 */
	public function sendMailtoLawFirmAdmin($id){
	// Get the user object.
		$user = JUser::getInstance($id);
		
		// Generate the new password hash.
		$salt		= JUserHelper::genRandomPassword(32);
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
	 * Method to get random password
	 */
	public function rand_string( $length ) {
	
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	return substr(str_shuffle($chars),0,$length);
	
	}


	/**
	 * Method to logout the user
	 * 
	 */
	public function logoutUser($uid){
		$db = JFactory::getDBO();
		$query = "DELETE FROM #__session WHERE userid = ".$uid;
		$db->setQuery($query);
		$db->query();
	}
}
