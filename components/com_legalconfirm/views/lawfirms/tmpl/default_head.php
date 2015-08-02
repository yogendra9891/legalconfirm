<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

?>

<tr id="adminformclass">
			    <th>
<!--				<input type="radio" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />-->
				</th>	
				
				<th>
				<?php echo JHtml::_('grid.sort', 'COM_LEGALCONFIRM_CLIENT_LAWFIRM_NAME', 'b.accounting_firm', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>
				
				<th>
				<?php echo JHtml::_('grid.sort', 'COM_LEGALCONFIRM_CLIENT_LAWFIRM_EMAIL', 'a.email', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>
				
</tr>