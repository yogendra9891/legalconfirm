<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.5
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
$doc = JFactory::getDocument();
$doc->addScript('components/com_legalconfirm/assets/js/validation.js');
?>
<script>
function validateForm(formId)
{
 var formId = document.getElementById(formId);
 if(validate(formId))
       {
          //this check triggers the validations
           formId.submit();
           return true;
       }
       else{
 	
        return false;
       }

}
</script>
<div class="register-box">
<div class="reset-confirm<?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_legalconfirmusers&task=reset.confirm'); ?>" method="post" id="reset-password-verification" onsubmit="return validateForm('reset-password-verification');">

				<p>An email has been sent to your email address. The email contains a verification code, please paste the verification code in the field below to prove that you are the owner of this account.</p>	
			<dl>
							<dt><label title="Username::Enter your username" class="hasTip required" for="jform_username" id="jform_username-lbl">Username:<span class="star">&nbsp;*</span></label></dt>
				<dd><input type="text" size="30" value="" id="jform_username" name="jform[username]" class="inputbox required" ></dd>
							<dt><label title="Verification Code::Enter the password reset verification code you received by email." class="hasTip required" for="jform_token" id="jform_token-lbl">Verification Code:<span class="star">&nbsp;*</span></label></dt>
				<dd><input type="text" size="32" value="" id="jform_token" name="jform[token]" class="inputbox required"></dd>
						</dl>
		
		
		<div>
			<button style="margin-top: 3px;" class="button" type="submit">Submit</button>
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
</div>
