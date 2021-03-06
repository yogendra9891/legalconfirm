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
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_legalconfirm' . DS . 'library' . DS . 'tcpdf_include.php' );
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_legalconfirm' . DS . 'library' . DS . 'tcpdf.php' );

/**
 * Auditors controller class.
 */
class LawfirmControllerLawfirmpartner extends LawfirmController
{

	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */

	public function &getModel($name = 'lawfirmpartner', $prefix = 'LawfirmModel')
	{

		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	/**
	 * Method to change status of user
	 * @Author Abhishek
	 * @param userid
	 */
	public function changeStatus(){
		$userId = array(JRequest::getVar(id));

		//get model
		$model = $this->getModel('Lawfirmadmin', 'LawfirmModel');
		$result = $model->activate($userId);
		if($result){
			$this->setMessage(JText::_('User status changed successfully'));
			$this->setRedirect(JRoute::_('index.php?option=com_lawfirm&view=lawfirmadmin', false));
		}
	}

	/**
	 * Method to pin task
	 */
	public function pintask(){
		$pid = JRequest::getVar('pid');
		$id = JRequest::getVar('id');
		$aid = JRequest::getVar('aid');
		//get model
		$model = $this->getModel('Lawfirmpartner', 'LawfirmModel');
		$result = $model->pintask($id,$pid,$aid);
		if($result=="false"){
			echo "fail";
			exit;
		}
		if($result=="already_pin"){
			echo "already_pin";
			exit;
		}
		echo "success";
		exit;
	}

	/**
	 * Method to edit the frequent client list
	 */
	public function edit(){
		//get model
		$model = $this->getModel('Lawfirmpartner', 'LawfirmModel');
		$result = $model->getAssignedPinnedTask();
		$view=$this->getView('lawfirmpartner','html');
		$view->assign('data',$result);
		$view->setLayout('changeproposal');
		$view->paginations	= $model->getPaginations();
		$view->setModel( $this->getModel( 'Lawfirmpartner' ) );
		$state	= $model->getstate();
		$view->assign('state', $state);
		$view->getFullDetail();
	}

	/**
	 * Method to unpin task
	 */
	public function unpintask(){
		$pid = JRequest::getVar('pid');
		$id = JRequest::getVar('id');
		$aid = JRequest::getVar('aid');
		//get model
		$model = $this->getModel('Lawfirmpartner', 'LawfirmModel');
		$result = $model->unpintask($id,$pid);
		if(!$result){
			echo "fail";
			exit;
		}
		echo "success";
		exit;
	}
	/**
	 * Method to unpin the task from default layot
	 */
	public function unpintaskdefault(){
		$pid = JRequest::getVar('pid');
		$id = JRequest::getVar('id');
		//get model
		$model = $this->getModel('Lawfirmpartner', 'LawfirmModel');
		$result = $model->unpintaskdefault($id,$pid);
		if(!$result){
			echo "fail";
			exit;
		}
		echo "success";
		exit;
	}

	/**
	 * Method to edit not frequent section
	 */
	public function notfrequentedit(){
		//get model
		 
		$model = $this->getModel('Lawfirmpartner', 'LawfirmModel');
		$result = $model->getUnassignedTask();
		$view=$this->getView('lawfirmpartner','html');
		$view->assign('data',$result);
		$view->setLayout('changenonfrqproposal');
		$view->setModel( $this->getModel( 'Lawfirmpartner' ) );
		$view->paginations	= $model->getPaginations();
		$state	= $model->getstate();
		$view->assign('state', $state);
		$view->getFullDetail();
	}

	/**
	 * Method to pin non frequent task
	 */
	public function pinnotfrqtask(){
		$pid = JRequest::getVar('pid');
		$id = JRequest::getVar('id');
		//get model
		$model = $this->getModel('Lawfirmpartner', 'LawfirmModel');
		$result = $model->pinnotfrqtask($id,$pid);
		if(!$result){
			echo "fail";
			exit;
		}
		echo "success";
		exit;
	}

	/**
	 * Method to get mail
	 */
	public function getmail(){
		//get model
		$pid = JRequest::getVar('id');
		$model = $this->getModel('Lawfirmpartner', 'LawfirmModel');
		$result = $model->getProposalEmail($pid);
		$view=$this->getView('lawfirmpartner','html');
		$view->assign('data',$result);
		$view->setLayout('auditormaildetail');
		//$view->setModel( $this->getModel( 'Lawfirmemp' ) );
		$view->getmaildetail();
	}

	/**
	 * Method to check mail template type
	 */
	public function checkmailtemplatetype(){
		$view=$this->getView('lawfirmpartner','html');
		$view->assign('data','test');
		$view->setModel( $this->getModel( 'Lawfirmpartner' ) );
		$view->setLayout('checkmailtemplate');
		$view->checktemplate();
	}

	/**
	 * Method to check mail template type
	 */
	public function checkmailtemplatetypePartner(){
		$view=$this->getView('lawfirmpartner','html');
		$view->assign('data','test');
		$view->setModel( $this->getModel( 'Lawfirmpartner' ) );
		$view->setLayout('partnermailtemplate');
		$view->checktemplate();
	}

	/**
	 * Method to unpin non frequent task
	 */
	public function unpinnonfrqtask(){
		$pid = JRequest::getVar('pid');
		$id = JRequest::getVar('id');
		//get model
		$model = $this->getModel('Lawfirmpartner', 'LawfirmModel');
		$result = $model->unpinnonfrqtask($id,$pid);
		if(!$result){
			echo "fail";
			exit;
		}
		echo "success";
		exit;
	}

	/**
	 * Method to edit the task assigned by partner
	 */
	public function taskbypartner(){
		//get model
		$model = $this->getModel('Lawfirmpartner', 'LawfirmModel');
		$result = $model->getTaskAssignedByPartner();
		$view=$this->getView('lawfirmpartner','html');
		$view->assign('data',$result);
		$view->setLayout('taskbypartner');
		$view->setModel( $this->getModel( 'Lawfirmpartner' ) );
		$view->getFullDetail();
	}

	/**
	 * Method to unpin the task from partner
	 */
	public function unpintaskfrompartner(){
		$pid = JRequest::getVar('pid');
		$id = JRequest::getVar('id');
			
		//get model
		$model = $this->getModel('Lawfirmpartner', 'LawfirmModel');
		$result = $model->unpintaskfrompartner($id,$pid);
		if(!$result){
			echo "fail";
			exit;
		}
		echo "success";
		exit;
	}

	/**
	 * Method to save lawfirm partner custom template
	 */
	public function addcustomhtml(){

		$content = $_POST['full_text'];
		//$content  = JRequest::getVar('full_text');
		$content1 = htmlentities($content);
		$aid = JRequest::getVar('aid');
		$id = JRequest::getVar('id');
		$buttontype = JRequest::getVar('buttontype');
			
		$type = JRequest::getVar('type');
		//get model
		$model = $this->getModel('Lawfirmpartner', 'LawfirmModel');
			
		//check for form submit type
		//if for approval
		if($buttontype == "approve"){
			$result = $model->approvetemplate($id,$aid,$content1,$type);
		}
		//if send to auditor
		elseif($buttontype="sendtoauditor"){
			
                $result = $model->approvetemplate($id,$aid,$content1,$type);
                if($result){
                        //send to auditor
			$result = $model->sendProposalToAuditor($aid);
                    }
		}
			
		//check for response
		if($result=="approved"){
				
			$this->setMessage(JText::_('Approved'));
			$this->setRedirect(JRoute::_('index.php?option=com_lawfirm&view=lawfirmpartner&task=lawfirmpartner.checkmailtemplatetype&tmpl=component&id='.$aid.'&aptype=1', false));
		}elseif($result == "savetopartner"){

			$view=$this->getView('lawfirmpartner','html');
			$view->assign('data',$aid);
			$view->setLayout('success');
			$view->setModel( $this->getModel( 'Lawfirmpartner' ) );
			$view->getFullDetail();
		}
		//$this->setRedirect(JRoute::_('index.php?option=com_lawfirm&view=lawfirmemp&layout=success&tmpl=component', false));
	}

	/**
	 * Method to uplaod pdf
	 */
	public function uploadPdf(){
		$aid = JRequest::getVar('aid');
		$id = JRequest::getVar('id');
		//get model
		$model = $this->getModel('Lawfirmpartner', 'LawfirmModel');
		$result = $model->uploadPdf($id,$aid);
		if($result == "save"){
			$this->setMessage(JText::_('Pdf Uploaded Successfully'));
			$this->setRedirect(JRoute::_('index.php?option=com_lawfirm&view=lawfirmpartner&task=lawfirmpartner.checkmailtemplatetype&tmpl=component&type=pdf&id='.$aid.'&aptype=1', false));
		}elseif($result == "savetoauditor"){

			$view=$this->getView('lawfirmpartner','html');
			$view->assign('data',$aid);
			$view->setLayout('success');
			$view->setModel( $this->getModel( 'Lawfirmpartner' ) );
			$view->getFullDetail();
		}
	}

	/**
	 * Method to upload new pdf by partner when the partner change the pdf uploaded by employee
	 */
	public function uploadNewPdf(){
		$aid = JRequest::getVar('aid');
		$id = JRequest::getVar('id');
		//get model
		$model = $this->getModel('Lawfirmpartner', 'LawfirmModel');
		$result = $model->uploadNewPdf($id,$aid);
		if($result == "save"){
			$this->setMessage(JText::_('Pdf Uploaded Successfully'));
			$this->setRedirect(JRoute::_('index.php?option=com_lawfirm&view=lawfirmpartner&task=lawfirmpartner.checkmailtemplatetype&tmpl=component&type=pdf&id='.$aid, false));
		}elseif($result == "savetopartner"){

			$view=$this->getView('lawfirmpartner','html');
			$view->assign('data',$aid);
			$view->setLayout('success');
			$view->setModel( $this->getModel( 'Lawfirmpartner' ) );
			$view->getFullDetail();
		}
	}
	/**
	 * Method to approve the template
	 * when lawfirm partner pin the task by himself
	 */
	public function addtemplate(){
			
		$content = $_POST['full_text'];
		//$content  = JRequest::getVar('full_text');
		$content1 = htmlentities($content);
			
		$aid = JRequest::getVar('aid');
		$id = JRequest::getVar('id');
		$buttontype = JRequest::getVar('buttontype');
			
		$type = JRequest::getVar('type');
			
		//get model
		$model = $this->getModel('Lawfirmpartner', 'LawfirmModel');
			
		//check for form submit type
		//if for approval
			
		if($buttontype == "approve"){
			$result = $model->addtemplate($id,$aid,$content1,$type);
			if($result){
				$this->setMessage(JText::_('Approved'));
				$this->setRedirect(JRoute::_('index.php?option=com_lawfirm&view=lawfirmpartner&task=lawfirmpartner.checkmailtemplatetypePartner&tmpl=component&id='.$aid.'&aptype=1', false));
			}
		}
		//if send to auditor then template would be saved again and send to the Auditor.
		elseif($buttontype=="sendtoauditor"){
                        //save the template
                        $result = $model->addtemplate($id,$aid,$content1,$type);
                if($result){
                        //send to auditor
			$result1 = $model->sendProposalToAuditor($aid);
                    }
			if($result1 =="savetopartner"){
				$view=$this->getView('lawfirmpartner','html');
				$view->assign('data',$aid);
				$view->setLayout('success');
				$view->setModel( $this->getModel( 'Lawfirmpartner' ) );
				$view->getFullDetail();
			}
		}
	}

	/**
	 * if task is approved by partner when he pin the task by himself and
	 * after that he unpin that task
	 */
	public function unpintaskbypartner(){
		$pid = JRequest::getVar('pid');
		$id = JRequest::getVar('id');
		$aid = JRequest::getVar('aid');
		//get model
		$model = $this->getModel('Lawfirmpartner', 'LawfirmModel');
		$result = $model->unpintaskbypartner($id,$pid);
		if(!$result){
			echo "fail";
			exit;
		}
		echo "success";
		exit;
	}

	/**
	 * Method to approve pdf by partner
	 */
	public function approvepdf(){
		$aid = JRequest::getVar('aid');
		$id = JRequest::getVar('id');
		$pdfname = JRequest::getVar('pdfname');
		$buttontype = JRequest::getVar('buttontype');

		//get model
		$model = $this->getModel('Lawfirmpartner', 'LawfirmModel');
		if($buttontype=="approve"){
			$result = $model->approvePdf($aid,$id);
			if($result){
				$this->setMessage(JText::_('Approved'));
				$this->setRedirect(JRoute::_('index.php?option=com_lawfirm&view=lawfirmpartner&task=lawfirmpartner.checkmailtemplatetype&tmpl=component&id='.$aid.'&aptype=1', false));
			}
		}elseif($buttontype=="sendtoauditor"){
			$result = $model->sendProposalToAuditor($aid);
			if($result){
				$view=$this->getView('lawfirmpartner','html');
				$view->assign('data',$aid);
				$view->setLayout('success');
				$view->setModel( $this->getModel( 'Lawfirmpartner' ) );
				$view->getFullDetail();
			}
		}
	}

	/**
	 * Method to send pdf to auditor
	 */
	public function sendPdfToAuditor(){
		$aid = JRequest::getVar('aid');
		echo $aid;
		die;
		$model = $this->getModel('Lawfirmpartner', 'LawfirmModel');
		$result = $model->sendProposalToAuditor($aid);
	}

	/**
	 * Method to disapprove the proposal by the partner
	 */
	public function disApprove(){
		//get assign id
		$aid = JRequest::getVar('aid');
		//get model object
		$model = $this->getModel('Lawfirmpartner', 'LawfirmModel');
		$result = $model->disApprove($aid);
		if($result){
			$view=$this->getView('lawfirmpartner','html');
			$view->assign('data','disapprove');
			$view->setLayout('success1');
			$view->setModel( $this->getModel( 'Lawfirmpartner' ) );
			$view->getFullDetail();
		}

	}

	/**
	 * Method to generate pdf of auditor mail to lawfirm
	 */
	public function generatepdf()
	{
		$pid        = JRequest::getVar('id');
		$model = $this->getModel();
		$templateresult = $model->getProposalEmail($pid);
		$responseDate = date("d/M/Y h:i:s", strtotime($templateresult->responsedate)) ;

		$html="
<p><b>Signer Info</b></p>
<p>Title: ".$templateresult->signertitle."</p>
<p>Name: ".$templateresult->fname." ".$templateresult->lname."</p>
<p>Email: ".$templateresult->email."</p>
<p>Company Name: ".$templateresult->company."</p>
<p>Date and time accepted by signer: ".$responseDate."</p>
";
		$template = htmlspecialchars_decode($templateresult->template.$html);
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Abhishek Yogendra');
		$pdf->SetTitle('Auditor request');
		$pdf->SetSubject('Auditor request');
		$pdf->SetKeywords('TCPDF, PDF, lawfirm, test, guide');
		// set default header data

		$pdf->SetHeaderData('logo.png', '40', '', '');
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
		$pdf->writeHTML($template, true, 0, true, 0);
		// reset pointer to the last page
		$pdf->lastPage();
		// ---------------------------------------------------------

		//Close and output PDF document
		$pdf->Output('Auditor_request.pdf', 'I');
		exit;
	}
}
