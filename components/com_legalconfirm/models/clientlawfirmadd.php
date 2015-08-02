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
require_once JPATH_COMPONENT.'/tables/auditorclients.php';
require_once JPATH_COMPONENT.'/tables/clientsigner.php';
/**
 * Methods supporting a list of Legalconfirm Clientlawfirmadd , adding more lawfirms for a client...
 */
class LegalconfirmModelClientlawfirmadd extends JModelList {

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
                'accounting_firm', 'b.accounting_firm',
                'email', 'a.email'
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
		$searchfltr = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');
		$this->setState('filter.search', $searchfltr);
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
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery() {
		// Create a new query object.
		$db = $this->getDbo();
		$user = JFactory::getUser();
		$query = $db->getQuery(true);
		$this->populateState();
		$app = JFactory::getApplication();
		$this->selectedlawfirmsdata = $app->getUserState('com_legalconfirm.selectedlawfirmoffices.data');
		$this->selectedclientid = $app->getUserState('com_legalconfirm.clientprofile.clientid');
		$lawfirmidsarray = array();
		foreach($this->selectedlawfirmsdata as $aarray)
		{
			$lawfirmidsarray[] = $aarray['lawfirm'];
		}
		$nowseletedlawfirms = implode(',', $lawfirmidsarray); //echo "<pre>"; print_r($nowseletedlawfirms); exit;


		$clientid = JRequest::getVar('id');
		$requestedLawfirms = array();
		$requestedLawfirms = $this->getLawfirmsGettings();	//function for finding the already requested proposal to the same client..
		$anewarray = array();
		foreach ($requestedLawfirms as $newarray)
		{
			$anewarray[] = $newarray->lawfirmid;
		}  //making comma seperated string fom array...
		$comma_separated = implode(",", @$anewarray);
		$incompletetasklawfirm = array();
		$incompletetasklawfirm = $this->getLawfirmIncompleteTask($clientid);//function calling for finding the lawfirms those are not completed thier task.
		$bnewarray = array();
		foreach ($incompletetasklawfirm as $cnewarray)
		{
			$bnewarray[] = $cnewarray->lawfirmid;
		}  //making comma seperated string fom array...
		$comma_separated_incomplete = implode(",", @$bnewarray);

		$config = JFactory::getConfig();
		$lawfirm_group = $config->getValue('lawfirm');
		// Select the required fields from the table.
		$query->select(
		$this->getState(
                        'list.select', 'a.*, a.id as lawfirmid'
                        )
                        );
                        $query->select(' b.*');
                        $query->from('`#__users` AS a');
                        $query->join('INNER', $db->quoteName('#__users_profile_detail').' AS b ON a.id = b.lid');
                        $query->join('INNER', $db->quoteName('#__user_usergroup_map').' AS c ON a.id = c.user_id');
                        $query->where('c.group_id = '.$lawfirm_group);
                        $query->where('a.block=' . '0');
//                        if($comma_separated != '')
//                        $query->where('a.id NOT IN('.$comma_separated.')');//last request law firms
//                        if($nowseletedlawfirms != '')
//                        $query->where('a.id NOT IN('.$nowseletedlawfirms.')');//now selected lawfirms
                        // Filter by search in title
                        if($comma_separated_incomplete != '')
                        {
                        	$query->where('a.id NOT IN('.$comma_separated_incomplete.')');
                        }

                        $search = $this->getState('filter.search');
                        if (!empty($search)) {
                        	if (stripos($search, 'id:') === 0) {
                        		$query->where('a.id = '.(int) substr($search, 3));
                        	} else {
                        		$search = $db->Quote('%'.$db->escape($search, true).'%');
                        	}
                        	$query->where('b.accounting_firm LIKE '.$search);
                        }
                        // Add the list ordering clause.
                        $orderCol = $this->getState('list.ordering', 'b.accounting_firm');
                        $orderDirn = $this->getState('list.direction', 'asc');
                        $query->order($db->escape($orderCol . ' ' . $orderDirn));
                        return $query;
	}

	/*
	 * function for finding the already requested lawfirms in a lawfirms
	 * @params clientid
	 */
	//	public function getRequestedLawfirms()
	//	{
	//		$app = JFactory::getApplication();
	//		$clientid = JRequest::getVar('id');
	//		$user = JFactory::getUser();
	//		$db = $this->getDbo();
	//		$date = JFactory::getDate();
	//		$currentdate = $date->toFormat();
	//		$query = $db->getQuery(true);
	//		$query->select('a.id as proposalid');
	//		$query->from('#__clientproposals as a');
	//		$query->where('a.cid = '.$clientid);
	//		$query->where('a.lid = '.$user->id);
	//		$query->where('a.status = '.'"0"');
	//		$query->where('DATE_ADD(a.requestdate, INTERVAL 2 MONTH)>NOW()');
	//		$db->setQuery($query);
	//		$db->query();
	//		$resultProposalid = $db->loadResult();
	//		//function for finding the lawfirmids.......
	//		$lawfirmids = $this->lawfirmsGetting($resultProposalid);
	//		return $lawfirmids;
	//	}
	/*
	* function for finding the lawfirms ids requested and not accepted by signer..
	* @params proposalid
	* function is also calling from view.html file for the same view(clientprofle)
	*/
	public function getLawfirmsGettings()
	{
		$clientid = JRequest::getVar('id');
		$db = $this->getDbo();
		$app = JFactory::getApplication();
		$this->selectedlawfirmsdata = $app->getUserState('com_legalconfirm.selectedlawfirmoffices.data');
		$this->selectedclientid = $app->getUserState('com_legalconfirm.clientprofile.clientid');
		$lawfirmidsarray = array();
		foreach($this->selectedlawfirmsdata as $aarray)
		{
			$lawfirmidsarray[] = $aarray['lawfirm'];
		}
		$nowseletedlawfirms = implode(',', $lawfirmidsarray); //echo "<pre>"; print_r($nowseletedlawfirms); exit;
		$query = $db->getQuery(true);
		$query->select('DISTINCT a.lawfirmid');
		$query->from('#__proposallawfirm as a');
		$query->join('INNER', '#__clientproposals as b ON a.pid = b.id');
		$query->where('a.status = '.'"0"');
		$query->where('b.cid = '.$clientid);
		$query->where('a.lawfirmid NOT IN(SELECT c.lawfirmid FROM #__proposallawfirm as c INNER JOIN #__clientproposals as d ON c.pid = d.id WHERE c.status ='.'"1"'.' AND d.cid ='. $clientid.')');
		if($nowseletedlawfirms != '')
		$query->where('a.lawfirmid NOT IN('.$nowseletedlawfirms.')');
		$db->setQuery($query);
		$db->query(); 
		$resultlawfirmsids = $db->loadObjectList(); 
		return $resultlawfirmsids;
	}
	/*
	 * function for finding the lawfirms those task are not completed corrosponding to a particular client.
	 * @params clientid
	 */
	public function getLawfirmIncompleteTask($clientid)
	{
		$db = JFactory::getDbo();
		$resultlawfirmsids = array();
		$query = $db->getQuery(true);
		$query->select('DISTINCT a.lawfirmid');
		$query->from('#__lawfirm_assignproposal as a');
		$query->join('INNER', '#__clientproposals as b ON a.pid = b.id');
		$query->where('a.taskstatus = '.'"0"');
		$query->where('b.cid = '.$clientid);
		$db->setQuery($query);
		$db->query();
		$resultarry = $db->loadObjectList();
		return $resultarry;
	}

	/*
	 * function for getting the client profile..
	 */
	public function getProfile()
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$clientid = JRequest::getVar('id');
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.*, a.id as clientid');
		$query->from('#__auditorclients as a');
		$query->where('a.id = '.$clientid);
		$query->where('a.lid = '.$user->id);
		$db->setQuery($query);
		$db->query();
		$resultProfile = $db->loadObject();
		return $resultProfile;
	}
	/*
	 * function for getting the signer of the current user and requested client..
	 * @params clientid , userid
	 */
	public function getSigner()
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$clientid = JRequest::getVar('id');
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('c.*');
		$query->from('#__clientsigner as c');
		$query->where('c.cid = '.$clientid);
		$query->where('c.lid = '.$user->id);
		$db->setQuery($query);
		$db->query();
		$resultSigner = $db->loadObject();
		return $resultSigner;
	}

}
