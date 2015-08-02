<?php
/**
 * @package			Easy QuickIcons
 * @version			$Id: default.php 88 2012-10-27 13:29:24Z allan $
 *
 * @author			Allan <allan@awynesoft.com>
 * @link			http://www.awynesoft.com
 * @copyright		Copyright (C) 2012 AwyneSoft.com All Rights Reserved
 * @license			GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
$user		= JFactory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_easyquickicons.category');
$saveOrder	= $listOrder == 'ordering';
?>
<form action="<?php echo JRoute::_('index.php?option=com_easyquickicons'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_EASYQUICKICONS_SEARCH_ITEMS'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_easyquickicons'), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', EasyquickiconsHelper::publishedOptions(), 'value', 'text', $this->state->get('filter.state'), true);?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<?php echo JHTML::_( 'grid.sort', 'COM_EASYQUICKICONS_HEAD_ID', 'id', $listDirn, $listOrder); ?>
				</th>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>
				<th>
					<?php echo JHTML::_( 'grid.sort', 'COM_EASYQUICKICONS_HEAD_NAME', 'name', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHTML::_( 'grid.sort', 'COM_EASYQUICKICONS_HEAD_LINK', 'link', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHTML::_( 'grid.sort', 'COM_EASYQUICKICONS_HEAD_PUBLISHED', 'published', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">

					<?php echo JHtml::_('grid.sort',  'COM_EASYQUICKICONS_HEAD_ORDERING', 'ordering', $listDirn, $listOrder); ?>
					<?php if ($canOrder && $saveOrder): ?>
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'easyquickicons.saveorder'); ?>
					<?php endif;?>
				</th>
				<th>
					<?php echo JHTML::_( 'grid.sort', 'COM_EASYQUICKICONS_HEAD_ICON_IMAGE', 'icon', $listDirn, $listOrder);?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JCATEGORY', 'category_title', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHTML::_( 'grid.sort', 'COM_EASYQUICKICONS_HEAD_DESC', 'description', $listDirn, $listOrder);?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<?php $icons = EasyquickiconsHelper::checkIcons();?>
			<?php if(!$icons):?>
			<tr>
				<td colspan="9">
					<?php echo JText::_('COM_EASYQUICKICONS_LIMIT_MSG') . '<a href="http://www.awynesoft.com" target="_blank">www.awynesoft.com</a>';?>
				</td>
			</tr>
			<?php endif;?>
			<tr>
				<td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			$default_link = JURI::base() . 'templates/bluestork/images/header/';
			//echo $default_path;
			foreach($this->items as $i => $item):
			$ordering	= ($listOrder == 'ordering');
			$canEdit	= $user->authorise('core.edit',	'com_easyquickicons');
			$canChange	= $user->authorise('core.edit.state', 'com_easyquickicons');

			//check if custom icon is used
			if($item->custom_icon == 1){

				$chk_img = stripos(strtolower($item->icon_path), 'http');

				if($chk_img === false){ //custom upload image

					$img_link = JURI::root() . trim($item->icon_path);

				} else { // external image link

					$img_link = trim($item->icon_path);

				}

			} else {
				$img_link = $default_link . trim($item->icon);
			}
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<?php echo $item->id; ?>
				</td>
				<td>
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<?php if ($canEdit) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_easyquickicons&task=easyquickicon.edit&id='.$item->id);?>" title="<?php echo $this->escape($item->name); ?>">
							<?php echo $item->name; ?></a>
					<?php else: echo $item->name; ?>
					<?php endif;?>
				</td>
				<td>
					<?php if( $item->name == 'Edit Profile' AND $item->catid== EasyQuickIconsHelper::standardCategoryId()): ?>
						<a href="<?php echo JRoute::_(trim($item->link).'&id='.JFactory::getUser()->id); ?>" target="_blank"><?php echo $item->link . '&id=' .JFactory::getUser()->id; ?></a>
					<?php else: ?>
						<a href="<?php echo JRoute::_(trim($item->link)); ?>" target="_blank"><?php echo $item->link; ?></a>
					<?php endif;?>
				</td>
				<td class="center">
					<?php echo JHtml::_('easyquickicons.published', $item->published, $i); ?>
				</td>
				<td class="order">
					<?php if ($canChange) : ?>
						<?php if ($saveOrder) :?>
							<?php if ($listDirn == 'asc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, true, 'easyquickicons.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'easyquickicons.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php elseif ($listDirn == 'desc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, true, 'easyquickicons.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'easyquickicons.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php endif; ?>
						<?php endif; ?>
						<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
					<?php else : ?>
						<?php echo $item->ordering; ?>
					<?php endif; ?>
				</td>
				<td class="center">
					<img src="<?php echo $img_link;?>" />
				</td>
				<td class="center">
					<?php
					if (empty($item->category_title)) {
						$category = '<div style="float:left">'.JHtml::tooltip(JText::_('COM_EASYQUICKICONS_ICONS_UNCATEGORIZED_DESC'), JText::_('COM_EASYQUICKICONS_ICONS_UNCATEGORIZED')).'</div>';
						$category .= JText::_('COM_EASYQUICKICONS_ICONS_UNCATEGORIZED');

					} else {
						$category = $item->category_title;
					}
					 echo $category;?>
				</td>
				<td>
					<?php echo $item->description;?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<?php echo $this->loadTemplate('copyright');?>