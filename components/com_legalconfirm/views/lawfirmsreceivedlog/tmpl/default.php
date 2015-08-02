<?php
/**
 * @version     1.0.0
 * @package     com_legalconfirm
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
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
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$clientid   = JRequest::getVar('id');
?>
<div class="clientname"><h3><?php echo ucfirst($this->getProfile($clientid)->company); ?></h3>

</div>
<div class="auditor-clientlogs-wrapper lawfirmlogs-received" id="tab-container">
 <ul class='etabs'>
   <li class='tab'><a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&id='.$clientid);?>"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_PROFILE');?></a></li>
   <li class='tab'><a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientlawfirmadd&id='.$clientid);?>"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_LAWYER');?></a></li>
   <li class='tab'><a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientslog&id='.$clientid);?>"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_AUTHORIZATON_CODE');?></a></li>
   <li class='tab'><a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=lawfirmslog&id='.$clientid);?>"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_INITIATE');?></a></li>
            <li class='tab client_profile'><a class="sentoattorney" href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=lawfirmsreceivedlog&id='.$clientid);?>"><?php echo JText::_('Received');?></a></li>
 </ul>


<div class="client-proposal-logs"><?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_RESPONSES_LOGS_RECEIVED');?>
<!--  <div class="pending-received">
 
 <a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=lawfirmslog&id='.$clientid);?>"><?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_PENDING_LOGS'); ?><img src="<?php echo JURI::base().'templates/legalconfirm/images/pending_proposal.png'?>" alt="" title="pending proposals" style="padding-left: 2px; position: relative; top: 3px;"/></a>&nbsp;|&nbsp;
 <a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=lawfirmsreceivedlog&id='.$clientid);?>" class="attorney-logs-active"><?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_RESPONSED_LOGS'); ?><img src="<?php echo JURI::base().'templates/legalconfirm/images/received_proposal.png'?>" alt="edit" title="received proposals" style="padding-left: 2px; position: relative; top: 3px;" /></a>
 </div> 
-->
 
</div>
 <form action="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=lawfirmsreceivedlog&id='.$clientid);?>" name="adminForm" id="adminForm" method="post">
 	<div class="clr"> </div>
	<table class="adminlist">
		<thead><?php echo $this->loadTemplate('head');?></thead>
		<tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
		<tbody><?php echo $this->loadTemplate('body');?></tbody>
	</table> 
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<input type="hidden" name="id" value="<?php echo $clientid;?>" >
       <?php echo JHtml::_('form.token');?>

 </form>
</div>
