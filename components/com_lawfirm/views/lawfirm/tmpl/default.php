<?php
/**
 * @version     1.0.0
 * @package     com_lawfirm
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Abhishek Gupta <abhishek.gupta@daffodilsw.com> - http://
 */
// no direct access
defined('_JEXEC') or die;

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_lawfirm', JPATH_ADMINISTRATOR);
$canEdit = JFactory::getUser()->authorise('core.edit', 'com_lawfirm.' . $this->item->id);
if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_lawfirm' . $this->item->id)) {
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>
<?php if ($this->item) : ?>

    <div class="item_fields">

        <ul class="fields_list">

            			<li><?php echo JText::_('COM_LAWFIRM_FORM_LBL_LAWFIRM_ID'); ?>:
			<?php echo $this->item->id; ?></li>
			<li><?php echo JText::_('COM_LAWFIRM_FORM_LBL_LAWFIRM_ORDERING'); ?>:
			<?php echo $this->item->ordering; ?></li>
			<li><?php echo JText::_('COM_LAWFIRM_FORM_LBL_LAWFIRM_STATE'); ?>:
			<?php echo $this->item->state; ?></li>
			<li><?php echo JText::_('COM_LAWFIRM_FORM_LBL_LAWFIRM_CHECKED_OUT'); ?>:
			<?php echo $this->item->checked_out; ?></li>
			<li><?php echo JText::_('COM_LAWFIRM_FORM_LBL_LAWFIRM_CHECKED_OUT_TIME'); ?>:
			<?php echo $this->item->checked_out_time; ?></li>
			<li><?php echo JText::_('COM_LAWFIRM_FORM_LBL_LAWFIRM_CREATED_BY'); ?>:
			<?php echo $this->item->created_by; ?></li>


        </ul>

    </div>
    <?php if($canEdit): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_lawfirm&task=lawfirm.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_LAWFIRM_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_lawfirm.lawfirm.'.$this->item->id)):
								?>
									<a href="javascript:document.getElementById('form-lawfirm-delete-<?php echo $this->item->id ?>').submit()"><?php echo JText::_("COM_LAWFIRM_DELETE_ITEM"); ?></a>
									<form id="form-lawfirm-delete-<?php echo $this->item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_lawfirm&task=lawfirm.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
										<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
										<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
										<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
										<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
										<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
										<input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />
										<input type="hidden" name="option" value="com_lawfirm" />
										<input type="hidden" name="task" value="lawfirm.remove" />
										<?php echo JHtml::_('form.token'); ?>
									</form>
								<?php
								endif;
							?>
<?php
else:
    echo JText::_('COM_LAWFIRM_ITEM_NOT_LOADED');
endif;
?>
