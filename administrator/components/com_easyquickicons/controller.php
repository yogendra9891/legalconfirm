<?php
/**
 * @package			Easy QuickIcons
 * @version			$Id: controller.php 96 2012-10-27 13:58:36Z allan $
 * @author			Allan <allan@awynesoft.com>
 * @link			http://www.awynesoft.com
 * @copyright		Copyright (C) 2012 AwyneSoft.com All Rights Reserved
 * @license			GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * General Controller of Easy QuickIcons component
 */

class EasyquickiconsController extends JController
{
	/**
	 * @var		string	The default view.
	 * @since	1.6
	 */
	protected $default_view = 'easyquickicons';
	/**
	 * display task
	 *
	 * @return void
	 */
	function display($cachable = false, $urlparams = false) 
	{
		require_once JPATH_COMPONENT.'/helpers/easyquickicons.php';
		
		// Load the submenu.
		EasyquickiconsHelper::addSubmenu(JRequest::getCmd('view', 'easyquickicons'));

		$view		= JRequest::getCmd('view', 'easyquickicons');
		$layout 	= JRequest::getCmd('layout', 'default');
		$id			= JRequest::getInt('id');

		// Check for edit form direct access with incorrect id.
		
		if ($view == 'easyquickicon' && $layout == 'edit' && !$this->checkEditId('com_easyquickicons.edit.easyquickicon', $id)) {
			// Don't allow direct link edit.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_easyquickicons', false));

			return false;
		} 
		// call parent behavior
		parent::display($cachable);
	}
}