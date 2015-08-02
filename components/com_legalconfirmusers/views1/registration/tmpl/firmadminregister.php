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
//get group id
$gid = JRequest::getVar('gid');

//get email from previous page.
$app	= JFactory::getApplication();
$prev_email = $app->getUserState('com_legalconfirmusers.email.data');
//get config setting
$config =& JFactory::getConfig();
//get auditor and lawfirm id
$auditor = $config->getValue( 'auditor');
$lawfirm = $config->getValue( 'lawfirm');
?>
<!-- Method to check form validation -->
<script>
function validateForm(formId)
{
 var formId=document.getElementById(formId);
 if(validate(formId))
       {   var email_check = $('#checkemail_ajax_id').val();
//           if(email_check != "Correct"){
//             return false;
//           }  
          //this check triggers the validations
           formId.submit();
           return true;
       }
       else{
        return false;
       }

}
//Method to check email
function checkemail(){
	var email1 = $('#email').val();
	
	$.ajax({
		type: "POST",
		url: "index.php?option=com_legalconfirmusers&task=registration.checkemail",
		data: { email:email1 }
		}).done(function( msg ) {
		$('#checkemail_ajax_id').val(msg)
		$('#err').html(msg);
		});
}
</script>
<script type="text/javascript">
jQuery( function ( $ ) {
    $( '#btnAdd' ).click( function() {
        var num = $( '.clonedInput' ).length;      // how many "duplicatable" input fields we currently have
        var newNum  = new Number( num + 1 );        // the numeric ID of the new input field being added
        var newElem = $( '#container' + num ).clone().attr( 'id', 'container' + newNum );

        newElem.children( ':first' ).attr( 'id', 'name' + newNum ).attr( 'name', 'name' + newNum );
        $( '#container' + num ).after( newElem );
        $( '#btnDel' ).attr( 'disabled', false );
        if ( newNum == 25 )
            $( '#btnAdd' ).attr( 'disabled', 'disabled' );
    });

    $( '#btnDel' ).click( function() {
        var num = $( '.clonedInput' ).length;      // how many "duplicatable" input fields we currently have
        $( '#container' + num ).remove();              // remove the last element
        $( '#btnAdd' ).attr( 'disabled', false );  // enable the "add" button

        // if only one element remains, disable the "remove" button
        if ( num-1 == 1 )
            $( '#btnDel' ).attr( 'disabled', 'disabled' );
    });

    $( '#btnDel' ).attr( 'disabled', 'disabled' );
});
        </script>
	

<form class="form"
	action="<?php echo JRoute::_('index.php'); ?>"
	method="post" name="register1" id="register1" onsubmit="return validateForm('register1');">

<div class="registration2">
  <fieldset>
  <legend>Personal Detail:</legend>
<table>
<tr>
<td>
<table>
<tr>
<td><?php if($gid == $auditor){echo JText::_('COM_LEGALCONFIRM_ACCOUNTING_FIRM');}else{echo JText::_('COM_LEGALCONFIRM_LAW_FIRM');} ?></td>
<td><input type="text" name = "personal[firm]" value="" class="inputbox required"/></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_EMPNAME'); ?></td>
<td><input type="text" name = "personal[emp_name]" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_EMAIL'); ?></td>
<td><input type="text" name = "personal[email]" id="email" value="<?php echo $prev_email ;?>" class="inputbox required" readonly="readonly" onkeyup="checkemail();"/><span class="err" id="err"></span></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_EMPTITLE'); ?></td>
<td><input type="text" name = "personal[title]" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_EMPPHONE'); ?></td>
<td><input type="text" name = "personal[phone]" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<?php if($gid == $auditor){
	?>
<td><?php echo JText::_('COM_LEGALCONFIRM_PASSWORD'); ?></td>
<td><input type="password" name = "personal[password]" value="" class="inputbox required" /></td>
<?php } else{
?>
<td></td><td></td>
<?php 
}?>
</tr>
</table>
</td>
</tr>
</table>
</fieldset>
 <div id="container1" class="clonedInput"> 
  <fieldset>
  <legend>Office Detail:</legend>
<table>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_OFFICE'); ?></td>
<td><input type="text" name = "ofc_detail[office][]" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_ADDRESS'); ?></td>
<td><input type="text" name = "ofc_detail[address][]" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_CITY'); ?></td>
<td><input type="text" name = "ofc_detail[city][]" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_STATE'); ?></td>
<td><input type="text" name = "ofc_detail[state][]" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_COUNTRY'); ?></td>
<td><input type="text" name = "ofc_detail[country][]" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_ZIP'); ?></td>
<td><input type="text" name = "ofc_detail[zip][]" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
</tr>
</table>
</fieldset>
 </div>
</div>
<input type="hidden" name="checkemail_ajax" value="" id="checkemail_ajax_id" /> 
<p class="button"><input type="button" id="btnAdd" value="Add More Office" /></p>
<p class="button"><input type="button" id="btnDel" value="Remove Office" /></p>
<p class="submit"><input type="submit" name="submit" value="Next" /></p>
<input type="hidden" name="option" value="com_legalconfirmusers" /> 
<input type="hidden" name="personal[gid]" value="<?php echo $gid;?>" /> 
<input type="hidden" name="task" value="registration.register2" /> 
<?php echo JHtml::_( 'form.token' ); ?>
</form>