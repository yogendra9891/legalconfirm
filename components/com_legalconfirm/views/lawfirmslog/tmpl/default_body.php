<?php
JHtml::_('behavior.modal');
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
$clientid   = JRequest::getVar('id');
$user		= JFactory::getUser();

			  $k = 0;
			  for ($i=0, $n=count( $this->items ); $i < $n; $i++)
   				 {
			  $row =& $this->items[$i];
		      $ordering	= ($this->state->get('list.ordering') == 'a.ordering');
		?>
		
	<tr class="row<?php echo $i % 2; ?>" id="adminformclass">
		<td>
			<?php echo $this->lawfirmname($row->lawfirmid);?>
		</td>

		<td>
			<?php echo date("M/d/Y H:i:s", strtotime($row->assigndate)); ?>
		</td>

    	<td>
			<?php
				if($row->taskstatus == '2') { $status = 'Expired'; echo $status; }
				elseif($row->taskstatus == '0') { ?>  <img src="<?php echo JURI::base().'templates/legalconfirm/images/pending_proposal.png'?>" alt="" title="Pending" /><?php  }
			     ?>
		</td>
		
	</tr>
	<?php
 		$k = 1 - $k; 
		} ?> 
