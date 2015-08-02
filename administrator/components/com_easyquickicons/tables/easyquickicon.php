<?php
/**
 * @package			Easy QuickIcons
 * @version			$Id: easyquickicon.php 92 2012-10-27 13:33:01Z allan $
 * @author			Allan <allan@awynesoft.com>
 * @link			http://www.awynesoft.com
 * @copyright		Copyright (C) 2012 AwyneSoft.com All Rights Reserved
 * @license			GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

//no direct access
defined('_JEXEC') or die('Restricted access');
// import Joomla table library
jimport('joomla.database.table');
class EasyquickiconsTableEasyquickicon extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$_db)
	{
		parent::__construct( '#__easyquickicons', 'id', $_db );
	}
	/**
	 * Overriden store method to set dates.
	 *
	 * @param	boolean	True to update fields even if they are null.
	 *
	 * @return	boolean	True on success.
	 * @see		JTable::store
	 * @since	1.6
	 */
	public function store($updateNulls = false)
	{
		// Initialise variables.
		$date = JFactory::getDate()->toSql();

		if ($this->id) {
			// Existing item
			$this->modified_date = $date;
		} else {
			// New record.
			$this->created_date = $date;
		}

		return parent::store($updateNulls);
	}
}
