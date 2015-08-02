<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_login
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
?>
<script>
//var jq = jQuery.noConflict();
 jQuery(document).ready(function(){
	 jQuery('#logout-user').click(function(){
	 jQuery('#login-form').submit();
 });
 });
</script>

<?php 
//get current login user
$user = JFactory::getUser();
$userName = $user->name;
$groupid = $user->groups;

//get global configuration
$config = JFactory::getConfig();
//get defined groupid from global config
$auditor_admin = $config->getValue('config.auditor');
$auditor_emp = $config->getValue('config.auditor_emp');
$lawfirm_admin = $config->getValue('config.lawfirm');
$lawfirm_partner = $config->getValue('config.lawfirm_partner');
$lawfirm_emp = $config->getValue('config.lawfirm_emp');

//check for user group id
foreach($groupid as $key => $value){
	switch($value){
        case $auditor_admin :
             $type = "Auditor Admin";
             break;
        case $auditor_emp :
             $type = "Auditor Employee";
             break;
        case $lawfirm_admin :
             $type = "Lawfirm Admin";
             break;
        case $lawfirm_partner :
             $type = "Lawfirm Partner";
             break;
        case $lawfirm_emp :
             $type = "Lawfirm Employee";
             break;
        default:
             $type = "";
             break;
 }
             
}
?>

<div class="logout-profile-wrraper">
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" id="login-form" name="login-form">
	<div class="profile-page">
       <!-- <span style="color:#fff;"><?php echo "Hello ".$userName." (".$type.")";?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>-->
	<a href="<?php echo JRoute::_('index.php?option=com_legalconfirmusers&task=user.checkprofile');?>"><?php echo $userName;?></a><span style="color:#fff;"> (<?php echo $type;?>)</span>
	</div>
	<div class="logout-button">
		<a href="index.php?option=com_legalconfirmusers&task=user.logout" id="logout-user"><span class="log-out-icon"><img src="<?php echo JURI::base().'templates/legalconfirm/images/log_out_button.png';?>" /></span><?php echo JText::_('JLOGOUT'); ?></a>
		<input type="hidden" name="option" value="com_legalconfirmusers" />
		<input type="hidden" name="task" value="user.logout" />
<!--		<input type="hidden" name="return" value="<?php echo $return; ?>" />-->
		<?php echo JHtml::_('form.token'); ?>
	</div>

</form>
</div>
