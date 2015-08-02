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
$app = JFactory::getApplication(); 
$this->requestdata = $app->getUserState('com_legalconfirm.selectedlawfirmoffices.data'); //echo "<pre>"; print_r($this->requestdata); exit;
$this->requestclientid = $app->getUserState('com_legalconfirm.clientprofile.clientid');
?>
<script>
//var jq = jQuery.noConflict();
jQuery(document).ready(function(){
 jQuery('.lawfirmremove').click(function(){
	var t =jQuery(this).attr('id');
	var lawfirmidt =jQuery(this).attr('name');
	jQuery('#lawfirm_removedid').val(lawfirmidt);
	jQuery('#lawfirm_remove_form').submit();
//	var url = 'index.php?option=com_legalconfirm&format=raw&task=clientprofile.removelawfirm';
//    jQuery.ajax({
//        type: 'POST',
//        url: url,
//        data: 'lawfirmid=' + lawfirmidt, 
//        datatype:'json',
//        success: function(res) { 
//			if(res) jQuery(tt).css('display','none');
//           },
// 	      error:function(){
// 	          alert("Lawfirm remove failure, please try again..");
// 	      }             
//          });
 });
});
</script>
<div class="addmore-wrraper"> 
<div class="tobeselectedlawfirms-rightside" id="add-more-Lawfirm">
<form action="<?php echo JRoute::_('index.php?option=com_legalconfirm&task=clientprofile.removelawfirm&id='.$this->client->clientid);?>" method="post" id="lawfirm_remove_form">
<table>
<tr class="selected-lawfirms-name">
<th width="58%"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_LAWFIRM_NAME');?></th>
<th width="30%"><?php //echo JText::_('COM_LEGALCONFIRM_CLIENT_LAWFIRM_EMAIL');?></th>
<th width="12%"></th>
</tr>
</table>
<div id="nowselectedscrollbar1">
<table><tbody>
<?php foreach ($this->requestdata as $newlawfirm): 
	$lawfirmdataarry = $this->findlawfirmdetail($newlawfirm['lawfirm']);
	$checked = JHTML::_('grid.id', $i, $lawfirmdataarry->lawfirm);
	?>
	<tr class="rownew" id="removelawfirm_<?php echo $lawfirmdataarry->lawfirmid;?>">

		<td width="60%">
			<?php
			 if(strlen($lawfirmdataarry->accounting_firm) > 20)
			 echo substr($lawfirmdataarry->accounting_firm, 0,20).'..'; 
			 else
			 echo $lawfirmdataarry->accounting_firm;
			 ?>
		</td>
			
		<td width="30%">
			<?php 
			//if(strlen($lawfirmdataarry->email) > 20)
			//echo substr($lawfirmdataarry->email, 0,20).'..'; 
			//else
			//echo $lawfirmdataarry->email; 
			?>
		</td>
		
		<td width="10%">
			<a href="#" class="lawfirmremove" name ="<?php  echo $lawfirmdataarry->lawfirmid; ?>" id="lawfirm_<?php echo $lawfirmdataarry->lawfirmid;?>"><img src="<?php echo JURI::base().'templates/legalconfirm/images/remove_button.png'; ?>" alt="remove"></a>
		</td>
	</tr>
	<?php endforeach;?>
	</tbody>
</table>
</div>
<input type="hidden" name="lawfirm_removed" value="" id="lawfirm_removedid">
<?php echo JHtml::_('form.token');?>
</form>
</div>
</div>
