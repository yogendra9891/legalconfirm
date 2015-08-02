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
?>
<div class="lawfirmoffices">
  <div class="lawfirmoffices-header">
 <span> <?php echo JText::sprintf('COM_LEGALCONFIRM_LAWFIRM_LIST_OFFICES',  $this->client->company);?></span>
  </div>
  
 <div class="lawfirmofficelocation">
 <fieldset>
  <legend> <?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_OFFICES_LOCATIONS');?> </legend>
 
  <table class="adminlist">
  <tr>
<!--  			    <th width="5%">-->
<!--				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->offices); ?>);" />-->
<!--				</th>	-->
				
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
  <?php  $k = 0;
			  for ($i=0, $n=count( $this->offices ); $i < $n; $i++)
   			  {
			  $row =& $this->offices[$i];
	    	//  $checked = JHTML::_('grid.id', $i, $row->id );
		?>
		
	<tr class="row<?php echo $i % 2; ?>" id="classsid"><!--
		<td>
			<?php echo $checked;?>
		</td>
		--><td>
			<?php echo $row->office_title; ?>
		</td>

		<td>
			<?php echo $row->address; ?>
		</td>

		<td>
			<?php echo $row->city; ?>
		</td>

		<td>
			<?php echo $row->state; ?>
		</td>
			
		<td>
			<?php echo $row->country; ?>
		</td>

		<td>
			<?php echo $row->zip; ?>
		</td>
		
	</tr>
	<?php
 		$k = 1 - $k; 
		} ?> 
  
  </table>
  <div class="submit">
  <a href="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=lawfirms&tmpl=component&id='.$this->client->clientid);?>"><?php echo JText::_('COM_LEGALCONFIRM_LAWFIRM_BACK');?></a>
  </div> 
  <input type="hidden" name="id" value="<?php echo $this->client->clientid; ?>" >
   </div>
  </fieldset>
</div>
