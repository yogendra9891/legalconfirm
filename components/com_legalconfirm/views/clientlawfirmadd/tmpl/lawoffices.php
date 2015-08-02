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
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
?>

<div class="lawfirmoffices">
  <div class="lawfirmoffices-header">
  <?php echo JText::sprintf('COM_LEGALCONFIRM_ADD_NEW_ACCOUNT_FOR',  $this->client->company);?>
  </div>
  <fieldset>
  <legend><?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_OFFICES_LOCATION');?> </legend>
  <form action="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&id='.$this->client->clientid);?>" name="adminForm" id="adminForm" method="post">
  <table class="adminlist">
  <?php $j = 0; foreach ($this->lawfirms as $lawfirms):?>
  <tr>          <?php $lawfirmdata = LegalconfirmHelper::getLawFirmDetail($lawfirms); ?>
  				<td><?php echo $lawfirmdata->accounting_firm;?></td>
  				<td><?php echo $lawfirmdata->email;?></td>
  				<?php $lawofficesdata = LegalconfirmHelper::getLawFirmOffices($lawfirmdata->lid); ?>
  				<tr>
				    <th>
					
					</th>	

					<th>
					<?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_OFFICE_LOCATION_TITLE');?>
					</th>
					
					<th>
					<?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_OFFICE_LOCATION_ADDRESS');?>
					</th>
					
					<th>
					<?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_OFFICE_LOCATION_CITY');?>
					</th>
	
					<th>
					<?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_OFFICE_LOCATION_STATE');?>
					</th>
					
					<th>
					<?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_OFFICE_LOCATION_COUNTRY');?>
					</th>
					
					<th>
					<?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_OFFICE_LOCATION_ZIP');?>
					</th>
				</tr>
  				<?php 
				 $k = 0;
				  for ($i=0, $n=count( $lawofficesdata ); $i < $n; $i++)
	   				 {
				  $row =& $lawofficesdata[$i];
		    	  $checked = JHTML::_('grid.id', $i, $row->id ); ?>
		    	 
  				  <tr class="rowclosed">
					<td>
						<?php //echo $checked;?>
						<input type="checkbox" class="my_checkbox_group<?php echo $j; ?>"  value="<?php echo $row->id;?>" name="cid[<?php echo $j; ?>][]" id="cb<?php echo $j; ?>">
					</td>

					<td>
					<?php echo $row->office_title;?>
					</td>
					
					<td>
					<?php echo $row->address;?>
					</td>
					
					<td>
					<?php echo $row->city;?>
					</td>
	
					<td>
					<?php echo $row->state;?>
					</td>
					
					<td>
					<?php echo $row->country;?>
					</td>
					
					<td>
					<?php echo $row->zip;?>
					</td>
  				 </tr> 
  				 <?php 
  				 $k = 1 - $k;
	   				 } 
  				 ?>
  </tr>
<?php $j++; endforeach;?>
  
  </table>
  <div class="submit">
   <!--   <input type="submit" name="next" value="<?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_NEXT');?>" >-->
  <a href="javascript::void();" onclick="if (checkcheckbox()){Joomla.submitbutton('clientprofile.requestProposals');}else{}"><?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_SUBMIT');?></a>
  </div> 
  <input type="hidden" name="id" value="<?php echo $this->client->clientid; ?>" >
  <input type="hidden" name="task" value="">
  <?php echo JHtml::_('form.token');?>
  </form></fieldset>
</div>
<script>
//var jq = jQuery.noConflict();
var t = <?php echo count($this->lawfirms); ?>;
function checkcheckbox()
{   
	var isChecked = false;
	for(var i = 0; i < t; i++){
		if(jQuery(".my_checkbox_group"+i+":checkbox:checked").length > 0)
		{
			isChecked = true;
		}else{
			isChecked = false;
		}
		if(isChecked==false)break; 
	} 
	if(isChecked==false)
	{
		$.msgBox({
                title:"Alert",
                content:"Please select atleast one office from each lawfirms."
                });
	}

  return (isChecked);
}
</script>
