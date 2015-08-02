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
//get usa states
$usa_states = $this->getUsaStates();
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
  <div class="auditor-clients--add-header"><span><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_COMPANY_SIGNER_EDIT_SIGNER');?></span></div>
 <span><?php echo JText::_('COM_LEGALCONFIRM_COMPANY_SIGNER_INFORMATION');?></span>
  <div class="auditor-clients-signer-information">
  
   <div class="first-information">
       
		   <label class="" for="client-signertitle"><?php echo JText::_('COM_LEGALCONFIRM_SIGNER_TITLE');?><span style="color:red;">*</span></label>
		   <input type="text" name="signertitle" id="client-signertitle" class="inputbox required" value="<?php echo $this->signer->signertitle;?>" />
		   <label class="" for="client-signerfname"><?php echo JText::_('COM_LEGALCONFIRM_SIGNER_FIRSTNAME');?><span style="color:red;">*</span></label>
		   <input type="text" name="fname" id="client-signerfname" class="inputbox required" size="15" value="<?php echo $this->signer->fname;?>" />
		   <label class="" for="client-signerlname"><?php echo JText::_('COM_LEGALCONFIRM_COMPANY_SIGNER_LASTNAME');?></label> 
		   <input type="text" name="lname" id="client-signerlname" class="clientcompnayinput" size="15"  value="<?php echo $this->signer->lname;?>" />
	   
	 
		   <label class="" for="client-signeraddress1"><?php echo JText::_('COM_LEGALCONFIRM_COMPANY_SIGNER_ADDRESS1');?><span style="color:red;">*</span></label>
		   <input type="text" name="address1" id="client-signeraddress1" class="inputbox required" value="<?php echo $this->signer->address1;?>"/>
		   <label class="" for="client-signeraddress2"><?php echo JText::_('COM_LEGALCONFIRM_COMPANY_SIGNER_ADDRESS2');?></label>
		   <input type="text" name="address2" id="client-signeraddress2" class="clientcompnayinput" value="<?php echo $this->signer->address2;?>" /><br>
	   
	   <!--
	   <div>
		   <input type="checkbox" name="locatedin" style="margin-left: 10px;" > <label class="" for="client-locatedin" style="width: 230px;"><?php echo JText::_('COM_LEGALCONFIRM_COMPANY_SIGNER_LOCATEDIN');?></label>
	   </div>
	   -->
	   
		   <label class="" for="client-city"><?php echo JText::_('COM_LEGALCONFIRM_COMPANY_SIGNER_CITY');?><span style="color:red;">*</span></label>
		   <input type="text" name="city" id="client-city" class="inputbox required" value="<?php echo $this->signer->city;?>" />
		   <label class="" for="client-state"><?php echo JText::_('COM_LEGALCONFIRM_COMPANY_SIGNER_STATE');?><span style="color:red;">*</span></label>
		   <!--<input type="text" name="state" id="client-state" class="inputbox required" value="<?php echo $this->signer->state;?>" />-->
		   		   <select name ="state" id="client-state">
<?php 
foreach($usa_states as $usastate){
	if($this->signer->state == $usastate->name) $signer='selected = true;';
	else $signer = '';
	?>
	<option value="<?php echo $usastate->name; ?>" <?php echo $signer; ?> ><?php echo $usastate->name; ?></option>
	<?php
}

?>
</select>
		   <label class="" for="client-zipcode"><?php echo JText::_('COM_LEGALCONFIRM_COMPANY_SIGNER_ZIPCODE');?><span style="color:red;">*</span></label>
		   <input type="text" name="zipcode" id="client-zipcode" class="zip required" value="<?php echo $this->signer->zipcode;?>" /><span id="errzip" class="err" ></span>
	   
   </div>
  </div>
  <div class="second-information">
	   <label class="" for="client-email1"><?php echo JText::_('COM_LEGALCONFIRM_SIGNER_EMAIL');?><span style="color:red;">*</span></label>
	   <input type="text" name="email" id="client-email1" class="required email1" value="<?php echo $this->signer->email;?>"/><br/>
	   <label class="clientemail2" for="client-email2"><?php echo JText::_('COM_LEGALCONFIRM_SIGNER_EMAIL1');?><span style="color:red;">*</span></label>
	   <input type="text" name="email1" id="client-email2" class="required email2" value="<?php echo $this->signer->email;?>"/> <span class="err" id="emailerr2"></span>
	   <label class="" for="client-signerphone"><?php echo JText::_('COM_LEGALCONFIRM_COMPANY_SIGNER_PHONE');?><span style="color:red;">*</span></label>
	   <input type="text" name="phone" id="client-signerphone" class="inputbox required" value="<?php echo $this->signer->phone;?>"/><br/>
	   <label class="" for="client-signerext"><?php echo JText::_('COM_LEGALCONFIRM_COMPANY_SIGNER_EXT');?></label>
	   <input type="text" name="ext" id="client-signerext" class="clientcompnayinput" value="<?php echo $this->signer->ext;?>" /><br/>
	   <label class="" for="client-signerfax"><?php echo JText::_('COM_LEGALCONFIRM_COMPANY_SIGNER_FAX');?></label>
	   <input type="text" name="fax" id="client-signerfax" class="clientcompnayinput" value="<?php echo $this->signer->fax;?>" /><br/>
	   <label class="" for="client-signerlanguage"><?php echo JText::_('COM_LEGALCONFIRM_COMPANY_SIGNER_LANGUAGE');?></label>
	   <input type="text" name="language" id="client-signerlanguage" class="clientcompnayinput" value="<?php echo $this->signer->language;?>" />
  

<div class="add-client-button">
	<input type="hidden" name="task" value="clientprofile.editsigner">
	<input type="hidden" name="signerid" value="<?php echo $this->signer->id;?>" >
	<input type="hidden" name="cid" value="<?php echo $this->client->clientid;?>" >
	<?php echo JHtml::_('form.token'); ?>
	<div class="requiredfield">
	<span style="color:red;">*&nbsp;</span><?php echo JText::_('COM_LEGALCONFIRM_COMPANY_SIGNER_DENOTES_MANDETORY_FILED');?></div>
	<div class="add-client-button-submit">
		<div class="submit">
			<input type="submit" value="<?php echo JText::_('COM_LEGALCONFIRM_COMPANY_SIGNER_SAVE'); ?>">
		</div>
	<!--	<div class="submit">
			<input type="button" value="<?php echo JText::_('COM_LEGALCONFIRM_COMPANY_SIGNER_CLOSE'); ?>" size="10" onclick="closepopup();">
	    </div> -->
    </div>
</div></div>
</div>
</form>
