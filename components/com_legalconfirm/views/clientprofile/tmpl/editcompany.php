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

?>
<script>
function validateForm(formId)
{
 var formId = document.getElementById(formId);
 if(validate(formId))
       {
         var email1 = $('#client-email1').val();
         var email2 = $('#client-email2').val();
         if(email1 != email2){
             $('#emailerr2').html('Email not matched');
           return false;
         }
          //this check triggers the validations
           formId.submit();
           return true;
       }
       else{
        return false;
       }

}
function closepopup() {
	parent.SqueezeBox.close();
}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&id='.$this->client->clientid); ?>" name="clientaddform" id="clientaddform" method="post" onsubmit="return validateForm('clientaddform');">
<div class="auditor-client-add-wrapper">
  <div class="auditor-clients--add-header"><span><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_EDIT_PROFILE');?></span></div>
  <span class="comapny-infor-title">
<?php echo JText::_('COM_LEGALCONFIRM_COMPANY_INFORMATION');?></span>
	   <div class="auditor-clients-company-information">
	   <div class="companyinfoclient">
	   <div class="companyinfoclient-client-info">
		   <label class="client-company" for="client-company"><?php echo JText::_('COM_LEGALCONFIRM_COMPANY_NAME');?><span style="color:red;">*</span></label>
		   <input type="text" name="company" id="client-company" class="inputbox required" size="12" value="<?php echo $this->client->company;?>" /></div>
		<div class="companyinfoclient-client-info">   <label class="client-company" for="client-engagementno"><?php echo JText::_('COM_LEGALCONFIRM_COMPANY_ENGAGEMENT_NUMBER');?><span style="color:red;">*</span></label>
		   <input type="text" name="engagementno" id="client-engagementno" class="inputbox required" size="12" value="<?php echo $this->client->engagementno;?>" /></div><div class="companyinfoclient-client-info">
		   <label class="client-company" for="client-website"><?php echo JText::_('COM_LEGALCONFIRM_COMPANY_CLIENT_WEBSITE');?></label>
		    <input type="text" name="website" id="client-website" class="clientcompnayinput" size="12" value="<?php echo $this->client->website;?>" /></div>
	   </div>
 </div>
<div class="edit-client-company required-fields-company">
	<input type="hidden" name="task" value="clientprofile.editcompany">
	<input type="hidden" name="cid" value="<?php echo $this->client->clientid;?>" >
	<?php echo JHtml::_('form.token'); ?>
	<div class="requiredfield">
	<span style="color:red;">*&nbsp;</span><?php echo JText::_('COM_LEGALCONFIRM_COMPANY_SIGNER_DENOTES_MANDETORY_FILED');?></div>
	<div class="submit">
		<input type="submit" value="<?php echo JText::_('COM_LEGALCONFIRM_COMPANY_SIGNER_SAVE'); ?>">

		<!-- <input type="button" value="<?php echo JText::_('COM_LEGALCONFIRM_COMPANY_SIGNER_CLOSE'); ?>" size="10" onclick="closepopup();"> -->
    </div>
</div>
</div>
</form>
