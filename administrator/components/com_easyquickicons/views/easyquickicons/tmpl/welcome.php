<?php
/**
 * @package			Easy QuickIcons
 * @version			$Id: welcome.php 87 2012-10-27 13:29:07Z allan $
 *
 * @author			Allan <allan@awynesoft.com>
 * @link			http://www.awynesoft.com
 * @copyright		Copyright (C) 2012 AwyneSoft.com All Rights Reserved
 * @license			GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
 
?>

<div class="width-60 fltlft">
<fieldset class="adminform">
	<legend style="font-size:21px;"><?php echo JText::_( 'COM_EASYQUICKICONS' ) . ' v'.$this->manifest->version;; ?></legend>
	<h2><?php echo JText::_('COM_EASYQUICKICONS_DESCRIPTION_TITLE');?></h2>
	<ul><li><?php echo JText::_('COM_EASYQUICKICONS_DESCRIPTION_MSG');?></li></ul>
	<h2><?php echo JText::_('COM_EASYQUICKICONS_DOCUMENTATION');?></h2>
	<ul>
		<li><?php echo JText::_('COM_EASYQUICKICONS_DOCUMENTATION_MSG_1');?><strong><a href="http://www.awynesoft.com/documentations/easy-quickicons-documentation.html" target="_blank">http://www.awynesoft.com/documentations/easy-quickicons-documentation.html</a></strong></li>
		<li><?php echo JText::_('COM_EASYQUICKICONS_DOCUMENTATION_MSG_2');?><strong><a href="mailto:support@awynesoft.com">support@awynesoft.com</a></strong></li>
	</ul>
	</fieldset>

</div>

