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
$this->requestdata = $app->getUserState('com_legalconfirm.selectedlawfirmoffices.data');
$this->requestclientid = $app->getUserState('com_legalconfirm.clientprofile.clientid');
$document =& JFactory::getDocument();
$document->addScript(JURI::base(). 'components/com_legalconfirm/assets/js/jquery.min.js');
?>
<script>
//var jq = jQuery.noConflict();
jQuery(document).ready(function(){
 jQuery('#deletesigner').click(function(){

	$.msgBox({
        title: "Are You Sure",
        content: "Are you sure to remove the signer?",
        type: "confirm",
        buttons: [{ value: "Yes" }, { value: "No" }, { value: "Cancel"}],
        success: function (result) {
       if (result == "Yes") {
           jQuery('#adminForm').submit();
        }
       }
      });
	
 });
 jQuery('#adminFormnotes').submit(function(){
    if(jQuery('#auditornotes').val() == ''){ 
  	 $.msgBox({
  	 title:"Alert",
  	 content:"Please add a note."
	});
        return false;}
 });

 //tabing code..
// $('#tab-container').easytabs();
// jQuery('#next-attorney').click(function(){
//	 $('#tab-container').easytabs('select', '#attorney');
// });
// jQuery('#next-contact-client').click(function(){
//	 $('#tab-container').easytabs('select', '#contactclient');
// });
// jQuery('#next-sent-attorney').click(function(){
//	 $('#tab-container').easytabs('select', '#sentattorney');
// });
// jQuery('#last-tab').click(function(){
//	 $('#tab-container').easytabs('select', '#clientprofile');
// });
 //tabing code end..
});

</script>
  <style>
    /* Example Styles for Demo */

  </style>
<div class="clientname"><h3><?php echo ucfirst($this->client->company); ?></h3>

</div>
<!-- tab starting -->
<div class="clientprofilewrapper" id="tab-container">
 <div class='etabs'><ul>
   <li class='tab client_profile'><a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&id='.$this->client->clientid);?>"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_PROFILE');?></a></li>
   <li class='tab'><a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientlawfirmadd&id='.$this->client->clientid);?>"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_LAWYER');?></a></li>
   <li class='tab'><a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientslog&id='.$this->client->clientid);?>"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_AUTHORIZATON_CODE');?></a></li>
   <li class='tab'><a class="sentoattorney1" href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=lawfirmslog&id='.$this->client->clientid);?>"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_INITIATE');?></a></li>
      <li class='tab'><a class="sentoattorney" href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=lawfirmsreceivedlog&id='.$this->client->clientid);?>"><?php echo JText::_('Received');?></a></li>
 </ul>
 <div class="submit"><a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=auditors');?>" id="button"><img src="<?php echo JURI::base().'templates/legalconfirm/images/back_button.png'?>"><?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_BACK');?></a>
</div>
 </div>
 <div class='panel-container'>

<div class="companyname">
<label class="company-name"><span class="authorised-signer-company"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_COMAPNYNAME');?></span>
	<a
		href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&tmpl=component&layout=editcompany&id='.$this->client->clientid);?>"
		class="modal" rel="{handler: 'iframe', size: {x: 500, y: 300}}"><img src="<?php echo JURI::base().'templates/legalconfirm/images/edit_button.png'?>" alt="edit" class="edit-button" title="edit company"></a>
</label>

	<span><?php echo $this->client->company; ?></span>
	</div>
	<div class="company-name-result">
	<label class="company-name"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_COMAPNYENGAGEMENTNO');?></label>
	<span><?php echo $this->client->engagementno; ?></span></div>
	<?php if(!empty($this->client->website)):?>
	<div class="companywebsite"><label class="company-name"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_COMAPNYWEBSITE');?></label>
	<?php $url = $this->client->website; ?>
	<span><?php echo $this->client->website;?></span> </div><?php endif;?>
	<div class="signerarea"><?php if($this->signer->id):?>
	<div class="testclass"><label class="company-name"><span class="authorised-signer-company"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_COMPANY_AUTHORIZED_SIGNER');?></span>
	<a
		href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&tmpl=component&layout=editsigner&id='.$this->client->clientid.'&signerid='.$this->signer->id);?>"
		class="modal" rel="{handler: 'iframe', size: {x: 730, y:470}}"><img src="<?php echo JURI::base().'templates/legalconfirm/images/edit_button.png'?>" alt="edit" class="edit-button" title="edit signer"></a>
	
	<form
		action="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&id='.$this->client->clientid);?>"
		name="adminForm" id="adminForm" method="post"><a href="#"
		id="deletesigner"><img src="<?php echo JURI::base().'templates/legalconfirm/images/remove_button.png'?>" class="delelte-signer" alt="delete" title="delete signer"></a>
	<input type="hidden" name="task" value="clientprofile.deletesigner"> <input
		type="hidden" name="signerid" value="<?php echo $this->signer->id;?>">
	<?php echo JHtml::_('form.token');?></form>
	</label>
	<span><?php echo $this->signer->fname. ' '. $this->signer->lname; ?></span></div>
	<?php else: ?>
	<div class="submit-signeradd"><!-- adding a new signer... -->
	<?php echo JText::_('COM_LEGALCONFIRM_CLIENT_COMPANY_SIGNER_ADD');?>
	 <a
		href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&tmpl=component&layout=addsigner&id='.$this->client->clientid);?>"
		class="modal" rel="{handler: 'iframe', size: {x: 700}}" id="button">
		<img src="<?php echo JURI::base().'templates/legalconfirm/images/signer_add_button.png'?>" alt="add signer" title="add signer">
		</a>
	</div>
	<?php endif; ?></div>
	<div class="submit-wrapper">
	<div class="submit">
	<a id="next-attorney" href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientlawfirmadd&id='.$this->client->clientid);?>"><?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_NEXT');?><img src="<?php echo JURI::base().'templates/legalconfirm/images/next_button.png'?>"></a></div></div>


<!--<div class="lawyerarea" id="attorney">
  <div class="lawyer-inner">
	<div class="submit"><a
		href="<?php echo JRoute::_('index.php?option=com_legalconfirm&task=clientprofile.checkSigner&id='.$this->client->clientid);?>"
		class="modal" rel="{handler: 'iframe', size: {x: 700}}" id="button"> <?php echo JText::_('COM_LEGALCONFIRM_CLIENT_LAWYER_ADD');?>
	</a> <a
		href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=lawfirms&tmpl=component&id='.$this->client->clientid);?>"
		class="modal" rel="{handler: 'iframe', size: {x: 700}}" id="button"> <?php echo JText::_('COM_LEGALCONFIRM_CLIENT_LAWYER_VIEW_ALL');?></a>
	</div>
	<?php if($this->requestdata[0]['lawfirm'] > 0 && ($this->client->clientid == $this->requestclientid)):?>
	
	<?php endif;?>  if we have added the lawfirm for proposal for current client and values are in session then we will show the selected lawfirms template 
	<?php if($this->requestclientid && $this->requestdata[0]['lawfirm']):
	if($this->requestclientid == $this->client->clientid):
	echo $this->loadTemplate('lawfirmaddremove');
	?>
	<div class="submit"><a
		href="<?php echo JRoute::_('index.php?option=com_legalconfirm&task=clienttemplate.checksigner&id='.$this->client->clientid);?>"
		class="modal" rel="{handler: 'iframe', size: {x: 700, y: 550}, scrollbars: 'no'}"> <?php echo JText::_('COM_LEGALCONFIRM_CLIENT_SEE_TEMPLATE');?>
	</a></div><?php 
	endif;
	endif;?></div>
	<div class="" style="margin-top: 30px;">
	<a id="next-contact-client" href="#"><?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_NEXT');?></a>
	</div>
</div>


<div class="authorizationcode" id="contactclient">
	 <div class="authorization-inner">
		<form action="<?php echo JRoute::_('index.php?option=com_legalconfirm&task=clientprofile.checkrequestdata&id='.$this->client->clientid);?>" name="request-form" id="request-form" method="post">
			<div class="submit">
			<button type="submit" id="request-form-button"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_AUTHORIZATON_CODE_REQUEST');?></button>
			<a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientslog&tmpl=component&id='.$this->client->clientid);?>" class="modal" rel="{handler: 'iframe', size: {x: 700}}"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_VIEW_LOGS');?></a>
			</div>
			<?php echo JHtml::_('form.token');?>
		</form>
	</div>
	<a id="next-sent-attorney" href="#"><?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_NEXT');?></a>
</div>

<div class="initiate" id="sentattorney">
	<div class="initaite-inner">
		<div class="submit">
	     <a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&task=clientprofile.initiateconfirmation&id='.$this->client->clientid);?>">
	     <?php echo JText::_('COM_LEGALCONFIRM_CLIENT_INITIATE_CONFIRMATIONS');?>
	     </a>
	    </div>
	  	<div class="submit">
	     <a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=lawfirmslog&tmpl=component&id='.$this->client->clientid);?>" class="modal" rel="{handler: 'iframe', size: {x: 700}}">
	     <?php echo JText::_('COM_LEGALCONFIRM_CLIENT_LAWYER_PENDING_TASKS');?>
	     </a>
	    </div>  
	    <div class="submit">
	     <a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=lawfirmsreceivedlog&tmpl=component&id='.$this->client->clientid);?>"  class="modal" rel="{handler: 'iframe', size: {x: 700}}">
	     <?php echo JText::_('COM_LEGALCONFIRM_CLIENT_LAWYER_RECEIVED_TASKS');?>
	     </a>
	    </div>
	 </div>
	<a id="last-tab" href="#"><?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_NEXT');?></a>
</div>
--></div>
</div>
