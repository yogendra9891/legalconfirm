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
<script>
var jq = jQuery.noConflict();
jq(document).ready(function(){
jq('#adminFormnotes').submit(function(){
    if(jq('#auditornotes').val() == ''){ alert('please add a notes.');
        return false;}
 });
});
function closepopup() {
	parent.SqueezeBox.close();
}
</script>
<div class="auditoreditnotes">
<form
	action="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&id='.$cid);?>"
	name="adminForm" id="adminFormnotes" method="post">
<fieldset><legend> <?php echo JText::_('COM_LEGALCONFIRM_CLIENT_AUDITOR_NOTES_EDIT');?>
</legend>
<div class="auditornotesedit"><?php $resultnotes = LegalconfirmHelper::auditornotesview($cid);?>
<textarea name="notes" id="auditornotes"><?php echo $resultnotes->notes;?></textarea>
</div>
</fieldset>
<input type="hidden" name="auditornotesid"
	value="<?php echo $resultnotes->id?>"> <input type="hidden" name="lid"
	value="<?php echo $resultnotes->lid;?>"> <input type="hidden"
	name="cid" value="<?php echo $resultnotes->cid;?>"> <input
	type="hidden" name="task" value="clientprofile.editauditornotes"> <?php echo JHtml::_('form.token');?>
<div class="auditoreditbuttonwrapper">
<div class="submit"><input type="submit" class="button"
	value="<?php echo JText::_('COM_LEGALCONFIRM_COMPANY_AUDITOR_NOTES_SAVE'); ?>">

<input type="button" class="button"
	value="<?php echo JText::_('COM_LEGALCONFIRM_COMPANY_AUDITOR_NOTES_CLOSE'); ?>"
	size="10" onclick="closepopup();"></div>
</div>
</form>
</div>
