<?php
/**
 * @version     1.0.0
 * @package     com_legalconfirm
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Abhishek Gupta <abhishek.gupta@daffodilsw.com> - http://
 */
// no direct access
defined('_JEXEC') or die;

?>	
<script type="text/javascript">
$(document).ready(function() {
	$("ul.pagination1").quickPagination();
});
</script>
<?php 
//get not frequent task
$no_frequent_task = $this->getTaskAssignedByPartner();
//echo "<pre>";alllist
//print_r($no_frequent_task);
//die;
$returnURL = base64_encode(JURI::root() . "\n");
//get proposal info



?>
<div class="register-box" style="margin-top:20px;">
<div style="float:left;"><h4>Hello Lawfirm Employee</h4></div>

<br />
<div style="clear:both;"><h4>Requests Not Included in Frequent Client List: [<a href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmemp&task=lawfirmemp.taskbypartner');?>">edit</a>]</h4></div>
<table class="alllist">
<tr class="formadmin">
<td class="textbold"><?php echo "Id";?></td>
<td class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_CLIENT_NAME');?></td>
<td class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_OWNER_NAME');?></td>
<td class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_ACCOUNTING_FIRM');?></td>
<td class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EMPLOYEE_OFFICE');?></td>
<td class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_VIEW_AUDITOR_REQUEST');?></td>
<td class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EMPLOYEE_DUE_DATE');?></td>
<td class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EDIT_TEMPLATE');?></td>
<td class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_SELECT_PARTNER_TO_REVIEW');?></td>
<td class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_STATUS');?></td>
</tr>
</table>
<ul class="pagination1">
<?php 
foreach($no_frequent_task as $single){
	$proposal_detail = $this->getProposalInfo($single->pid);
	
	//start date
    $sartdate2 = date("d/M/Y", strtotime($single->assigndate)) ;
	//get end date
	$duedate = (strtotime ( $single->assigndate ))+(2*30*24*3600);
    $duedate1 = date ( 'Y-m-d H:i:s' , $duedate );
    $duedate2 = date("d/M/Y", strtotime($duedate1)) ;
	?>
	<li style="list-style:none;">
	<table>
	<tr>
	<td><?php echo $single->id;?></td>
	<td><?php echo $proposal_detail['company_name'];?></td>
	<td><?php echo $proposal_detail['owner_name'];?></td>
	<td><?php echo $proposal_detail['firm_name'];?></td>
	<td><?php echo $proposal_detail['office'];?></td>
	<td><td><a href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmemp&task=lawfirmemp.getmail&tmpl=component&id='.(int) $single->pid);?>" class="modal" rel="{handler: 'iframe', size: {x: 700}}"><?php echo "View Detail" ?></a></td></td>
	<td><?php echo $duedate2;?></td>
	<td><a href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmemp&task=lawfirmemp.checkmailtemplatetype&tmpl=component&id='.(int) $single->pid);?>" class="modal" rel="{handler: 'iframe', size: {x: 700}}"><?php echo "Edit Template" ?></a></td>
	<td><?php echo "Click here";?></td>
	<td><?php echo "Prepared/Not prepared";?></td>
	</tr>
	</table>
	</li>
	<?php
}
?>
</ul>
<table>
</table>