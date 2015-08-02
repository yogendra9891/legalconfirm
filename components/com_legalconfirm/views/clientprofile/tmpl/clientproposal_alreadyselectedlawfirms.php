<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
if(count($this->alreadySelectedLawfirm)){
?>
<div class="alreadyselectedlawfirms">
<fieldset>
 <legend><?php echo JText::_('COM_LEGALCONFIRM_LAW_FIRM_ALREADY_SELECTED');?></legend>
 <table class="adminlist"><thead>
 <tr>
  				<th><input type="checkbox" name="toggle" checked="true" id="alreadyselected-lawfirm" value="" /></th>
				<th>
				<?php echo JText::_('COM_LEGALCONFIRM_CLIENT_LAWFIRM_NAME'); ?>
				</th>
				
				<th>
				<?php echo JText::_('COM_LEGALCONFIRM_CLIENT_LAWFIRM_EMAIL'); ?>
				</th>
</tr></thead>
<tbody>
<?php foreach ($this->alreadySelectedLawfirm as $newlawfirm): 
	$lawfirmdataarry = $this->findlawfirmdetail($newlawfirm->lawfirmid);
	$checked = JHTML::_('grid.id', $i, $lawfirmdataarry->lawfirmid );
	?>
	<tr class="rownew">
		<td >
			<input type="checkbox" class="todocheck" checked="true" onclick="Joomla.isChecked(this.checked);" value="<?php echo $lawfirmdataarry->lawfirmid;?>" name="cid[]" id="cb">
		</td>
		<td>
			<?php echo $lawfirmdataarry->accounting_firm; ?>
		</td>
			
		<td>
			<?php echo $lawfirmdataarry->email; ?>
		</td>
		
	</tr>
	<?php endforeach;?>
</tbody>
</table>
</fieldset>
 </div>
 <?php }?>
