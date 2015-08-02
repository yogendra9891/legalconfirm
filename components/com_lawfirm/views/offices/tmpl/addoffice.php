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

<div class="register-box editprofile">
<div style="float: right"><a
	href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmadmin');?>">Dashboard</a>&nbsp;&nbsp;|&nbsp;
	<a
	href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=offices');?>">Offices</a>
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
<td><?php echo JText::_('COM_LAWFIRM_OFFICE'); ?><span style="color:red;">*</span></td>
<td><input type="text" name = "ofc_detail[office]" value="" class="inputbox required" />
</td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LAWFIRM_ADDRESS'); ?><span style="color:red;">*</span></td>
<td><input type="text" name = "ofc_detail[address]" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LAWFIRM_CITY'); ?><span style="color:red;">*</span></td>
<td><input type="text" name = "ofc_detail[city]" value="" class="inputbox required" /></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LAWFIRM_STATE'); ?></td>
<td>
<select name = "ofc_detail[state]" >
<?php 
foreach($usa_states as $usastate){
	?>
	<option value="<?php echo $usastate->name; ?>"><?php echo $usastate->name; ?></option>
	<?php
}

?>
</select>
</td>

</tr>
</table>
</td>
</tr>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LAWFIRM_COUNTRY'); ?></td>
<td><input type="text" name = "ofc_detail[country]" value="USA" class="inputbox required" readonly="readonly"/></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LAWFIRM_ZIP'); ?><span style="color:red;">*</span></td>
<td><input type="text" name = "ofc_detail[zip]" value="" class="zip required" /><div class="err" id="errzip"></div></td>
</tr>
</table>
</td>
</tr>
</table>
<input type="submit" name="submit" value="Save" class="button" />
<input type="hidden" name="option" value="com_lawfirm" /> 
<input type="hidden" name="task" value="offices.addnewoffice" /> 
<?php echo JHtml::_( 'form.token' ); ?>
</form>
</div>
