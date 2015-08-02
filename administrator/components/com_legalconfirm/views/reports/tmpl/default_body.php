<?php
JHtml::_('behavior.modal');
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

$user		= JFactory::getUser();

			 $k = 0;
			  for ($i=0, $n=count( $this->items ); $i < $n; $i++)
   				 {
			  $row =& $this->items[$i];
	    	  $checked = JHTML::_('grid.id', $i, $row->clientid );
	          $link = JRoute::_( 'index.php?option=com_legalconfirm&view=clients&layout=redirection&id='. (int)$row->clientid );
		      $ordering	= ($this->state->get('list.ordering') == 'a.ordering');
		?>
		
	<tr class="row<?php echo $i % 2; ?>" id="adminformclass">
				<td class="center">
					<?php // echo JHtml::_('grid.id', $i, $row->id); ?>
					<input type="radio" title="Checkbox for row 1" onclick="Joomla.isChecked(this.checked);" value="<?php echo $row->id; ?>" name="cid[]" id="cb<?php echo $i; ?>">
				</td>

		<td>
			<?php echo $row->id; ?>
		</td>
			
		<td>
			<?php echo $this->auditorname($row->lid); ?>
		</td>

		<td>
			<?php echo $this->clientname($row->cid); ?>
		</td>

		<td>
			<?php echo $row->amount; ?>
		</td>

		<td>
			<?php echo date("d/M/Y", strtotime($row->date)); ?>
		</td>
		<td>
			<?php echo $row->transaction_id; ?>
		</td>
		
	</tr>
	<?php
 		$k = 1 - $k; 
		} ?> 
