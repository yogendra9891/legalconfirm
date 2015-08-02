<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.modal');
$template = JFactory::getApplication()->getTemplate();
$config	= JFactory::getConfig();
$auditor = $config->getValue('auditor');
$auditor_emp = $config->getValue('auditor_emp');
$lawfirm = $config->getValue('lawfirm');
$lawfirm_emp = $config->getValue('lawfirm_emp');
$lawfirm_partner = $config->getValue('lawfirm_partner');
?>
<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_config
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
?>
 <link rel="stylesheet" href="templates/system/css/system.css" type="text/css" />
 <link rel="stylesheet" href="templates/bluestork/css/template.css" type="text/css" />

<?php 
//get user detail
$userid = JRequest::getVar('id');
$user_detail = $this->getUserDetail($userid);

$fullname = $user_detail['profile']->name;
$email = $user_detail['profile']->email;
$firm_name = $user_detail['profile']->accounting_firm;
$emp_title = $user_detail['profile']->emp_title;
$phone = $user_detail['profile']->phone;

?>
<fieldset>
<legend>Personal Info:</legend>
<table class="adminlist">
		<tr>
		<td>Full Name</td><td><?php echo $fullname;?></td>
		</tr>
		<tr>
		<td>Email</td><td><?php echo $email;?></td>
		</tr>
		<tr>
		<td>Employee Title</td><td><?php echo $emp_title;?></td>
		</tr>
		<tr>
		<td>Phone</td><td><?php echo $phone;?></td>
		</tr>
</table>
</fieldset>
<br />
<fieldset>
<legend>Office:</legend>
<table class="adminlist">
<tr>
<td>Sr No.</td><td>Title</td><td>Address</td><td>City</td><td>State</td><td>Country</td><td>Zip</td>
</tr>
<?php 
$a = 0;
foreach($user_detail['offices'] as $single){
?>
       <tr>
       <td><?php echo ++$a; ?></td>
		<td><?php echo $single->office_title; ?></td>
		<td><?php echo $single->address; ?></td>
		<td><?php echo $single->city; ?></td>
		<td><?php echo $single->state; ?></td>
		<td><?php echo $single->country; ?></td>
		<td><?php echo $single->zip; ?></td>
	   </tr>
	  
<?php	
}?>
</table>
</fieldset>
<br />
<?php 
//check for group
$groupid = $user_detail['groupid'];
$allowuser = array($lawfirm,$auditor);
if(in_array($groupid,$allowuser)){
?>
<fieldset>
<legend>Employee:</legend>
<table class="adminlist">
<tr>
<td>Sr No.</td><td>Name</td><td>Email</td>
</tr>
<?php 
$c = 0;
foreach($user_detail['employeelist'] as $single){
	?>
	<tr>
	<td><?php echo ++$c;?></td><td><a href="<?php echo JRoute::_('index.php?option=com_users&view=users&layout=userdetail&tmpl=component&id='.(int) $single->id);?>"><?php echo $single->name;?></a></td><td><?php echo $single->email;?></td>
	<?php
	
}
?>
</table>
</fieldset>
<?php 
}else{
	?>
	<fieldset>
<legend>Employer:</legend>
<table class="adminlist">
<?php 
//echo "<pre>";
//print_r($user_detail['employer']);
?>
<tr>

<td>Name</td><td><a href="<?php echo JRoute::_('index.php?option=com_users&view=users&layout=userdetail&tmpl=component&id='.(int) $user_detail['employer']->id);?>"><?php echo $user_detail['employer']->name;?></a></td>
</tr>
<tr>
<td>Email</td><td><a href="<?php echo JRoute::_('index.php?option=com_users&view=users&layout=userdetail&tmpl=component&id='.(int) $user_detail['employer']->id);?>"><?php echo $user_detail['employer']->email;?></a></td>
</tr>
</table>
</fieldset>
<?php }
?>
<br />
<?php 
if($groupid==$auditor){
?>
<fieldset>
<legend>Payment Info:</legend>
<table  class="adminlist">
<tr>
<td>Credit Card Number</td><td><?php echo $user_detail['payment']->cc_number;?></td>
</tr>
<tr>
<td>Name on credit card</td><td><?php echo $user_detail['payment']->name_on_cc;?></td>
</tr>
<tr>
<td>ESN#</td><td><?php echo $user_detail['payment']->esn;?></td>
</tr>
<tr>
<td>Address</td><td><?php echo $user_detail['payment']->address;?></td>
</tr>
<tr>
<td>City</td><td><?php echo $user_detail['payment']->city;?></td>
</tr>
<tr>
<td>State</td><td><?php echo $user_detail['payment']->state;?></td>
</tr>
<tr>
<td>Country</td><td><?php echo $user_detail['payment']->country;?></td>
</tr>
<tr>
<td>Zip</td><td><?php echo $user_detail['payment']->zip;?></td>
</tr>
</table >
</fieldset>
<?php } ?>