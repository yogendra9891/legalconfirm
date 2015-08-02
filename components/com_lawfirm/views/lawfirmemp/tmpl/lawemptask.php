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
//echo "<pre>";
//print_r($this->items);
//die;
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
$returnURL = base64_encode(JURI::root() . "\n");
//get proposal info
//get partner of employee
$lawfirm_partners = $this->lawfirmpartner();
//get current login user
$user = JFactory::getUser();

//echo "<pre>";
//print_r($lawfirm_partners);
//die;

?>
<div class="register-box attorney-task-list-assigned">

<div class="assignedtask" style="clear:both;">
<span class="attorney-list-tasks-title">
Assigned Client List 
</span>
<span class="attorney-list-tasks">
<a
	href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmemp');?>"><span class="task-icons">Available Tasks</span><img src="<?php echo JURI::base().'templates/legalconfirm/images/btn.png';?>" /></a>&nbsp;&nbsp;<span class="dash-border">|</span>&nbsp;
	<a
	href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmemp&task=lawfirmemp.edit');?>"><span class="task-icons">Edit Tasks</span><img src="<?php echo JURI::base().'templates/legalconfirm/images/edit-task.png';?>" /></a>&nbsp;&nbsp;<span class="dash-border">|</span>&nbsp;&nbsp;<a
	href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmemp&task=lawfirmemp.notfrequentedit');?>"><span class="task-icons">Add Tasks</span><img src="<?php echo JURI::base().'templates/legalconfirm/images/add-task.png';?>" /></a>
</span>
</div>
<form name="adminForm" method="post" action = "index.php?option=com_lawfirm&view=lawfirmemp&layout=lawfirmemptask">
<table cellspacing="10" class="alllist">
	<tr class="formadmin">
		
		<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_CLIENT_NAME');?></th>
		<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_OWNER_NAME');?></td>
		<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_ACCOUNTING_FIRM');?></th>
		<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EMPLOYEE_OFFICE');?></th>
		<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_VIEW_AUDITOR_REQUEST');?></th>
		<th class="textbold">
<?php echo JHtml::_('grid.sort', 'COM_LAWFIRM_EMPLOYEE_DUE_DATE', 'a.id', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
</th>
		<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EDIT_TEMPLATE');?></th>
		<th class="textbold partner1"><?php echo JTEXT::_('COM_LAWFIRM_SELECT_PARTNER_TO_REVIEW');?></th>
		<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_STATUS');?></th>
	</tr>

<?php

foreach($this->items as $single){
	$proposal_detail = $this->getProposalInfo($single->pid);

	//get assigned partner
	$assigned_partner = $this->getassignedpartner($single->id);
	
	//check for the task is_disapprove proposal
	$is_disapprove = $this->checkDisapprove($single->id);

	
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
				href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmemp&task=lawfirmemp.getmail&tmpl=component&id='.(int) $single->pid);?>"
				class="modal" rel="{handler: 'iframe', size: {x: 700,y: 500}}"><img src="<?php echo JURI::base().'templates/legalconfirm/images/view_button.png'; ?>" alt="View Template" title="view template"/></a></td>
			<td class="marktdedit<?php echo $single->id;?>"><?php echo $duedate2;?></td>
			<td class="marktdedit<?php echo $single->id;?>">
			<?php if($single->is_readybyemp ==1){
		?>
		<a href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmemp&task=lawfirmemp.getmytemplate&tmpl=component&id='.(int) $single->id);?>"
		class="modal" rel="{handler: 'iframe', size: {x: 700,y: 500},iframeOptions: {scrolling: 'no'}}"><img src="<?php echo JURI::base().'templates/legalconfirm/images/view_button.png'; ?>" alt="View Template" title="view template" /></a>
		<?php 
				
			}else{?>
			<a href="javascript:void(0)"
	               onclick="showpopup(<?php echo $single->id?>)">
	               <img src="<?php echo JURI::base().'templates/legalconfirm/images/edit_button.png'; ?>" alt="Edit Template" title="edit template"/>
	               </a>
	         <?php }?>
	         </td>
	        
			<td class="marktdedit<?php echo $single->id;?>">
			<!-- Partner list drop down -->
			<select name="partner" id="partnerid<?php echo $single->id;?>" class="partner-name">
			<option value=""><?php echo "Select partner" ?></option>
			<?php foreach($lawfirm_partners as $partner){?>
			<option value="<?php echo $partner->lid;?>" <?php if($assigned_partner == $partner->lid ){echo "selected";}?>><?php echo $partner->name."  (".$partner->email.")" ?></option>
			<?php } ?>
			</select>
			<!-- End of partner list -->
			</td>
			<td class="marktdedit<?php echo $single->id;?>">
			<?php if($single->is_readybyemp == "0" && $is_disapprove == 1)
			{
				?>
				<a style="color:red;" href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmemp&task=lawfirmemp.getpartnerinfo&tmpl=component&id='.(int) $single->id);?>"
		class="modal" rel="{handler: 'iframe', size: {x: 200,y:100}}"><?php echo "Disapproved" ?></a>
				<?php 
			}
			elseif($single->is_readybyemp == "0" && $is_disapprove == 0){
				echo "Not Prepared";
			}
			else{
				echo "Prepared";
			}
			?></td>
		</tr>
	
	<div id="popup<?php echo $single->id; ?>" class="tpopup">
	<div id="light<?php echo $single->id; ?>" class="white_content">
	<div class="register-box" style="min-height: 44px;text-align:center;">
	<a onclick="closepopup(<?php echo $single->id ;?>)" href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmemp&task=lawfirmemp.checkmailtemplatetype&tmpl=component&id='.(int) $single->id);?>"
		class="modal" rel="{handler: 'iframe', size: {x: 700,y:570},iframeOptions: {scrolling: 'no'}}" ><?php echo "Edit Template" ?></a>
		<span class="template-or-pdf">OR</span>
	<a onclick="closepopup(<?php echo $single->id ;?>)" href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmemp&task=lawfirmemp.checkmailtemplatetype&tmpl=component&type=pdf&id='.(int) $single->id);?>"
		class="modal" rel="{handler: 'iframe', size: {x: 700,y:260},iframeOptions: {scrolling: 'no'}}" ><?php echo "Upload Pdf" ?></a>	
	<span class="sbox-btn-close1" style="float:right">
	<a href="javascript:void(0)"
		onclick="closepopup(<?php echo $single->id ;?>)"><img src="<?php echo JURI::base().'templates/legalconfirm/images/closebox.png';?>"></a></span></div>
	</div>
	</div>
	

	<?php
}
?>

</table>

<div class="pagination">
<?php if(count($this->items)>0)
       echo $this->paginations->getListFooter(); else echo JText::_('COM_MYFRIEND_NO_RESULT_FOUND');
       echo JHtml::_('form.token');?>
      <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	  <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
       </div>
</form>
</div>
<?php //echo $this->loadTemplate('notfrequent');?>

<div id="fade"
	class="black_overlay"></div>
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
