<?php
/**
 * @version     1.0.0
 * @package     com_legalconfirm
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Abhishek Gupta <abhishek.gupta@daffodilsw.com> - http://
 */
// no direct access
defined('_JEXEC') or die;
JHTML::_('behavior.modal');
$app = JFactory::getApplication();
$cid = JRequest::getVar('id');
?>
<div class="auditernotesviewwrraper">
<fieldset><legend><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_AUDITOR_NOTES');?></legend>
<div class="auditornotes-view"><?php echo LegalconfirmHelper::auditornotesview($cid)->notes;?><!-- finding the notes of a auditor for a client -->
</div>
<div class="submit"><a
	href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&tmpl=component&layout=auditornotesedit&id='.$cid);?>"><?php echo JText::_('COM_LEGALCONFIRM_EDIT_ITEM');?></a>
</div>
</fieldset>
</div>
