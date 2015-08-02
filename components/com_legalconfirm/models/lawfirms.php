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
 * Methods supporting a list of Legalconfirm records.
 */
class LegalconfirmModelLawfirms extends JModelList {

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
                        // Filter by search in title
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
	/*
	 * function for finding the offices..of a law firm
	 * @params law firm id..
	 */
	public function getOffices($lawfirmid)
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('u.*');
		$query->from('#__users_office as u');
		$query->where('u.lid = '.$lawfirmid);
		$db->setQuery($query);
		$db->query();
		$resultLawoffices = $db->loadObjectList();
		return $resultLawoffices;
	}


}
