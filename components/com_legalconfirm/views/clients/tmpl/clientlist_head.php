<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

?>

<tr id="adminformclass">
			    <th  width="5%">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>	
				
				<th>
				<?php echo JHtml::_('grid.sort', 'COM_LEGALCONFIRM_CLIENT_COMPANY', 'a.company', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>
				
				<th>
				<?php echo JHtml::_('grid.sort', 'COM_LEGALCONFIRM_CLIENT_ENGANEMENTNO', 'a.engagementno', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>
				
				<th>
				<?php echo JHtml::_('grid.sort', 'COM_LEGALCONFIRM_CLIENT_SIGNERE_TITLE', 'b.signertitle', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>

				<th>
				<?php echo JHtml::_('grid.sort', 'COM_LEGALCONFIRM_CLIENT_SIGNERE_PREVIEW_DATE', 'a.previewdate', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>
</tr>