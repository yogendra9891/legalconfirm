<?php
/**
 * @version     1.0.0
 * @package     com_legalconfirm
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Abhishek Gupta <abhishek.gupta@daffodilsw.com> - http://
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Legalconfirm helper.
 */
class LegalconfirmHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = '')
	{
//		JSubMenuHelper::addEntry(
//			JText::_('COM_LEGALCONFIRM_TITLE_LAWFIRMS'),
//			'index.php?option=com_legalconfirm&view=lawfirms',
//			$vName == 'lawfirms'
//		);
		JSubMenuHelper::addEntry(
			JText::_('COM_LEGALCONFIRM_REPORTS'),
			'index.php?option=com_legalconfirm&view=reports',
			$vName == 'reports'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_LEGALCONFIRM_PAYMENTS'),
			'index.php?option=com_legalconfirm&view=payments',
			$vName == 'payments'
		);
		
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_legalconfirm';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}
