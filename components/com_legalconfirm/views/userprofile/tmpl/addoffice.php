<?php 
$getmsg = JRequest::getVar('msgtype');
 
if($getmsg=="success"){
?>
<script>
var test="<?php echo $getmsg; ?>";
window.parent.location = window.top.location.href+'&message='+test;
window.parent.SqueezeBox.close();
</script>
<?php } ?>

<form class="form"
	action="<?php echo JRoute::_('index.php'); ?>"
	method="post" name="addoffice" id="addoffice" onsubmit="return validateForm('addoffice');">
<fieldset>
  <legend>Office Detail:</legend>
<table>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_OFFICE'); ?></td>
<td><input type="text" name = "ofc_detail[office]" value="<?php echo $office->office_title; ?>" class="inputbox required" />
</td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_ADDRESS'); ?></td>
<td><input type="text" name = "ofc_detail[address]" value="<?php echo $office->address; ?>" class="inputbox required" /></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_CITY'); ?></td>
<td><input type="text" name = "ofc_detail[city]" value="<?php echo $office->city; ?>" class="inputbox required" /></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_STATE'); ?></td>
<td><input type="text" name = "ofc_detail[state]" value="<?php echo $office->state; ?>" class="inputbox required" /></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_COUNTRY'); ?></td>
<td><input type="text" name = "ofc_detail[country]" value="<?php echo $office->country; ?>" class="inputbox required" /></td>
</tr>
</table>
</td>
<td>
<table>
<tr>
<td><?php echo JText::_('COM_LEGALCONFIRM_ZIP'); ?></td>
<td><input type="text" name = "ofc_detail[zip]" value="<?php echo $office->zip; ?>" class="inputbox required" /></td>
</tr>
</table>
</td>
</tr>
</table>
</fieldset>
<?php 
$lid = JRequest::getVar('id');
$gid = JRequest::getVar('gid');
?>
<input type="submit" name="submit" value="Save" class="button" />
<input type="hidden" name="option" value="com_legalconfirm" /> 
<input type="text" name="lid" value="<?php echo $lid;?>" /> 
<input type="text" name="gid" value="<?php echo $gid;?>" /> 
<input type="hidden" name="task" value="userprofile.addoffice" /> 
<?php echo JHtml::_( 'form.token' ); ?>
</form>

