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
<style type="text/css">
.pin2 {
display:none;

}
</style>
<script type="text/javascript">
$(document).ready(function() {
	$("ul.pagination1").quickPagination();
});
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
		}
		});
}

function unpintask(id,pid){
	var x;
	var r=confirm("Are you sure !!");
	if (r==true)
	  {
		//call ajax request to save the pin id of task
		$.ajax({
			type: "POST",
			url: "index.php?option=com_lawfirm&task=lawfirmemp.unpintaskfrompartner",
			data: { id:id,pid:pid}
			}).done(function( msg ) {
				
			if(msg=="success"){
				//$('#row'+id).fadeIn("slow");
			 var usermsg = "Task removed from your task list successfully."
			$('#msg').html(usermsg);
			// $('#row'+id).css("background-color", "red");
			$('#unpin'+id).hide();
			$('#pin'+id).show();
			// $('#row'+id).hide("slow");
			}
			});
	  }
	else
	  {
	 
	  }
	
}
</script>
<?php 
//	echo "<pre>";
//	print_r($this->items);
//	die;
$returnURL = base64_encode(JURI::root() . "\n");
//get proposal info



?>
<div class="register-box">
<div style="float:left;"><h4>Hello Lawfirm Employee</h4></div>
<div style="clear:both;"><p>Edit client from frequent list</p></div>
<div class="assignedtask">
[<a href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmemp&layout=lawfirmemptask');?>">Assigned Tasks</a>]
</div>
<div style="clear:both;">
<p id="msg"></p>
<table>
<tr class="formadmin">
<td class="textbold"><?php echo "Id";?></td>
<td class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_CLIENT_NAME');?></td>
<td class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_OWNER_NAME');?></td>
<td class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_ACCOUNTING_FIRM');?></td>
<td class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EMPLOYEE_OFFICE');?></td>
<td class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EMPLOYEE_DATE_INITIATED_AUDITOR');?></td>
<td class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EMPLOYEE_DUE_DATE');?></td>
<td class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_PIN_CLIENT_OWNER');?></td>
</tr>
</table>
<ul class="pagination1">
<?php 
foreach($this->items as $single){
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
	<tr id="row<?php echo $single->id;?>">
	<td><?php echo $single->id;?></td>
	<td><?php echo $proposal_detail['company_name'];?></td>
	<td><?php echo $proposal_detail['owner_name'];?></td>
	<td><?php echo $proposal_detail['firm_name'];?></td>
	<td><?php echo $proposal_detail['office'];?></td>
	<td><?php echo $sartdate2;?></td>
	<td><?php echo $duedate2;?></td>
	<td>
	<?php if($single->is_pin == '0'){?>
	<span class="pin2" id ="pin<?php echo $single->id;?>"><input style="cursor:pointer;" type="button" onclick="pintask(<?php echo $single->id ;?>,<?php echo $single->pid ;?>,<?php echo $single->aid ;?>);" value="Pin"/></span>
	<?php } else {?>
	<span class="pin2" id ="pin<?php echo $single->id;?>"><input style="cursor:pointer;" type="button" onclick="pintask(<?php echo $single->id ;?>,<?php echo $single->pid ;?>);" value="Pin"/></span>
	<span class= "unpin2" id="unpin<?php echo $single->id;?>"><input style="cursor:pointer;" type="button" onclick="unpintask(<?php echo $single->id ;?>,<?php echo $single->pid ;?>);" value="UnPin"/></span>
	<?php } ?>
	</td>
	</tr>
	</table>
	</li>
	<?php
}
?>
</ul>
<table>
</table>
</div>
</div>
