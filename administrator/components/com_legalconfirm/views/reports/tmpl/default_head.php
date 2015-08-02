<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

?>

<tr id="adminformclass">
				<th width="1%">
				<!--	<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" /> -->
				</th>

				<th>
				<?php echo JHtml::_('grid.sort', 'COM_LEGALCONFIRM_CLIENT_ID', 'a.id', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>
				
				<th>
				<?php echo JHtml::_('grid.sort', 'COM_LEGALCONFIRM_CLIENT_AUDITOR', 'a.lid', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>
				
				<th>
				<?php echo JHtml::_('grid.sort', 'COM_LEGALCONFIRM_AUDITOR_CLIENT', 'a.cid', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>

				<th>
				<?php echo JHtml::_('grid.sort', 'COM_LEGALCONFIRM_CLIENT_TRANSACTION_AMOUNT', 'a.amount', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>

				<th>
				<?php echo JHtml::_('grid.sort', 'COM_LEGALCONFIRM_CLIENT_TRANSACTION_DATE', 'a.date', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>

				<th>
				<?php echo JHtml::_('grid.sort', 'COM_LEGALCONFIRM_CLIENT_TRANSACTION_ID', 'a.transaction_id', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>

</tr>
