<?php
JHtml::_('behavior.modal');
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
jimport( 'zest.html.grid' );
$user		= JFactory::getUser();

			 $k = 0;
			  for ($i=0, $n=count( $this->items ); $i < $n; $i++)
   				 {
			  $row =& $this->items[$i];
			  //$checked commented by yogendra because we wants radio button selection....
			  // for this added a new library by yogendra in libraries/zest/html/grid.php
	    	 // $checked = JHTML::_('grid.id', $i, $row->id );
	    	// $checked = JHTMLGridZest::id( $i, $row->lawfirmid );
	    	$checked = '<input type="radio" id="cb'.$i.'" name="cid[]" value="'.$row->lawfirmid.'" />';
	      // $link = JRoute::_( 'index.php?option=com_legalconfirm&view=clientprofile&id='. (int)$row->id );
		     $ordering	= ($this->state->get('list.ordering') == 'a.ordering');
		?>
		
	<tr class="row<?php echo $i % 2; ?>" id="adminformclass">
		<td>
			<?php echo $checked;?>
		</td>
		<td>
			<?php echo $row->accounting_firm; ?>
		</td>
			
		<td>
			<?php echo $row->email; ?>
		</td>
		
	</tr>
	<?php
 		$k = 1 - $k; 
		} ?> 
