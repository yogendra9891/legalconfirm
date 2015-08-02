<?php
/**
 * @version     1.0.0
 * @package     com_legalconfirm
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt, 22 June, 2013
 * @author      Abhishek Gupta <abhishek.gupta@daffodilsw.com> - http://
 */
// no direct access
defined('_JEXEC') or die('Restricted Access'); 
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.keepalive');
require_once JPATH_COMPONENT.'/helpers/legalconfirm.php';
// load tooltip behavior
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
$user		= JFactory::getUser();
$userId		= $user->get('id');
$pid        = JRequest::getVar('pid');
$clientid   = JRequest::getVar('id');
$lawfirms = $this->findLawfirms($pid, $clientid); //echo "<pre>"; print_r($lawfirms); exit;
?>
<!-- Note in case of the proposal Ignored/denied/pending the lawfirms will not seen in this layout.
     If you wants to show lawfirms of a proposal then you have to first find the status from clientproposal table, if these are Ignored(3)/Denied(2)/Pending(0) then
     find these lawfirm from lawfirmsproposal table and handle below in for loop 
 -->
<div class="auditor-clientlogs-lawfirm-wrapper">
	<div class="auditor-clientlogs-lawfirm-header"><span><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_LOGS_LAWFIRMS');?></span></div>
<fieldset>
	<legend>
	<?php echo JText::_('COM_LEGALCONFIRM_CLIENT_PROPOSAL_LAWFIRMS');?>
	</legend>
	<div class="lawfirm-task-details">
	<table>
	<tr>
	<th><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_PROPOSAL_LAWFIRMS_NAME');?></th>
	<th><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_PROPOSAL_LAWFIRMS_EMPLOYEE');?></th>
	<th><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_PROPOSAL_LAWFIRMS_TASK_STATUS');?></th>
	<th><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_PROPOSAL_LAWFIRMS_EMPLOYEE_RESPONSE');?></th>
	</tr>
	<?php for ($i=0, $n=count( $lawfirms ); $i < $n; $i++):
			  $row =& $lawfirms[$i];
		 ?>
		 <tr class="findtemplate">
		 <td><?php echo $this->lawfirmname($row->lawfirmid);?></td>
		 <td><?php echo $this->employeename($row->emp_id);?></td>
		 <td><?php
		   if($row->taskstatus == '0') $status = 'pending'; elseif($row->taskstatus == '1') $status = 'complete'; elseif($row->taskstatus == '2') $status = 'expired';
		   echo $status;?></td>
		 <td><?php if($row->taskstatus == '1'): 
		 $templateresult = $this->findtemplateresponse($row->id); 
		  if($templateresult->template_type == 'custom'):?><a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&task=clientslog.generatepdf&assign_proposalid='.$row->id.'&pid='.$pid.'&id='.$clientid);?>"  target="_blank">click here</a>
		  <?php elseif($templateresult->template_type == 'pdf'):?><a href="<?php echo JRoute::_(JURI::base().'media/com_lawfirm/pdf/'.$templateresult->pdf)?>"  target="_blank">click here</a>
		  <?php endif;?>
		     <?php else:?>Not available
		     <?php endif;?>
		 </td> 
		 </tr>
	<?php endfor;?>
	</table>
	</div>
	<div class="submit">
	<a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientslog&tmpl=component&id='.$clientid);?>"><?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_BACK');?></a>
	</div>
</fieldset>
</div>