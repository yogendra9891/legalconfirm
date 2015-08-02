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
?>
<div class="register-box auditor-employee-list">
<div style="float: left;">
<h4>Offices List </h4>
</div>
<div class="auditor-admin-offices-add"><a
	href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=auditoradmin');?>">Dashboard</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a
	href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=offices&layout=addoffice'); ?>"><?php echo "Add Office";?></a>
</div>
<table class="alllist">
	<form name="adminForm" method="post" action="index.php?option=com_legalconfirm&view=offices">

	<tr class="formadmin">

		<th class="textbold"><?php echo JHtml::_('grid.sort', JTEXT::_('COM_LEGALCONFIRM_OFFICE'), 'a.office_title', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
		</th>
		<th class="textbold"><?php echo JText::_('COM_LEGALCONFIRM_ADDRESS'); ?></th>
		<th class="textbold"><?php echo JHtml::_('grid.sort', JTEXT::_('COM_LEGALCONFIRM_CITY'), 'a.city', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>
		</th>
		<th class="textbold"><?php echo JHtml::_('grid.sort', JTEXT::_('COM_LEGALCONFIRM_STATE'), 'a.state', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>

		</th>
		<th class="textbold"><?php echo JText::_('COM_LEGALCONFIRM_COUNTRY'); ?></th>
		<th class="textbold"><?php echo JText::_('COM_LEGALCONFIRM_ZIP'); ?></th>
		<th class="textbold">Edit</th>
		<th class="textbold">
<?php echo JHtml::_('grid.sort', JTEXT::_('COM_LEGALCONFIRM_STATUS'), 'a.status', $this->state->get('list.direction'),$this->state->get('list.ordering') ); ?>		
</th>
	</tr>


	<!--
	<tr class="formadmin">
		<th class="textbold"><?php echo JText::_('COM_LEGALCONFIRM_OFFICE'); ?></th>
		<th class="textbold"><?php echo JText::_('COM_LEGALCONFIRM_ADDRESS'); ?></th>
		<th class="textbold"><?php echo JText::_('COM_LEGALCONFIRM_CITY'); ?></th>
		<th class="textbold"><?php echo JText::_('COM_LEGALCONFIRM_STATE'); ?></th>
		<th class="textbold"><?php echo JText::_('COM_LEGALCONFIRM_COUNTRY'); ?></th>
		<th class="textbold"><?php echo JText::_('COM_LEGALCONFIRM_ZIP'); ?></th>
		<th class="textbold">Edit</td>
		
	</tr>
	--><?php
	foreach($this->items as $office){
		?>
	<tr>
		<td><?php echo $office->office_title; ?></td>
		<td><?php echo $office->address; ?></td>
		<td><?php echo $office->city; ?></td>
		<td><?php echo $office->state; ?></td>
		<td><?php echo $office->country; ?></td>
		<td><?php echo $office->zip; ?></td>
		<td><a
			href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=offices&layout=edit&task=edit&id=' . (int)$office->id); ?>"><img src="<?php echo JURI::base().'templates/legalconfirm/images/edit_button.png';?>" alt="edit office"></a></td>
	    <td><?php if($office->status == "0"){echo "<span ><a style='color:red;' href='".JRoute::_('index.php?option=com_legalconfirm&task=offices.unBlockOffice&id='. (int)$office->id)."'>Activate</a></span>";}else{ ?>
	    <img src="<?php echo JURI::base().'templates/legalconfirm/images/tick.png'; ?>" alt="activated" /><?php } ?>
	    
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
      <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	  <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
       </div>
</form>
</div>
