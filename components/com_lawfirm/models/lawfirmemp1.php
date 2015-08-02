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
require_once JPATH_COMPONENT.'/tables/clientproposals.php';
require_once JPATH_COMPONENT.'/tables/employeeassign.php';
/**
 * Methods supporting a list of Legalconfirm clients.
 */
class LawfirmModelLawfirmemp extends JModelList {

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
	protected function populateState($ordering = null, $direction = null) {

		// Initialise variables.
		$app = JFactory::getApplication();

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit', $limit);

		$limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
		$this->setState('list.start', $limitstart);


		if(empty($ordering)) {
			$ordering = 'a.ordering';
		}

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
		$lawfirm_emp = $config->getValue('lawfirm_emp');
		// Create a new query object.
		$db = $this->getDbo();
		
		//get logged in userid
		$user = JFactory::getUser();
	    $userId = $user->id;
	    //get lawfirm admin
	    $query = "SELECT a.parent FROM #__users_profile_detail as a WHERE a.lid = ".$db->Quote($userId);
	    $db->setQuery($query);
	    $db->query();
	    $lawfirm_admin = $db->loadResult();
	    //list task list to the employee which are not assigned to other.
	    $query = "SELECT a.* 
	    		 FROM #__lawfirm_assignproposal as a 
	    		 WHERE a.lawfirmid=".$db->Quote($lawfirm_admin)." 
	    		 AND a.requestsent = '1' 
	    		 AND a.taskstatus = '0' AND a.is_pinbyemp = '0' AND a.is_readybyemp = '0' AND a.assign_by_partner = '0' OR (a.is_pinbyemp = '1' AND a.emp_id = ".$db->Quote($userId).")";
	    $db->setQuery($query);
        $db->query();
        
		return $query;

	}
	public function getTable($type = 'User', $prefix = 'JTable', $config = array())
	{
		$table = JTable::getInstance($type, $prefix, $config);

		return $table;
	}
	
	/*
	 * Method to get proposal detail
	 * @param propsal id
	 */
	public function getProposalInfo($pid){
		$db = $this->getDBO();
		$propposal_detailtable = &JTable::getInstance('clientproposals', 'LawfirmTable');
		$propposal_detailtable->load($pid);
		$signer_id = $propposal_detailtable->cid;
		$auditor_id = $propposal_detailtable->lid;
		
		//get client or signer name 
		$query = "SELECT a.company FROM #__auditorclients as a WHERE id = ".$db->Quote($signer_id);
		$db->setQuery($query);
		$db->query();
		$company_name = $db->loadResult();
		
		//get Auditor info
		$query2 = "SELECT a.accounting_firm as firmname ,b.name as name FROM #__users_profile_detail as a , #__users as b WHERE a.lid = b.id AND b.id = ".$db->Quote($auditor_id);
		
		$db->setQuery($query2);
		$db->query();
		$result = $db->loadObject();
		$firm_name = $result->firmname;
		$owner_name = $result->name;
		
		//get office name
		$query3 = "SELECT a.office_title FROM #__users_office as a WHERE a.lid = ".$db->Quote($auditor_id);
		$db->setQuery($query3);
		$db->query();
		$office_name = $db->loadResult();
		
		//make array of send data
		$infodata = array('company_name'=>$company_name,'firm_name'=>$firm_name,'owner_name'=>$owner_name,'office'=>$office_name);
		return $infodata;
	}
	
	/**
	 * @param pid is proposal id
	 * @param id is lawfirm_assignproposal id
	 */
	function pintask($id,$pid){
		$user = JFactory::getUser();
		$userId = $user->id;
		$db = $this->getDBO();
		//check for if other employee of same firm has already pin the task
		$checkpin = $this->checkaddedpin($id,$pid);
        
		if($checkpin != 0){
			return "already_pin";
			exit;
		}
		$query = "UPDATE #__lawfirm_assignproposal SET is_pinbyemp = 1,emp_id =".$db->Quote($userId)." WHERE id = ".$db->Quote($id);
		$db->setQuery($query);
		if(!$db->query()){
			return "false";
		}
		$emp_assign = &JTable::getInstance('employeeassign', 'LawfirmTable');
		$data['pid'] = $pid;
		$data['aid'] = $id;
		$data['assign_from'] = $userId;
		$data['assign_to'] = $userId;
		$data['is_pin'] = "1";
		$data['is_ready'] = "0";
		$data['partnerid'] = "0";
		//Get the configuration
		$config =& JFactory::getConfig();
		$tzoffset = $config->getValue('config.offset');	
	    $assign_date =& JFactory::getDate('',$tzoffset);
	   // $assign_date2 = $assign_date1->toFormat();
	      
		$data['mailtemplate'] = "";
		$data['assign_date'] = $assign_date->toMySQL(true);
        $emp_assign->save($data);
		return "true";
	}
	
	/**
	 * Method to get pinned task
	 */
	public function getPinnedtask(){
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$userId = $user->id;
		$query = "SELECT count(*) as assigncount FROM #__lawfirm_employee_assign WHERE assign_to = ".$db->Quote($userId)." AND is_pin = '1' ";
		$db->setQuery($query);
		$db->query();
		$result = $db->loadResult();
		return $result;
	}
	
    /**
	 * Method to get the tasks pinned by user
	 * when the user get second time login
	 */
    public function getAssignedPinnedTask(){
    
    	$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$userId = $user->id;
		$query = "SELECT a.*,b.assigndate FROM #__lawfirm_employee_assign as a , #__lawfirm_assignproposal as b WHERE b.id = a.aid AND assign_to = ".$db->Quote($userId)." AND is_pin = '1' ";
		$db->setQuery($query);
		$db->query();
		$result = $db->loadObjectlist();
		echo "<pre>";
		print_r($result);
		die;
		return $result;
    }
    
    /**
     * Method to get unassigned task
     */
    public function getUnassignedTask(){
    	
    	$db = JFactory::getDBO();
		//get logged in userid
		$user = JFactory::getUser();
	    $userId = $user->id;
	    //get lawfirm admin
	    $query = "SELECT a.parent FROM #__users_profile_detail as a WHERE a.lid = ".$db->Quote($userId);
	    $db->setQuery($query);
	    $db->query();
	    $lawfirm_admin = $db->loadResult();
	    $query = "SELECT a.* 
	    		 FROM #__lawfirm_assignproposal as a 
	    		 WHERE a.lawfirmid=".$db->Quote($lawfirm_admin)." 
	    		 AND a.requestsent = '1' 
	    		 AND a.taskstatus = '0' AND a.is_pinbyemp = '0' AND a.is_readybyemp = '0' AND a.assign_by_partner = '0'";
		$db->setQuery($query);
		$db->query();
		$result = $db->loadObjectlist();
		return $result;
    }
    
    /**
     * Change the propoasl of lawfirm employee
     */
    public function editfrequenttask(){
    	$db = $this->getDBO();
    	//get current login user
    	$user = JFactory::getUser();
    	$userId = $user->id;
    	$query = "SELECT a.* FROM #__lawfirm_assignproposal as a WHERE a.emp_id = ".$db->Quote($userId);
    	$db->setQuery($query);
    	$db->query();
    	$result = $db->loadObjectlist();
    	return $result;
    	//get the pinned 
    }
    
     /**
	 * Method to unpin task
	 * @param id lawfirm_employee_assign id
	 * @param pid is proposal id
	 * @param aid is lawfirm_assignproposal id
	 */
	function unpintask($id,$pid,$aid=null){
		
		$user = JFactory::getUser();
		$userId = $user->id;
		$db = $this->getDBO();
		$query = "UPDATE #__lawfirm_assignproposal SET is_pinbyemp = '0' ,emp_id = '0' WHERE id = ".$db->Quote($aid);
		$db->setQuery($query);
		if(!$db->query()){
			return false;
		}
		
		//Get the configuration
		$config =& JFactory::getConfig();
		$tzoffset = $config->getValue('config.offset');	
	    $assign_date =& JFactory::getDate('',$tzoffset);
	   // $assign_date2 = $assign_date1->toFormat();
	      
		$data['mailtemplate'] = "";
		$data['deassign_date'] = $assign_date->toMySQL(true);
        //$emp_assign->save($data);
		$query = "UPDATE #__lawfirm_employee_assign SET is_pin = 0,deassign_date = ".$db->Quote($data['deassign_date'])." WHERE pid = ".$db->Quote($pid)." AND assign_to = ".$db->Quote($userId);
		$db->setQuery($query);
		if(!$db->query()){
			return false;
		}
		return true;
	}
	
	/**
	 * Method to unpin the task from default layout
	 */
	public function unpintaskdefault($id,$pid){
		$user = JFactory::getUser();
		$userId = $user->id;
		$db = $this->getDBO();
		$query = "UPDATE #__lawfirm_assignproposal SET is_pinbyemp = '0',emp_id = '0' WHERE id = ".$db->Quote($id);
		$db->setQuery($query);
		if(!$db->query()){
			return false;
		}
		
		//Get the configuration
		$config =& JFactory::getConfig();
		$tzoffset = $config->getValue('config.offset');	
	    $assign_date =& JFactory::getDate('',$tzoffset);
	   // $assign_date2 = $assign_date1->toFormat();
	      
		$data['mailtemplate'] = "";
		$data['assign_date'] = $assign_date->toMySQL(true);
        //$emp_assign->save($data);
		$query = "UPDATE #__lawfirm_employee_assign SET is_pin = 0 WHERE pid = ".$db->Quote($pid)." AND assign_to = ".$db->Quote($userId);
		$db->setQuery($query);
		if(!$db->query()){
			return false;
		}
		return true;
	}
	
   /**
    * Method to pin the task from not frequent list
    */
	public function pinnotfrqtask(){
		
	}
	/**
	 * Method to check the added pin
	 */
	public function checkaddedpin($id,$pid){
		$db = $this->getDBO();
		$query = "SELECT is_pinbyemp FROM #__lawfirm_assignproposal WHERE id = ".$db->Quote($id);
		$db->setQuery($query);
		$db->query();
		$result = $db->loadResult();
		return $result;
	}
	
	/**
	 * Method to get proposal email 
	 * @param proposal id
	 */
	public function getProposalEmail($pid){
		//get db object
		$db = $this->getDBO();
		$query = "SELECT a.template FROM #__clientproposals as a WHERE a.id = ".$db->Quote($pid);
		$db->setQuery($query);
		$db->query();
		$result = $db->loadResult();
		return $result;
	}
	/**
	 * Method to unpin non frequent task
	 */
	public function unpinnonfrqtask($id,$pid){
		$user = JFactory::getUser();
		$userId = $user->id;
		$db = $this->getDBO();
		$query = "UPDATE #__lawfirm_assignproposal SET is_pinbyemp = '0',emp_id = '0' WHERE id = ".$db->Quote($id);
		$db->setQuery($query);
		if(!$db->query()){
			return false;
		}
		
		//Get the configuration
		$config =& JFactory::getConfig();
		$tzoffset = $config->getValue('config.offset');	
	    $assign_date =& JFactory::getDate('',$tzoffset);
	   // $assign_date2 = $assign_date1->toFormat();
	      
		$data['mailtemplate'] = "";
		$data['assign_date'] = $assign_date->toMySQL(true);
        //$emp_assign->save($data);
		$query = "UPDATE #__lawfirm_employee_assign SET is_pin = 0 WHERE pid = ".$db->Quote($pid)." AND assign_to = ".$db->Quote($userId);
		$db->setQuery($query);
		if(!$db->query()){
			return false;
		}
		return true;
	}
	
	/**
	 * Method to get task assigned by partner to employee
	 */
	public function getTaskAssignedByPartner(){
		//get current user login id
		$user = JFactory::getUser();
		$userId = $user->id;
		
		//get db object
		$db = $this->getDBO();
		$query = "SELECT a.* FROM #__lawfirm_assignproposal as a where a.emp_id = ".$db->Quote($userId)." AND a.assign_by_partner != '0'";
		
		$db->setQuery($query);
		$db->query();
		$result = $db->loadObjectlist();
		return $result;
	}
}
