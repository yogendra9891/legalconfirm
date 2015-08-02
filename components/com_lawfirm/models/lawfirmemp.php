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

//library for file uploading
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
//

require_once JPATH_COMPONENT.'/tables/clientproposals.php';
require_once JPATH_COMPONENT.'/tables/employeeassign.php';
require_once JPATH_COMPONENT.'/tables/lawfirmtemplate.php';
require_once JPATH_COMPONENT.'/tables/taskbyemployeetopartner.php';
require_once JPATH_COMPONENT.'/tables/lawfirm_assignproposal.php';
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
				if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
                'id', 'a.id'
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
	    		 AND a.taskstatus = '0' 
	    		 AND date_add(`assigndate`,INTERVAL 2 MONTH) >= NOW()";
		$ordering = $this->getState('list.ordering');
		if(!empty($ordering)){
		 $query .= ' order by '.$this->getState('list.ordering') .' '.$this->getState('list.direction'); 
		} 
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
		$client_id = $propposal_detailtable->cid;
		$auditor_id = $propposal_detailtable->lid;

		//get client or signer name
		$query = "SELECT a.company FROM #__auditorclients as a WHERE id = ".$db->Quote($client_id);
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
        $query3 = "SELECT a.office_title FROM #__users_office as a JOIN #__employee_office as b WHERE a.id = b.office_id AND b.empid = ".$db->Quote($auditor_id);
		$db->setQuery($query3);
		$db->query();
		$office_name = $db->loadResult();
		
		//get signer name
		$query = "SELECT concat(a.fname,' ',a.lname) FROM #__clientsigner as a WHERE a.cid = ".$db->Quote($client_id);
		$db->setQuery($query);
		$db->query();
		$result = $db->loadResult();
		
		
		$signername = $result;

		//make array of send data
		$infodata = array('company_name'=>$company_name,'firm_name'=>$firm_name,'owner_name'=>$signername,'office'=>$office_name);
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
		$query = "SELECT count(*) as assigncount FROM #__lawfirm_assignproposal
		WHERE emp_id = ".$db->Quote($userId)." 
		AND is_pinbyemp = '1' 
		AND date_add(`assigndate`,INTERVAL 2 MONTH) >= NOW()";
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
        $this->populateState();
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$userId = $user->id;
		$query = "SELECT a.* FROM #__lawfirm_assignproposal as a WHERE a.emp_id = ".$db->Quote($userId)."
		AND a.is_pinbyemp = '1' 
		AND a.assign_by_partner = '0'
		AND a.taskstatus != '2'";
		$ordering = $this->getState('list.ordering');
		if(!empty($ordering)){
		 $query .= ' order by '.$this->getState('list.ordering') .' '.$this->getState('list.direction'); 
		} 
		else{
			 $query .= ' order by a.is_readybyemp asc'; 
		}
		$db->setQuery($query);
		$db->query();
		$result = $db->loadObjectlist();
		//pagination code start fro here
		$mainframe = JFactory::getApplication();
		// Get pagination request variables from configuration file.....
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$this->setState('limit', JRequest::getVar('limit', $limit, '', 'int'));
		$this->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));
		$this->_totalresults = count($result);
		if ($this->getState('limit') > 0) {
			$result    = array_splice($result , $this->getState('limitstart'), $this->getState('limit'));
		}
		
		return $result;
		//return $result;
	}

	/**
	 * function for custom pagination.....
	 */
	public function getPaginations()
	{
		jimport('joomla.html.pagination');
		$this->_pagination = new JPagination($this->getTotals(), $this->getState('limitstart'), $this->getState('limit') );
		return $this->_pagination;
	}
	
	/**
	 * total result of friends.
	 */
	public function getTotals()
	{
		return $this->_totalresults;
	}



	/**
	 * Method to get unassigned task
	 */
	public function getUnassignedTask(){
	    $this->populateState();
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
	    		 AND a.taskstatus = '0' 
	    		 AND a.is_pinbyemp = '0'
	    		 AND a.is_readybyemp = '0' 
	    		 AND a.assign_by_partner = '0'
	    		 AND date_add(`assigndate`,INTERVAL 2 MONTH) >= NOW()";
		$ordering = $this->getState('list.ordering');
		if(!empty($ordering)){
		 $query .= ' order by '.$this->getState('list.ordering') .' '.$this->getState('list.direction'); 
		} 
		$db->setQuery($query);
		$db->query();
		$result = $db->loadObjectlist();
		//pagination code start fro here
		$mainframe = JFactory::getApplication();
		// Get pagination request variables from configuration file.....
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$this->setState('limit', JRequest::getVar('limit', $limit, '', 'int'));
		$this->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));
		$this->_totalresults = count($result);
		if ($this->getState('limit') > 0) {
			$result    = array_splice($result , $this->getState('limitstart'), $this->getState('limit'));
		}
		
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
	function unpintask($id,$pid){

		$user = JFactory::getUser();
		$userId = $user->id;
		$db = $this->getDBO();
		$query = "UPDATE #__lawfirm_assignproposal SET is_pinbyemp = '0' ,emp_id = '0' WHERE id = ".$db->Quote($id);
		$db->setQuery($query);
		if(!$db->query()){
			return false;
		}

                //delete the task from disapproved task table
		$query = "DELETE FROM #__lawfirm_disapproved_tasks WHERE aid = ".$id;
		$db->setQuery($query);
		$db->query();
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

                 //delete the task from disapproved task table
		$query = "DELETE FROM #__lawfirm_disapproved_tasks WHERE aid = ".$id;
		$db->setQuery($query);
		$db->query();

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
		$query = "SELECT a.template,a.id FROM #__clientproposals as a WHERE a.id = ".$db->Quote($pid);
		$db->setQuery($query);
		$db->query();
		$result = $db->loadObject();
		//return $result;
		
		//get signer info
		$signer_info = $this->getSignerInfo($pid);
		$detailData = (object)array_merge((array)$result,(array)$signer_info);
		return $detailData;
	}
	
	/**
	 *Method to get signer info 
	 */
	public function getSignerInfo($pid){
		$db = $this->getDBO();
		//get signer id
		$query = "SELECT a.cid, a.responsedate, b.fname, b.lname ,b.email, b.signertitle ,c.company
		           FROM #__clientproposals as a 
		           JOIN #__clientsigner as b ON a.cid = b.cid 
		           JOIN #__auditorclients as c ON a.cid = c.id
		           WHERE a.id = ".$pid;
		$db->setQuery($query);
		$db->query();
		$result = $db->loadObject();
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
		$query = "SELECT a.* FROM #__lawfirm_assignproposal as a
		where a.emp_id = ".$db->Quote($userId)." 
		AND a.assign_by_partner != '0'
		AND date_add(`assigndate`,INTERVAL 2 MONTH) >= NOW()";

		$db->setQuery($query);
		$db->query();
		$result = $db->loadObjectlist();
		return $result;
	}

	/**
	 * Method to unpin the task from partner
	 */
	public function unpintaskfrompartner($id,$pid){
		$user = JFactory::getUser();
		$userId = $user->id;
		$db = $this->getDBO();
		$query = "UPDATE #__lawfirm_assignproposal SET is_pinbyemp = '0' ,emp_id = '0',assign_by_partner='0' WHERE id = ".$db->Quote($id);
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
	 * Method to save template
	 */
	public function savetemplate($id,$aid,$content1,$type){
		$button_type = JRequest::getVar('buttontype');
		$partner_id = JRequest::getVar('partner_id1');
		//get user id
		$user = JFactory::getUser();
		$userId = $user->id;
		//get table object
		$template_table = &JTable::getInstance('lawfirmtemplate', 'LawfirmTable');
		$data['id'] = $id;
		$data['aid'] = $aid;
		$data['lid'] = $userId;
		$data['template_type'] = $type;
		$data['custom_template'] = $content1;

		$template_table->save($data);
		
		if($button_type == "save"){
			return "save";
		}elseif($button_type == "savetopartner"){
			//save the data in #__lawfirm_task_status table for asssigning
			// the task to partner by employee
			$taskstopartner_table = &JTable::getInstance('taskbyemployeetopartner', 'LawfirmTable');
			$data1['aid'] = $aid;

			//get propsal id
			$lawfirm_assignproposal = &JTable::getInstance('lawfirm_assignproposal', 'LawfirmTable');
			$lawfirm_assignproposal->load($aid);
			$pid = $lawfirm_assignproposal->pid;
			$data1['pid'] = $pid;
			$data1['lid'] = $userId;
			$data1['submit_date'] =
			$data1['partner_id'] = $partner_id;
			$config =& JFactory::getConfig();
			$tzoffset = $config->getValue('config.offset');
			$assign_date =& JFactory::getDate('',$tzoffset);
			$data1['submit_date'] = $assign_date->toMySQL(true);

			//save the data
			if(!$taskstopartner_table->save($data1)){
				echo JText::_( 'ERROR IN SAVE DATA' );
				return;
			}
			//update the lawfirm_assignpropsal table for 'is_readybyemp as 1'

			//get db object
			$db = $this->getDBO();
			$query = "UPDATE #__lawfirm_assignproposal as a set a.is_readybyemp = 1 WHERE id = ".$aid;
			$db->setQuery($query);
			if(!$db->query()){
				echo JText::_( 'ERROR IN SAVE DATA' );
				return;
			}

			return "savetopartner";
		}


	}

	/**
	 * Method to get mail template by lawfirm
	 */
	public function getMailTemplateByLawfirm($id){
		//get user id
		$user = JFactory::getUser();
		$userId = $user->id;

		//get assign_table id
		$aid  = $id;
		//get db object
		$db = JFactory::getDBO();
		$query = "SELECT a.*
		           FROM #__lawfirm_employee_mailtemplate as a 
		           WHERE a.aid = ".$db->Quote($aid)." AND a.lid = ".$db->Quote($userId);
		
		$db->setQuery($query);
		$db->query();
		$result = $db->loadObject();
		return $result;
	}

	/**
	 * Method to get detail for mail template by lawfirm
	 */
	public function getInfo($aid){
		//get db object
		$db = JFactory::getDBO();
		$query = "SELECT a.lid , b.cid , b.assigndate ,b.lawfirmid,b.is_readybyemp FROM #__clientproposals as a LEFT JOIN  #__lawfirm_assignproposal as b ";
		$query .= "ON a.id = b.pid";
		$query .= " WHERE b.id = ".$db->Quote($aid);
		$db->setQuery($query);
		$db->query();
		$result = $db->loadObject();
		
		//get auditor id
		$lid = $result->lid;
		$cid = $result->cid;
		$assign_date = $result->assigndate;
		$lawfirm_id = $result->lawfirmid;
		$is_readybyemp = $result->is_readybyemp;

		//get Auditor info
		$query2 = "SELECT a.accounting_firm as firmname ,b.name as name FROM #__users_profile_detail as a , #__users as b WHERE a.lid = b.id AND b.id = ".$db->Quote($lid);
		$db->setQuery($query2);
		$db->query();
		$result = $db->loadObject();
		$firm_name = $result->firmname;

		//get signer name
		$query = "SELECT a.fname,a.lname FROM #__clientsigner as a WHERE a.cid = ".$db->Quote($cid);
		$db->setQuery($query);
		$db->query();
		$result = $db->loadObject();
		$signername = $result->fname." ".$result->lname;

		//get client or signer company name
		$query = "SELECT a.company FROM #__auditorclients as a WHERE a.id = ".$db->Quote($cid);
		$db->setQuery($query);
		$db->query();
		$company_name = $db->loadResult();

		//get lawfirm name
		$query = "SELECT a.accounting_firm FROM #__users_profile_detail as a WHERE a.lid = ".$lawfirm_id;
		$db->setQuery($query);
		$db->query();
		$result = $db->loadResult();
		$lawfirm_name = $result;

		$all_info['auditor_firm_name'] = $firm_name;
		$all_info['signername'] = $signername;
		$all_info['company_name'] = $company_name;
		$all_info['assigned_date'] = $assign_date;
		$all_info['lawfirm_name'] = $lawfirm_name;
		$all_info['is_readybyemp'] = $is_readybyemp;

		return $all_info;
	}

	/**
	 * Method to upload pdf
	 */
	public function uploadpdf($id,$aid){
		$data = JRequest::get('POST');
		$button_type = JRequest::getVar('buttontype');
		
		$file = JRequest::getVar('pdf', null, 'files', 'array');
		$filename = time().$file['name'];
		$destination = JPATH_SITE . DS . 'media' .DS.'com_lawfirm'.DS .'pdf'.DS.$filename;

		// Move uploaded file destination
		if(!JFile::upload($file['tmp_name'], $destination)){
			echo JText::_( 'ERROR MOVING FILE' );
			return;
		}
		else{
			//update the pdf path in #__lawfirm_employee_mailtemplate
			//get user id
			$user = JFactory::getUser();
			$userId = $user->id;
			//get table object
			$template_table = &JTable::getInstance('lawfirmtemplate', 'LawfirmTable');
			$data['id'] = $id;
			$data['aid'] = $aid;
			$data['lid'] = $userId;
			$data['template_type'] = 'pdf';
			$data['pdf'] = $filename;

			$template_table->save($data);

			if($button_type == "approve"){
				return "save";
			}elseif($button_type == "savetopartner"){
				//save the data in #__lawfirm_task_status table for asssigning
				// the task to partner by employee
				$taskstopartner_table = &JTable::getInstance('taskbyemployeetopartner', 'LawfirmTable');
				$data1['aid'] = $aid;
					
				//get propsal id
				$lawfirm_assignproposal = &JTable::getInstance('lawfirm_assignproposal', 'LawfirmTable');
				$lawfirm_assignproposal->load($aid);
				$pid = $lawfirm_assignproposal->pid;
				$data1['pid'] = $pid;
				$data1['lid'] = $userId;
				$data1['submit_date'] =
				$data1['partner_id'] = $data['partner_id1'];
				$config =& JFactory::getConfig();
				$tzoffset = $config->getValue('config.offset');
				$assign_date =& JFactory::getDate('',$tzoffset);
				$data1['submit_date'] = $assign_date->toMySQL(true);
					
				//save the data
				if(!$taskstopartner_table->save($data1)){
					echo JText::_( 'ERROR IN SAVE DATA' );
					return;
				}
				//update the lawfirm_assignpropsal table for 'is_readybyemp as 1'
					
				//get db object
				$db = $this->getDBO();
				$query = "UPDATE #__lawfirm_assignproposal as a set a.is_readybyemp = 1 WHERE id = ".$aid;
				$db->setQuery($query);
				if(!$db->query()){
					echo JText::_( 'ERROR IN SAVE DATA' );
					return;
				}
					
				return "savetopartner";
			}
		}

	}

	/**
	 * Method to get lawfirm partners of lawfirm
	 */
	public function lawfirmpartner(){
		//get db object
		$db = JFactory::getDBO();
		//get user id
		$user = JFactory::getUser();
		$userId = $user->id;

		//get lawfirm admin
		$query = "SELECT a.parent FROM #__users_profile_detail as a WHERE a.lid = ".$db->Quote($userId);
		$db->setQuery($query);
		$db->query();
		$lawfirm_admin = $db->loadResult();
			
		//get partners
		$query1 = "SELECT a.lid,c.name,c.email FROM #__users_profile_detail as a
	              JOIN #__user_usergroup_map as b ON a.lid = b.user_id 
	              JOIN #__users as c ON a.lid = c.id 
	    		  WHERE a.parent = ".$lawfirm_admin." AND b.group_id = 13".' AND c.block !='. $db->quote(1);
		$db->setQuery($query1);
		$db->query();
		$result = $db->loadObjectlist();
		return $result;
	}

	/**
	 * Method to get assigned partner of proposal by lawfirm
	 * @param assign id
	 * @return partner id
	 */
	public function getassignedpartner($id){
		//get db object
		$db = JFactory::getDBO();
		$query = "SELECT a.partner_id FROM #__lawfirm_task_status as a WHERE a.aid = ".$id;
		$db->setQuery($query);
		$db->query();
		$result = $db->loadResult();
		return $result;

	}
	/**
	 * Method to check the proposal is waitig for approval
	 * by partner
	 */
	public function checkProposalForApproval($id){
		$db = $this->getDBO();
		$query = "SELECT a.is_approve FROM #__lawfirm_task_status as a WHERE a.aid = ".$id;
		$db->setQuery($query);
		$db->query();
		$result  = $db->loadResult();
		return $result;
	}
	/**
	 * Method to get mail template of prepared task
	 * @param assign id
	 */
	public function getMyTemplate($id){
		//get user id
		$user = JFactory::getUser();
		$userId = $user->id;

		//get assign_table id
		$aid  = $id;
		//get db object
		$db = JFactory::getDBO();
		$query = "SELECT a.*
		           FROM #__lawfirm_employee_mailtemplate as a
		           WHERE a.aid = ".$aid." AND a.lid = ".$userId;
		
		$db->setQuery($query);
		$db->query();
		$result = $db->loadObject();
      
		return $result;
	}
	
	/**
	 * Method to check the disapprove status of proposal
	 * @param assign id
	 */
	public function checkDisapprove($aid){
                //get user id
		$user = JFactory::getUser();
		$userId = $user->id;

		$db = $this->getDBO();
		$query = "SELECT count(a.id) FROM #__lawfirm_disapproved_tasks as a WHERE a.aid = ".$aid ." AND a.is_disapprove = 1 AND a.lid = ".$userId;
		$db->setQuery($query);
		$db->query();
		$result = $db->loadResult();

		return $result;
	}
	
	/**
	 * Method to get partner info of disapproved proposal
	 */
	public function getPartnerInfo($id){
               //get user id
		$user = JFactory::getUser();
		$userId = $user->id;
		$db = $this->getDBO();
		$query = "SELECT a.partner_id FROM #__lawfirm_disapproved_tasks as a WHERE a.aid = ".$id;
		$db->setQuery($query);
		$db->query();
		$result = $db->loadObject();
		//get name
		$user = JFactory::getUser($result->partner_id);
		$userEmail = $user->email;
		return $userEmail;
	}
}
