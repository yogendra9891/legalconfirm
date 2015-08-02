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
//get usa states
$usa_states = $this->getUsaStates();
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
<div class="register-box">
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
<td><?php echo JText::_('COM_LEGALCONFIRM_PAYMENT_CC_TYPE'); ?><span style="color:red;">*</span></td>
<td><select name="cc_type" id="cc_type">
<option value="visa"><?php echo JText::_('COM_LEGALCONFIRM_PAYMENT_CC_TYPE_VISA');?></option>
<option value="MasterCard"><?php echo JText::_('COM_LEGALCONFIRM_PAYMENT_CC_TYPE_MASTERO');?></option>
<option value="AmericanExpress"><?php echo JText::_('COM_LEGALCONFIRM_PAYMENT_CC_TYPE_AMREICAN_EXPRESS');?></option>
<option value="Discover"><?php echo JText::_('COM_LEGALCONFIRM_PAYMENT_CC_TYPE_DISCOVER');?></option>
<option value="JCB"><?php echo JText::_('COM_LEGALCONFIRM_PAYMENT_CC_TYPE_JCB');?></option>
<option value="Diners"><?php echo JText::_('COM_LEGALCONFIRM_PAYMENT_CC_TYPE_DINERS');?></option>
</select></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_PAYMENT_CCNUMBER'); ?><span style="color:red;">*</span></td>
<td><input type="text" name = "cc_number" value="" class="ccnumber required"/><span class="err" id="errccnumber"></span></td>
</tr>
</table>
</td>
</tr>


<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_PAYMENT_CCVNUMBER'); ?><span style="color:red;">*</span></td>
<td><input type="text" name = "cc_ccvno" value="" class="ccvnumber required"/><span class="err" id="errccvnumber"></span></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_PAYMENT_ESN'); ?><span style="color:red;">*</span></td>
<td><input type="text" name = "esn" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
</tr>


<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_PAYMENT_CC_EXPDATE_MONTH'); ?><span style="color:red;">*</span></td>
<td><input type="text" name = "cc_expdatemonth" value="" class="ccvexpdatemonth required"/><span class="err" id="errccvexpdatemonth"></span></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_PAYMENT_CC_EXPDATE_YEAR'); ?><span style="color:red;">*</span></td>
<td><input type="text" name = "cc_expdateyear" value="" class="ccvexpdateyear required" /><span class="err" id="errccvexpdateyear"></span></td>
</tr>
</table>
</td>
</tr>

<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_PAYMENT_NAME_ON_CC'); ?><span style="color:red;">*</span></td>
<td><input type="text" name = "name_on_cc" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_ADDRESS'); ?><span style="color:red;">*</span></td>
<td><input type="text" name = "address" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_CITY'); ?><span style="color:red;">*</span></td>
<td><input type="text" name = "city" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_STATE'); ?><span style="color:red;">*</span></td>
<td>
<select name = "state" >
<?php 
foreach($usa_states as $usastate){
	?>
	<option value="<?php echo $usastate->name; ?>"><?php echo $usastate->name; ?></option>
	<?php
}

?>
</select>
<!--<input type="text" name = "state" value="" class="inputbox required" />-->


</td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_COUNTRY'); ?><span style="color:red;">*</span></td>
<td><input type="text" name = "country" value="USA" readonly="readonly" class="inputbox required" /></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_ZIP'); ?><span style="color:red;">*</span></td>
<td><input type="text" name = "zip" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
</tr>
</table>
</fieldset>
 
</div>
<p><input type="submit" name="submit" value="Next" class="button register-next"/></p>
<input type="hidden" name="option" value="com_legalconfirmusers" /> 
<input type="hidden" name="task" value="registration.payment" /> 
<?php echo JHtml::_( 'form.token' ); ?>
</form>
</div>
