<?php 
$document =& JFactory::getDocument();
$document->addScript(JURI::base(). 'components/com_legalconfirm/assets/js/jquery.min.js');
$document->addScript(JURI::base(). 'components/com_lawfirm/assets/js/validation.js');
?>
<script>
//Method to validate the form
function validateForm(formId)
{
 var formId=document.getElementById(formId);
 if(validate(formId))
       {
          //get user email
          
           formId.submit();
           return true;
       }
       else{
        return false;
       }

}
</script>
<form class="form" action="<?php echo JRoute::_('index.php'); ?>"
	method="post" name="register1" id="register1"
	onsubmit="return validateForm('register1');">

<div class="changeadmin">

<table>
	<tr>
		<td>
		<table>
			<tr>
				<td><?php echo JText::_('COM_LEGALCONFIRM_NEWADMIN_NAME'); ?> </td>
				<td><input type="text" name="personal[name]"
					value=""
					class="inputbox required" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_LEGALCONFIRM_NEWADMIN_EMAIL'); ?> </td>
				<td><input type="text" name="personal[email]"
					value=""
					 class="inputbox required" /><?php echo  strstr($this->item['personalinfo']->email,'@'); ?></td>
			</tr>
		</table>
		</td>
		<td>
		
		</td>
	</tr>
	
	
</table>
 <input type="submit" name="submit" value="Save" class="button" />
</div>
<input type="hidden" name="personal[email_domain]" value="<?php echo  strstr($this->item['personalinfo']->email,'@'); ?>" /> 
<input type="hidden" name="option" value="com_legalconfirm" /> 
<input type="hidden" name="task" value="userprofile.checkAdminEmail" /> 
<?php echo JHtml::_( 'form.token' ); ?>
</form>
