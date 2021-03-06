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
 * Methods supporting a lawfirms received log.
 */
class LegalconfirmModelLawfirmsreceivedlog extends JModelList {

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
                'id', 'a.id',
			    'lawfirmid', 'c.lawfirmid',
			    'assigndate', 'c.assigndate',
			    'taskstatus', 'c.taskstatus',
			    'responsedate', 'c.responsedate'
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
		//set the state for sorting the column..
		$ordering =  $this->setState('list.ordering', JRequest::getVar('filter_order'));
		$direction =  $this->setState('list.direction', JRequest::getVar('filter_order_Dir'));
		// List state information.
		parent::populateState($ordering, $direction);
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id.= ':' . $this->getState('filter.search');
		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function _getListQuery() {
		// Create a new query object.
		$db = $this->getDbo();
		$user = JFactory::getUser();
		$query = $db->getQuery(true);
		$this->populateState();
		$clientid = JRequest::getVar('id'); 
		// Select the required fields from the table.
		$query->select(' distinct c.id as assignid, b.id, c.lawfirmid, a.pid, c.assigndate, c.taskstatus, c.responsedate');
                        $query->from('#__proposallawfirm AS a');
                        $query->join('INNER', '#__clientproposals as b ON a.pid = b.id');
                        $query->join('INNER', '#__lawfirm_assignproposal as c ON c.pid = b.id');
                        $query->where('b.cid = '.$clientid);
                        $query->where('a.status ='. $db->quote(1));
                        $query->where('c.taskstatus ='. $db->quote(1));  
                        // Add the list ordering clause.
                        $orderCol = $this->getState('list.ordering', 'a.id');
                        $orderDirn = $this->getState('list.direction', 'asc');
                        $query->order($db->escape($orderCol . ' ' . $orderDirn)); //echo $query; exit;
                        return $query; 
	}
	/*
	 * function for finding the custom template for the auditor can save the generated pdf
	 * @params assign_proposal_id 
	 */
	public function getTemplate($asid)
	{
		// Create a new query object.
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$query = $db->getQuery(true);	
		$query->select('a.*');
		$query->from('#__lawfirm_employee_mailtemplate as a');
		$query->where('a.aid = '.$asid);
		$db->setQuery($query);
		$db->query();
		$templateresult = $db->loadObject();
		return $templateresult;	
	}
	/*
	 * function for finding the lawfirmname from user_profile_detail
	 * @params lawfirmid
	 */
	public function lawfirmname($lawfirmid)
	{
		// Create a new query object.
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$query = $db->getQuery(true);	
		$query->select('a.accounting_firm');
		$query->from('#__users_profile_detail as a');
		$query->where('a.lid = '.$lawfirmid);
		$db->setQuery($query);
		$db->query();
		$lawfirmname = $db->loadResult();
		return $lawfirmname;	
	}
	/*
	 * function for finding the lawfirm partner information.
	 * @params assignproposal id 
	 */
	public function getPartnerinfo($assign_proposal_id)
	{
		// Create a new query object.
		$db = JFactory::getDbo(); 
		$user = JFactory::getUser();
		$query = $db->getQuery(true);	
		$query->select('a.partner_id, c.email, c.name, b.responsedate');
		$query->from('#__lawfirm_task_status as a');
		$query->join('INNER','#__lawfirm_assignproposal as b ON a.aid = b.id');
		$query->join('INNER','#__users as c ON a.partner_id = c.id');
		$query->where('a.aid = '.$assign_proposal_id);
		$db->setQuery($query); 
		$db->query();
		$lawfirmpartnerinfo = $db->loadObject();
		return $lawfirmpartnerinfo;	
	}
}

