<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

?>

<tr id="adminformclass">
				
				<!--<th>
				<?php echo JHtml::_('grid.sort', 'COM_LEGALCONFIRM_CLIENT_LOGS_ID', 'a.id', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th> -->
				
				<th>
				<?php echo JHtml::_('grid.sort', 'COM_LEGALCONFIRM_CLIENT_LOGS_REQUESTDATE', 'a.requestdate', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>
				
				<th>
				<?php echo JHtml::_('grid.sort', 'COM_LEGALCONFIRM_CLIENT_LOGS_RESPONSEDATE', 'a.responsedate', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>

				<th>
				<?php echo JHtml::_('grid.sort', 'COM_LEGALCONFIRM_CLIENT_LOGS_STATUS', 'a.status', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>
</tr>
