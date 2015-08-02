<?php
/**
 * @package		Easy QuickIcons
 * @author		Allan <allan@awynesoft.com>
 * @link		http://www.awynesoft.com
 * @copyright	Copyright (C) 2012 AwyneSoft.com All Rights Reserved
 * @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version		$Id: default_copyright.php 24 2012-09-22 05:30:05Z allan $
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
$version = ' v' . $this->manifest->version;
?>

<div class="eqi_footer">
	<p>
		<?php echo JText::_('COM_EASYQUICKICONS_FOOTER_1');?><br/>
		<?php echo '<a href="http://extensions.joomla.org/extensions/administration/extensions-quickicons/21688" target="_blank">' . 
				JText::_('COM_EASYQUICKICONS') . $version .'</a>&nbsp;&nbsp;|&nbsp;&nbsp;' .
				'<a href="http://www.awynesoft.com/" target="_blank">www.AwyneSoft.com</a>';
		?>
	</p>
</div>
