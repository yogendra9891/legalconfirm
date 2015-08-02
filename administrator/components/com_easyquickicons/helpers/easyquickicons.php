<?php
/**
 * @package			Easy QuickIcons
 * @version			$Id: easyquickicons.php 36 2012-09-25 14:55:47Z allan $
 *
 * @author			Allan <allan@awynesoft.com>
 * @link			http://www.awynesoft.com
 * @copyright		Copyright (C) 2012 AwyneSoft.com All Rights Reserved
 * @license			GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Easyquickicons component helper.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_easyquickicons
 * @since		1.6
 */
class EasyquickiconsHelper
{
	public static $extension = 'com_easyquickicons';

	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 */
	public static function addSubmenu($submenu)
	{
		$layout = JRequest::getCmd('layout', 'default');
		if($layout != 'welcome'){
			JSubMenuHelper::addEntry(JText::_('COM_EASYQUICKICONS'), 'index.php?option=com_easyquickicons', $submenu == 'easyquickicons');
			JSubMenuHelper::addEntry(JText::_('COM_EASYQUICKICONS_SUBMENU_CATEGORY'), 'index.php?option=com_categories&view=categories&extension=com_easyquickicons', $submenu == 'categories');
		}
		$document = JFactory::getDocument();

		if ($submenu == 'categories')
		{
			$document->setTitle(JText::_('COM_EASYQUICKICONS_ADMINISTRATION_CATEGORIES'));
		}
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 */
	public static function getActions()
	{
		$user		= JFactory::getUser();
		$result		= new JObject;
		$assetName 	= 'com_easyquickicons';

		$actions = JAccess::getActions($assetName);

		foreach ($actions as $action) {
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return	string			The HTML code for the select tag
	 */
	public static function publishedOptions()
	{
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '*', 'JALL');
		$options[]	= JHtml::_('select.option', '1', 'JPUBLISHED');
		$options[]	= JHtml::_('select.option', '0', 'JUNPUBLISHED');
		$options[]	= JHtml::_('select.option', '-2', 'JTRASHED');

		return $options;
	}
	public function checkIcons(){
		$db =& JFactory::getDbo();
		
		$category = EasyquickiconsHelper::standardCategory();
		
		$query = $db->getQuery(true);
		// Select some fields
		$query->select('e.id, e.name, c.title as category');
		// From the easyquickicons table
		$query->from('#__easyquickicons as e');
		$query->join('LEFT', '#__categories as c ON e.catid = c.id');
		
		$query->where('e.published = 1');
		
		$db->setQuery($query);
		$db->query();
		
		$items = $db->loadObjectList();
		//var_dump($items);
		if(count($items) >= 23){
			return false;
		} else {
			return true;
		}
	}
	/**
	 * Returns an array quickicons items.
	 *
	 * @return	array of items from easyquickicons db
	 */
	public static function eqiItems()
	{
		$db =& JFactory::getDbo();

		$query = $db->getQuery(true);
		// Select some fields
		$query->select('e.*');
		// From the easyquickicons table
		$query->from('#__easyquickicons as e');
		
		//join over the categories
		$query->select('c.title as category, c.id as cid');
		$query->join('LEFT', '#__categories AS c ON e.catid = c.id');
		//query conditions
		$query->where('e.published=1');
		$query->where('c.published=1');
		//order the result
		$query->order('e.ordering', 'ASC');
			
		$db->setQuery($query);
		$db->query();
			
		$items = $db->loadObjectList();

		return $items;
	}
	/**
	 * Returns a component name link to the quickicon.
	 * @param the id of the quickicon
	 * @return	the component name
	 */
	public static function eqiComponentName($id = null)
	{
		$db =& JFactory::getDbo();

		$query = $db->getQuery(true);
		// Select some fields
		$query->select('link');
		// From the easyquickicons table
		$query->from('#__easyquickicons');
		$query->where(array('published=1', 'id=' . $id));
			
		$db->setQuery($query);
		$db->query();

		$item = $db->loadObject();

		// find the com_ part of the link string to put in the access array
		$spos = stripos ($item->link, 'option=com_');
		$component = '';
		if ($spos !== false ) {
			// option_com string found, find the end of the component string
			$component = substr ($item->link, $spos + 7);

			// check for & after the component name
			$epos = stripos ($component, '&');
			if ($epos !== false) {
				// & found, remove the remaining component string
				$component = substr ($component, 0, $epos);
			}

		}

		return $component;
	}
	/**
	 * Returns the icon path of the quickicon.
	 * @param the id of the quickicon
	 * @param image published state
	 * @return	the icon path
	 */
	public static function eqiImage($id = null, $published = null)
	{
		$db =& JFactory::getDbo();

		$query = $db->getQuery(true);
		// Select some fields
		$query->select('custom_icon,icon_path,icon');
		// From the easyquickicons table
		$query->from('#__easyquickicons');

		$query->where(array('id=' . $db->quote($id), $published == null ? 'published IN (0,1,2,-2)' : 'published=' . $db->quote($published)));
			
		$db->setQuery($query);
		$db->query();

		$row = $db->loadObject();

		if($row->custom_icon == 1){
				
			$chk_img = stripos(strtolower($row->icon_path), 'http');
				
			if($chk_img === false){ //custom upload image
				$img_link = JURI::root() . trim($row->icon_path);
			} else { // external image link
				$img_link = trim($row->icon_path);
			}

		} else {
			$img_link = '/header/' . trim($row->icon);
		}

		return $img_link;
	}
	/**
	 * Fix task and layout link
	 * @param the link to check
	 * @return the fixed link
	 */
	public static function eqiCheckLink($uri = '')
	{
		$link = JFactory::getURI($uri) ;
		if($link->getVar('layout') == 'edit'){
			$link->setVar('task', "{$link->getVar('view')}.{$link->getVar('layout')}") ;
			$link->delVar('view');
			$link->delVar('layout');
		}
		
		return JRoute::_($link->toString());
	}
	/**
	 * Returns the list of easyquickicons categories.
	 */
	public static function eqiCategory(){
		
		$db =& JFactory::getDbo();
		
		$query = $db->getQuery(true);
		// Select some fields
		$query->select('id, title as category');
		// From the easyquickicons table
		$query->from('#__categories');
		$query->where(array('published=1', 'extension=' . $db->quote('com_easyquickicons')));
		$db->setQuery($query);

	    if (!$db->query()) {
			throw new Exception($db->getErrorMsg());
		}
		$result = $db->loadObjectList();
		
		return $result;
	}
	/**
	 * Get the category title for default Joomla! quickicons
	 */
	public static function standardCategory(){
		$db =& JFactory::getDbo();
		
		$query = $db->getQuery(true);
		// Select some fields
		$query->select('title as category');
		// From the easyquickicons table
		$query->from('#__categories');
		$query->where('extension=' . $db->quote('com_easyquickicons'));
		$db->setQuery($query);

	    if (!$db->query()) {
			throw new Exception($db->getErrorMsg());
		}
		$category = $db->loadResult();
		
		return $category;
			
	}
	/**
	 * Get the category title for default Joomla! quickicons
	 */
	public static function standardCategoryId(){
		$db =& JFactory::getDbo();
		
		$query = $db->getQuery(true);
		// Select some fields
		$query->select('id as categoryId');
		// From the easyquickicons table
		$query->from('#__categories');
		$query->where('extension=' . $db->quote('com_easyquickicons'));
		$db->setQuery($query);

	    if (!$db->query()) {
			throw new Exception($db->getErrorMsg());
		}
		$category = $db->loadResult();
		
		return $category;
			
	}
}
