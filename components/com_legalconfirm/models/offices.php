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
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_legalconfirmusers' . DS . 'tables' . DS . 'legaluseroffice.php' );

/**
 * Methods supporting a list of Legalconfirm clients.
 */
class LegalconfirmModelOffices extends JModelList {

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
                'id','a.id','office_title', 'a.office_title','city','a.city','state','a.state','status','a.status'
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
		        $layout = JRequest::getVar('layout');
        if($layout == "addoffice"){ 
        	JRequest::setVar('filter_order', 'a.id');
        	JRequest::setVar('filter_order_Dir','desc');
        }
		$limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
		$this->setState('list.start', $limitstart);
        $ordering =  $this->setState('list.ordering', JRequest::getVar('filter_order'));
		$direction =  $this->setState('list.direction', JRequest::getVar('filter_order_Dir'));
        
		if(empty($ordering)) {
			$ordering = 'a.id';
			
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
		$auditor_emp = $config->getValue('auditor_emp');
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		//get logged in userid
		$user = JFactory::getUser();
                $user_id = $user->id;
		$email = $user->email;
		//get firm name
		$firm_array = explode('@',$email);
		$firm_name = $firm_array['1'];

		// Select the required fields from the table.
		$query = "SELECT * FROM #__users_office as a where a.lid = ".$db->Quote($user_id);
		 
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
	 * Method to edit offices
	 */
   public function getdata(){
    
    $office_id = JRequest::getVar('id');
    //get login user
    $user = JFactory::getUser();
    $userId = $user->id;
    //get db object
    $db = JFactory::getDbo();
    $query = "select * from #__users_office where id = ".$db->QUote($office_id)." AND lid = ".$db->Quote($userId);
    
    $db->setQuery($query);
    if(!$db->query()){
    	return false;
    }
    $result = $db->loadObject();
    
    
    return $result;
   }
   
   /**
    * Method to add office
    */
   public function addoffice($data){
   	//get office id
   	
   	$data['ofc_detail']['office_title'] = $data['ofc_detail']['office'];
   	$data['ofc_detail']['id'] = $data['id'];

   	//get table object
   	$userofficetable = &JTable::getInstance('legaluser', 'LegaluserofficeTable');
   	$userofficetable->load($data['id']);
   	if(!$userofficetable->save($data['ofc_detail'])){
   		return false;
   	}else{
   		
   		return true;
   	}
   	
   }
   
   /**
    * Method to add new office
    */
   public function addnewoffice($data){
   	//get login user
    $user = JFactory::getUser();
    $userId = $user->id;

     //get group id
		foreach($user->groups as $key=>$value){
			$gid = $value;
		}
		
		$data['ofc_detail']['gid'] = $gid;

    $data['ofc_detail']['lid'] = $userId;
   	$data['ofc_detail']['office_title'] = $data['ofc_detail']['office'];
        $data['ofc_detail']['status'] = '1';
   	//get table object
   	$userofficetable = &JTable::getInstance('legaluser', 'LegaluserofficeTable');
   	$userofficetable->load($data['id']);
   	if(!$userofficetable->save($data['ofc_detail'])){
   		return false;
   	}else{
     		return true;
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
    * Method to activate the office
    */
   public function unBlockOffice($id){
   	$db = $this->getDBO();
   	$query = "UPDATE #__users_office set status = '1', activation_key = '' WHERE id = ".$id;
   	$db->setQuery($query);
   	if(!$db->query()){
   		return false;
   	}
   	return true;
   	
   	
   }
}
