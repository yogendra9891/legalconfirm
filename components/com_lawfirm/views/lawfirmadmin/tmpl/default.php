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
<?php 
//echo "<pre>";
//print_r($this->items);
//die;
$returnURL = base64_encode(JURI::root() . "\n");
//get group id
$config = JFactory::getConfig();
$lawfirm_emp = $config->getValue('config.lawfirm_emp');
$lawfirm_partner = $config->getValue('config.lawfirm_partner');
?>
<div class="register-box auditor-employee-list">
<div style="float:left;"><h4>Employee List</h4></div>
<div class="attorney-list-tasks">
<a href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=offices');?>">Offices</a>
</div>
<table class="alllist">
<form name="adminForm" method="post" action = "index.php?option=com_lawfirm&view=lawfirmadmin">

<tr class="formadmin">
<th class="textbold">
<?php echo JHtml::_('grid.sort', JTEXT::_('COM_LAWFIRM_EMPLOYEE_NAME'), 'a.name', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?></th>
<th class="textbold">
<?php echo JHtml::_('grid.sort', JTEXT::_('COM_LAWFIRM_EMPLOYEE_EMAIL'), 'a.email', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?></th>
<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EMPLOYEE_TITLE');?></th>
<th class="textbold">
<?php echo JHtml::_('grid.sort', JTEXT::_('COM_LAWFIRM_EMPLOYEE_TYPE'), 'e.group_id', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?></th>
<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EMPLOYEE_OFFICE');?></th>
<th class="textbold">
<?php echo JHtml::_('grid.sort', JTEXT::_('COM_LAWFIRM_EMPLOYEE_STATUS'), 'a.block', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
<?php //echo JTEXT::_('COM_LAWFIRM_EMPLOYEE_STATUS');?></th>
<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EMPLOYEE_CHANGE_STATUS');?></th>
</tr>
<?php 
foreach($this->items as $single){
	?>
	<tr>
	<td><?php echo $single->name;?></td>
	<td><?php echo $single->email;?></td>
	<td><?php echo $single->emp_title;?></td>
	<td><?php if($single->gid==$lawfirm_emp){echo "Employee";}elseif($single->gid==$lawfirm_partner){echo "Partner";}?></td>
	<td><?php echo $single->office_title;?></td>
	<td><?php 
	if($single->block==0){ ?>
	<img src="<?php echo JURI::base().'templates/legalconfirm/images/tick.png'?>" alt="active">
<?php	}
    elseif($single->block==1 && $single->activation==""){ ?>
	<img src="<?php echo JURI::base().'templates/legalconfirm/images/publish_x.png'?>" alt="blocked">	
<?php	}
    else{
	echo "Pending";	
	}
	
	?></td>
	<td><a href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmadmin&task=lawfirmadmin.changeStatus&id=' . (int)$single->id); ?>"><img src="<?php echo JURI::base().'templates/legalconfirm/images/edit_status.png'?>" alt="edit"></a></td>
	</tr>
	<?php
}
?>
</tr>
</table>

<div class="pagination">
<?php if(count($this->items)>0)
       echo $this->pagination->getListFooter(); else echo JText::_('COM_MYFRIEND_NO_RESULT_FOUND');
       echo JHtml::_('form.token');?>
      <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	  <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
       </div>
</form>
</div>


