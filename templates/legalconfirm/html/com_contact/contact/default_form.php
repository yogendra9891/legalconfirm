<?php

 /**
 * @package		Joomla.Site
 * @subpackage	com_contact
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
//JHtml::_('behavior.tooltip');
$doc = JFactory::getDocument();
$doc->addScript('components/com_legalconfirm/assets/js/validation.js');
 if (isset($this->error)) : ?>
	<div class="contact-error">
		<?php echo $this->error; ?>
	</div>
<?php endif; ?>
<script>
function validateForm(formId)
{
 var formId = document.getElementById(formId);
 var email1 = $('#jform_contact_email').val();
// if(email1 != ''){
//     $('#emailerr1').html('Invalid Email');
//   return false;
// }
 if(validate(formId))
       {
          //this check triggers the validations
           formId.submit();
           return true;
       }
       else{
    	
        return false;
       }

}
function isEmail(str) {
    // Check whether email is proper.
    var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;  
   return emailPattern.test(str);  
}

</script>
<div class="contact-form">
<div class="contact-form-llc">
	<form id="contact-form" action="<?php echo JRoute::_('index.php'); ?>" method="post" onsubmit="return validateForm('contact-form');">
       <?php echo JText::_('COM_CONTACT_FORM_LABEL'); ?>
			<dl>
				<dt><label title="" for="jform_contact_name" >Name<span class="star">&nbsp;*</span></label></dt>
				<dd><input type="text" size="30" class="inputbox required" value="" name="jform[contact_name]" ></dd>
				<dt><label title="" for="jform_contact_phone" id="jform_contact_phone-lbl">Phone</label></dt>
				<dd><input type="text" size="30" value="" id="jform_contact_phone" name="jform[contact_phone]"></dd>
				<dt><label title="" for="jform_contact_email" id="jform_contact_email-lbl">Email<span class="star">&nbsp;*</span></label></dt>
				<dd><input  size="30" value="" id="jform_contact_email" class="required email1" name="jform[contact_email]" >
					<span id="emailerr1" class="erremail1"></span>
				</dd>
				<dt><label title="" for="jform_contact_emailmsg" id="jform_contact_emailmsg-lbl">Subject<span class="star">&nbsp;*</span></label></dt>
				<dd><input type="text" size="60" class="inputbox required" value="" id="jform_contact_emailmsg" name="jform[contact_subject]" ></dd>
				<dt><label title="" for="jform_contact_message" id="jform_contact_message-lbl">Message<span class="star">&nbsp;*</span></label></dt>
				<dd><textarea  rows="10" cols="50" id="jform_contact_message" name="jform[contact_message]" class="inputbox required"></textarea></dd>
										<dt><label title="" for="jform_contact_email_copy" id="jform_contact_email_copy-lbl">Send copy to yourself</label></dt>
						<dd><input type="checkbox" value="" id="jform_contact_email_copy" name="jform[contact_email_copy]"></dd><!--
			<?php //Dynamically load any additional fields from plugins. ?>
			     <?php foreach ($this->form->getFieldsets() as $fieldset): ?>
			          <?php if ($fieldset->name != 'contact'):?>
			               <?php $fields = $this->form->getFieldset($fieldset->name);?>
			               <?php foreach($fields as $field): ?>
			                    <?php if ($field->hidden): ?>
			                         <?php echo $field->input;?>
			                    <?php else:?>
			                         <dt>
			                            <?php echo $field->label; ?>
			                            <?php if (!$field->required && $field->type != "Spacer"): ?>
			                               <span class="optional"><?php echo JText::_('COM_CONTACT_OPTIONAL');?></span>
			                            <?php endif; ?>
			                         </dt>
			                         <dd><?php echo $field->input;?></dd>
			                    <?php endif;?>
			               <?php endforeach;?>
			          <?php endif ?>
			     <?php endforeach;?>
				--><dt></dt>
				<dd><button type="submit"><?php echo JText::_('COM_CONTACT_CONTACT_SEND'); ?></button>
					<input type="hidden" name="option" value="com_contact" />
					<input type="hidden" name="task" value="contact.submit" />
					<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
					<input type="hidden" name="id" value="<?php echo $this->contact->slug; ?>" />
					<?php echo JHtml::_( 'form.token' ); ?>
				</dd>
			</dl>
	</form>
</div>
</div>

