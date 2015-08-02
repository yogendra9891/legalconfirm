<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
$clientid   = JRequest::getVar('id');
?>
<tr class="tfoot">
	<td colspan="5">
	<div class="pagination">	
	<?php echo $this->pagination->getListFooter(); ?>
	        	<div class="submit">
	     <a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&task=clientprofile.initiateconfirmation&id='.$clientid);?>">
	     <?php echo JText::_('COM_LEGALCONFIRM_CLIENT_INITIATE_CONFIRMATIONS');?>
	     </a>
	    </div>
	</div>
	</td>
	
</tr>
