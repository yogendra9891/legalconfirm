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
 * Model supporting for reports transaction records.
 */
class LegalconfirmModelReports extends JModelList
{

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                                'id', 'a.id',
            					'lid', 'a.lid',
            					'cid', 'a.cid',
            					'amount', 'a.amount',
            					'date', 'a.date',
            					'transaction_id', 'a.transaction_id',
            );
        }

        parent::__construct($config);
    }


	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
 
        
		// Load the parameters.
		$params = JComponentHelper::getParams('com_legalconfirm');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.id', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id.= ':' . $this->getState('filter.search');
		$id.= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from('#__initiation_payment_record AS a');

		// Add the list ordering clause.
        $orderCol	= $this->state->get('list.ordering');
        $orderDirn	= $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol.' '.$orderDirn));
        }

		return $query;
	}
	/*
	 * function for generating the report
	 */
//	public function generateReport($id)
//	{
//		// Create a new query object.
//		$db		= $this->getDbo();
//		$query	= $db->getQuery(true);
//
//		$query = "SELECT * FROM #__initiation_payment_record";
//        $db->setQuery($query);
//        $db->query();
//        $items = $db->loadObjectList();
//        header('Content-Description: File Transfer');
//        header('Content-Type: text/csv');
//        header('Content-disposition: attachment; filename="user-report.csv"');
//        echo 'id,Auditor,Client,Transaction Date';
//        foreach( $items as $group => $item )
//         {
//           echo $item->id . ',' . $item->lid. ',' . $item->cid . ',' . $item->date  . ',';
//           echo "\r\n";
//         }
//         echo "\r\n"; exit;
//  }

	/*
	 * function for finding the detail for a payment record id..
	 */
		public function generateReport($id)
		{
			// Create a new query object.
			$db		= $this->getDbo();
			$query	= $db->getQuery(true);
	
			$query = "SELECT * FROM #__initiation_payment_record where id = ".$id;
	        $db->setQuery($query);
	        $db->query();
	        $items = $db->loadObject(); 
            return $items;
	  }
	/*
	 * function for finding the auditor name
	 */
	public function auditorname($lid)
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$clientid = JRequest::getVar('id');
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.name');
		$query->from('#__users as a');
		$query->where('a.id = '.$lid);
		$db->setQuery($query);
		$db->query();
		$profilename = $db->loadResult();
		return $profilename;
	}
	/*
	 * function for finding the client name
	 * @params clientid
	 */
	public function clientname($cid)
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$clientid = JRequest::getVar('id');
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.company');
		$query->from('#__auditorclients as a');
		$query->where('a.id = '.$cid);
		$db->setQuery($query);
		$db->query();
		$clientname = $db->loadResult();
		return $clientname;
	}
	
}