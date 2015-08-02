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
<script>
function pintask(id,pid){
	//call ajax request to save the pin id of task
	$.ajax({
		type: "POST",
		url: "index.php?option=com_lawfirm&task=lawfirmemp.pintask",
		data: { id:id,pid:pid }
		}).done(function( msg ) {
		if(msg=="success"){
		 $('#pin'+id).hide();
         $('#unpin'+id).show();
		}
		if(msg=="already_pin"){
			alert('pinned by some other employee !! Please refresh the page to get update.');
			}
		});
}

function unpintask(id,pid){
	//call ajax request to save the pin id of task
	$.ajax({
		type: "POST",
		url: "index.php?option=com_lawfirm&task=lawfirmemp.unpintaskdefault",
		data: { id:id,pid:pid}
		}).done(function( msg ) {
			if(msg=="success"){
				 $('#unpin'+id).hide();
		         $('#pin'+id).show();
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
<div class="register-box">
<div style="float:left;"><h4>Hello <?php echo $user->name." ( employee )";?></h4></div>
<div class="assignedtask">
<a href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmemp&layout=lawfirmemptask');?>">Assigned Tasks</a>
</div>
<form name="adminForm" method="post" action = "index.php?option=com_lawfirm&view=lawfirmemp">
<table class="alllist" style="clear:both;" >
<tr class="formadmin">
<th class="textbold"><?php echo "Id";?></th>
<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_CLIENT_NAME');?></th>
<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_OWNER_NAME');?></th>
<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_ACCOUNTING_FIRM');?></th>
<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EMPLOYEE_OFFICE');?></th>
<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EMPLOYEE_DATE_INITIATED_AUDITOR');?></th>
<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EMPLOYEE_DUE_DATE');?></th>
<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_ASSIGN_CLIENT_OWNER');?></th>
</tr>
<?php 
foreach($this->items as $single){
	$proposal_detail = $this->getProposalInfo($single->pid);
	//get lawfirm name
	
	
	//start date
    $sartdate2 = date("d/M/Y", strtotime($single->assigndate)) ;
	//get end date
	$duedate = (strtotime ( $single->assigndate ))+(2*30*24*3600);
    $duedate1 = date ( 'Y-m-d H:i:s' , $duedate );
    $duedate2 = date("d/M/Y", strtotime($duedate1)) ;
	?>
	<tr>
	<td><?php echo $single->id;?></td>
	<td><?php echo $proposal_detail['company_name'];?></td>
	<td><?php echo $proposal_detail['owner_name'];?></td>
	<td><?php echo $proposal_detail['firm_name'];?></td>
	<td><?php echo $proposal_detail['office'];?></td>
	<td><?php echo $sartdate2;?></td>
	<td><?php echo $duedate2;?></td>
	<td>
	<?php if($single->emp_id=='0'){?>
	<span class="pin" id ="pin<?php echo $single->id;?>"><input style="cursor:pointer;" type="button" onclick="pintask(<?php echo $single->id ;?>,<?php echo $single->pid ;?>);" value="Click to assign"/></span>
	<span class= "unpin1" id="unpin<?php echo $single->id;?>"><input style="cursor:pointer;" type="button" onclick="unpintask(<?php echo $single->id ;?>,<?php echo $single->pid ;?>);" value="Click to unassign"/></span>
	<?php }elseif($single->is_pinbyemp =='1' && $single->emp_id != $userId){
		$lawuser = JFactory::getUser($single->emp_id);
		?>
	    <p style="background-color:darkgoldenrod;font-weight:bold; text-align: center;"><?php echo "Assigned By ".$lawuser->name; ?></p>
	<?php }elseif($single->is_pinbyemp =='1' && $single->emp_id == $userId){
		
		?>
		  <p style="background-color:darkgoldenrod;font-weight:bold; text-align: center;"><?php echo "Submitted to partner"; ?></p>
		<?php
	}else{
	?>
		<span class="pin1" id ="pin<?php echo $single->id;?>"><input style="cursor:pointer;" type="button" onclick="pintask(<?php echo $single->id ;?>,<?php echo $single->pid ;?>);" value="Click to assign"/></span>
	<span class= "unpin" id="unpin<?php echo $single->id;?>"><input style="cursor:pointer;" type="button" onclick="unpintask(<?php echo $single->id ;?>,<?php echo $single->pid ;?>);" value="Click to unassign"/></span>
	<?php
	}	?>
	</td>
	</tr>
	<?php
}
?>

</table>
<div class="pagination">
<?php if(count($this->items)>0)
       echo $this->pagination->getListFooter(); else echo JText::_('COM_MYFRIEND_NO_RESULT_FOUND');
       echo JHtml::_('form.token');?>

</div>
</div>
</form>