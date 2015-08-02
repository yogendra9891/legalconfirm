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
$config =& JFactory::getConfig();
$auditor_emp = $config->getValue( 'auditor_emp');
$lawfirm_emp = $config->getValue( 'lawfirm_emp');
$lawfirm_partner = $config->getValue( 'lawfirm_partner');
$usa_states = $this->getUsaStates();
//get email from previous page.
$app	= JFactory::getApplication();
$prev_email = $app->getUserState('com_legalconfirmusers.email.data');

//get offices detail
if($gid==$auditor_emp || $gid = $lawfirm_emp){
	$offices = $this->getOffice($prev_email);
	//get accounting firm name;
	$firm_name = $this->getFirmName($prev_email);
}
?>
<!-- Method to call method on page load -->
<script>
$(document).ready(function(){

	$('#container1').show();
	$('#container2').hide();

	//assign the temp value
    $('#ofc_title1').val('test');
    $('#ofc_address1').val('test');
    $('#ofc_city1').val('test');
    $('#ofc_zip1').val('11111');
	
//call ajax when page load to auto populate the office form
	var ofc_id = $('#ofc_id').val();
	$.ajax({
		type: "POST",
		url: "index.php?option=com_legalconfirmusers&task=registration.getofficedetail",
		data: { ofcid:ofc_id }
		}).done(function( ofcdata ) {
			//get object of json
			var obj = jQuery.parseJSON(ofcdata);
			//get field value of json
			var title = obj.office_title;
			var address = obj.address;
			var city = obj.city;
			var state = obj.state;
			var country = obj.country;
			var zip = obj.zip;
            //assign these value in textbox
            $('#ofc_title').val(title);
            $('#ofc_address').val(address);
            $('#ofc_city').val(city);
            $('#ofc_zip').val(zip);
            $('#ofc_state').val(state);
            $('#ofc_country').val(country);
		
		});
});

function newOffice(){
	$('#check_office').val('1');
	//remove the temp value
    $('#ofc_title1').val('');
    $('#ofc_address1').val('');
    $('#ofc_city1').val('');
    $('#ofc_zip1').val('');
	$('#container1').hide();
	$('#container2').show("slide", { direction: "left" }, 1000);
	//$('#container2').show();
	return false;
}

function selectOffice(){
	    $('#check_office').val('2');

	    //assign the temp value
	    $('#ofc_title1').val('test');
	    $('#ofc_address1').val('test');
	    $('#ofc_city1').val('test');
	    $('#ofc_zip1').val('11111');
	    
	    $('#container2').hide();
	    $('#container1').show("slide", { direction: "left" }, 1000);
	
	return false;
}
</script>
<!-- Method to check form validation -->
<script>
function validateForm(formId)
{
 var formId=document.getElementById(formId);
 var password1 = $('#password1').val();
 var password2 = $('#password2').val();
 if(validate(formId))
       {   
           //check confirm password
           
           if(password1 != password2){
            var pass_msg = "Password not matched";
            $('#passerr').html(pass_msg	);
            $("#password2").css({
         	   border:"2px solid red"
         	});

            return false;
           }else{
        	   $('#passerr').html('');
           }
           
           formId.submit();
           return true;
       }
       else{
    	   if(password1 != password2){
               var pass_msg = "Password not matched";
               $('#passerr').html(pass_msg	);
               $("#password2").css({
            	   border:"2px solid red"
            	});
              }
    	   else{
        	   $('#passerr').html('');
           }
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

//Method to get office detail
function showofcdetail(){
	var ofc_id = $('#ofc_id').val();
	$.ajax({
		type: "POST",
		url: "index.php?option=com_legalconfirmusers&task=registration.getofficedetail",
		data: { ofcid:ofc_id }
		}).done(function( ofcdata ) {
			//get object of json
			var obj = jQuery.parseJSON(ofcdata);
			//get field value of json
			var title = obj.office_title;
			var address = obj.address;
			var city = obj.city;
			var state = obj.state;
			var country = obj.country;
			var zip = obj.zip;
            //assign these value in textbox
            $('#ofc_title').val(title);
            $('#ofc_address').val(address);
            $('#ofc_city').val(city);
            $('#ofc_state').val(state);
            $('#ofc_country').val(country);
            $('#ofc_zip').val(zip);
		
		});
}
</script>

	
<div class="register-box emp-registration-form">
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
<td><?php echo JText::_('COM_LEGALCONFIRM_ACCOUNTING_FIRM'); ?> <span style="color:red;">*</span></td>
<td><input type="text" name = "personal[firm]" value="<?php echo $firm_name->accounting_firm; ?>" class="inputbox required" readonly="readonly"/></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_EMPNAME'); ?> <span style="color:red;">*</span></td>
<td><input type="text" name = "personal[emp_name]" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_EMAIL'); ?> <span style="color:red;">*</span></td>
<td><input type="text" name = "personal[email]" id="email" value="<?php echo $prev_email ;?>" class="inputbox required" readonly="readonly" /><span class="err" id="err"></span></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_EMPTITLE'); ?> <span style="color:red;">*</span></td>
<td><input type="text" name = "personal[title]" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_EMPPHONE'); ?> <span style="color:red;">*</span></td>
<td><input type="text" name = "personal[phone]" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_PASSWORD'); ?> <span style="color:red;">*</span></td>
<td><input id="password1" type="password" name = "personal[password]" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_CONFIRM_PASSWORD'); ?> <span style="color:red;">*</span></td>
<td><input id="password2" type="password" name = "personal[password2]" value="" class="inputbox required" autocomplete="off"/><span class="err" id="passerr"></span></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td></td>
<td></td>
</tr>
</table>
</td>
</tr>
<?php if($gid == $lawfirm_emp) {?>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_EMP_USERTYPE'); ?> <span style="color:red;">*</span></td>
<td>
<select name="personal[gid]" style="margin-top:13px;">
<option value="<?php echo $lawfirm_emp;?>">Lawfirm Employee</option>
<option value="<?php echo $lawfirm_partner;?>">Lawfirm Partner</option>
</select>
</td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td></td>
<td></td>
</tr>
</table>
</td>
</tr>
<?php } else{ ?>
<input type="hidden" name="personal[gid]" value="<?php echo $gid;?>" /> 
<?php } ?>
</table>
</fieldset>
<div id="container1" class="clonedInput"> 
  <fieldset>
  <legend>Select Office Detail:</legend>
<table>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_OFFICE'); ?>  <span style="color:red;">*</span></td>
<td>
<select  onchange="showofcdetail();" id="ofc_id" name="ofc_detail[ofc_val][]" >
<?php 
foreach($offices as $office){
	?>
	<option value="<?php echo $office->id; ?>"><?php echo $office->office_title; ?></option>
<?php 
}?>
</select>
</td>
</tr>
</table>
</td>
<td class="office-address-test">
<span class="office-choice">OR</span> <a  class="button" style="margin-left:20px;" href="javascript:void(0)" onclick="newOffice();"><img src='<?php echo JURI::root()."templates/legalconfirm/images/add_button.png";?>' alt=""/>Add New Office</a>
</td>

</tr>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_OFFICE_TITLE'); ?> <span style="color:red;">*</span></td>
<td><input type="text" name = "ofc_detail[office][]" value="" class="inputbox required" id="ofc_title" readonly="readonly"/></td>
</tr>
</table>
</td>
<td class="office-address-test">
<table class="office-address-table">
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_ADDRESS'); ?> <span style="color:red;">*</span></td>
<td><input type="text" name = "ofc_detail[address][]" value="" class="inputbox required" id="ofc_address" readonly="readonly"/></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_CITY'); ?> <span style="color:red;">*</span></td>
<td><input type="text" name = "ofc_detail[city][]" value="" class="inputbox required" id="ofc_city"readonly="readonly" /></td>
</tr>
</table>
</td>
<td class="office-address-test">
<table class="office-address-table">
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_STATE'); ?> <span style="color:red;">*</span></td>
<td><input type="text" name = "ofc_detail[state][]" value="" class="inputbox required" id="ofc_state" readonly="readonly" /></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_COUNTRY'); ?> <span style="color:red;">*</span></td>
<td><input type="text" name = "ofc_detail[country][]" value="" class="inputbox required" id="ofc_country" readonly="readonly" /></td>
</tr>
</table>
</td>
<td class="office-address-test">
<table class="office-address-table">
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_ZIP'); ?> <span style="color:red;">*</span></td>
<td><input type="text" name = "ofc_detail[zip][]" value="" class="inputbox required" id="ofc_zip" readonly="readonly"/></td>
</tr>
</table>
</td>
</tr>
</table>
</fieldset>
 </div>
 <div id="container2" class="clonedInput"> 
  <fieldset>
  <legend>Add New Office:</legend>
<span class="office-choicees">OR</span> <span class="button" onclick="selectOffice();">Select Office</span>
<table>

<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_OFFICE_TITLE'); ?> <span style="color:red;">*</span></td>
<td><input type="text" name = "ofc_detail_new[office][]" value="" class="inputbox required" id="ofc_title1" /></td>
</tr>
</table>
</td>
<td class="office-address-test">
<table class="office-address-table">
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_ADDRESS'); ?> <span style="color:red;">*</span></td>
<td><input type="text" name = "ofc_detail_new[address][]" value="" class="inputbox required" id="ofc_address1" /></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_CITY'); ?> <span style="color:red;">*</span></td>
<td><input type="text" name = "ofc_detail_new[city][]" value="" class="inputbox required" id="ofc_city1" /></td>
</tr>
</table>
</td>
<td class="office-address-test">
<table class="office-address-table">
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_STATE'); ?> <span style="color:red;">*</span></td>
<td>
<select name = "ofc_detail_new[state][]" id="ofc_state1" >
<?php 
foreach($usa_states as $usastate){
	?>
	<option value="<?php echo $usastate->name; ?>"><?php echo $usastate->name; ?></option>
	<?php
}

?>
</select>

<!-- <input type="text" name = "ofc_detail[state][]" value="" class="inputbox required" id="ofc_state"  />-->
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_COUNTRY'); ?> <span style="color:red;">*</span></td>
<td><input type="text" name = "ofc_detail_new[country][]" value="USA" class="inputbox required" id="ofc_country1" readonly="readonly" /></td>
</tr>
</table>
</td>
<td class="office-address-test">
<table class="office-address-table">
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_ZIP'); ?> <span style="color:red;">*</span></td>
<td><input type="text" name = "ofc_detail_new[zip][]" value="" class="zip required" id="ofc_zip1" /></td>
</tr>
</table>
</td>
</tr>
</table>
</fieldset>
 </div>
</div>
<input type="hidden" name="check_office" value="" id="check_office" /> 
<input type="hidden" name="checkemail_ajax" value="" id="checkemail_ajax_id" /> 
<p class="submit"><input type="submit" name="submit" value="Next" class="button next register-next"/> <span></span></p>
<input type="hidden" name="option" value="com_legalconfirmusers" /> 

<input type="hidden" name="task" value="registration.register2" /> 
<?php echo JHtml::_( 'form.token' ); ?>
</form>
</div>
