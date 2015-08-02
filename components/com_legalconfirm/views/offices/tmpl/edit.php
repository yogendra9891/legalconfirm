<?php 
//echo "<pre>";
//print_r($this->items);

//get usa states
$usa_states = $this->getUsaStates();
?>
<script>
function validateForm(formId)
{
 var formId=document.getElementById(formId);
 if(validate(formId))
       {  
           formId.submit();
           return true;
       }
       else{
        return false;
       }

}
</script>
<div class="register-box editprofile auditor-admin">
<div style="float: right"><a
	href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=auditoradmin');?>">Dashboard</a>&nbsp;&nbsp;|&nbsp;
	<a
	href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=offices');?>">Offices</a>
</div>
<form style="clear:both;" class="form"
	action="<?php echo JRoute::_('index.php'); ?>"
	method="post" name="addoffice" id="addoffice" onsubmit="return validateForm('addoffice');">
<span>Office Detail</span>
<table>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_OFFICE'); ?><span style="color:red;">*</span></td>
<td><input type="text" name = "ofc_detail[office]" value="<?php echo $this->items->office_title; ?>" class="inputbox required" />
</td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_ADDRESS'); ?><span style="color:red;">*</span></td>
<td><input type="text" name = "ofc_detail[address]" value="<?php echo $this->items->address; ?>" class="inputbox required" /></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_CITY'); ?><span style="color:red;">*</span></td>
<td><input type="text" name = "ofc_detail[city]" value="<?php echo $this->items->city; ?>" class="inputbox required" /></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_STATE'); ?></td>
<td>

<select name = "ofc_detail[state]" >
<?php 
foreach($usa_states as $usastate){
	?>
	<option <?php if($this->items->state == $usastate->name){ echo "selected='selected'";}?> value="<?php echo $usastate->name; ?>"><?php echo $usastate->name; ?></option>
	<?php
}

?>
</select>

<!--<input type="text" name = "ofc_detail[state]" value="<?php echo $this->items->state; ?>" class="inputbox required" />-->

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
<td><input type="text" name = "ofc_detail[country]" value="<?php echo $this->items->country; ?>" class="inputbox required" readonly="readonly"/></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_ZIP'); ?><span style="color:red;">*</span></td>
<td><input type="text" name = "ofc_detail[zip]" value="<?php echo $this->items->zip; ?>" class="zip required" /><div class="err" id="errzip"></div></td>
</tr>
</table>
</td>
</tr>
</table>
<?php 
$lid = JRequest::getVar('id');
$gid = JRequest::getVar('gid');
?>
<input type="submit" name="submit" value="Save" class="button" />
<input type="hidden" name="option" value="com_legalconfirm" /> 
<input type="hidden" name="lid" value="<?php echo $this->items->lid; ?>" /> 
<input type="hidden" name="gid" value="<?php echo $this->items->gid; ?>" /> 
<input type="hidden" name="id" value="<?php echo $this->items->id; ?>" /> 
<input type="hidden" name="task" value="offices.addoffice" /> 
<?php echo JHtml::_( 'form.token' ); ?>
</form>
</div>
