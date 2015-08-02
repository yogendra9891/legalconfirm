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
$editor =& JFactory::getEditor(); 
$user = JFactory::getUser();
$accountingfirmname = $this->client->company;
$auditorfirmnaame = $this->findAuditorFirmName($user->id);
$app = JFactory::getApplication(); 
$session = JFactory::getSession();
$clientid = JRequest::getVar('id'); 
$session_id = $session->getId();
$templatedata = $this->findTemplate($session_id, $clientid); //echo "<pre>"; print_r($templatedata); exit;
$this->requestfinaldata = $app->getUserState('com_legalconfirm.selectedlawfirmoffices.data');
if($templatedata->cid && $templatedata->template != ''): 
 if($templatedata->session_id == $session_id):
 $temp .= "<div class=\"wrapperdiv\" id=\"wrapperdiv\">";
 $temp .= $templatedata->template;
 $temp .= "</div>";
 endif;
else:
$temp = "<div class=\"wrapperdiv\" id=\"wrapperdiv\"><div class=\"innerwrapperdiv\" id=\"innerwrapperdiv\">";
$temp .= "Our auditors,&nbsp;<span style=\"color:red\">".$auditorfirmnaame."</span> ,&nbsp;are conducting an audit of our financial statements at  and for the  then ended.
          Please provide to them the information requested below involving matters with respect to which you have been engaged and to which you have devoted substantive attention on behalf of the Company in the form of legal consultation or representation.<br/>";
$temp .= "<p style=\"text-decoration: underline;\">Pending or Threatened Litigation, Claims, and Assessments (excluding unasserted claims and assessments)</p>";
$temp .= "Pending or Threatened Litigation, Claims, and Assessments (excluding unasserted claims and assessments)
          Please prepare a description of all material litigation, claims, and assessments (excluding unasserted claims and assessments). Materiality for purposes of this letter includes items involving amounts exceeding $ <span style=\"color:red;\">% of MATERIALITY </span> individually or in the aggregate. The description of each case should include: the nature of the litigation, the progress of the case to date,
           how management is responding or intends to respond to the litigation, e.g., to contest the case vigorously or to seek an out-of-court settlement, 
          and an evaluation of the likelihood of an unfavorable outcome and an estimate, if one can be made, of the amount or range of potential loss.  Also, please identify any pending or threatened litigation, claims,
          and assessments with respect to which you have been engaged but as to which you have not yet devoted substantive attention.<br/>";
$temp .= "<p style=\"text-decoration: underline;\">Unasserted Claims and Assessments</p>";
$temp .= "We understand that whenever, in the course of performing legal services for us with respect to a matter recognized to involve an unasserted possible claim 
          or assessment that may call for financial statement disclosure, you have formed a professional conclusion that we should disclose or consider disclosure
          concerning such possible claim or assessment, as a matter of professional responsibility to us, you will so advise us and will consult with us concerning the question of such disclosure and the applicable requirements of FASB Accounting Standards Codification 450, Contingencies
          (formerly Statement of Financial Accounting Standards No. 5) (excerpts of which can be found in the ABA’s Auditor’s Letter Handbook).
          Please specifically confirm to our auditors that our understanding is correct.<br/>";
$temp .= "We have represented to our auditors that there are no unasserted possible claims or assessments that you have advised us are probable of assertion 
          and must be disclosed in accordance with FASB Accounting Standards Codification 450, Contingencies  (formerly Statement of Financial Accounting Standards No. 5 ).";
$temp .= "<p style=\"text-decoration: underline;\">Response</p>";
$temp .= "Your response should include matters that existed as of , and during the period from that date to the effective date of your response.
          Please specify the effective date of your response if it is other than the date of reply.Please specifically identify the nature of, and reasons for,
          any limitations on your response. Our auditors expect to have the audit completed on <span style=\"color:red;\"> FIELDWORK COMPLETION DATE</span> and would appreciate receiving your 
          reply by that date with a specified effective date no earlier than <span style=\"color: red;\">2 WEEKS FROM DATE OF THIS REQUEST</span>. You may also be requested to provide verbal updates to your written response at a later date.
          We appreciate your timely response to such requests.";
$temp .= "<p style=\"text-decoration: underline;\">Other Matters</p>";
$temp .= "Please also indicate the amount we were indebted to you for services and expenses (billed or unbilled) on<span style=\"color: red;\"> BALANCE SHEET DATE</span>.<br/><br/>";
$temp .= "Very truly yours,<br/><br/>";
$temp .= "CFO / OWNER <br/>";
$temp .= "$accountingfirmname";
$temp .= "<br/></div></div>"; 
endif; 
?>
<link rel="stylesheet" href="administrator/templates/bluestork/css/template.css" type="text/css" /><!-- edit externally because in the editor we need the default css -->
<div class="register-box">
<h1><?php echo JText::_('COM_LEGALCONFIRM_CREATE_TEMPLATE');?></h1>
<div class="clienttemplate" id="clienttemplate">
  <?php   
    echo $editor->display( 'full_text', $temp ,  '100%', '400', '80', '15' );
  ?>
</div>
<form action="<?php echo JRoute::_('index.php?option=com_legalconfirm&task=clientprofile.setTemplate&id='.$this->client->clientid);?>" name="template-form" id="template-form" method="post">
<div style="clear: both">
		<button class="button" id="save-template" type="submit" >Save</button>
</div>
<input type="hidden" name="tempate-input" id="tempate-input">
<input type="hidden" id="templateid" name="templateid" value="<?php echo $templatedata->id;?>" />
<?php echo JHtml::_('form.token');?>
</form></div>
<script>

jQuery(document).ready(function(){
	jQuery('#template-form').submit(function(){
	var y =	jQuery('#full_text_ifr').contents().find('div#wrapperdiv').html(); 
	if(y == null)
	{
		//alert('Blank template not allowed');
		$.msgBox({
                 title:"Alert",
                 content:"Blank template not allowed."
                 });
		return false;
	}
    var t =  htmlspecialchars(y);
    jQuery('#tempate-input').val(t);
    return true;
});
});
function htmlspecialchars(str) {
	 if (typeof(str) == "string") {
	  str = str.replace(/&/g, "&amp;"); /* must do &amp; first */
	  str = str.replace(/"/g, "&quot;");
	  str = str.replace(/'/g, "&#039;");
	  str = str.replace(/</g, "&lt;");
	  str = str.replace(/>/g, "&gt;");
	  }
	 return str;
	 }
</script>
