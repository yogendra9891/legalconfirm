<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
$check = count( $this->items );
if(!$check)
{
$checktry = 'disabled=true';
}
?>

<tr>
			    <th width="10%">
                   <input type="checkbox" name="toggle" value="" id="now-to-becurrent-selected-lawfirm"  <?php echo $checktry; ?>/>
				</th>	
				
				<th width="51%">
				<?php echo JHtml::_('grid.sort', 'COM_LEGALCONFIRM_CLIENT_LAWFIRM_NAME', 'b.accounting_firm', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>
				
				<th width="39%">
				<?php //echo JHtml::_('grid.sort', 'COM_LEGALCONFIRM_CLIENT_LAWFIRM_EMAIL', 'a.email', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
				</th>
				
</tr>
