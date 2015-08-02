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
?>
<div class="register-box">
<div class="reset-complete<?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_legalconfirmusers&task=reset.complete'); ?>" method="post" >

				<p>To complete the password reset process, please enter a new password.</p>		
			<dl>
							<dt><label title="Password::Enter your new password"  for="jform_password1" id="jform_password1-lbl" >Password:<span class="star">&nbsp;*</span></label></dt>
				<dd><input type="password" size="30" class="validate-password required invalid" autocomplete="off" value="" id="jform_password1" name="jform[password1]" ></dd>
							<dt><label title="Confirm Password::Confirm your new password" for="jform_password2" id="jform_password2-lbl">Confirm Password:<span class="star">&nbsp;*</span></label></dt>
				<dd><input type="password" size="30" class="validate-password required invalid" autocomplete="off" value="" id="jform_password2" name="jform[password2]" ></dd>
						</dl>
	
		
		<div>
			<button class="validate button" type="submit" style="margin-top: 3px;">Submit</button>
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
</div>
