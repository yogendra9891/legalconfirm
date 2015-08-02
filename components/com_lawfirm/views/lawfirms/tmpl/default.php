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
?>

<div class="items">
    <ul class="items_list">
<?php $show = false; ?>
        <?php foreach ($this->items as $item) : ?>

            
				<?php
					if($item->state == 1 || ($item->state == 0 && JFactory::getUser()->authorise('core.edit.own',' com_lawfirm.lawfirm.'.$item->id))):
						$show = true;
						?>
							<li>
								<a href="<?php echo JRoute::_('index.php?option=com_lawfirm&view=lawfirm&id=' . (int)$item->id); ?>"><?php echo $item->id; ?></a>
								<?php
									if(JFactory::getUser()->authorise('core.edit.state','com_lawfirm.lawfirm.'.$item->id)):
									?>
										<a href="javascript:document.getElementById('form-lawfirm-state-<?php echo $item->id; ?>').submit()"><?php if($item->state == 1): echo JText::_("COM_LAWFIRM_UNPUBLISH_ITEM"); else: echo JText::_("COM_LAWFIRM_PUBLISH_ITEM"); endif; ?></a>
										<form id="form-lawfirm-state-<?php echo $item->id ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_lawfirm&task=lawfirm.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
											<input type="hidden" name="jform[id]" value="<?php echo $item->id; ?>" />
											<input type="hidden" name="jform[ordering]" value="<?php echo $item->ordering; ?>" />
											<input type="hidden" name="jform[state]" value="<?php echo (int)!((int)$item->state); ?>" />
											<input type="hidden" name="jform[checked_out]" value="<?php echo $item->checked_out; ?>" />
											<input type="hidden" name="jform[checked_out_time]" value="<?php echo $item->checked_out_time; ?>" />
											<input type="hidden" name="option" value="com_lawfirm" />
											<input type="hidden" name="task" value="lawfirm.save" />
											<?php echo JHtml::_('form.token'); ?>
										</form>
									<?php
									endif;
									if(JFactory::getUser()->authorise('core.delete','com_lawfirm.lawfirm.'.$item->id)):
									?>
										<a href="javascript:document.getElementById('form-lawfirm-delete-<?php echo $item->id; ?>').submit()"><?php echo JText::_("COM_LAWFIRM_DELETE_ITEM"); ?></a>
										<form id="form-lawfirm-delete-<?php echo $item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_lawfirm&task=lawfirm.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
											<input type="hidden" name="jform[id]" value="<?php echo $item->id; ?>" />
											<input type="hidden" name="jform[ordering]" value="<?php echo $item->ordering; ?>" />
											<input type="hidden" name="jform[state]" value="<?php echo $item->state; ?>" />
											<input type="hidden" name="jform[checked_out]" value="<?php echo $item->checked_out; ?>" />
											<input type="hidden" name="jform[checked_out_time]" value="<?php echo $item->checked_out_time; ?>" />
											<input type="hidden" name="jform[created_by]" value="<?php echo $item->created_by; ?>" />
											<input type="hidden" name="option" value="com_lawfirm" />
											<input type="hidden" name="task" value="lawfirm.remove" />
											<?php echo JHtml::_('form.token'); ?>
										</form>
									<?php
									endif;
								?>
							</li>
						<?php endif; ?>

<?php endforeach; ?>
        <?php
        if (!$show):
            echo JText::_('COM_LAWFIRM_NO_ITEMS');
        endif;
        ?>
    </ul>
</div>
<?php if ($show): ?>
    <div class="pagination">
        <p class="counter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
<?php endif; ?>


									<?php if(JFactory::getUser()->authorise('core.create','com_lawfirm')): ?><a href="<?php echo JRoute::_('index.php?option=com_lawfirm&task=lawfirm.edit&id=0'); ?>"><?php echo JText::_("COM_LAWFIRM_ADD_ITEM"); ?></a>
	<?php endif; ?>