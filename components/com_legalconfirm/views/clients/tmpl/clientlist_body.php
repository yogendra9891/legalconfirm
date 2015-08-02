<?php
JHtml::_('behavior.modal');
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

$user		= JFactory::getUser();

			 $k = 0;
			  if(count( $this->items ))
			  for ($i=0, $n=count( $this->items ); $i < $n; $i++)
   				 {
			  $row =& $this->items[$i];
	    	  $checked = JHTML::_('grid.id', $i, $row->clientid );
	          $link = JRoute::_( 'index.php?option=com_legalconfirm&view=clients&layout=redirection&id='. (int)$row->clientid );
		      $ordering	= ($this->state->get('list.ordering') == 'a.ordering');
		?>
		
	<tr class="row<?php echo $i % 2; ?>" id="adminformclass">
		<td>
			<?php //echo $checked;?>
			<input type="checkbox" onclick="Joomla.isChecked(this.checked);" value="<?php echo $row->clientid; ?>" name="cid[]" id="cb<?php echo $i;?>">
		</td>
		<td>
			<a href="<?php echo $link; ?>"><?php echo $row->company; ?></a>
		</td>
			
		<td>
			<?php echo $row->engagementno; ?>
		</td>

		<td>
			<?php echo $row->fname. ' '. $row->lname; ?>
		</td>

		<td>
			<?php echo date("M/d/Y H:i:s", strtotime($row->previewdate)); ?>
		</td>
		
	</tr>
	<?php
 		$k = 1 - $k; 
		}else { ?> 
		<tr><td style="text-align: center; float:left; width: 100px;">No Result found.</td></tr>
		<?php }?> 
