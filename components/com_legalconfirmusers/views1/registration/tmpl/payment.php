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
?>
<!-- Method to check form validation -->
<script>
function validateForm(formId)
{
 var formId=document.getElementById(formId);
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
<form class="form"
	action="<?php echo JRoute::_('index.php?option=com_legalconfirmusers&view=registration&layout=terms'); ?>"
	method="post" name="register1" id="register1" onsubmit="return validateForm('register1');">

<div class="registration2">
  <fieldset>
  <legend>Add Billing Info:</legend>
<table>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_PAYMENT_CCNUMBER'); ?></td>
<td><input type="text" name = "cc_number" value="" class="inputbox required"/></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_PAYMENT_ESN'); ?></td>
<td><input type="text" name = "esn" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_PAYMENT_NAME_ON_CC'); ?></td>
<td><input type="text" name = "name_on_cc" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_ADDRESS'); ?></td>
<td><input type="text" name = "address" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_CITY'); ?></td>
<td><input type="text" name = "city" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_STATE'); ?></td>
<td><input type="text" name = "state" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_COUNTRY'); ?></td>
<td><input type="text" name = "country" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_ZIP'); ?></td>
<td><input type="text" name = "zip" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
</tr>
</table>
</fieldset>
 
</div>
<p class="submit"><input type="submit" name="submit" value="Next" /></p>
<input type="hidden" name="option" value="com_legalconfirmusers" /> 
<input type="hidden" name="task" value="registration.payment" /> 
<?php echo JHtml::_( 'form.token' ); ?>
</form>