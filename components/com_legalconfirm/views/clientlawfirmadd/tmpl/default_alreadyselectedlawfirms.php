<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
if(count($this->alreadySelectedLawfirm)){
?>
<fieldset>
  <legend><?php echo JText::_('COM_LEGALCONFIRM_LAW_FIRM_ALREADY_SELECTED');?></legend>
<div class="alreadyselectedlawfirms">
 <table class="adminlist"><thead>
 <tr>
  				<th><input type="checkbox" name="toggle" value="" id="alreadyselectedlawfirms-selected-lawfirm" /></th>
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
		<td id="alreadyselected-done">
			<input type="checkbox" class="lasttobeselected" title="JGRID_CHECKBOX_ROW_N"  value="<?php echo $lawfirmdataarry->lawfirmid;?>" name="cid[]" id="cb" />
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
</div>
</fieldset>
 <?php }?>