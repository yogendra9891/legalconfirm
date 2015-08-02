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
<style>
.logintitle{text-align: center;}
#main ul li, #main ol li.loginlinks{display: inline; line-height: 1.7em; margin: 0; padding: 0;}
.innerattorneylogin{margin-left: 10px;}
</style>
<!-- Auditor Login form area.. -->
<div class="login<?php echo $this->pageclass_sfx?>" style="width: 400px; float: left; margin: 0px; padding: 0px; display: block;">
<h1 class="logintitle"><?php echo JText::_('COM_LEGALCONFIRMUSER_AUDITOR_LOGIN_FORM');?></h1>
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>

	<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
	<div class="login-description">
	<?php endif ; ?>

		<?php if($this->params->get('logindescription_show') == 1) : ?>
			<?php echo $this->params->get('login_description'); ?>
		<?php endif; ?>

		<?php if (($this->params->get('login_image')!='')) :?>
			<img src="<?php echo $this->escape($this->params->get('login_image')); ?>" class="login-image" alt="<?php echo JTEXT::_('COM_USER_LOGIN_IMAGE_ALT')?>"/>
		<?php endif; ?>

	<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
	</div>
	<?php endif ; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_legalconfirmusers&task=user.login'); ?>" method="post">

		<fieldset>
			<?php foreach ($this->form->getFieldset('credentials') as $field): ?>
				<?php if (!$field->hidden): ?>
					<div class="login-fields"><?php echo $field->label; ?>
					<?php echo $field->input; ?></div>
				<?php endif; ?>
			<?php endforeach; ?>
			<?php //if (JPluginHelper::isEnabled('system', 'remember')) : ?><!--
			<div class="login-fields">
				<label id="remember-lbl" for="remember"><?php echo JText::_('JGLOBAL_REMEMBER_ME') ?></label>
				<input id="remember" type="checkbox" name="remember" class="inputbox" value="yes"  alt="<?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>" />
			</div>
			--><?php //endif; ?>
			<button type="submit" class="button"><?php echo JText::_('JLOGIN'); ?></button>
			<input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('login_redirect_url', $this->form->getValue('return'))); ?>" />
			<input type="hidden" name="auditor" value="1">
			<input type="hidden" name="auditor_group" value="<?php echo $auditor;?>" >
			<input type="hidden" name="auditor_employee_group" value="<?php echo $auditor_emp; ?>" >
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
	</form>
	<div>
	<ul class="loginlinks">
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_legalconfirmusers&view=reset'); ?>">
			<?php echo JText::_('COM_USERS_LOGIN_RESET'); ?></a>
		</li>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_legalconfirmusers&view=remind'); ?>">
			<?php echo JText::_('COM_USERS_LOGIN_REMIND'); ?></a>
		</li>
		<?php
		$usersConfig = JComponentHelper::getParams('com_users');
		if ($usersConfig->get('allowUserRegistration')) : ?>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=register1'); ?>">
				<?php echo JText::_('COM_USERS_LOGIN_REGISTER'); ?></a>
		</li>
		<?php endif; ?>
	</ul>
</div>
</div>

<!-- Lawyer login form area.. -->
<div class="login<?php echo $this->pageclass_sfx?>" style="width: 424px; float: left; margin-left: 13px; padding: 0px; display: block; border-left:1px solid #000;">
<div class="innerattorneylogin">
<h1 class="logintitle"><?php echo JText::_('COM_LEGALCONFIRMUSER_LAWYER_LOGIN_FORM');?></h1>
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>

	<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
	<div class="login-description">
	<?php endif ; ?>

		<?php if($this->params->get('logindescription_show') == 1) : ?>
			<?php echo $this->params->get('login_description'); ?>
		<?php endif; ?>

		<?php if (($this->params->get('login_image')!='')) :?>
			<img src="<?php echo $this->escape($this->params->get('login_image')); ?>" class="login-image" alt="<?php echo JTEXT::_('COM_USER_LOGIN_IMAGE_ALT')?>"/>
		<?php endif; ?>

	<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
	</div>
	<?php endif ; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_legalconfirmusers&task=user.login'); ?>" method="post">

		<fieldset>
			<?php foreach ($this->form->getFieldset('credentials') as $field): ?>
				<?php if (!$field->hidden): ?>
					<div class="login-fields"><?php echo $field->label; ?>
					<?php echo $field->input; ?></div>
				<?php endif; ?>
			<?php endforeach; ?>
			<?php //if (JPluginHelper::isEnabled('system', 'remember')) : ?><!--
			<div class="login-fields">
				<label id="remember-lbl" for="remember"><?php echo JText::_('JGLOBAL_REMEMBER_ME') ?></label>
				<input id="remember" type="checkbox" name="remember" class="inputbox" value="yes"  alt="<?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>" />
			</div>
			--><?php //endif; ?>
			<button type="submit" class="button"><?php echo JText::_('JLOGIN'); ?></button>
			<input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('login_redirect_url', $this->form->getValue('return'))); ?>" />
			<input type="hidden" name="lawyer" value="1">
			<input type="hidden" name="lawfirm_group" value="<?php echo $lawfirm;?>">
			<input type="hidden" name="lawfirm_employee_group" value="<?php echo $lawfirm_emp;?>" >
			<input type="hidden" name="lawfirm_partner_group" value="<?php echo $lawfirm_partner;?>" >
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
	</form>
	<div>
	<ul class="loginlinks">
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_legalconfirmusers&view=reset'); ?>">
			<?php echo JText::_('COM_USERS_LOGIN_RESET'); ?></a>
		</li>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_legalconfirmusers&view=remind'); ?>">
			<?php echo JText::_('COM_USERS_LOGIN_REMIND'); ?></a>
		</li>
		<?php
		$usersConfig = JComponentHelper::getParams('com_users');
		if ($usersConfig->get('allowUserRegistration')) : ?>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=register1'); ?>">
				<?php echo JText::_('COM_USERS_LOGIN_REGISTER'); ?></a>
		</li>
		<?php endif; ?>
	</ul>
</div>
</div>
</div>


