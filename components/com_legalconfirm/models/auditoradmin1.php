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

/**
 * Methods supporting a list of Legalconfirm clients.
 */
class LegalconfirmModelAuditoradmin extends JModelList {

	/**
	 * Constructor.
	 *
	 * @param    array    An optional associative array of configuration settings.
	 * @see        JController
	 * @since    1.6
	 */
	public function __construct($config = array()) {
	if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
                'name', 'a.name','email','a.email','gid','d.gid','block','a.block'
			);
		}
		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null) {

		// Initialise variables.
		$app = JFactory::getApplication();

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit', $limit);

		$limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
		$this->setState('list.start', $limitstart);

        $ordering =  $this->setState('list.ordering', JRequest::getVar('filter_order'));
		$direction =  $this->setState('list.direction', JRequest::getVar('filter_order_Dir'));
		
//		if(empty($ordering)) {
//			$ordering = 'a.ordering';
//		}

		// List state information.
		parent::populateState($ordering, $direction);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery() {
		//get auditor employee group value
		$config	= JFactory::getConfig();
		$auditor_emp = $config->getValue('auditor_emp');
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		//get logged in userid
		$user = JFactory::getUser();
		$email = $user->email;
		//get firm name
		$firm_array = explode('@',$email);
		$firm_name = $firm_array['1'];

		// Select the required fields from the table.
		$query = "Select a.*,c.lid,c.accounting_firm,c.emp_title,c.phone,d.office_title,d.gid FROM #__users as a ,#__user_usergroup_map as b,#__users_profile_detail as c,#__users_office as d"
		." WHERE a.id = b.user_id AND b.group_id = ".$db->escape($auditor_emp,true)." AND c.lid=a.id AND a.id = d.lid AND a.email like '%".$firm_name."%'";
		 
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');

			}
		}

		$ordering = $this->getState('list.ordering');
        if(!empty($ordering)){
		 $query .= ' order by '.$this->getState('list.ordering') .' '.$this->getState('list.direction'); 
		} 
		
		return $query;

	}
	public function getTable($type = 'User', $prefix = 'JTable', $config = array())
	{
		$table = JTable::getInstance($type, $prefix, $config);

		return $table;
	}

	/**
	 *
	 */
	function activate(&$pks)
	{
		// Initialise variables.
		$dispatcher	= JDispatcher::getInstance();
		$user		= JFactory::getUser();
		// Check if I am a Super Admin
		//$iAmSuperAdmin	= $user->authorise('core.admin');
		$table		= $this->getTable();
		$pks		= (array) $pks;
		 
		JPluginHelper::importPlugin('user');

		// Access checks.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk))
			{
				$old	= $table->getProperties();
				//check for block user
				$block = $table->block;
				//check for user activation
				$activation = $table->activation;
				// Don't allow non-super-admin to delete a super admin
				$allow = true;
				if ($allow)
				{
					if($block == 0){
						$table->block = 1;
					}elseif($block ==1){
						$table->block = 0;
					}
					$table->activation	= '';

					// Allow an exception to be thrown.
					try
					{
						if (!$table->check())
						{
							$this->setError($table->getError());
							return false;
						}


						// Store the table.
						if (!$table->store())
						{
							$this->setError($table->getError());
							return false;
						}
						if($activation==""){
							//registered user
							//check for block and active
							if($table->block==1){
								//return "block";
							}elseif($table->block==0){
							//		return "active";
							}
							
						}else{
							//new user 
							//send mail to user when the account get activated
							$this->sendMailToEmp($table->id);
						}

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
    * Method to activate the office
    */
    public function activateOffice(){
 
    $token = JRequest::getVar('token');
    if($token == ""){
     return "empty_token";
     exit;
    }
    //get db object
    $db = JFactory::getDBO();

    //check for token in db
    $query = "select count(id) as ofc_count FROM #__users_office WHERE activation_key = ".$db->Quote($token);

    $db->setQuery($query);
    $db->query();
    $result = $db->loadObject();
    if($result->ofc_count>0){
    //activate the office
     $query1 = "UPDATE #__users_office set status = '1',activation_key='' WHERE activation_key = ".$db->Quote($token);
     $db->setQuery($query1);
     if(!$db->query()){
       return false;
     }
      return "success";
    }else{
     return "invalid_token";
    }

 
    }


}
