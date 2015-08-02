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
//require_once JPATH_COMPONENT.'/library/tcpdf_include.php';
//require_once JPATH_COMPONENT.'/library/tcpdf.php';
/**
 * Auditors controller class.
 */
class LawfirmControllerLawfirmemp extends LawfirmController
{
	
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	
	public function &getModel($name = 'lawfirmemp', $prefix = 'LawfirmModel')
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
			$model = $this->getModel('Lawfirmemp', 'LawfirmModel');
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
			$model = $this->getModel('Lawfirmemp', 'LawfirmModel');
			$result = $model->getAssignedPinnedTask();
			$view=$this->getView('lawfirmemp','html');
			$view->assign('data',$result);
			$view->setLayout('changeproposal');
			$view->paginations	= $model->getPaginations();
			$view->setModel( $this->getModel( 'Lawfirmemp' ) );
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
			$model = $this->getModel('Lawfirmemp', 'LawfirmModel');
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
			$model = $this->getModel('Lawfirmemp', 'LawfirmModel');
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
		
			$model = $this->getModel('Lawfirmemp', 'LawfirmModel');
			$result = $model->getUnassignedTask();
			
			$view=$this->getView('lawfirmemp','html');
			$view->assign('data', $result);
			$view->setLayout('changenonfrqproposal');
			$view->setModel( $this->getModel( 'Lawfirmemp' ) );
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
			$model = $this->getModel('Lawfirmemp', 'LawfirmModel');
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
			$model = $this->getModel('Lawfirmemp', 'LawfirmModel');
			$result = $model->getProposalEmail($pid);
			$view=$this->getView('lawfirmemp','html');
			$view->assign('data',$result);
			$view->setLayout('auditormaildetail');
			//$view->setModel( $this->getModel( 'Lawfirmemp' ) );
			$view->getmaildetail(); 
		}
		
		/**
		 * Method to check mail template type
		 */
		public function checkmailtemplatetype(){
			$view=$this->getView('lawfirmemp','html');
			$view->assign('data','test');
			$view->setModel( $this->getModel( 'Lawfirmemp' ) );
			$view->setLayout('checkmailtemplate');
			$view->checktemplate(); 
		}
		/**
		 * Method to unpin non frequent task
		 */
		public function unpinnonfrqtask(){
			$pid = JRequest::getVar('pid');
			$id = JRequest::getVar('id');
			//get model 
			$model = $this->getModel('Lawfirmemp', 'LawfirmModel');
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
			$model = $this->getModel('Lawfirmemp', 'LawfirmModel');
			$result = $model->getTaskAssignedByPartner();
			$view=$this->getView('lawfirmemp','html');
			$view->assign('data',$result);
			$view->setLayout('taskbypartner');
			$view->setModel( $this->getModel( 'Lawfirmemp' ) );
			$view->getFullDetail(); 
		}
		
		/**
		 * Method to unpin the task from partner
		 */
		public function unpintaskfrompartner(){
			$pid = JRequest::getVar('pid');
			$id = JRequest::getVar('id');
			
			//get model 
			$model = $this->getModel('Lawfirmemp', 'LawfirmModel');
			$result = $model->unpintaskfrompartner($id,$pid);
			if(!$result){
				echo "fail";
				exit;
			}
			echo "success";
			exit;
		}
		
		/**
		 * Method to save lawfirm employee custom template
		 */
		public function addcustomhtml(){
			$content = $_POST['full_text'];
			//$content  = JRequest::getVar('full_text');
			$content1 = htmlentities($content);
			$aid = JRequest::getVar('aid');
			$id = JRequest::getVar('id');
			$content = $_POST['full_text'];
			$type = JRequest::getVar('type');
			//get model 
			$model = $this->getModel('Lawfirmemp', 'LawfirmModel');
			$result = $model->savetemplate($id,$aid,$content1,$type);
			
            if($result=="save"){
            	$this->setMessage(JText::_('Custom template updated successfully'));
		        $this->setRedirect(JRoute::_('index.php?option=com_lawfirm&view=lawfirmemp&task=lawfirmemp.checkmailtemplatetype&tmpl=component&id='.$aid, false));
            }elseif($result == "savetopartner"){
            	
            $view=$this->getView('lawfirmemp','html');
			$view->assign('data',$aid);
			$view->setLayout('success');
			$view->setModel( $this->getModel( 'Lawfirmemp' ) );
			$view->getFullDetail(); 
            }
		   //$this->setRedirect(JRoute::_('index.php?option=com_lawfirm&view=lawfirmemp&layout=success&tmpl=component', false));
		}
		
		/**
		 * Method to uplaod pdf
		 */
		public function uploadpdf(){
			
			$aid = JRequest::getVar('aid');
			$id = JRequest::getVar('id');

			//get model 
			$model = $this->getModel('Lawfirmemp', 'LawfirmModel');
			$result = $model->uploadpdf($id,$aid);
			
			if($result == "save"){
				$this->setMessage(JText::_('Pdf Uploaded Successfully'));
		        $this->setRedirect(JRoute::_('index.php?option=com_lawfirm&view=lawfirmemp&task=lawfirmemp.checkmailtemplatetype&tmpl=component&type=pdf&id='.$aid, false));
			}elseif($result == "savetopartner"){
            $view=$this->getView('lawfirmemp','html');
			$view->assign('data',$aid);
			$view->setLayout('success');
			$view->setModel( $this->getModel( 'Lawfirmemp' ) );
			$view->getFullDetail(); 
            }
		}
		
		/**
		 * Method to get prepared templete
		 * @param assiign id.
		 */
		public function getMyTemplate(){
			$id = JRequest::getVar('id');
			//get model object
			$model = $this->getModel('Lawfirmemp', 'LawfirmModel');
			$view=$this->getView('lawfirmemp','html');
			$data = $model->getMyTemplate($id);
			$view->assign('data',$data);
			$view->setModel( $this->getModel( 'Lawfirmemp' ) );
			$view->setLayout('getmytemplate');
			$view->getmytemplate();
		}
		
		/**
		 * Method to get partner detail who disapproved the task.
		 */
		public function getPartnerInfo(){
			$id = JRequest::getVar('id');
			//get model object
			$model = $this->getModel('Lawfirmemp', 'LawfirmModel');
			$data = $model->getPartnerInfo($id);
			$view=$this->getView('lawfirmemp','html');
			$view->assign('data',$data);
			$view->setModel( $this->getModel( 'Lawfirmemp' ) );
			$view->setLayout('getpartnerinfo');
			$view->getmytemplate();
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
		
		//get lawfirm name
		//$lawfirm_name = $model->getLawfirmName();
		
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
		$pdf->Output('Auditor request.pdf', 'I');
		exit;		
	}
}
