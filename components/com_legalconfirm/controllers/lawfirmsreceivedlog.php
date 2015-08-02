<?php
/**
 * @version     1.0.0
 * @package     com_legalconfirm
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Abhishek Gupta <abhishek.gupta@daffodilsw.com> - http://
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';
require_once JPATH_COMPONENT.'/library/tcpdf_include.php';
require_once JPATH_COMPONENT.'/library/tcpdf.php';
/**
 * Clientslog controller class.
 */
class LegalconfirmControllerLawfirmsreceivedlog extends LegalconfirmController
{

	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Lawfirmsreceivedlog', $prefix = 'LegalconfirmModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	/*
	 * function for generating the pdf for the custom template submitted by law firm partner for auditor
	 * @params clientid, proposalid, assign_proposalid
	 */
	public function generatepdf()
	{
		$pid                = JRequest::getVar('pid');
		$clientid           = JRequest::getVar('id');
		$lawfirmid          = JRequest::getVar('lawfirmid');
		$assign_proposal_id = JRequest::getVar('assign_proposalid');
		$model              = $this->getModel();
		$lawfirmname        = $model->lawfirmname($lawfirmid); 
		$templateresult     = $model->getTemplate($assign_proposal_id);
		$lawfirmpartnerinfo = $model->getPartnerinfo($assign_proposal_id); 
		$template = htmlspecialchars_decode($templateresult->custom_template);
		$customtempale = '<div><b>Lawfirm Info:</b></div><div style=\"\">Lawfirm:    '.ucfirst($lawfirmname). '</div><div>LawFirmPartner:     '.$lawfirmpartnerinfo->name.'</div>';
		$customtempale .= '<div style=\"\">Email: '.$lawfirmpartnerinfo->email.'</div>';
		$customtempale .= '<div style=\"\">Date sent to Auditor: '.date("d/M/Y h:i:s", strtotime($lawfirmpartnerinfo->responsedate)).'</div>';
		$complete_template = $template.$customtempale;
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Abhishek Yogendra');
		$pdf->SetTitle('Lawfirm Partner');
		$pdf->SetSubject('Lawfirm Partner Response');
		$pdf->SetKeywords('TCPDF, PDF, lawfirm, test, guide');
		// set default header data
		
		$pdf->SetHeaderData('', '0', $lawfirmname, '');
		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		// ---------------------------------------------------------
		// set font
		$pdf->SetFont('helvetica', '', 9);
		// add a page
		$pdf->AddPage();
		
		// output the HTML content
		$pdf->writeHTML($complete_template, true, 0, true, 0);
		// reset pointer to the last page
		$pdf->lastPage();
		// ---------------------------------------------------------
		
		//Close and output PDF document
		$pdf->Output('Lawfirm_Partner.pdf', 'I');
		exit;		
	}

}
