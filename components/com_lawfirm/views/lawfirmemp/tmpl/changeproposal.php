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
$listOrder   = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
?>	
<style type="text/css">
.pin2 {
display:none;

}
</style>
<script type="text/javascript">

</script>
<script>
function pintask(aid,pid){
	//call ajax request to save the pin id of task
	$.ajax({
		type: "POST",
		url: "index.php?option=com_lawfirm&task=lawfirmemp.pintask",
		data: { id:aid,pid:pid}
		}).done(function( msg ) {
		if(msg=="success"){
		 $('#pin'+aid).hide();
         $('#unpin'+aid).show();
         var usermsg = "Task added to your task list successfully.";
		 $('#msg').hide();
		 $('#msg').show("slow");	
		 $('#msg').html(usermsg);
		 $('#msg').css("background-color", "#7469B8");
		 $('#msg').css("color", "#ffffff");
		 $('#msg').css("font-weight", "bold");
		}
		});
}

function unpintask(id,pid){

	//check if proposal is waiting for approval by partner
	//if waiting for approval then employee can not unpin the task
	var is_approve = $('#approvestatus'+id).val();
	if(is_approve == "0" || is_approve == "1"){
    alert('This task is waiting for approval.You can not unpin task.');
    return false;
	}
//jquery confirm box
$.msgBox({
    title: "Are You Sure?",
    content: "Please confirm task unassignment.",
    type: "confirm",
    buttons: [{ value: "Yes" }, { value: "No" }, { value: "Cancel"}],
    success: function (result) {
        if (result == "Yes") {
           //call ajax request to save the pin id of task
		$.ajax({
			type: "POST",
			url: "index.php?option=com_lawfirm&task=lawfirmemp.unpintask",
			data: { id:id,pid:pid}
			}).done(function( msg ) {
				
			if(msg=="success"){
				//$('#row'+id).fadeIn("slow");
			 var usermsg = "Task removed from your task list successfully.";
			 $('#msg').hide();
			 $('#msg').show("slow");	
			$('#msg').html(usermsg);
			
			$('#msg').css("background-color", "#7469B8");
			$('#msg').css("color", "#fff");
			$('#msg').css("font-weight", "bold");
			// $('#row'+id).css("background-color", "orange");
			$('#unpin'+id).hide();
			$('#pin'+id).show();
			// $('#row'+id).hide("slow");
			}
			});
        }
    }
});	
}
</script>
<?php 

$returnURL = base64_encode(JURI::root() . "\n");
//get proposal info
//get current login user
$user = JFactory::getUser();
$userId = $user->id;


?>
<div class="register-box attorney-task-list">
<div class="assignedtask">
<span class="attorney-list-tasks-title">You can edit the assigned client from here</span>
<span class="attorney-list-tasks">
<a href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmemp&layout=lawfirmemptask');?>"><span style="position:relative;top:-6px;">Assigned Tasks</span><img src='<?php echo JURI::root()."templates/legalconfirm/images/tasks.png"?>' /></a><span>
</div>
<div style="clear:both;">
<p id="msg"></p>
<form name="adminForm" method="post" action = "index.php?option=com_lawfirm&view=lawfirmemp&task=lawfirmemp.edit">
<table class="alllist">
<tr class="formadmin">

<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_CLIENT_NAME');?></th>
<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_OWNER_NAME');?></th>
<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_ACCOUNTING_FIRM');?></th>
<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EMPLOYEE_OFFICE');?></th>
<th class="textbold">
<?php echo JHtml::_('grid.sort', 'COM_LAWFIRM_EMPLOYEE_DATE_INITIATED_AUDITOR', 'a.id', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
</th>
<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EMPLOYEE_DUE_DATE');?></th>
<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_UNASSIGN_CLIENT_OWNER');?></th>
</tr>

<?php 
foreach($this->items as $single){
	$proposal_detail = $this->getProposalInfo($single->pid);
	
	//check for proposal waitig for approval
	$check_proposal = $this->checkProposalForApproval($single->id);
	
	
	//start date
    $sartdate2 = date("M/d/Y", strtotime($single->assigndate)) ;
	//get end date
	$duedate = (strtotime ( $single->assigndate ))+(2*30*24*3600);
    $duedate1 = date ( 'Y-m-d H:i:s' , $duedate );
    $duedate2 = date("M/d/Y", strtotime($duedate1)) ;
	?>
	
	<tr id="row<?php echo $single->id;?>">
	<td><?php echo $proposal_detail['company_name'];?></td>
	<td><?php echo $proposal_detail['owner_name'];?></td>
	<td><?php echo $proposal_detail['firm_name'];?></td>
	<td><?php echo $proposal_detail['office'];?></td>
	<td><?php echo $sartdate2;?></td>
	<td><?php echo $duedate2;?></td>
	<td>
	<?php 
	if($single->is_readybyemp == 0){
	if($single->is_pin == '0'){?>
	<span class="pin2" id ="pin<?php echo $single->id;?>"><input style="cursor:pointer;" type="button" onclick="pintask(<?php echo $single->id ;?>,<?php echo $single->pid ;?>,<?php echo $single->aid ;?>);" value="Click to assign"/></span>
	<?php } else {?>
	<span class="pin2" id ="pin<?php echo $single->id;?>"><input style="cursor:pointer;" type="button" onclick="pintask(<?php echo $single->id ;?>,<?php echo $single->pid ;?>);" value="Click to assign"/></span>
	<span class= "unpin2" id="unpin<?php echo $single->id;?>"><input style="cursor:pointer;" type="button" onclick="unpintask(<?php echo $single->id ;?>,<?php echo $single->pid ;?>);" value="Click to unassign"/></span>
	<?php } ?>
	<input type="hidden" value="<?php echo $check_proposal;?>" id="approvestatus<?php echo $single->id;?>"/>
	</td>
	</tr>
	
	<?php
	}else{
	echo '<span class="partner-submit">Submitted to partner</span>';
	}
}
?>

</table>
</div>
<div class="pagination">
<?php if(count($this->items)>0)
       echo $this->paginations->getListFooter(); else echo JText::_('COM_MYFRIEND_NO_RESULT_FOUND');
       echo JHtml::_('form.token');?>
       	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
       </div>
</form>
</div>
