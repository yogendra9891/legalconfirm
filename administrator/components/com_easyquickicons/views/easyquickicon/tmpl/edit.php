<?php
/**
 * @package			Easy QuickIcons
 * @version			$Id: edit.php 91 2012-10-27 13:32:14Z allan $
 * @author			Allan <allan@awynesoft.com>
 * @link			http://www.awynesoft.com
 * @copyright		Copyright (C) 2012 AwyneSoft.com All Rights Reserved
 * @license			GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

if(!EasyquickiconsHelper::checkIcons() && JRequest::getInt('id') == null){
	$app = JFactory::getApplication();
	
	$app->redirect(JRoute::_('index.php?option=com_easyquickicons'),JText::_('COM_EASYQUICKICONS_DIRECT_ACCESS_ERROR'), 'error');
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_easyquickicons&layout=edit&id='.(int) $this->item->id); ?>"
      method="post" name="adminForm" id="easyquickicon-form" class="form-validate">
	<div class="width-60 fltlft">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_EASYQUICKICONS_EASYQUICKICON_DETAILS' ); ?></legend>
		<ul class="adminformlist">
			<li><?php echo $this->form->getLabel('name'); ?>
			<?php echo $this->form->getInput('name'); ?></li>
			<li><?php echo $this->form->getLabel('componentlink'); ?>
			<?php echo $this->form->getInput('componentlink'); ?>
			</li>
			<li><?php echo $this->form->getLabel('link'); ?>
			<?php echo $this->form->getInput('link'); ?></li>
			<li><?php echo $this->form->getLabel('target'); ?>
			<?php echo $this->form->getInput('target'); ?></li>
			<li><?php echo $this->form->getLabel('published'); ?>
			<?php echo $this->form->getInput('published'); ?></li>
			<li><?php echo $this->form->getLabel('catid'); ?>
				<?php echo $this->form->getInput('catid'); ?>
			</li>
			<li><?php echo $this->form->getLabel('ordering'); ?>
			<?php echo $this->form->getInput('ordering'); ?></li>
			<li><?php echo $this->form->getLabel('icon'); ?>
			<?php echo $this->form->getInput('icon'); ?></li>
			<li><?php echo $this->form->getLabel('usecustomicon'); ?> 
			<?php echo $this->form->getInput('usecustomicon'); ?>
			</li>
			<li><?php echo $this->form->getLabel('custompath'); ?> 
			<?php echo $this->form->getInput('custompath'); ?>
			</li>
			<li><?php echo $this->form->getLabel('description'); ?>
			<?php echo $this->form->getInput('description'); ?>
			</li>
		</ul>
	</fieldset>
	</div>
	<div class="width-40 fltrt">
		<fieldset class="adminform">
			<legend>
			<?php echo JText::_('COM_EASYQUICKICONS_ADVANCE_DETAILS'); ?>
			</legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('iconpreview'); ?> <?php echo $this->form->getInput('iconpreview'); ?>
				</li>
				<li><?php echo $this->form->getLabel('created_date'); ?> <?php echo $this->form->getInput('created_date'); ?>
				</li>

				<li><?php echo $this->form->getLabel('modified_date'); ?> <?php echo $this->form->getInput('modified_date'); ?>
				</li>
			</ul>
		</fieldset>
	</div>
	<div>
		<input type="hidden" name="task" value="easyquickicon.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>