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
	          $link = JRoute::_( 'index.php?option=com_legalconfirm&view=clientslog&tmpl=component&layout=proposaldetail&pid='.(int)$row->id.'&id='.$clientid  );
		      $ordering	= ($this->state->get('list.ordering') == 'a.ordering');
		?>
		
	<tr class="row<?php echo $i % 2; ?>" id="adminformclass">
		<!--<td>
			<a href="<?php echo $link;?>" ><?php echo $row->id;?></a>
             <?php echo $row->id;?>
		</td>-->

		<td>
			<?php echo date("M/d/Y H:i:s", strtotime($row->requestdate)); ?>
		</td>

		<td>
			<?php 
   				 if($row->responsedate == 0){
					    echo "Response Pending"; 
					}else
			echo date("M/d/Y H:i:s", strtotime($row->responsedate)); ?>
		</td>

		<td>
			<?php
				if($row->status == '2') $status = 'Denied'; 
				elseif($row->status == '0') $status = 'Pending';
				elseif($row->status == '1') $status = 'Approved'; 
				elseif($row->status == '3') $status = 'Ignored'; 
			    echo $status; ?>
		</td>
		
	</tr>
	<?php
 		$k = 1 - $k; 
		} ?> 
