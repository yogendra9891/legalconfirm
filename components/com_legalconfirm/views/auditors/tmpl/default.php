<?php
/**
 * @version     1.0.0
 * @package     com_legalconfirm
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Abhishek Gupta <abhishek.gupta@daffodilsw.com> - http://
 */
// no direct access
defined('_JEXEC') or die('Restricted Access'); 
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.keepalive');
require_once JPATH_COMPONENT.'/helpers/legalconfirm.php';
// load tooltip behavior
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$user		 = JFactory::getUser();
$userId		 = $user->get('id');

$listOrder   = $this->state->get('list.ordering');
$listDirn    = $this->state->get('list.direction');
?>

<div class="auditor-clientlist-add-wrapper">
<span class="clientlist-name"><?php echo JText::_('Client List');?></span>
<form action="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=auditors'); ?>" name="adminForm" id="adminForm" method="post">
	<fieldset id="filter-bar"><!--
		<div class="filter-select fltrt">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JFILTER_COMPANY_LABEL'); ?></label>
			<select name="filter_company" class="inputbox" onchange="this.form.submit()">
				<?php echo JHtml::_('select.options',  LegalconfirmHelper::getCompanyOptions(), 'value', 'text', $this->state->get('filter.company'), true);?>
			</select>
		</div>
		--><div class="filter-search fltlft"><div class="auditor-search-dashboard"><span class="search-client-label"><?php echo JText::_('Search'); ?></span>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_LEGALCONFIRM_SEARCH_IN_COMPANY_TITLE'); ?>" />
		    <span class="submit"><input type="submit" value="<?php //echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" ></span></div>
		</div>

	</fieldset>
	
	<div class="clr"> </div>
	<div class="audito-dashboard">
	<div class="auditor-operation-dashboard">
	<table class="adminlist">
		<thead><?php echo $this->loadTemplate('head');?></thead>
		<tfoot class="clients-list-footer"><?php echo $this->loadTemplate('foot');?></tfoot>
		<tbody><?php echo $this->loadTemplate('body');?></tbody>
	</table>
</div></div>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<div class="recently-viewed" id="recently-viewed">
<div class="recently-viewed-title"><span><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_RECENTLY_VIEW');?></span></div>
<table class="adminlist">
<tbody>		
  <?php $recentlyviewed = $this->rececentViewedClient(); ?>
  <tr id="adminformclass" class="recently-viewed-title-heading">  
  <td>
  <?php echo JText::_('COM_LEGALCONFIRM_CLIENT_COMPANY');?>
  </td>
  <td>
  <?php echo JText::_('COM_LEGALCONFIRM_CLIENT_ENGANEMENTNO');?>
  </td>
  <td>
  <?php echo JText::_('COM_LEGALCONFIRM_CLIENT_SIGNERE_TITLE');?>
  </td>
    <td>
  <?php echo JText::_('COM_LEGALCONFIRM_CLIENT_SIGNERE_PREVIEW_DATE');?>
  </td>
  </tr>
  <?php
  foreach($recentlyviewed as $recently):
   $link = JRoute::_( 'index.php?option=com_legalconfirm&view=clientprofile&id='. (int)$recently->clientid );
  ?>
  	<tr id="adminformclass" >
  	<!--<td class="blank-auditor-space" width="10%"></td> -->
		<td>
			<a href="<?php echo $link; ?>"><?php echo $recently->company; ?></a>
		</td>
			
		<td>
			<?php echo $recently->engagementno; ?>
		</td>

		<td class="lead-auditor-name">
			<?php echo $recently->fname. ' '. $recently->lname; ?>
		</td>

		<td>
			<?php echo date("M/d/Y H:i:s", strtotime($recently->previewdate)); ?>
		</td>
  	</tr>
  <?php endforeach;?>	
  </div>
</tbody>
</table>
</div>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#recent-review').change(function(){
		   if(this.checked){
			   jQuery('#recently-viewed').slideDown(400);
		   }else{
			   jQuery('#recently-viewed').slideUp(200);
		   }

		});
});
</script>
