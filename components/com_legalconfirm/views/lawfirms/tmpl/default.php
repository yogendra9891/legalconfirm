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
$document =& JFactory::getDocument();
$document->addScript(JURI::base(). 'components/com_legalconfirm/assets/js/jquery.min.js');
?>
<script>
//var jq = jQuery.noConflict();
function checkradio()
{ 
	if (!jQuery('input:radio[name=cid[]]:checked').val()){
	        $.msgBox({
                title:"Alert",
                content:"Please first make a selection from the list for seeing offices."
              });
		return true;
		}
}
</script>
<div class="clientprofileproposal">
 <div class="clientprofileproposal-header">
 <span><?php echo JText::sprintf('COM_LEGALCONFIRM_LAWYER_LIST');?></span>
 </div>
<fieldset>
<legend><?php echo JText::_('COM_LEGALCONFIRM_SEARCH_LAW_FIRM_SELECT');?></legend>
 <form action="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=lawfirms&tmpl=component&id='.$this->client->clientid); ?>" name="adminForm" id="adminForm" method="post" >
       
   		<div class="filter-search-fltlft">
			<input type="text" name="filter_search" id="filter_search" size="14" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_LEGALCONFIRM_SEARCH_IN_LAWFIRM'); ?>" />
			<button type="submit" class="button"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" class="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
 	<table class="adminlist" id="lawfirms-list">
		<thead><?php echo $this->loadTemplate('head');?></thead>
		<tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
		<tbody><?php echo $this->loadTemplate('body');?></tbody>
    </table>
  <div class="submit">
<!--   <input type="submit" name="next" value="<?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_NEXT');?>" >-->
  <a href="javascript::void();" onclick="if (checkradio()){}else{ Joomla.submitbutton('lawfirms.lawfirmoffices');}"><?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_NEXT');?></a>
  </div> 
   <input type="hidden" name="id" value="<?php echo $this->client->clientid;?>" >
   <input type="hidden" name="task" value="">
   <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
   <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
   <?php echo JHtml::_('form.token'); ?>
 </form>
</fieldset>
</div>
