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
			<?php if($row->assigndate != 0): echo date("M/d/Y H:i:s", strtotime($row->assigndate)); endif;?>
		</td>

		<td>
			<?php if($row->responsedate == 0) echo "Not available"; else echo date("M/d/Y H:i:s", strtotime($row->responsedate));?>
		</td>

    	<td>
	       <?php if($row->taskstatus == '1'): 
		  $templateresult = $this->findtemplateresponse($row->assignid); 
		  if($templateresult->template_type == 'custom'):?><a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&task=lawfirmsreceivedlog.generatepdf&assign_proposalid='.$row->assignid.'&lawfirmid='.$row->lawfirmid.'&pid='.$row->pid.'&id='.$clientid);?>"  target="_blank">Download&nbsp;<img src="<?php echo JURI::base().'templates/legalconfirm/images/received_proposal.png'?>" alt="edit" title="received proposals" style="padding-left: 2px; position: relative; top: 3px;" /></a>
		  <?php elseif($templateresult->template_type == 'pdf'):?><a href="<?php echo JRoute::_(JURI::base().'media/com_lawfirm/pdf/'.$templateresult->pdf)?>"  target="_blank">Download&nbsp;<img src="<?php echo JURI::base().'templates/legalconfirm/images/received_proposal.png'?>" alt="edit" title="received proposals" style="padding-left: 2px; position: relative; top: 3px;" /></a>
		  <?php endif;?>
		  <?php endif;?>
		</td>
		
	</tr>
	<?php
 		$k = 1 - $k; 
		} ?> 
