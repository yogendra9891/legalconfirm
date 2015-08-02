<?php
/**
 * @package			Easy QuickIcons
 * @version			$Id: easyquickicons.php 31 2012-09-22 08:23:32Z allan $
 *
 * @author			Allan <allan@awynesoft.com>
 * @link			http://www.awynesoft.com
 * @copyright		Copyright (C) 2012 AwyneSoft.com All Rights Reserved
 * @license			GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * EasyquickiconList Model
 */
class EasyquickiconsModelEasyquickicons extends JModelList
{
	
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
				'link', 'a.link',
				'published', 'a.published',
				'catid', 'a.catid',
				'ordering', 'a.ordering',
				'icon', 'a.icon',
				'custom_icon', 'a.custom_icon',
				'icon_path', 'a.icon_path',
				'description', 'a.description',
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
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);
		
		$categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id', null);
		$this->setState('filter.category_id', $categoryId);
		// Load the parameters.
		$params = JComponentHelper::getParams('com_easyquickicons');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.name', 'asc');
	}
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string	A prefix for the store id.
	 *
	 * @return	string	A store id.
	 * @since	1.6
	 */
	
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.state');
		$id .= ':'.$this->getState('filter.category_id');
		
		return parent::getStoreId($id);
	}
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// Create a new query object.		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		// Select some fields
		$query->select(
			$this->getState(
				'list.select','a.*'
			)
		);
		// From the easyquickicons table
		$query->from($db->quoteName('#__easyquickicons') . ' AS a');
		
		// Join over the categories.
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');
		// Filter by published state
		$state = $this->getState('filter.state');
		if (is_numeric($state)) {
			$query->where('a.published = '.(int) $state);
		} elseif ($state === '') {
			$query->where('(a.published IN (0,1,2))');
		}
		// Filter by category.
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId)) {
			$query->where('a.catid = ' . (int) $categoryId);
		}
		// Filter the items over the search string if set.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where(
					'('.$db->quoteName('a.name').' LIKE '.$search .
					' OR '.$db->quoteName('a.link').' LIKE '.$search . ')'
				);
			}
		}
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'a.id');
		$orderDirn	= $this->state->get('list.direction', 'ASC');
		$query->order($db->escape($orderCol.' '.$orderDirn));
 
		return $query;
	}
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */

	public function getTable($type = 'Easyquickicon', $prefix = 'EasyquickiconsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Returns an array of the components version info.
	 */
	public function getVersionInfo()
	{
		$db =& JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('manifest_cache');
		$query->from($db->quoteName('#__extensions'));
		$query->where('element = ' . $db->quote('com_easyquickicons'));
		
		$db->setQuery($query);
		
		$val = $db->loadResult();
		
		$json = json_decode($val);
		
		return $json;
	}

}