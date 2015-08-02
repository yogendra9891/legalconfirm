<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

?>

<tr id="adminformclass" class="attorney-pending-logs-class">
				
				<th>
				<?php echo JHtml::_('grid.sort', 'COM_LEGALCONFIRM_LAWFIRM_LOGS_ID', 'c.lawfirmid', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>
				
				<th>
				<?php echo JHtml::_('grid.sort', 'COM_LEGALCONFIRM_LAWFIRM_LOGS_ASSIGNTDATE', 'c.assigndate', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>
				
				<th>
				<?php echo JHtml::_('grid.sort', 'COM_LEGALCONFIRM_LAWFIRM_LOGS_STATUS', 'c.taskstatus', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>
</tr>
