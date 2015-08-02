<?php
/**
 * @package			Easy QuickIcons
 * @version			$Id: easyquickicons.php 96 2012-10-27 13:58:36Z allan $
 * @author			Allan <allan@awynesoft.com>
 * @link			http://www.awynesoft.com
 * @copyright		Copyright (C) 2012 AwyneSoft.com All Rights Reserved
 * @license			GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

//load css
$docs = JFactory::getDocument();
$docs->addStyleSheet(JURI::base() . '/components/com_easyquickicons/assets/css/style.css');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_easyquickicons')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// import joomla controller library
jimport('joomla.application.component.controller');
 
// Get an instance of the controller prefixed by Easyquickicon
$controller = JController::getInstance('Easyquickicons');
 
// Perform the Request task
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();