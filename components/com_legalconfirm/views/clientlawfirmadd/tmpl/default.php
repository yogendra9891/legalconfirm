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
$app = JFactory::getApplication();
$this->requestdata = $app->getUserState('com_legalconfirm.selectedlawfirmoffices.data');//now selected lawfirms..
$this->requestclientid = $app->getUserState('com_legalconfirm.clientprofile.clientid'); 

JHTML::_('behavior.modal'); 
?>
<script>
//var jq = jQuery.noConflict();
function checkradio()
{  
	if (!jQuery('input:checkbox[name=cid[]]:checked').length){ //jQuery('#clickcheck').removeClass('modal');
	       	$.msgBox({
                title:"Alert",
                content:"Please first make a selection from the list."
                });
		return true;
		}
	
	if(!((jQuery('input:checkbox:checked.nowtobeselected').length) || (jQuery('input:checkbox:checked.lasttobeselected').length) || (jQuery('input:checkbox:checked.nowselectedlawfirms-lawfirms').length)))
	{       jQuery('#clickcheck').removeClass('modal');
	       	$.msgBox({
                title:"Alert",
                content:"Please select a lawfirm first."
                });	
	       	jQuery('#clickcheck').removeClass('modal');
	  return true;
	}else{ jQuery('#clickcheck').addClass('modal');
	//	jQuery("#adminForm").attr("action", 'index.php');
		}	
}
function call()
{
	//jQuery('#adminForm').submit();
	//Joomla.submitbutton('clientprofile.lawfirmoffices');
	
}
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
<div class="clientname">
<h3><?php echo ucfirst($this->client->company); ?></h3>

</div>
<div class="clientprofileproposal" id="tab-container">
 <ul class='etabs'>
   <li class='tab'><a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&id='.$this->client->clientid);?>"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_PROFILE');?></a></li>
   <li class='tab client_profile'><a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientlawfirmadd&id='.$this->client->clientid);?>"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_LAWYER');?></a></li>
   <li class='tab'><a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientslog&id='.$this->client->clientid);?>"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_AUTHORIZATON_CODE');?></a></li>
   <li class='tab'><a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=lawfirmslog&id='.$this->client->clientid);?>"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_INITIATE');?></a></li>
         <li class='tab'><a class="sentoattorney" href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=lawfirmsreceivedlog&id='.$this->client->clientid);?>"><?php echo JText::_('Received');?></a></li>
 </ul>
 <div class="attorney-wrapper">
   <div class="non-member-wrraper">
   <span>
  <?php echo JText::_('COM_LEGALCONFIRM_NON_MEMBER_AREA');?></span>
  
  <form action="<?php echo JRoute::_('index.php?option=com_legalconfirm&task=clients.nonmembersendmail&id='.$this->client->clientid);?>" name="non-member-form" id="non-member-form" method="post" onsubmit="return validateForm('non-member-form');">
  <div class="non-member-area">
	   <label><?php echo JText::_('COM_LEGALCONFIRM_NON_MEMBER_EMAIL');?></label>
	   <input type="text" name="nonmemberemail" class="required email1" id="client-email1"/>  
	   <span class="submit">
	    <button type="submit" class="button"><?php echo JText::_('COM_LEGALCONFIRM_SUBMIT_BUTTON'); ?></button>
	   </span>
	   <span class="err" id="emailerr1"></span>
  </div>
  <?php echo JHtml::_('form.token');?>
  </form>
  </div>
  <div class="search-select-lawfirm-wrraper">
 <form action="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientlawfirmadd&id='.$this->client->clientid); ?>" name="adminForm" id="adminForm" method="post" >
<div class="to-be-selected-lawfirms">
    <span class="search-select-lawfirm-title">  <?php echo JText::_('COM_LEGALCONFIRM_SEARCH_LAW_FIRM_SELECT');?></span>
   		<div class="filter-search-fltlft">
			<input type="text" name="filter_search" id="filter_search" size="14" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_LEGALCONFIRM_SEARCH_IN_LAWFIRM'); ?>" />
			<button type="submit" class="button"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" class="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>	
	<div class="tobeselectedlawfirms-leftside">
 	<table class="adminlist" id="tobeselectedlawfirms">
		<thead><?php echo $this->loadTemplate('head');?></thead>
	</table>
	<div id="testingscroll">
	<table>
		<tbody><?php echo $this->loadTemplate('body');?></tbody>
<!--		<tfoot><?php echo $this->loadTemplate('foot');?></tfoot>-->
		
    </table>
    </div>
    </div></div> 
  <div class="submit lawfirm-add-button">
<!--   <input type="submit" name="next" value="<?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_NEXT');?>" >-->
  <a href="javascript:void(0);" class="" id="clickcheck" rel="{handler: 'iframe', size: {x: 700, y: 200}}" 
   onclick="if (checkradio()){}else{Joomla.submitbutton('clientprofile.lawfirmoffices');}"><?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_ADD');?><img src="<?php echo JURI::base().'templates/legalconfirm/images/add_button.png'; ?>"></a>
   </div>
<!--  Joomla.submitbutton('clientprofile.lawfirmofficesaddmore');-->
  
   <input type="hidden" name="id" value="<?php echo $this->client->clientid;?>" >
   <input type="hidden" name="task" value="">
   <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
   <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
   <?php echo JHtml::_('form.token'); ?>

 </form>
 </div>
<div class="now-selectedlawfirm">
<span class="now-selected-attorney-title"><?php echo JText::_('COM_LEGALCONFIRM_ADDED_LAWFIRMS');?></span>
	<?php if($this->requestdata[0]['lawfirm'] > 0 && ($this->client->clientid == $this->requestclientid)):?>
	
	<?php //endif;?>   
	<?php if($this->requestclientid && $this->requestdata[0]['lawfirm']):
	if($this->requestclientid == $this->client->clientid):?>
	<div class="submit see-template-button"><a
		href="<?php echo JRoute::_('index.php?option=com_legalconfirm&task=clienttemplate.checksigner&id='.$this->client->clientid);?>"
		class="modal" rel="{handler: 'iframe', size: {x: 700, y: 550}, iframeOptions: {scrolling: 'no'}}"> <?php echo JText::_('COM_LEGALCONFIRM_CLIENT_SEE_TEMPLATE');?>
	</a></div>
	<?php
	echo $this->loadTemplate('lawfirmaddremove');?>
	<?php 
	endif;
	endif;
	else: ?>
<div class="addmore-wrraper"> 
<div class="tobeselectedlawfirms-rightside" id="add-more-Lawfirm">
<form action="<?php echo JRoute::_('index.php?option=com_legalconfirm&task=clientprofile.removelawfirm&id='.$this->client->clientid);?>" method="post" id="lawfirm_remove_form">
<table>
<tr class="selected-lawfirms-name">
<th width="38%"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_LAWFIRM_NAME');?></th>
<th width="50%"><?php //echo JText::_('COM_LEGALCONFIRM_CLIENT_LAWFIRM_EMAIL');?></th>
<th width="12%"></th>
</tr>
</table>
<div id="nowselectedscrollbar1">
<table><tbody>
<?php 
	$checked = JHTML::_('grid.id', $i, $lawfirmdataarry->lawfirm);
	?>
	<tr class="rownew" id="removelawfirm_<?php echo $lawfirmdataarry->lawfirmid;?>">

		<td width="80%">
	<div style=""><span style="position: relative; top: 11px; padding-right: 10px;"><img src="<?php echo JURI::base().'templates/legalconfirm/images/alert.png'; ?>" height="30px;"></span><span>No Attorney selected.</span></div>
		</td>
			
		<td width="10%">

		</td>
		
		<td width="10%">
			
		</td>
	</tr>
	<?php ?>
	</tbody>
</table>
</div>
</form>
</div>
</div>
	<?php 
	endif;?>   
	</div>
	<div class="submit next-tocontact-client">
	<a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&task=clienttemplate.checktemplate&id='.$this->client->clientid);?>"><?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_NEXT');?><img src="<?php echo JURI::base().'templates/legalconfirm/images/next_button.png'?>"></a>
	</div>
	</div>
</div>
</div>
<script type="text/javascript">
//var jq = jQuery.noConflict();
jQuery(document).ready(function(){ 
	jQuery("#now-to-becurrent-selected-lawfirm").click(function () {
        if (jQuery("#now-to-becurrent-selected-lawfirm").is(':checked')) {
        	jQuery(".nowtobeselected").prop("checked", true);
        } else {
        	jQuery(".nowtobeselected").prop("checked", false);
        }
    });
	jQuery("#nowselectedlawfirms-selected-lawfirm").click(function () {
        if (jQuery("#nowselectedlawfirms-selected-lawfirm").is(':checked')) {
        	jQuery(".nowselectedlawfirms-lawfirms").prop("checked", true);
        } else {
        	jQuery(".nowselectedlawfirms-lawfirms").prop("checked", false);
        }
    });
	jQuery("#alreadyselectedlawfirms-selected-lawfirm").click(function () {
        if (jQuery("#alreadyselectedlawfirms-selected-lawfirm").is(':checked')) {
        	jQuery(".lasttobeselected").prop("checked", true);
        } else {
        	jQuery(".lasttobeselected").prop("checked", false);
        }
    });

//	jq("#sbox-btn-close").click(function(){ alert('dd');
//      //window.parent.location=  window.parent.location;
//	});
//	SqueezeBox.initialize({
//	    onClose: function() {
//	        alert('dont close me');
//	    }
//	});

	jQuery('#testingscroll').slimscroll({
		  height: '253px'
		});
		jQuery('#nowselectedscrollbar1').slimscroll({
			  height: '253px'
			});
});
</script>

