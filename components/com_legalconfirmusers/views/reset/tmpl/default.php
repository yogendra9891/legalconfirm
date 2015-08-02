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
JHtml::_('behavior.tooltip');
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
<div class="reset<?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>

	<form id="user-registration" action="<?php echo JRoute::_('index.php?option=com_legalconfirmusers&task=reset.request'); ?>" method="post" onsubmit="return validateForm('user-registration');">
		<div class="email-reset-class">
				<p>Please enter the email address for your account. A verification code will be sent to you. Once you have received the verification code, you will be able to choose a new password for your account.</p>		
			<dl class="reset-password"><span>
							<dt><label title="" for="jform_email" id="jform_email-lbl">Email Address:<span class="star">&nbsp;*</span></label></dt>
				<dd><input type="text" size="30" class="validate-username required" value="" id="jform_email" name="jform[email]" ><span class="erremail1" id="emailerr1"></span><br/></dd>
							<dt></dt>
				<dd></dd>
			</span>
			</dl>
	
				</div>
		<div class="reset-password-button">
			<button class="validate button" type="submit">Submit</button>
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
</div>
