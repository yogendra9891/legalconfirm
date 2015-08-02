<?php
/**
 * @author			Allan <allan@awynesoft.com>
 * @link			http://www.awynesoft.com
 * @copyright		Copyright (C) 2012 AwyneSoft.com All Rights Reserved
 * @license			GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version			$Id: mod_easyquickicons.php 37 2012-09-25 15:00:56Z allan $			
 */

// No direct access.
defined('_JEXEC') or die;

$document = JFactory::getDocument();
$document->addStyleSheet("components/com_easyquickicons/assets/css/icons.css");

require_once dirname(__FILE__).'/helper.php';

$buttons = modEasyQuickIconsHelper::getButtons($params);

require JModuleHelper::getLayoutPath('mod_easyquickicons', $params->get('layout', 'default'));
