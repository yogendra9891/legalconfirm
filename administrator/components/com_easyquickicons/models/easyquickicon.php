<?php
/**
 * @package			Easy QuickIcons
 * @version			$Id: easyquickicon.php 36 2012-09-25 14:55:47Z allan $
 *
 * @author			Allan <allan@awynesoft.com>
 * @link			http://www.awynesoft.com
 * @copyright		Copyright (C) 2012 AwyneSoft.com All Rights Reserved
 * @license			GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelform library
jimport('joomla.application.component.modeladmin');
 
/**
 * Easyquickicon Model
 */
class EasyquickiconsModelEasyquickicon extends JModelAdmin
{
		/**
	 * @var    string  The prefix to use with controller messages.
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_EASYQUICKICONS';

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	2.5
	 */
	
	public function getTable($type = 'Easyquickicon', $prefix = 'EasyquickiconsTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	2.5
	 */
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_easyquickicons.easyquickicon', 'easyquickicon',
		                        array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		return $form;
	}
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	2.5
	 */
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_easyquickicons.edit.easyquickicon.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
		}
		return $data;
	}
	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   JTable  &$table  The database object
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function prepareTable(&$table)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		if (empty($table->id))
		{
			// Set the values
			//$table->created	= $date->toSql();
		}
		else
		{
			// Set the values
			//$table->modified	= $date->toSql();
			//$table->modified_by	= $user->get('id');
		}
	}
	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param   JTable  $table  A record object.
	 *
	 * @return  array  An array of conditions to add to add to ordering queries.
	 *
	 * @since   1.6
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		//$condition[] = 'catid = '. (int) $table->catid;
		//$condition[] = 'state >= 0';
		return $condition;
	}
	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to change the state of the record. Defaults to the permission set in the component.
	 * @since	1.6
	 */
	/*
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		// Check the component since there are no categories or other assets.
			return $user->authorise('core.admin', 'com_easyquickicons');

	}
	*/
}