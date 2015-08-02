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
JHtml::_('behavior.modal');
$mainframe =& JFactory::getApplication();
$mark_id = $mainframe->getUserState( "mark_id");
$listOrder   = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
?>

<script type="text/javascript">

jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) + 
                                                $(window).scrollTop()) + "px");
    this.css("left","32%");
    return this;
}

function showpopup(a){
	
 document.getElementById('popup'+a).style.display='block';
 $('#popup'+a).center();
 document.getElementById('light'+a).style.display='block';
 document.getElementById('fade').style.display='block';  
}

function closepopup(a){
	 document.getElementById('popup'+a).style.display='none';
	 document.getElementById('light'+a).style.display='none';
	 document.getElementById('fade').style.display='none';  
}
</script>
<?php
//echo "<pre>";
//print_r($this->items);
//die;

$user = JFactory::getUser();
?>
<div class="register-box attorney-task-list">

<div class="assignedtask">
<span class="attorney-list-tasks-title">
Assigned Client List 
</span>
<span class="attorney-list-tasks">
<a
	href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmpartner');?>"><span class="task-icons">Available Tasks</span><img src="<?php echo JURI::base().'templates/legalconfirm/images/btn.png';?>" /></a>&nbsp;&nbsp;<span class="dash-border">|</span>&nbsp;
	<a
	href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmpartner&task=lawfirmpartner.edit');?>"><span class="task-icons">Edit Tasks</span><img src="<?php echo JURI::base().'templates/legalconfirm/images/edit-task.png';?>" /></a>&nbsp;&nbsp;<span class="dash-border">|</span>&nbsp;&nbsp;<a
	href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmparter&task=lawfirmpartner.notfrequentedit');?>"><span class="task-icons">Add Tasks</span><img src="<?php echo JURI::base().'templates/legalconfirm/images/add-task.png';?>" /></a>
</span>
</div>
<form name="adminForm" method="post"
	action="index.php?option=com_lawfirm&view=lawfirmpartner&layout=lawfirmpartnertask">
<table cellspacing="10" class="alllist">
	<tr class="formadmin">
		
		<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_CLIENT_NAME');?></th>
		<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_OWNER_NAME');?></th>
		<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_ACCOUNTING_FIRM');?></th>
		<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EMPLOYEE_OFFICE');?></th>
		<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_VIEW_AUDITOR_REQUEST');?></th>
		<th class="textbold">
<?php echo JHtml::_('grid.sort', 'COM_LAWFIRM_EMPLOYEE_DUE_DATE', 'a.id', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
</th>
		<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_PREPARED_BY');?></th>
		<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EDIT_TEMPLATE');?></th>
		<th class="textbold partner1"><?php echo JTEXT::_('COM_LAWFIRM_PARTNER_APPROVED');?></th>
		<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_PARTNER_SENT');?></th>
	</tr>

	<?php

	foreach($this->items as $single){
		$proposal_detail = $this->getProposalInfo($single->pid);
        //check proposal approve status by lawfirm parter
        $is_pp_approve = $this->checkApprovedProposal($single->id);
        
		//start date
		$sartdate2 = date("M/d/Y", strtotime($single->assigndate)) ;
		//get end date
		$duedate = (strtotime ( $single->assigndate ))+(2*30*24*3600);
		$duedate1 = date ( 'Y-m-d H:i:s' , $duedate );
		$duedate2 = date("M/d/Y", strtotime($duedate1)) ;
		?>

	<tr>
		<td class="marktdedit<?php echo $single->id;?>"><?php echo $proposal_detail['company_name'];?></td>
		<td class="marktdedit<?php echo $single->id;?>"><?php echo $proposal_detail['owner_name'];?></td>
		<td class="marktdedit<?php echo $single->id;?>"><?php echo $proposal_detail['firm_name'];?></td>
		<td class="marktdedit<?php echo $single->id;?>"><?php echo $proposal_detail['office'];?></td>
		<td class="marktdedit<?php echo $single->id;?>"><a
			href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmpartner&task=lawfirmpartner.getmail&tmpl=component&id='.(int) $single->pid);?>"
			class="modal" rel="{handler: 'iframe', size: {x: 700}}"><img src="<?php echo JURI::base().'templates/legalconfirm/images/view_button.png'; ?>" title="view auditor request" /></a></td>
		<td class="marktdedit<?php echo $single->id;?>"><?php echo $duedate2;?></td>
		<td class="marktdedit<?php echo $single->id;?>"><?php 
		//get current login user id
		$cuser = JFactory::getUser();
		//check for task status complete or not
		if($single->taskstatus == 0){
		if($single->emp_id != "" && $single->emp_id !=$cuser->id){
			//get user object
			$user = JFactory::getUser($single->emp_id);
			$user_name = $user->name;
			echo $user_name;
		}else{ echo "Not prepared";}
		}else{
			$user = JFactory::getUser($single->emp_id);
			$user_name = $user->name;
			echo $user_name;
		}
		?></td>
		<td class="marktdedit<?php echo $single->id;?>">
		<?php 
		if($single->emp_id != "" && $single->emp_id !=$cuser->id){
			//get user object
			$user = JFactory::getUser($single->emp_id);
			$user_name = $user->name;
			
			?>
			<a
			href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmpartner&task=lawfirmpartner.checkmailtemplatetype&tmpl=component&id='.(int) $single->id);?>"
		class="modal" rel="{handler: 'iframe', size: {x: 700,y: 550},iframeOptions: {scrolling: 'no'}}"><img src="<?php echo JURI::base().'templates/legalconfirm/images/view_button.png'; ?>" title="view template" /></a>
			<?php 
		}else{ 
		
			if($single->taskstatus == 1){
			?>
			<a
			href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmpartner&task=lawfirmpartner.checkmailtemplatetype&tmpl=component&id='.(int) $single->id);?>"
		class="modal" rel="{handler: 'iframe', size: {x: 700,y: 550},iframeOptions: {scrolling: 'no'}}"><img src="<?php echo JURI::base().'templates/legalconfirm/images/view_button.png'; ?>" title="view template" /></a>
			<?php 
		}else{?>
		<a href="javascript:void(0)"
				onclick="showpopup(<?php echo $single->id?>)"><img src="<?php echo JURI::base().'templates/legalconfirm/images/edit_button.png'; ?>" title="edit template" /></a>
		
		<?php 
		}
		}
		?>
		</td>
		<td class="marktdedit<?php echo $single->id;?>">
		<?php 
		//check if proposal has approved by lawfirm parter
		if($is_pp_approve == 1){
			$is_pp_approve_forhidden = "1"; ?>
		<img src="<?php echo JURI::base().'templates/legalconfirm/images/tick.png'; ?>" alt="Yes" title="approved" /> 
	<?php	}else{
			$is_pp_approve_forhidden = "0";
?>
<div id="changeactive<?php echo $single->id;?>">
<img src="<?php echo JURI::base().'templates/legalconfirm/images/publish_x.png'; ?>" alt="No" title="not approved" /> 
</div>
<?php	
		}
		
		?>
		<input type="hidden" value="<?php echo $is_pp_approve_forhidden;?>" id="approved<?php echo $single->id;?>" />
		</td>
		<td class="marktdedit<?php echo $single->id;?>">
       <?php if($single->taskstatus == 1){ ?>
    <img src="<?php echo JURI::base().'templates/legalconfirm/images/tick.png'; ?>" alt="Yes" title="sent to auditor" /> 
	<?php }else{ ?>
	<img src="<?php echo JURI::base().'templates/legalconfirm/images/no_button.png'; ?>" alt="No" title="not send to auditor" /> 
	<?php }?>
        </td>
	</tr>

	<div id="popup<?php echo $single->id; ?>" class="tpopup">
	<div id="light<?php echo $single->id; ?>" class="white_content">
	<div class="register-box" style="min-height: 44px;text-align:center;">
	<a onclick="closepopup(<?php echo $single->id ;?>)" href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmpartner&task=lawfirmpartner.checkmailtemplatetypePartner&tmpl=component&id='.(int) $single->id);?>"
		class="modal" rel="{handler: 'iframe', size: {x: 700,y: 560},iframeOptions: {scrolling: 'no'}}">
		
		<?php echo "Edit Template" ?>
		<img src="<?php echo JURI::base().'templates/legalconfirm/images/edit_button.png'; ?>" title="edit template" />
		</a>
		<span class="template-or-pdf">OR</span>
	<a onclick="closepopup(<?php echo $single->id ;?>)" href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmpartner&task=lawfirmpartner.checkmailtemplatetypePartner&tmpl=component&type=pdf&id='.(int) $single->id);?>"
		class="modal" rel="{handler: 'iframe', size: {x: 700,y: 260},iframeOptions: {scrolling: 'no'}}"><?php echo "Upload Pdf" ?>
		<img src="<?php echo JURI::base().'templates/legalconfirm/images/pdf_icon.png'; ?>" title="edit template" width="16" />
		</a>	
	<span class="sbox-btn-close1" style="float:right">
	<a href="javascript:void(0)"
		onclick="closepopup(<?php echo $single->id ;?>)"><img src="<?php echo JURI::base().'templates/legalconfirm/images/closebox.png';?>"></a></span></div>
	</div>
	</div>

	<?php
	}
	?>


</table>

<div class="pagination"><?php if(count($this->items)>0)
echo $this->paginations->getListFooter(); else { ?> <span class="no-data-found"><?php echo JText::_('COM_MYFRIEND_NO_RESULT_FOUND'); ?></span> <?php }
echo JHtml::_('form.token');?></div>
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
</form>
</div>
<?php //echo $this->loadTemplate('notfrequent');?>

<div id="fade" class="black_overlay"></div>

<script>
	$(document).ready(function(){
		$('.marktdedit<?php echo $mark_id;?>').css( "background-color", "#E3E181" );
		
		//return false;
	});
$('#fade').click(function(){
	 document.getElementById('fade').style.display='none';  
         $('.tpopup').hide();
});
</script>
<?php 
 $mainframe->setUserState( "mark_id", "" );
?>
