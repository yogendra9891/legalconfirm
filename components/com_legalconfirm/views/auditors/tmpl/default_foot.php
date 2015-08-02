<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
	<td colspan="3">
	<div class="pagination">	
	<?php echo $this->pagination->getListFooter(); ?>
	</div>
	</td>
	<td colspan="2">
		<div class="opreration_button">
		<div class="add-client-auditor">
	     <a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clients&layout=add&tmpl=component');?>" class="modal" rel="{handler: 'iframe', size: {x: 700, y: 520}}"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_ADD_CLIENTS');?></a>
	    </div>
	<div class="delete-client-auditor">
		<input class="deleteclient" type="button" value="<?php echo JText::_('COM_LEGALCONFIRM_CLIENT_SELECTED_DELETE');?>" onclick="if (document.adminForm.boxchecked.value==0){$.msgBox({
	          title:'Alert',
	          content:'Please select a client first.'
	          });}else{ Joomla.submitbutton('clients.deleteclient');}" >
	</div>
    <span class="recent-view-label">
    <input type="checkbox" name="recent-review" id="recent-review">
    <label for="recent-review"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_RECENTLY_VIEW');?></label>
    </span>
	</div>
	</td>
	
</tr>
