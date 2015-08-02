<?php
/**
 * @version     1.0.0
 * @package     com_legalconfirm
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Abhishek Gupta <abhishek.gupta@daffodilsw.com> - http://
 */
// no direct access
defined('_JEXEC') or die('Restricted Access'); 
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.keepalive');
require_once JPATH_COMPONENT.'/helpers/legalconfirm.php';
// load tooltip behavior
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$user		 = JFactory::getUser();
$userId		 = $user->get('id');

$listOrder   = $this->state->get('list.ordering');
$listDirn    = $this->state->get('list.direction');
?>
<script>
function closepopup() {
	parent.SqueezeBox.close();
}
</script>
<div class="auditor-clientlist-add-wrapper">
<div class="auditor-clients--add-header"><span><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_SELECT');?></span></div>
<form action="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clients&layout=clientlist&tmpl=component'); ?>" name="adminForm" id="adminForm" method="post">
	<fieldset id="filter-bar"><!--
		<div class="filter-select fltrt">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JFILTER_COMPANY_LABEL'); ?></label>
			<select name="filter_company" class="inputbox" onchange="this.form.submit()">
				<?php echo JHtml::_('select.options',  LegalconfirmHelper::getCompanyOptions(), 'value', 'text', $this->state->get('filter.company'), true);?>
			</select>
		</div>
		--><div class="filter-search fltlft"><?php echo JText::_('JFILTER_COMPANY_LABEL'); ?>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_LEGALCONFIRM_SEARCH_IN_COMPANY_TITLE'); ?>" />
		    <span class="submit"><input type="submit" value="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" ></span>
			<span class="submit"><input type="button" onclick="document.id('filter_search').value='';this.form.submit();" value="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" ></span>
		</div>

	</fieldset>
	
	<div class="clr"> </div>
	<table class="adminlist">
		<thead><?php echo $this->loadTemplate('head');?></thead>
		<tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
		<tbody><?php echo $this->loadTemplate('body');?></tbody>
	</table>
	<div class="opreration_button">
	<div class="submit">
		<input class="deleteclient" type="button" value="<?php echo JText::_('COM_LEGALCONFIRM_CLIENT_SELECTED_DELETE');?>" onclick="if (document.adminForm.boxchecked.value==0){$.msgBox({
	          title:'Alert',
	          content:'Please select a client first.'
	          });}else{ Joomla.submitbutton('clients.deleteclient');}" >
	</div>	
	<div class="submit">
	    <input class="closepopup" type="button" value="<?php echo JText::_('COM_LEGALCONFIRM_COMPANY_SIGNER_CLOSE');?>" onclick="closepopup();">
	</div>
	</div>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
</div>