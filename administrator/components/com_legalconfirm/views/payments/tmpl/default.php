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

JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_legalconfirm/assets/css/legalconfirm.css');

$user	= JFactory::getUser();
$userId	= $user->get('id');

?>
<style>
.payments-detail .adminlist tr{}
.payments-detail .adminlist td{float: left; width: 172px !important;}
.payments-detail .adminlist td input{width: 264px !important;}
</style>
<form action="<?php echo JRoute::_('index.php?option=com_legalconfirm&view=payments'); ?>" method="post" name="adminForm" id="adminForm"><div class="clr"> </div>
<div class="payments-detail">
	<table class="adminlist">
		<tr style="border: 1px solid #000; width: 100px;"><td><?php echo JText::_('COM_LEGALCONFIRM_PAYMENTS_ENVIORNMENT');?></td>
		<?php $selected = ''; $selected1 = '';
		      if($this->item->payment_type == 'sandbox')
		      $selected = "selected";
		      else $selected1 = "selected";
		?>
		<td><select name="payment_type" for="amount">
		         <option value="live" <?php echo $selected1;?>><?php echo JText::_('COM_LEGALCONFIRM_PAYMENTS_TYPE_LIVE');?></option>
                 <option value="sandbox" <?php echo $selected;?>><?php echo JText::_('COM_LEGALCONFIRM_PAYMENTS_TYPE_SANDBOX');?></option>
            </select>
        </td>
	</tr>
	<tr>
	  <td><?php echo JText::_('COM_LEGALCONFIRM_PAYMENTS_AMOUNT');?></td>
	  <td><input type="text" name="amount" value="<?php echo $this->item->amount;?>"></td>
	</tr>
	<tr>
	  <td><?php echo JText::_('COM_LEGALCONFIRM_PAYMENTS_CURRENCY');?></td>
	  <td><input type="text" name="currency" value="<?php echo $this->item->currency;?>"></td>
	</tr>
	<tr>
	  <td><?php echo JText::_('COM_LEGALCONFIRM_PAYMENTS_API_USERNAME');?></td>
	  <td><input type="text" name="api_username" value="<?php echo $this->item->api_username;?>"></td>
	</tr>
	<tr>
	  <td><?php echo JText::_('COM_LEGALCONFIRM_PAYMENTS_API_PASSWORD');?></td>
	  <td><input type="text" name="api_password" value="<?php echo $this->item->api_password;?>"></td>
	</tr>
	<tr>
	  <td><?php echo JText::_('COM_LEGALCONFIRM_PAYMENTS_API_SIGNATURE');?></td>
	  <td><input type="text" name="api_signature" value="<?php echo $this->item->api_signature;?>"></td>
	</tr>

	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $this->item->id;?>" >
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</div>
</form>