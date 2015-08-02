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
function check()
{
$.msgBox({
   title:"Alert",
   content:"Please first make a selection from the list"
});
}
function checkradio()
{ 
	if (!jQuery('input:checkbox[name=cid[]]:checked').length){
        $.msgBox({
          title:"Alert",
          content:"Please select a lawfirm first."
          });
	   return true;
	 }
	if(!((jQuery('input:checkbox:checked.todoselect-lawfirm').length) || (jQuery('input:checkbox:checked.todocheck').length)))
	{
	  $.msgBox({
          title:"Alert",
          content:"Please select a lawfirm first."
          });
	  return true;
	}
}
//var jq = jQuery.noConflict();

function validateForm(formId)
{
     var formId = document.getElementById(formId);
     var email = jQuery('#client-email1').val();
     if(email == ''){
    	 jQuery('#client-email1').css("border","2px solid red"); return false;}
	 var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;  
	 if(!emailPattern.test(email)){
		  jQuery('#emailerr1').html('Invalid email'); return false;}
}
</script>
<div class="clientprofileproposal">
 <div class="clientprofileproposal-header"><span>
 <?php echo JText::sprintf('COM_LEGALCONFIRM_ADD_NEW_ACCOUNT_FOR',  $this->client->company);?></span>
 </div>
  <fieldset><legend>
  <?php echo JText::_('COM_LEGALCONFIRM_NON_MEMBER_AREA');?>
  </legend>
  <form action="<?php echo JRoute::_('index.php?option=com_legalconfirm&task=clients.nonmembersendmail&id='.$this->client->clientid);?>" name="non-member-form" id="non-member-form" method="post" onsubmit="return validateForm('non-member-form');">
  <div class="non-member-area">
	   <label><?php echo JText::_('COM_LEGALCONFIRM_NON_MEMBER_EMAIL');?></label><span style="color:red;">*</span>
	   <input type="text" name="nonmemberemail" class="required email1" id="client-email1"/>  
	   <span class="submit">
	    <button type="submit" class="button"><?php echo JText::_('COM_LEGALCONFIRM_SUBMIT_BUTTON'); ?></button>
	   </span>
	   <span class="err" id="emailerr1"></span>
  </div>
  <?php echo JHtml::_('form.token');?>
  </form>
  </fieldset>
 <form action="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&tmpl=component&layout=clientproposal&id='.$this->client->clientid); ?>" name="adminForm" id="adminForm" method="post" >

 <?php echo $this->loadTemplate('alreadyselectedlawfirms');?>

      <fieldset><legend><?php echo JText::_('COM_LEGALCONFIRM_SEARCH_LAW_FIRM_SELECT');?></legend>
   		<div class="filter-search-fltlft">
			<input type="text" name="filter_search" id="filter_search" size="14" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_LEGALCONFIRM_SEARCH_IN_LAWFIRM'); ?>" />
			<button type="submit" class="button"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" class="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
 	<table class="adminlist" id="adminList">
		<thead><?php echo $this->loadTemplate('head');?></thead>
		<tbody><?php echo $this->loadTemplate('body');?></tbody>
		<tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
		
    </table>
    </fieldset>
  <div class="submit">
<!--   <input type="submit" name="next" value="<?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_NEXT');?>" >-->
  <a href="javascript::void();" onclick="if (checkradio()){}else{ Joomla.submitbutton('clientprofile.lawfirmoffices');}"><?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_NEXT');?></a>
  </div> 
   <input type="hidden" name="id" value="<?php echo $this->client->clientid;?>" >
   <input type="hidden" name="task" value="">
   <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
   <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
   <?php echo JHtml::_('form.token'); ?>
 </form>
    
</div>
<script>
jQuery(document).ready(function(){
	jQuery("#alreadyselected-lawfirm").click(function () {
        if (jQuery("#alreadyselected-lawfirm").is(':checked')) {
        	jQuery(".todocheck").prop("checked", true);
        } else {
        	jQuery(".todocheck").prop("checked", false);
        }
    });
	jQuery("#current-selected-lawfirm").click(function () {
        if (jQuery("#current-selected-lawfirm").is(':checked')) {
        	jQuery(".todoselect-lawfirm").prop("checked", true);
        } else {
        	jQuery(".todoselect-lawfirm").prop("checked", false);
        }
    });
}); 
</script>
