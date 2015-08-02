<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_legalconfirmusers
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<?php
$document = JFactory::getDocument();
//add css

//get cofig
$config =& JFactory::getConfig();
$auditor = $config->getValue( 'auditor');
$lawfirm = $config->getValue( 'lawfirm');
//get user type from previous page
$user_register_type = JRequest::getVar('type');
?>
<!-- Method to check form validation -->
<script>
function validateForm(formId)
{
 var formId=document.getElementById(formId);
 if(validate(formId))
       {
         var email1 = $('#email1').val();
         var email2 = $('#email2').val();
         if(email1 != email2){
             $('#emailerr2').html('Email not matched');
           return false
         }
          var usertype = checkuser(formId);
          //this check triggers the validations
           //formId.submit();
           return false;
       }
       else{
        return false;
       }

}

//method to check user
function checkuser(formId){
	 var email1 = $('#email1').val();
	 var group = $('#user_type').val();
	 //ajax request to check if the user is parent
	 $.ajax({
			type: "POST",
			url: "index.php?option=com_legalconfirmusers&task=registration.checkuser",
			data: { email:email1,group:group}
			}).done(function( msg ) {
				if(msg=="admin"){
                var txtmsg = "Hello, you are the first person from your firm to sign up for LegalConfirm.  LegalConfirm requires that your firm have an administrator that will be verified by management of LegalConfirm.  Please click 'OK' to continue or 'Cancel'  and email info@legalconfirm.com and we can assist your firm with setting up an administrator.  Thanks, the LegalConfirm team.";
				
				$.msgBox({
				    title: "Are You Sure",
				    content: txtmsg,
				    type: "confirm",
				  
				    buttons: [{ value: "Ok" }, { value: "Cancel"}],
				    success: function (result) {
				        if (result == "Ok") {
				        	formId.submit();
				        }
				    }
				});
				}else{
				formId.submit();
				}
			});
}
</script>

<!-- From Starts Here -->
<div class="login-container">
<div class="login-box">
<h4>Create an Account</h4>
<form class="form"
	action="<?php echo JRoute::_('index.php?option=com_legalconfirmusers&view=registration'); ?>"
	method="post" name="register1" id="register1" onsubmit="return validateForm('register1');">
<p class="name"><label for="email1"><?php echo JText::_('LEGALCONFIRM_EMAIL1'); ?><span style="color:red;">*</span></label>
<input type="text" name="email1" id="email1" class="required email1" /><span class="err" id="emailerr1"></span></p>

<p class="email"><label for="email2"><?php echo JText::_('LEGALCONFIRM_EMAIL2'); ?><span style="color:red;">*</span></label>
<input type="text" name="email2" id="email2" class="required email2" /><span class="err" id="emailerr2"></p>

<p class="web"><label for="email2"><?php echo JText::_('LEGALCONFIRM_USER_TYPE'); ?></label>
<select name="user_type" id="user_type">
	<option value="<?php echo $auditor; ?>" <?php  if($user_register_type=="auditor"){ echo "selected='selected'";}?>>Accountant</option>
	<option value="<?php echo $lawfirm; ?>" <?php  if($user_register_type=="lawyer"){ echo "selected='selected'";}?>>Lawyer</option>
</select></p>

<p><input type="submit" value="Next" class="button register-next"/></p>

<input type="hidden" name="option" value="com_legalconfirmusers" /> <input
	type="hidden" name="task" value="registration.register1" /> <?php echo JHtml::_( 'form.token' ); ?>

</form>
</div>
</div>
