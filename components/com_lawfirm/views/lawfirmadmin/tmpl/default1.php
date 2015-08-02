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
<?php 
//echo "<pre>";
//print_r($this->items);
//die;
$returnURL = base64_encode(JURI::root() . "\n");
?>
<div class="register-box">
<div style="float:left;"><h4>Employee List</h4></div>
<div style="float:right">
<a href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=userprofile');?>">Profile</a>&nbsp;&nbsp;|&nbsp;
<a href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=offices');?>">Offices</a>
</div>
<table class="alllist">
<tr class="formadmin">
<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EMPLOYEE_NAME');?></th>
<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EMPLOYEE_EMAIL');?></th>
<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EMPLOYEE_TITLE');?></th>
<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EMPLOYEE_TYPE');?></th>
<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EMPLOYEE_OFFICE');?></th>
<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EMPLOYEE_STATUS');?></th>
<th class="textbold"><?php echo JTEXT::_('COM_LAWFIRM_EMPLOYEE_CHANGE_STATUS');?></th>
</tr>
<?php 
foreach($this->items as $single){
	?>
	<tr>
	<td><?php echo $single->name;?></td>
	<td><?php echo $single->email;?></td>
	<td><?php echo $single->emp_title;?></td>
	<td><?php echo $single->gid;?></td>
	<td><?php echo $single->office_title;?></td>
	<td><?php 
	if($single->block==0){
	echo "Active";	
	}
    elseif($single->block==1 && $single->activation==""){
	echo "Block";	
	}
    else{
	echo "Pending";	
	}
	
	?></td>
	<td><a href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirmadmin&task=lawfirmadmin.changeStatus&id=' . (int)$single->id); ?>"><?php echo "Click Here";?></a></td>
	</tr>
	<?php
}
?>
</tr>
</table>
<div class="pagination">
<p class="counter"><?php echo $this->pagination->getPagesCounter(); ?></p>
<?php echo $this->pagination->getPagesLinks(); ?></div>
</div>