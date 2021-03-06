<?php

$document =& JFactory::getDocument();
$document->addScript(JURI::base(). 'components/com_legalconfirm/assets/js/jquery.min.js');
$document->addScript(JURI::base(). 'components/com_lawfirm/assets/js/validation.js');
?>
<script>



function validateFormsave(formId)
{
 var formId=document.getElementById(formId);
 if(validate(formId))
       {
		 //set the value in hidden field for button type
	     $('#formtype').val('save');
	     
          //this check triggers the validations
           formId.submit();
           return true;
       }
       else{
        return false;
       }

}

function validateFormsavetopartner(formId)
{
	
	 var partner_id =  window.parent.document.getElementById('partnerid<?php echo $aid;?>').value;
	
	 
	 if(partner_id == ""){
		 alert('Please select the partner first before edting the mail template.');
	  return false;
      }else{
          
          $('#partner_id1').val(partner_id);
          $('#formtype').val('savetopartner');
          
      }    
 var formId=document.getElementById(formId);
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
</script>
<link
	rel="stylesheet"
	href="administrator/templates/bluestork/css/template.css"
	type="text/css" />
<!-- edit externally because in the editor we need the default css -->
<link
	rel="stylesheet" href="templates/legalconfirm/css/style.css"
	type="text/css" />
<div class="register-box">
<div>

</div>
<?php


//get mail template detail
$content = $this->item;
$type = $content->template_type;
$id = $content->id;

//get auditor name
$info = $this->getInfo($aid);

//assign the value to the corresponding variable
$accounting_firm_name = $info['auditor_firm_name'];
$signername = $info['signername'];
$company_name = $info['company_name'];
$assigned_date = $info['assigned_date'];
$lawfirm_name = $info['lawfirm_name'];
$is_readybyemp = $info['is_readybyemp'];

if($type=="pdf"){
	$pdf_name = $content->pdf;
	?>
	<h4>Pdf Submitted to Partner</h4>


	<?php 
	if($pdf_name != ""){
	$pdf_path = JURI::root().'media' .DS.'com_lawfirm'.DS .'pdf'.DS.$pdf_name;
	//echo $pdf_path
	?>
	<img src="<?php echo JURI::base( true ).DS.'templates' .DS.'legalconfirm'.DS .'images'.DS.'pdf_icon.png'; ?>"/>
	<a href="<?php echo $pdf_path; ?>">View Pdf</a>
	<?php 
	}
}else{
	//get mail template
	$temp =  $content->custom_template;

	?>
<div id="mailtemplate">
<h4>Edit Template</h4>
<?php 
//check for mail content
if($temp == ""){
	$temp = "<p>Dear <b>".$accounting_firm_name."</b>:</p>
<p>We have been requested by your firm on Date <b>".$assigned_date."</b>, from <b>".$signername."</b> of <b>".$company_name."</b> (The 'Company'), to furnish you with certain information in connection with your examination of the accounts of the Company as of Balance Sheet Date and for the period from that date to Date <b>".$assigned_date."</b>, the date on which we commenced our internal review procedures for the purpose of preparing our response.</p>
<p>While this firm represents the Company on a periodic basis, our engagement has been limited to specific matters as to which we were consulted by the Company, and our response necessarily relates to such matters. Accordingly, there may exist matters of a legal nature that could have a bearing on the Company's financial condition with respect to which we have not been consulted.</p>
<p>Subject to the foregoing and to the last paragraph of this letter, we advise you that as of Balance Sheet Date and until the date on which we commenced our internal review procedure, we have not been engaged to give substantive attention to or represent the Company in connection with, loss contingencies in excess of individually or in the aggregated (which is the standard of materiality set for in the Company's letter to us) coming with the scope of Clause (a) of Paragraph 5 of the Statement of Policy referred to in the last paragraph of this letter.</p>
<p>The Company has not identified any matters in its letter to us coming within scope of Clauses (b) or (c) of Paragraph 5 of the Statement of Policy referred to in the last paragraph of this letter.</p>
<p>The information set forth herein is as of the date on which we commenced our internal review, and we disclaim any undertaking to advise you of changes which may be brought to our attention after that date.</p>
<p>In collecting the information disclosed to you in this letter, we have consulted, to the extent believed necessary, with lawyers currently in the firm who have performed services for the Company since the beginning of the fiscal period under audit to determine whether such services involved substantive attention in the form of legal consultation or representation concerning matters relevant to the Company's request. We disclaim responsibility to comment on any matters to which any lawyer why is presently with this firm may have given substantive attention prior to, but not after, joining the firm.</p>
<p>We would like to bring the following to your attentions: INCLUDE SPACE HERE FOR LAWYER TO EXPAND ON SPECIFIC ISSUES</p>
<p>Accounting to our accounting records, the Company owed our firm as of the Balance Sheet Date.</p>
<p>According to our work records, as of the Balance Sheet Date, there were unbilled fees of accrued in connection with services performed by us for the Company on or before Balance Sheet Date. Of that amount, fees was billed on Date.</p>
<p>This response is limited by, and in accordance with, the American Bar Association Statement of Policy Regarding Lawyers' Responses to Auditors' Requests for Information (December, 1975). Without limiting the generality of the foregoing, the limitations set forth in such Statement on the scope and use of this response (Paragraph 2 and 7) are specifically incorporated herein by reference, and the term 'loss contingencies' as used herein is qualified in its entirety by Paragraph 5 of the Statement and accompanying Commentary (Which is an integral part of the Statement). Consistent with the last sentence of Paragraph 6 of the American Bar Association Statement of Policy and pursuant to the Company's request, this will confirm the Company's understanding that whenever, in the course of performing legal services for the Company with respect to a matter recognized to involve an unasserted possible claim or assessment that may call for financial statement disclosure, we have formed a professional consultation that the Company must disclose or consider disclosure concerning such possible claim or assessment, we will so advise the Company, as a matter of professional responsibility to the Company, and will consult with the Company concerning the question of such disclosure and the applicable requirements of Statement of Financial Accounting Standards No. 5 now codified as FASB Accounting Standards Codification Subtopic 450-20, Contingencies - Loss Contingencies.</p>
<p><b>".$lawfirm_name."</b></p>";
}
$editor =& JFactory::getEditor();
echo $editor->display( 'full_text', $temp ,  '100%', '400', '80', '15' );
?> 


</div>
<?php } ?></div>

