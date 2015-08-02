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

jimport('joomla.application.component.controlleradmin');
require_once JPATH_COMPONENT.'/library/fpdf.php';
/**
 * Report generator controller class for admin of transaction.
 */
class LegalconfirmControllerReports extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'reports', $prefix = 'LegalconfirmModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
    
   /*
    * function for generating the report
    */ 
	public function generatereports()
	{
		$id = JRequest::getVar('cid'); 
		$recordid = $id[0];
		$model = $this->getModel(); 
		$recorddetail = $model->generateReport($recordid); 
		$paymentid = JText::_('COM_LEGALCONFIRM_PAYMENT_ID').' '.$recorddetail->id;
		$paymenamount = JText::_('COM_LEGALCONFIRM_PAYMENT_AMOUNT').' '.$recorddetail->amount;
		$auditorname = $model->auditorname($recorddetail->lid);
		$clientname = $model->clientname($recorddetail->cid);
		$Auditorname = JText::_('COM_LEGALCONFIRM_PAYMENT_AUDITOR_NAME').' '. $auditorname;
		$Clientname = JText::_('COM_LEGALCONFIRM_PAYMENT_CLIENT_NAME').' '.$clientname;
		$Transactiondate = JText::_('COM_LEGALCONFIRM_PAYMENT_TRANSACTION_DATE').' '.date('M/d/Y', strtotime($recorddetail->date));
		$config =& JFactory::getConfig();
        $tzoffset = $config->getValue('config.offset');        
        $assign_date =& JFactory::getDate('',$tzoffset);
		$date = JText::_('COM_LEGALCONFIRM_PAYMENT_DATE').' '.date('M/d/Y', strtotime($assign_date->toMySQL(true)));
		$TransactionId = JText::_('COM_LEGALCONFIRM_PAYMENT_TRANSACTION_ID').' '.$recorddetail->transaction_id;
		$tag_line = JText::_('COM_LEGALCONFIRM_TAG_LINE');
		$imagepath = JURI::root().'/templates/legalconfirm/images/logo.png';
		$pdf = new FPDF();
		$pdf->AddPage();
		$title = JText::_('COM_LEGALCONFIRM_REPORT_TITLE');
		$pdf->SetTitle($title);
		$pdf->Image($imagepath,150,10,30,0,'','');
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(150,15,$tag_line,0,1);
		$pdf->Cell(40,10,$paymentid,0,1);
		$pdf->Cell(40,10,$paymenamount,0,1);
		$pdf->Cell(40,10,$Auditorname,0,1);
		$pdf->Cell(40,10,$Clientname,0,1);
		$pdf->Cell(40,10,$Transactiondate,0,1);
		$pdf->Cell(40,10,$date,0,1);
		$pdf->Cell(40,10,$TransactionId,0,1);
		$pdf->Output();
		exit;
		$this->setRedirect('index.php?option=com_legalconfirm&view=reports');
	}
   
    
}
