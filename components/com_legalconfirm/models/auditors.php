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
class LegalconfirmModelAuditors extends JModelList {

	/**
	 * Constructor.
	 *
	 * @param    array    An optional associative array of configuration settings.
	 * @see        JController
	 * @since    1.6
	 */
//	public function __construct($config = array()) {
//		parent::__construct($config);
//	}
	
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
                'company', 'a.company',
            	'engagementno', 'a.engagementno',
				'signertitle', 'b.signertitle',
                'previewdate', 'a.previewdate',
			);
		}
		parent::__construct($config);
		
	}

//	protected function populateState($ordering = null, $direction = null) {
//
//		// Initialise variables.
//		$app = JFactory::getApplication();
//
//		// List state information
//		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
//		$this->setState('list.limit', $limit);
//
//		$limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
//		$this->setState('list.start', $limitstart);
//		//set the state for sorting the column..
//		$ordering =  $this->setState('list.ordering', JRequest::getVar('filter_order'));
//		$direction =  $this->setState('list.direction', JRequest::getVar('filter_order_Dir'));
//		$searchfltr = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');
//		$companyfltr = $app->getUserStateFromRequest($this->context.'.filter.company', 'filter_company', '', 'string');
//		$this->setState('filter.search', $searchfltr);
//		$this->setState('filter.company', $companyfltr);
//		// List state information.
//		parent::populateState($ordering, $direction);
//	}
//
//	protected function getStoreId($id = '')
//	{
//		// Compile the store id.
//		$id.= ':' . $this->getState('filter.search');
//		return parent::getStoreId($id);
//	}
//
//	/**
//	 * Build an SQL query to load the list data.
//	 *
//	 * @return	JDatabaseQuery
//	 * @since	1.6
//	 */
//	protected function getListQuery() {
//		// Create a new query object.
//		$db = $this->getDbo();
//		$user = JFactory::getUser();
//		$query = $db->getQuery(true);
//		$this->populateState();
//		// Select the required fields from the table.
//		$query->select('a.*, a.id as clientid');
//		$query->select(' b.*');
//		$query->from('`#__auditorclients` AS a');
//		$query->join('INNER', $db->quoteName('#__clientsigner').' AS b ON a.id = b.cid');
//		// Filter by search in title
//		$search = $this->getState('filter.search');
//		if (!empty($search)) {
//			if (stripos($search, 'id:') === 0) {
//				$query->where('a.id = '.(int) substr($search, 3));
//			} else {
//				$search = $db->Quote('%'.$db->escape($search, true).'%');
//			}
//			$query->where('(a.company LIKE '.$search.' OR b.fname LIKE '.$search.' OR b.lname LIKE '.$search.')');
//		}
//		$company = $this->getState('filter.company');
//		if (!empty($company)) {
//			$query->where('a.id = '.$company);
//		}
//		$query->where('a.lid='. $user->id);
//		// Add the list ordering clause.
//		$orderCol = $this->getState('list.ordering', 'a.company');
//		$orderDirn = $this->getState('list.direction', 'asc');
//		$query->order($db->escape($orderCol . ' ' . $orderDirn)); 
//		return $query;
//	}
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
		$companyfltr = $app->getUserStateFromRequest($this->context.'.filter.company', 'filter_company', '', 'string');
		$this->setState('filter.search', $searchfltr);
		$this->setState('filter.company', $companyfltr);
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
		// Select the required fields from the table.
		$query->select(
		$this->getState(
                        'list.select', 'a.*, a.id as clientid'
                        )
                        );
                        $query->select(' b.*');
                        $query->from('`#__auditorclients` AS a');
                        $query->join('LEFT', $db->quoteName('#__clientsigner').' AS b ON a.id = b.cid');
                        // Filter by search in title
                        $search = $this->getState('filter.search');
                        if (!empty($search)) {
                        	if (stripos($search, 'id:') === 0) {
                        		$query->where('a.id = '.(int) substr($search, 3));
                        	} else {
                        		$search = $db->Quote('%'.$db->escape($search, true).'%');
                        	}
                        	$query->where('a.company LIKE '.$search);
                        }
                        $company = $this->getState('filter.company');
                        $query->where('a.lid='. $user->id);
                        // Add the list ordering clause.
                        $orderCol = $this->getState('list.ordering', 'a.company');
                        $orderDirn = $this->getState('list.direction', 'asc');
                        $query->order($db->escape($orderCol . ' ' . $orderDirn));
                        return $query;
	}
	
}
