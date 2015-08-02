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
	var tt = '#removelawfirm_'+lawfirmidt;
	var url = 'index.php?option=com_legalconfirm&format=raw&task=clientprofile.removelawfirm';
    jQuery.ajax({
        type: 'POST',
        url: url,
        data: 'lawfirmid=' + lawfirmidt, 
        datatype:'json',
        success: function(res) { 
			if(res) jQuery(tt).css('display','none');
           },
 	      error:function(){
 	          alert("Lawfirm remove failure, please try again..");
 	      }             
          });
 });
});
</script>
<div class="addmore-wrraper"> 
<?php echo JText::_('COM_LEGALCONFIRM_ADDED_LAWFIRMS');?>
<div class="submit1">
<a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientlawfirmadd&tmpl=component&id='.$this->client->clientid);?>" class="modal" rel="{handler: 'iframe', size: {x: 700}}"">
	<?php echo JText::_('COM_LEGALCONFIRM_ADD_MORE_LAWFIRM');?>
</a>
</div>
<div class="add-more-Lawfirm" id="add-more-Lawfirm">

<table>
<tr class="rownew">
<th><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_LAWFIRM_NAME');?></th>
<th><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_LAWFIRM_EMAIL');?></th>
<th></th>
</tr>
<?php foreach ($this->requestdata as $newlawfirm): 
	$lawfirmdataarry = $this->findlawfirmdetail($newlawfirm['lawfirm']);
	$checked = JHTML::_('grid.id', $i, $lawfirmdataarry->lawfirm);
	?>
	<tr class="rownew" id="removelawfirm_<?php echo $lawfirmdataarry->lawfirmid;?>">

		<td>
			<?php
			 if(strlen($lawfirmdataarry->accounting_firm) > 10)
			 echo substr($lawfirmdataarry->accounting_firm, 0,10).'..'; 
			 else
			 echo $lawfirmdataarry->accounting_firm;
			 ?>
		</td>
			
		<td>
			<?php 
			if(strlen($lawfirmdataarry->email) > 20)
			echo substr($lawfirmdataarry->email, 0,20).'..'; 
			else
			echo $lawfirmdataarry->email; 
			?>
		</td>
		
		<td>
			<a href="#" class="lawfirmremove" name ="<?php  echo $lawfirmdataarry->lawfirmid; ?>" id="lawfirm_<?php echo $lawfirmdataarry->lawfirmid;?>"><?php echo JText::_('COM_LEGALCONFIRM_CLIENT_LAWFIRM_REMOVE');?></a>
		</td>
	</tr>
	<?php endforeach;?>
</table>
</div>
</div>
<script type="text/javascript">
//var jq = jQuery.noConflict(); 
jQuery(document).ready(function(){  
jQuery('#add-more-Lawfirm').slimscroll({
	  height: '100px'
	});
});
</script>
