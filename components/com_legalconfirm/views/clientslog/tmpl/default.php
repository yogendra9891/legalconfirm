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

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$clientid   = JRequest::getVar('id');
?>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#request-form-button').click(function(){  
	//	 if(jQuery('#template-input').val() == '')
	//	 {
	//		$.msgBox({
	 //	        title:"Alert",
	// 	        content:"Please first prepare template."
	//	       });
	//		 return false;
	//	 }	
		 var checkstr1 =  callcheck(); 
		 
		 if(checkstr1 == true){ 
		  return true;
		 }
	
	  return false;
	});
});
function callcheck()
{ 
	$.msgBox({
        title: "Are You Sure",
        content: "you want to send the proposal to client.",
        type: "confirm",
        buttons: [{ value: "Yes" }, { value: "No" }, { value: "Cancel"}],
        success: function (result) {
        if (result == "Yes") { 
         jQuery('#request-form').submit();
        }
       }
      });
  return false;
}
</script>
<div class="clientname"><h3><?php echo ucfirst($this->getProfile($clientid)->company); ?></h3>

</div>
<div  id="tab-container" class="auditor-clientlogs-wrapper">
 <ul class='etabs'>
   <li class='tab'><a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&id='.$clientid);?>"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_PROFILE');?></a></li>
   <li class='tab'><a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientlawfirmadd&id='.$clientid);?>"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_LAWYER');?></a></li>
   <li class='tab client_profile'><a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientslog&id='.$clientid);?>"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_AUTHORIZATON_CODE');?></a></li>
   <li class='tab'><a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=lawfirmslog&id='.$clientid);?>"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_INITIATE');?></a></li>
         <li class='tab'><a class="sentoattorney" href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=lawfirmsreceivedlog&id='.$clientid);?>"><?php echo JText::_('Received');?></a></li>
 </ul>
<div class="client-proposal-logs"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_PROPOSAL_LOGS');?></div>
 <form action="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientslog&layout=default&id='.$this->client->clientid);?>" name="adminForm" id="adminForm" method="post">
 	<div class="clr"> </div>
	<table class="adminlist">
		<thead><?php echo $this->loadTemplate('head');?></thead>
		<tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
		<tbody><?php echo $this->loadTemplate('body');?></tbody>
	</table> 
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<input type="hidden" name="id" value="<?php echo $clientid;?>" >
       <?php echo JHtml::_('form.token');?>
 </form>
 <div class="authorization-inner">
		<form action="<?php echo JRoute::_('index.php?option=com_legalconfirm&task=clientprofile.checkrequestdata&id='.$clientid);?>" name="request-form" id="request-form" method="post">
			<div class="submit">
			<button type="submit" id="request-form-button"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_AUTHORIZATON_CODE_REQUEST');?></button>
			</div>
			<?php echo JHtml::_('form.token');?>
			 <div class="submit client-logs-next">
 <a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&task=clientprofile.checkrequestpending&id='.$clientid);?>" ><?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_NEXT');?><img src="<?php echo JURI::base().'templates/legalconfirm/images/next_button.png'?>"></a>
 </div>
		</form>

	</div>


</div>
