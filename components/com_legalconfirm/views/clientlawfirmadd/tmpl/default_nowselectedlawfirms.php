<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
$app = JFactory::getApplication();
$this->selectedlawfirmsdata = $app->getUserState('com_legalconfirm.selectedlawfirmoffices.data');
$this->selectedclientid = $app->getUserState('com_legalconfirm.clientprofile.clientid');
?>

 <table class="adminlist"><thead>
 <tr>
  				<th><input type="checkbox" name="toggle" value="" id="nowselectedlawfirms-selected-lawfirm" checked="true"/></th>
				<th>
				<?php echo JText::_('COM_LEGALCONFIRM_CLIENT_LAWFIRM_NAME'); ?>
				</th>
				
				<th>
				<?php echo JText::_('COM_LEGALCONFIRM_CLIENT_LAWFIRM_EMAIL'); ?>
				</th>
</tr></thead>
<tbody>
<?php foreach ($this->selectedlawfirmsdata as $newlawfirm): 
	$lawfirmdataarry = $this->findlawfirmdetail($newlawfirm['lawfirm']);
//	$checked = JHTML::_('grid.id', $i, $lawfirmdataarry->lawfirmid );
	?>
	<tr class="rownew">
		<td>
			
			<input type="checkbox" class="nowselectedlawfirms-lawfirms" title="JGRID_CHECKBOX_ROW_N"  value="<?php echo $lawfirmdataarry->lawfirmid;?>" name="cid[]" id="cb" checked="true"/>
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