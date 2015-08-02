<?php
/**
 * @package			Easy QuickIcons
 * @version			$Id: easyquickicons.php 94 2012-10-27 13:54:03Z allan $
 * @author			Allan <allan@awynesoft.com>
 * @link			http://www.awynesoft.com
 * @copyright		Copyright (C) 2012 AwyneSoft.com All Rights Reserved
 * @license			GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Utility class for creating HTML Grids
 *
 * @static
 * @package		Joomla.Administrator
 * @subpackage	com_easyquickicons
 * @since		1.6
 */
class JHtmlEasyquickicons
{
	/**
	 * @param	int $value	The state value.
	 * @param	int $i
	 * @param	string		An optional prefix for the task.
	 * @param	boolean		An optional setting for access control on the action.
	 */
	
	public static function published($value = 0, $i, $canChange = true)
	{
		// Array of image, task, title, action
		$states	= array(
			1	=> array('tick.png',		'easyquickicons.unpublish',	'JENABLED',	'COM_EASYQUICKICONS_DISABLE_ITEM'),
			0	=> array('publish_x.png',	'easyquickicons.publish',		'JDISABLED',	'COM_EASYQUICKICONS_ENABLE_ITEM'),
			2	=> array('disabled.png',	'easyquickicons.unpublish',	'JARCHIVED',	'JUNARCHIVE'),
			-2	=> array('trash.png',		'easyquickicons.publish',		'JTRASHED',	'COM_EASYQUICKICONS_ENABLE_ITEM'),
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[0]);
		$html	= JHtml::_('image', 'admin/'.$state[0], JText::_($state[2]), NULL, true);
		if ($canChange) {
			$html	= '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.JText::_($state[3]).'">'
					. $html.'</a>';
		}

		return $html;
	}

}
