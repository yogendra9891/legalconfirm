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
JHTML::_('behavior.modal'); 
$search = $this->state->get('filter.search');
?>
<div class="auditor-wrapper">

 <div class="auditor-clients"><div class="newlyadded-link">
 <a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clients&layout=add&tmpl=component');?>" class="modal" rel="{handler: 'iframe', size: {x: 700, y: 550}}"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_ADD_CLIENTS');?></a>
  </div>
  <div class="auditor-clients-header"><span><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_LIST');?></span></div>
  <div class="client-searcharea">
  <form action="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=auditors');?>" name="adminForm" id="adminForm" method="post">
  <!--
  		<div class="filter-select fltrt">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_SEARCHBY'); ?></label>
			<select name="filter_client" class="inputbox" onchange="this.form.submit()">
				<?php //echo JHtml::_('select.options',  EventHelper::getCountryOptions(), 'value', 'text', $this->state->get('filter.country'), true);?>
			</select>
		</div>
  -->
  		<div class="filter-search-fltlft">
  		    <label class="filter-search-lbl" for="filter_search"><?php echo 'Search By'; ?></label>
  		    <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_SEARCHBY_CLIENT_NAME'); ?></label>
			<input type="text" name="filter_search" id="filter_search" size="14" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_LEGALCONFIRM_SEARCH_IN_CLIENT'); ?>" />
			<button type="submit"  class="button"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			
		</div>
	<input type="hidden" name="">
	<?php echo JHtml::_('form.token'); ?>
	</form>
  </div>
     <?php if(!empty($search)): if(count($this->items)):?>
     <span><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_SEARCHED_RESULT'); ?></span>
  <div class="searched-clients" id="searched-clients">
  
  <?php if(count($this->items)): foreach($this->items as $items):?>
  <div class="companyname">
  <a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&id='.$items->clientid);?>"><?php echo $items->company; ?></a>
  </div>
  <?php endforeach; else:?>
  <div class="noresult-matched">
  <?php echo JText::_('COM_LEGALCONFIRM_CLIENT_NO_MATCHED');?>
  </div>
  <?php endif;?>
  </div><?php else: ?><span style="color: red; "><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_NO_MATCHED');?></span> <?php  endif; endif;?>

  <div class="recently-view-client">
  <label class=""><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_RECENTLY_VIEW'); ?></label>
  <?php $recentlyviewed = $this->rececentViewedClient();
  foreach($recentlyviewed as $recently):
  ?>
  	<br/><a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&id='.$recently->id);?>"><?php echo $recently->company; ?></a>
  <?php endforeach;?>	
  </div>

  <div class="view-client-list">
  <label class=""><a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clients&layout=clientlist&tmpl=component');?>" class="modal" rel="{handler: 'iframe', size: {x: 700, y: 480}}"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_VIEW_LIST'); ?></a></label>
  </div>
  
 </div>
<!--
 <div class="auditor-statuses">
   <div class="auditor-status-header"><span><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_STATUSES');?></span></div>
 </div>

 <div class="auditor-reports">
    <div class="auditor-reports-header"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_REPORTS');?></div>
 </div>
 -->
 <div class="auditor-wrapper-divider"></div>
<!--<div class="auditor-quicklinks">
     <div class="auditor-quicklinks-header"><span><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_QUICKLINKS');?></span></div>
     <ul class="auditor-quicklinks-links">
      <li><a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clients&layout=add&tmpl=component');?>" class="modal" rel="{handler: 'iframe', size: {x: 700}}"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_ADD_CLIENTS');?></a></li>
      
      <li><a href="#"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_ADD_CLIENTS_ACCOUNTS');?></a></li>
      <li><a href="#"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_ADD_CLIENTS_REQUEST_AUTHORIZATION_CODE');?></a></li>
     </ul>
 
 </div>

 --></div>
 <script type="text/javascript">
var jq = jQuery.noConflict(); 
jq(document).ready(function(){  
jq('#searched-clients').slimscroll({
	  height: '90px'
	});
});
</script>
