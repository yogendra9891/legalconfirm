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
		//finding the group id from configuration file..
		$config = JFactory::getConfig(); 
		$auditor = $config->getValue('config.auditor'); 
		$auditor_emp = $config->getValue('config.auditor_emp');
		$lawfirm = $config->getValue('config.lawfirm');
		$lawfirm_emp = $config->getValue('config.lawfirm_emp');
		$lawfirm_partner = $config->getValue('config.lawfirm_partner');

?>

<script language="Javascript">
 $(document).ready(function() {

	 //for auditor
	$('#password-clear').show();
	$('#password').hide();
 
	$('#password-clear').focus(function() {
		$('#password-clear').hide();
		$('#password').show();
		$('#password').focus();
	});
	$('#password').blur(function() {
		if($('#password').val() == '') {
			$('#password-clear').show();
			$('#password').hide();
		}
	});

//for lawfirm
	$('#password-clear1').show();
	$('#password1').hide();
 
	$('#password-clear1').focus(function() {
		$('#password-clear1').hide();
		$('#password1').show();
		$('#password1').focus();
	});
	$('#password1').blur(function() {
		if($('#password1').val() == '') {
			$('#password-clear1').show();
			$('#password1').hide();
		}
	});
 
	
});
 
</script>
<style>
#password-clear {
    display: none;
}
#password-clear1 {
    display: none;
}
.logintitle{text-align: center;}
#main ul li, #main ol li.loginlinks{display: inline; line-height: 1.7em; margin: 0; padding: 0;}
.innerattorneylogin{margin-left: 10px;}
</style>
<!-- Auditor Login form area.. -->

	<div class="login-container">
	<div class="login-box">
	<h4>Accountant Login</h4>
	<form action="<?php echo JRoute::_('index.php?option=com_legalconfirmusers&task=user.login'); ?>" method="post" name="accontant">
      
		
			<div>
<input id="username" class="validate-username" type="text" size="25" name="username" value="Email" onclick="if(this.value=='Email'){this.value='';}" onblur="if(this.value==''){this.value='Email';}" >
</div>
<div>
<input id="password-clear" type="text" value="Password" autocomplete="off" />
<input id="password" class="validate-password" type="password" size="25" name="password" >
</div>
			<?php //if (JPluginHelper::isEnabled('system', 'remember')) : ?><!--
			<div class="login-fields">
				<label id="remember-lbl" for="remember"><?php echo JText::_('JGLOBAL_REMEMBER_ME') ?></label>
				<input id="remember" type="checkbox" name="remember" class="inputbox" value="yes"  alt="<?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>" />
			</div>
			--><?php //endif; ?>
			<button type="submit" class="button"><?php echo JText::_('JLOGIN'); ?></button>
			<a href="<?php echo JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=register1&type=auditor'); ?>" class="button" >
				<?php echo JText::_('COM_USERS_LOGIN_REGISTER'); ?></a>
			<input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('login_redirect_url', $this->form->getValue('return'))); ?>" />
			<input type="hidden" name="auditor" value="1">
			<input type="hidden" name="auditor_group" value="<?php echo $auditor;?>" >
			<input type="hidden" name="auditor_employee_group" value="<?php echo $auditor_emp; ?>" >
			<?php echo JHtml::_('form.token'); ?>
		
	</form>
	<div>
<div class="row">
			<a href="<?php echo JRoute::_('index.php?option=com_legalconfirmusers&view=reset'); ?>">
			<?php echo JText::_('COM_USERS_LOGIN_RESET'); ?></a>
</div>
		
		
</div>
</div>

<!-- Lawyer login form area.. -->

<div class="divider"></div>
<div class="login-box">
<h4>Attorney Login</h4>
	<form action="<?php echo JRoute::_('index.php?option=com_legalconfirmusers&task=user.login'); ?>" method="post" name="lawyer">

		
						<div>
<input id="username" class="validate-username" type="text" size="25" name="username" value="Email" onclick="if(this.value=='Email'){this.value='';}" onblur="if(this.value==''){this.value='Email';}" >
</div>
<div>
<input id="password-clear1" type="text" value="Password" autocomplete="off" />
<input id="password1" class="validate-password" type="password" size="25" name="password" >
</div>
			<?php //if (JPluginHelper::isEnabled('system', 'remember')) : ?><!--
			<div class="login-fields">
				<label id="remember-lbl" for="remember"><?php echo JText::_('JGLOBAL_REMEMBER_ME') ?></label>
				<input id="remember" type="checkbox" name="remember" class="inputbox" value="yes"  alt="<?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>" />
			</div>
			--><?php //endif; ?>
			<button type="submit" class="button"><?php echo JText::_('JLOGIN'); ?></button>
			<a href="<?php echo JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=register1&type=lawyer'); ?>" class="button">
				<?php echo JText::_('COM_USERS_LOGIN_REGISTER'); ?></a>
			<input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('login_redirect_url', $this->form->getValue('return'))); ?>" />
			<input type="hidden" name="lawyer" value="1">
			<input type="hidden" name="lawfirm_group" value="<?php echo $lawfirm;?>">
			<input type="hidden" name="lawfirm_employee_group" value="<?php echo $lawfirm_emp;?>" >
			<input type="hidden" name="lawfirm_partner_group" value="<?php echo $lawfirm_partner;?>" >
			<?php echo JHtml::_('form.token'); ?>
		
	</form>
	<div>
	<div class="row">
			<a href="<?php echo JRoute::_('index.php?option=com_legalconfirmusers&view=reset'); ?>">
			<?php echo JText::_('COM_USERS_LOGIN_RESET'); ?></a>
</div>
</div>
</div>
</div>


