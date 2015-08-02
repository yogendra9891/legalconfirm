<?php
/**
 * @version     1.0.0
 * @package     com_legalconfirm
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Abhishek Gupta <abhishek.gupta@daffodilsw.com> - http://
 */
// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.modal');

?>
<?php

//echo "<pre>";
//print_r($this->item);
//die;
$config =& JFactory::getConfig();
$auditor = $config->getValue( 'auditor');
$auditor_emp = $config->getValue( 'auditor_emp');
$lawfirm_emp = $config->getValue( 'lawfirm_emp');
$lawfirm_partner = $config->getValue( 'lawfirm_partner');
$allow_group_id = array($auditor_emp,$lawfirm_emp,$lawfirm_partner);
$gid= $this->item['groupId'];

//get usa states
$usa_states = $this->getUsaStates();
?>
<script>
function validateForm(formId)
{
 var formId=document.getElementById(formId);
 var password1 = $('#password1').val();
 var password2 = $('#password2').val();
 
 if(validate(formId))
       {   
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
          //this check triggers the validations
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
</script>

<div class="register-box editprofile">
<div style="float: left;">
<h4>Edit Your Profile</h4>
</div>
<div style="float: right;"><?php if(!in_array($this->item[groupId],$allow_group_id)){ ?>
<a
	href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=auditoradmin');?>">Dashboard</a>
&nbsp;&nbsp;|&nbsp; <a
	href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=offices');?>">Offices</a>
	&nbsp;&nbsp;|&nbsp; <a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=userprofile&layout=changeadmin&tmpl=component');?>"
		class="modal" rel="{handler: 'iframe', size: {x: 550,y: 240}}"><?php echo JText::_('COM_LEGALCONFIRM_CHANGE_ADMIN'); ?></a>
<?php } 
if($this->item[groupId] == $auditor_emp){ ?>
 <a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=auditors');?>">Dashboard</a>
 <?php
}

?></div>
<div style="clear: both;">
<form class="form" action="<?php echo JRoute::_('index.php'); ?>"
	method="post" name="register1" id="register1"
	onsubmit="return validateForm('register1');">
<br />
<div class="registration2">
<fieldset><legend>Personal Detail</legend>
<table>
	<tr>
		<td>
		<table>
			<tr>
				<td><?php echo JText::_('COM_LEGALCONFIRM_ACCOUNTING_FIRM');?></td>
				<td><input type="text" name="personal[firm]"
					value="<?php echo $this->item['personalinfo']->accounting_firm; ?>"
					<?php if($gid != $auditor){echo "readonly='readonly'";}?>class="inputbox required" /></td>
			</tr>
		</table>
		</td>
		<td>
		<table>
			<tr>
				<td><?php echo JText::_('COM_LEGALCONFIRM_EMPNAME'); ?></td>
				<td><input type="text" name="personal[emp_name]"
					value="<?php echo $this->item['personalinfo']->name; ?>"
					class="inputbox required" /></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table>
			<tr>
				<td><?php echo JText::_('COM_LEGALCONFIRM_EMAIL'); ?></td>
				<td><input type="text" name="personal[email]" id="email"
					value="<?php echo  $this->item['personalinfo']->email; ?>"
					class="inputbox required" readonly="readonly"
					onkeyup="checkemail();" /><span class="err" id="err"></span></td>
			</tr>
		</table>
		</td>
		<td>
		<table>
			<tr>
				<td><?php echo JText::_('COM_LEGALCONFIRM_EMPTITLE'); ?></td>
				<td><input type="text" name="personal[title]"
					value="<?php echo $this->item['personalinfo']->emp_title; ?>"
					class="inputbox required" /></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table>
			<tr>
				<td><?php echo JText::_('COM_LEGALCONFIRM_EMPPHONE'); ?></td>
				<td><input type="text" name="personal[phone]"
					value="<?php echo $this->item['personalinfo']->phone; ?>"
					class="inputbox required" /></td>
			</tr>
		</table>
		</td>
		<td>
		<table>
			<tr>

				<td><?php echo JText::_('COM_LEGALCONFIRM_PASSWORD'); ?></td>
				<td><input type="password" name="personal[password]" value=""
					class="inputbox" id="password1" /></td>

			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table>
			<tr>
				<td><?php echo JText::_('COM_LEGALCONFIRM_CONFIRM_PASSWORD'); ?></td>
				<td><input type="password" name="personal[password2]" value=""
					class="inputbox" id="password2" />
				<div class="err" id="passerr"></div>
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
</table>
</fieldset>
<div style="clear: both;"></div>
<?php

if(in_array($this->item[groupId],$allow_group_id)){
	?>
<div id="container1" class="clonedInput">
<fieldset><legend>Office Detail</legend>
<table>
	<tr>
		<td>
		<table>
			<tr>
				<td><?php echo JText::_('COM_LEGALCONFIRM_OFFICE_TITLE'); ?></td>
				<td><input type="text" name="ofc_detail[office][]"
					value="<?php echo $this->item['offices']->office_title; ?>"
					class="inputbox required" id="ofc_title" readonly="readonly" /> <input
					type="hidden" name="ofc_detail[id][]"
					value="<?php echo $this->item['offices']->id; ?>"
					class="inputbox required" id="ofc_title" readonly="readonly" /></td>
			</tr>
		</table>
		</td>
		<td>
		<table>
			<tr>
				<td><?php echo JText::_('COM_LEGALCONFIRM_ADDRESS'); ?></td>
				<td><input type="text" name="ofc_detail[address][]"
					value="<?php echo $this->item['offices']->address; ?>"
					class="inputbox required" id="ofc_address" readonly="readonly" /></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table>
			<tr>
				<td><?php echo JText::_('COM_LEGALCONFIRM_CITY'); ?></td>
				<td><input type="text" name="ofc_detail[city][]"
					value="<?php echo $this->item['offices']->city; ?>"
					class="inputbox required" id="ofc_city" readonly="readonly" /></td>
			</tr>
		</table>
		</td>
		<td>
		<table>
			<tr>
				<td><?php echo JText::_('COM_LEGALCONFIRM_STATE'); ?></td>
				<td><input type="text" name="ofc_detail[state][]"
					value="<?php echo $this->item['offices']->state; ?>"
					class="inputbox required" id="ofc_state" readonly="readonly" /></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table>
			<tr>
				<td><?php echo JText::_('COM_LEGALCONFIRM_COUNTRY'); ?></td>
				<td><input type="text" name="ofc_detail[country][]"
					value="<?php echo $this->item['offices']->country; ?>"
					class="inputbox required" id="ofc_country" readonly="readonly" /></td>
			</tr>
		</table>
		</td>
		<td>
		<table>
			<tr>
				<td><?php echo JText::_('COM_LEGALCONFIRM_ZIP'); ?></td>
				<td><input type="text" name="ofc_detail[zip][]"
					value="<?php echo $this->item['offices']->zip; ?>"
					class="inputbox required" id="ofc_zip" readonly="readonly" /></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</fieldset>
</div>
<?php  } ?> <?php

if($this->item[groupId]==$auditor){
	?>
<fieldset><legend>Add Billing Info</legend>
<table>
	<tr>
		<td>
		<table>
			<tr>
				<td><?php echo JText::_('COM_LEGALCONFIRM_PAYMENT_CCNUMBER'); ?></td>
				<td><input type="text" name="cc_number"
					value="<?php echo $this->item['paymentInfo']->cc_number; ?>"
					class="ccnumber required" /><span class="err" id="errccnumber"></span></td>
			</tr>
		</table>
		</td>
		<td>
		<table>
			<tr>
				<td><?php echo JText::_('COM_LEGALCONFIRM_PAYMENT_ESN'); ?></td>
				<td><input type="text" name="esn"
					value="<?php echo $this->item['paymentInfo']->esn; ?>"
					class="inputbox required" /></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table>
			<tr>
				<td><?php echo JText::_('COM_LEGALCONFIRM_PAYMENT_NAME_ON_CC'); ?></td>
				<td><input type="text" name="name_on_cc"
					value="<?php echo $this->item['paymentInfo']->name_on_cc; ?>"
					class="inputbox required" /></td>
			</tr>
		</table>
		</td>
		<td>
		<table>
			<tr>
				<td><?php echo JText::_('COM_LEGALCONFIRM_ADDRESS'); ?></td>
				<td><input type="text" name="address"
					value="<?php echo $this->item['paymentInfo']->address; ?>"
					class="inputbox required" /></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table>
			<tr>
				<td><?php echo JText::_('COM_LEGALCONFIRM_CITY'); ?></td>
				<td><input type="text" name="city"
					value="<?php echo $this->item['paymentInfo']->city; ?>"
					class="inputbox required" /></td>
			</tr>
		</table>
		</td>
		<td>
		<table>
			<tr>
				<td><?php echo JText::_('COM_LEGALCONFIRM_STATE'); ?></td>
				<td><select name="state">
				<?php
				foreach($usa_states as $usastate){
					?>
					<option
					<?php if($this->item['paymentInfo']->state == $usastate->name){ echo "selected='selected'";}?>
						value="<?php echo $usastate->name; ?>"><?php echo $usastate->name; ?></option>
						<?php
				}

				?>
				</select> <!--				<input type="text" name="state"--> <!--					value="<?php echo $this->item['paymentInfo']->state; ?>"-->
				<!--					class="inputbox required" />-->
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table>
			<tr>
				<td><?php echo JText::_('COM_LEGALCONFIRM_COUNTRY'); ?></td>
				<td><input type="text" name="country" value="USA"
					readonly="readonly" class="inputbox required" /></td>
			</tr>
		</table>
		</td>
		<td>
		<table>
			<tr>
				<td><?php echo JText::_('COM_LEGALCONFIRM_ZIP'); ?></td>
				<td><input type="text" name="zip"
					value="<?php echo $this->item['paymentInfo']->zip; ?>"
					class="inputbox required" /></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</fieldset>

</div>
				<?php } ?> <input type="submit" name="submit" value="Save"
	class="button" /> <input type="hidden" name="option"
	value="com_legalconfirm" /> <input type="hidden" name="personal[gid]"
	value="<?php echo $gid;?>" /> <input type="hidden" name="task"
	value="userprofile.save" /> <?php echo JHtml::_( 'form.token' ); ?></form>
</div>
</div>
</div>

