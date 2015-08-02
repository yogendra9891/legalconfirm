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
          //this check triggers the validations
           formId.submit();
           return true;
       }
       else{
        return false;
       }

}
</script>

<!-- From Starts Here -->
<div class="registration">
<form class="form"
	action="<?php echo JRoute::_('index.php?option=com_legalconfirmusers&view=registration'); ?>"
	method="post" name="register1" id="register1" onsubmit="return validateForm('register1');">
<p class="name"><label for="email1"><?php echo JText::_('LEGALCONFIRM_EMAIL1'); ?></label>
<input type="text" name="email1" id="email1" class="required email1" /><span class="err" id="emailerr1"></span></p>

<p class="email"><label for="email2"><?php echo JText::_('LEGALCONFIRM_EMAIL2'); ?></label>
<input type="text" name="email2" id="email2" class="required email2" /><span class="err" id="emailerr2"></p>

<p class="web"><label for="email2"><?php echo JText::_('LEGALCONFIRM_USER_TYPE'); ?></label>
<select name="user_type">
	<option value="<?php echo $auditor; ?>">Auditor</option>
	<option value="<?php echo $lawfirm; ?>">Lawyer</option>
</select></p>

<p class="submit"><input type="submit" value="NEXT" /></p>

<input type="hidden" name="option" value="com_legalconfirmusers" /> <input
	type="hidden" name="task" value="registration.register1" /> <?php echo JHtml::_( 'form.token' ); ?>

</form>
</div>
