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
/**
 * clientProfile controller class.
 */
class LegalconfirmControllerClientprofile extends LegalconfirmController
{

	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Clientprofile', $prefix = 'LegalconfirmModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	/*
	 * function for finding the law firm offices
	 */
	public function lawfirmoffices()
	{
		$app = JFactory::getApplication();
		$lawfirmidarray = JRequest::getVar('cid'); 
		$clientid = JRequest::getVar('id'); //echo "<pre>"; print_r($lawfirmidarray); exit;
		//		$lawfirmid = $lawfirmidarray[0];
		//		$model = $this->getModel();
		//		$lawFirmOffies = $model->getOffices($lawfirmid);
		$app->setUserState('com_legalconfirm.lawfirms.data', $lawfirmidarray);
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&layout=lawoffices&id='.$clientid, false));
//      $view = $this->getView('clientprofile', 'html');
//      $view->setLayout('lawoffices');
//      $view->display();
	}
	/*
	 * function for finding the law firm offices
	 */
	public function lawfirmofficesaddmore()
	{
		$app = JFactory::getApplication();
		$lawfirmidarray = JRequest::getVar('cid'); 
		$clientid = JRequest::getVar('id');
		//		$lawfirmid = $lawfirmidarray[0];
		//		$model = $this->getModel();
		//		$lawFirmOffies = $model->getOffices($lawfirmid);
		$app->setUserState('com_legalconfirm.lawfirms.data', $lawfirmidarray);
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientlawfirmadd&tmpl=component&layout=lawoffices&id='.$clientid, false));
	}
	
	/*
	 * function for putting the data in session.. after law firm offices selection...
	 */
	public function requestProposal()
	{
		$app = JFactory::getApplication();
		$lawfirmofficesarray = JRequest::getVar('cid');
		$clientid = JRequest::getVar('id');
		$model = $this->getModel();
		$lawfirmids = array();
		$lawfirmofficesids = array();
		//fincding the lawfirmids...
		foreach($lawfirmofficesarray as $firstarray)
		{
			//finding the lawfirmid by only the 0 index value because firmid will be same for all lawfirmoffice of a lawfirm..
			$lawfirmids[] = 	$this->getLawfirmId($firstarray[0]);
			$lawfirmofficesids[] = json_encode($firstarray);
		}
		//we are using the for loop, if we use foreach then we have to use two foreach loop for each array lawfirmids & lawfirmofficesids..
		$tempdata = array();
		for($j = 0; $j <count($lawfirmids); $j++)
		{
			$tempdata[$j]['lawfirm'] = $lawfirmids[$j];
			$tempdata[$j]['lawfirmoffices'] = $lawfirmofficesids[$j];
		}
		$app->setUserState('com_legalconfirm.selectedlawfirmoffices.data', $tempdata);
		$app->setUserState('com_legalconfirm.clientprofile.clientid', $clientid);
		$templateid = $app->getUserState('com_legalconfirm.selectedlawfirms.templateid');
		if(($templateid > 0) || ($templateid != ''))
		{
			$app->setUserState('com_legalconfirm.selectedlawfirms.templateid', null);
			$model->updateTemplateTemporary($templateid);
		}
		$this->setMessage(JText::_('COM_LEGALCONFIRM_PLEASE_MAKE_REQUEST'));
//		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&tmpl=component&layout=lawofficesconfirmation&id='.$clientid, false));
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientlawfirmadd&id='.$clientid, false));
	}
	/*
	 * function for add more lawfirm..
	 */
	public function requestProposals()
	{
		$app = JFactory::getApplication();  
		$lawfirmofficesarray = JRequest::getVar('cid');
		$clientid = JRequest::getVar('id');
		$model = $this->getModel();
		$lawfirmids = array();
		$lawfirmofficesids = array();
		//fincding the lawfirmids...
		foreach($lawfirmofficesarray as $firstarray)
		{
			//finding the lawfirmid by only the 0 index value because firmid will be same for all lawfirmoffice of a lawfirm..
			$lawfirmids[] = 	$this->getLawfirmId($firstarray[0]);
			$lawfirmofficesids[] = json_encode($firstarray);
		}
		//we are using the for loop, if we use foreach then we have to use two foreach loop for array lawfirmids & lawfirmofficesids..
		$tempdata = array();
		for($j = 0; $j <count($lawfirmids); $j++)
		{
			$tempdata[$j]['lawfirm'] = $lawfirmids[$j];
			$tempdata[$j]['lawfirmoffices'] = $lawfirmofficesids[$j];
		} 
		$app->setUserState('com_legalconfirm.selectedlawfirmoffices.data', $tempdata);
		$app->setUserState('com_legalconfirm.clientprofile.clientid', $clientid);
		$this->setMessage(JText::_('COM_LEGALCONFIRM_REQUEST_MAKING_kINDLY_MAKE_REQUEST'));
		//$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&layout=lawofficesconfirmation&id='.$clientid, false));
		//$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&layout=lawofficesconfirmation&id='.$clientid, false));
		//$this->setMessage(JText::_('COM_LEGALCONFIRM_PLEASE_MAKE_REQUEST'));
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientlawfirmadd&id='.$clientid, false));
		
	}
	
	/*
	 * function for removing a signer..
	 * @params signer id
	 *
	 */
	public function deletesigner()
	{
		$app = JFactory::getApplication();
		$signerid = JRequest::getVar('signerid');
		$clientid = JRequest::getVar('id');
		$model = $this->getModel();
		$model->removeSigner($signerid, $clientid);
		$this->setMessage(JText::_('COM_LEGALCONFIRM_SIGNER_REMOVED'));
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&id='.$clientid, false));
	}
	/*
	 * function for adding a new signer
	 * @params clientid, userid and post data
	 */
	public function addsigner()
	{
		$data = JRequest::get('post');
		$clientid = JRequest::getVar('id');
		$model = $this->getModel();
		$resultid = $model->addsigner($data);
		if($resultid)
		{
			$this->setMessage(JText::_('COM_LEGALCONFIRM_ADD_SIGNER_SUCCESS'));
		}else{
			$this->setMessage(JText::_('COM_LEGALCONFIRM_ADD_SIGNER_ERROR'));
		}
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&tmpl=component&layout=success&id='.$clientid.'&signerid= '.$resultid, false));
	}
	/*
	 * function for edting a signer..
	 * @params postdata, clientid,signerid.
	 */
	public function editsigner()
	{
		$data = JRequest::get('post');
		$clientid = JRequest::getVar('id');
		$model = $this->getModel();
		$resultid = $model->editsigner($data);
		if($resultid)
		{
			$this->setMessage(JText::_('COM_LEGALCONFIRM_ADD_SIGNER_SUCCESS'));
		}else{
			$this->setMessage(JText::_('COM_LEGALCONFIRM_ADD_SIGNER_ERROR'));
		}
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&tmpl=component&layout=success&id='.$clientid.'&signerid= '.$resultid), false);
	}
	/*
	 * function for editing the comapny profile......
	 * @params postdata, client(company) id..
	 */
	public function editcompany()
	{
		$data = JRequest::get('post');
		$clientid = JRequest::getVar('id');
		$model = $this->getModel();
		if(!($clientid == $data['cid'])) //if clause for if anybody make changes in firebug and given other clientid..
		{
			$this->setMessage(JText::_('COM_LEGALCONFIRM_EDIT_SIGNER_WRONG_TRY'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&tmpl=component&layout=editcompany&id='.$clientid, false));
			return true;
		}
		$resultid = $model->editcompany($data);
		if($resultid)
		{
			$this->setMessage(JText::_('COM_LEGALCONFIRM_EDIT_COMPANY_SUCCESS'));
		}else{
			$this->setMessage(JText::_('COM_LEGALCONFIRM_EDIT_COMPANY_ERROR'));
		}
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&tmpl=component&layout=companysuccess&id='.$clientid.'&resultid= '.$resultid, false));
	}
	/*
	 * function for checking the signer is available for current employee
	 * @params clientid
	 */
	public function checkSigner()
	{
		$clientid = JRequest::getVar('id');
		$model = $this->getModel();
		$signerid = $model->checkSigner($clientid);
		$pendinginitation = $model->checkPendingInitiation($clientid); 
		if($pendinginitation > 0) // check for the pending initiation for the same client, if exists then auditor first make to that initiation.
		{
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&tmpl=component&layout=initiationpending&id='.$clientid, false));
		}elseif($signerid > 0 && $signerid != ''){ 
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&tmpl=component&layout=clientproposal&id='.$clientid, false));
		}else{
			$this->setMessage(JText::_('COM_LEGALCONFIRM_SIGNER_ADD_FIRST'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&tmpl=component&layout=signerfailed&id='.$clientid, false));
		}
	}
	/*
	 * function for redirecting the app on client page....
	 */
	public function redirectinitiation()
	{
		$clientid = JRequest::getVar('id');
		$model = $this->getModel();
		$this->setMessage(JText::_('COM_LEGALCONFIRM_FIRST_MAKE_PENDING_INITIATION'));
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&id='.$clientid, false));
	}
	/*
	 * function for redirecting the app on client page....
	 */
	public function redirectsigner()
	{
		$clientid = JRequest::getVar('id');
		$model = $this->getModel();
		$this->setMessage(JText::_('COM_LEGALCONFIRM_SIGNER_ADD_FIRST'));
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&id='.$clientid, false));
	}
	/*
	 * function for redirecting the app on client page....
	 */
	public function redirectclient()
	{
		$clientid = JRequest::getVar('id');
		$model = $this->getModel();
		$this->setMessage(JText::_('COM_LEGALCONFIRM_PLEASE_MAKE_REQUEST'));
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientlawfirmadd&id='.$clientid, false));
	}

	/*
	 * function for getting the lawfirm ids...
	 */
	private function getLawfirmId($lawfirmoffice)
	{
		$model = $this->getModel();
		$lawfirmid = $model->getLawfirmId($lawfirmoffice);
		return $lawfirmid;
	}
	/*
	 * function for cheking the lawfirm data already selected by use for making a client request..
	 * @params
	 */
	public function checkrequestdata()
	{    
		$clientid = JRequest::getVar('id');
		$model = $this->getModel(); 
		$signerid = $this->checkSigneragain($clientid);
		$app = JFactory::getApplication();
		$templateid = $app->getUserState('com_legalconfirm.selectedlawfirms.templateid');
		$requestData = $app->getUserState('com_legalconfirm.selectedlawfirmoffices.data'); 
		$requestclientid = $app->getUserState('com_legalconfirm.clientprofile.clientid');
		if(!($requestclientid == $clientid))
		{
			$this->setMessage(JText::_('COM_LEGALCONFIRM_MAKE_FOR_REQUEST_THIS_CLIENT'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientlawfirmadd&id='.$clientid, false));
		}
		elseif($requestData[0]['lawfirm'] > 0){
			if(($templateid == '') && (!$templateid > 0)) //if clause is for checking the template is prepared or not.
			{
					$this->setMessage(JText::_('COM_LEGALCONFIRM_FIRST_PREPARE_TEMPLATE'));
					$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientlawfirmadd&id='.$clientid, false));
			}
			else{ 
				//making the proposal deactivate those are already not accepted...
				$this->makeDeactivateclientProposal($clientid);
				$proposalid =  $this->saveRequestdata($requestData, $clientid, $templateid);
				if($proposalid){
					//doing the mail process for client signer...
					$this->sendMailtoSigner($proposalid, $clientid, $templateid);
					//removing the data from session..
					$app->setUserState('com_legalconfirm.selectedlawfirmoffices.data', null);
					$app->setUserState('com_legalconfirm.selectedlawfirms.templateid', null);
					$requestData = null;
					$this->setMessage(JText::_('COM_LEGALCONFIRM_THANKS_FOR_REQUEST'));
					$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=lawfirmslog&id='.$clientid, false));
				}else{
					$this->setMessage(JText::_('COM_LEGALCONFIRM_CLIENT_REQUEST_MAKING'));
					$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&id='.$clientid, false));
				}
			}
		}
		else
		{
			$this->setMessage(JText::_('COM_LEGALCONFIRM_ADD_LAWFIRM_FIRST_FOR_REQUEST'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientlawfirmadd&id='.$clientid, false));
		}
	}
	/*
	 * function for making the client proposal deactivate
	 * @params clientid
	 */
	private function makeDeactivateclientProposal($clientid)
	{
		$model = $this->getModel();
		$result = $model->makeDeactivateclientProposal($clientid);
		return true;
	}
	/*
	 * Function for saving the requested data..
	 */
	private function saveRequestdata($requestData, $clientid, $templateid)
	{
		$model = $this->getModel();
		$resultid = $model->saveRequest($requestData, $clientid, $templateid);
		return $resultid;
	}
	/*
	 * function for checking the signer is available for current employee
	 * @params clientid
	 */
	private function checkSigneragain($clientid)
	{
		$model = $this->getModel();
		$signerid = $model->checkSigner($clientid);
		$app = JFactory::getApplication();
		if($signerid > 0 && $signerid != '')
		{
			return $signerid;
		}else{
			$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_SIGNER_ADD_FIRST'));
			$app->Redirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&id='.$clientid, false));
		}
	}
	/*
	 * function for sending the mail to clientsigner.
	 * @params 1.proposalid, 2.clientid, 3. autogenerate token
	 */
	private function sendMailtoSigner($proposalid, $clientid, $templateid)
	{
		$model = $this->getModel();
		//finding the email of signer of a client..
		$signeremail = $model->emailSigner($clientid);
		//client info..
		$clientDetail = $model->clientInfo($clientid);
		//generating token..
		$token = JApplication::getHash(JUserHelper::genRandomPassword());
		$returntoken = $model->updateProposal($proposalid, $token);
		$templateresult = $model->updateTemplateTemporary($templateid);
		$clientTemplate = $this->prepareTemplate($clientDetail, $proposalid, $clientid, $token, $templateid);
		$user = JFactory::getUser();
		$mail = &JFactory::getMailer();
		$app		= JFactory::getApplication();
		$mailfrom	= $app->getCfg('mailfrom');
		$fromname	= $app->getCfg('fromname');
		$mail->setSubject(JText::_('COM_LEGALCONFIRM_NEW_REQUEST_AUDITING'));
		$text = $clientTemplate;
		$mail->IsHTML= true;
		$mail->ContentType = 'text/html';
		$joomla_config = new JConfig();
		$mail->addRecipient($signeremail);
		$mail->setSender($mailfrom, $fromname);
		$mail->setBody($text);
		$mail->Send(); 
		return true;
	}
	/*
	 * function for preparing the client template for mail
	 * @params clientinformation, proposalid, token, clientid
	 */
	private function prepareTemplate($clientDetail, $proposalid, $clientid, $token, $templateid)
	{
		$app = JFactory::getApplication();
		$model = $this->getModel();
		$user = JFactory::getUser();
		$lawfirms = $model->getlawfirm($proposalid, $clientid);
		$accountingfirmname = $clientDetail->company;
		$auditorfirmnaame = $model->findAuditorFirmName($user->id);
		$temp  ="<p>Auditor Employee <b>$user->name</b> of Accounting Firm <b>$auditorfirmnaame</b> requests your approval to contact the following law firms: ";
		$lawfirmstring = implode(', ', $lawfirms);
		$temp .= $lawfirmstring.'.';
		$temp .="</p>";
		$temp  .= "<div style=\"border: 1px solid #000; margin-bottom: 10px; padding: 5px;\">";
		$temp .= htmlspecialchars_decode($model->getTemplate($proposalid));
		$accepturl = JURI::base().'index.php?option=com_legalconfirm&task=clientprofile.acceptrequest&proposalid='.$proposalid.'&clientid='.$clientid.'&token='.$token;
		$rejecturl = JURI::base().'index.php?option=com_legalconfirm&task=clientprofile.denyrequest&proposalid='.$proposalid.'&clientid='.$clientid.'&token='.$token;
		$temp .= '<br /><a href="'.$accepturl.'" style="background-color:#deeef5;border:1px solid #a3cfe4;text-align:center;border-radius:4px;padding:3px;font-weight:normal;color:#069;">Accept&nbsp;&nbsp;</a>';
$temp .= '&nbsp;&nbsp;&nbsp;&nbsp;';
		$temp .= '<a href="'.$rejecturl.'" style="background-color:#deeef5;border:1px solid #a3cfe4;text-align:center;border-radius:4px;padding:3px;font-weight:normal;color:#069;">Deny&nbsp;&nbsp;</a><br /><br />';
		$temp .= "</div>";
		return $temp;
	}
	/*
	 * function for accepting the request by client..
	 * @params token, clientid, proposalid
	 */
	public function acceptrequest()
	{
		$token = JRequest::getVar('token');
		$clientid = JRequest::getVar('clientid');
		$proposalid = JRequest::getVar('proposalid');
		$model = $this->getModel();
		if($token == '' || $clientid == '' || $proposalid == '')
		{
			$this->setMessage(JText::_('COM_LEGALCONFIRM_WRONG_REQUEST'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
		}
		$result = $model->acceptrequest($clientid, $proposalid, $token);
		if($result == 1)
		{
			$this->setMessage(JText::_('COM_LEGALCONFIRM_WRONG_REQUEST'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
		}
		elseif($result == 2)
		{
			$auditorid = $model->getAuditorid($proposalid);
			//call for making the entry for the email will be be sent to the lawfirms which are added at the request time, mail will be sent when initiate confirmation will be done.
			$result = $this->proposalNotify($auditorid, $clientid, $proposalid); 
			if(!$result)//if result is false, means any error occured in the internal process in above function calling..
			{
				//updating the proposal table by token and status, its just a error handling.
				$model->updateproposalstatus($clientid, $proposalid, $token);
				$this->setMessage(JText::_('COM_LEGALCONFIRM_CONFIRMATION_REQUEST_NOT_ACCEPTED_TRY_AGAIN'));
			    $this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
			    return false;
			}
			//sending the mail to the auditor client for making the initiate confirmation..			
			$this->sendMailtoAuditor($auditorid, $clientid);			
			$this->setMessage(JText::_('COM_LEGALCONFIRM_THANKS_FOR_CONFIRMATION_REQUEST'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
		}
		elseif($result == 4)
		{
			$this->setMessage(JText::_('COM_LEGALCONFIRM_EXPIRES_CONFIRMATION_REQUEST'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
		}
	}
	/*
	 * function for sending the mail to the auditor for initiate the confirmation on the site., just a reminder by mail
	 * @params auditor
	 */
	private function sendMailtoAuditor($auditorid, $clientid)
	{
		$user = JFactory::getUser($auditorid);
		$auditoremail = $user->email; 
		$mail =& JFactory::getMailer();
		$model = $this->getModel();
		$clientDetail = $model->clientInfo($clientid);
		$app		= JFactory::getApplication();
		$mailfrom	= $app->getCfg('mailfrom');
		$fromname	= $app->getCfg('fromname');
		$mail->setSubject(JText::_('COM_LEGALCONFIRM_NEW_REQUEST_INITIATE_CONFIRMATION'));
		$text  = JText::_('COM_LEGALCONFIRM_HELLO').' '.$user->name.',<br/>';
		$text .= JText::sprintf( JText::_('COM_LEGALCONFIRM_A_CLIENT_HAS_BEEN_ACEEPT_REQUEST'), $clientDetail->company );
		$text .= '<br>';
		$baseurl = JURI::base();
		$text .= '<a href="'.$baseurl.'">'.JText::_('COM_LEGALCONFIRM_LINK_START_INITIATION').'</a>';
		$text .= '<br>';
		$text .= JText::_('COM_LEGALCONFIRM_THANKS');
		$mail->setBody($text);
		$mail->IsHTML(true);
		$joomla_config = new JConfig();
		$mail->addRecipient($auditoremail);
		$mail->setSender($mailfrom, $fromname);
		$mail->Send();
		return true;
	}
	/*
	 * function for denied the request by client
	 * @params token, clientid, proposalid
	 */
	public function denyrequest()
	{
		$token = JRequest::getVar('token');
		$clientid = JRequest::getVar('clientid');
		$proposalid = JRequest::getVar('proposalid');
		$model = $this->getModel();
		if($token == '' || $clientid == '' || $proposalid == '')
		{
			$this->setMessage('COM_LEGALCONFIRM_WRONG_REQUEST');
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
		}
		$result = $model->denyrequest($clientid, $proposalid, $token);
		if($result == 1)
		{
			$this->setMessage(JText::_('COM_LEGALCONFIRM_WRONG_REQUEST'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
		}
		elseif($result == 2)
		{
			//sending the mail to the auditor client for making the initiete confirmation..
			$auditorid = $model->getAuditorid($proposalid);
			$this->sendDeniedMailtoAuditor($auditorid, $clientid);
			$this->setMessage(JText::_('COM_LEGALCONFIRM_THANKS_FOR_DENIED_REQUEST'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
		}
		elseif($result == 4)
		{
			$this->setMessage(JText::_('COM_LEGALCONFIRM_EXPIRES_CONFIRMATION_REQUEST'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
		}
	}
	/*
	 * function for sending the mail to the auditor that client signer rejected the request., just a reminder by mail
	 * @params auditor
	 */
	private function sendDeniedMailtoAuditor($auditorid, $clientid)
	{
		$user = JFactory::getUser($auditorid);
		$auditoremail = $user->email;
		$mail =& JFactory::getMailer();
		$model = $this->getModel();
		$clientDetail = $model->clientInfo($clientid);
		$app		= JFactory::getApplication();
		$mailfrom	= $app->getCfg('mailfrom');
		$fromname	= $app->getCfg('fromname');
		$mail->setSubject(JText::_('COM_LEGALCONFIRM_NEW_REQUEST_DENIED'));
		$text  = JText::_('COM_LEGALCONFIRM_HELLO').' '.$user->name.',<br/>';
		$text .= JText::sprintf( JText::_('COM_LEGALCONFIRM_A_CLIENT_HAS_BEEN_DENIED_REQUEST'), $clientDetail->company );
		$text .= '<br>';
		$text .= '<br>';
		$text .= JText::_('COM_LEGALCONFIRM_THANKS');
		$mail->setBody($text);
		$mail->IsHTML(true);
		$joomla_config = new JConfig();
		$mail->addRecipient($auditoremail);
		$mail->setSender($mailfrom, $fromname);
		$mail->Send();
		return true;
	}
	/*
	 * function for adding the notes..
	 * @params postdata
	 */
	public function addnotes()
	{
		$urlid = JRequest::getVar('id');
		$app = JFactory::getApplication();
		$postdata = JRequest::get('post');
		$model = $this->getModel();
		if(!((int)$postdata['clientidnotes'] == $urlid))
		{
			$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_WRONG_URL_TRYING'));
			$app->redirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&id='.$urlid, false));
		}else{
			$resultid = $model->addnotes($postdata);
			if($resultid)
			{
				$this->setMessage(JText::_('COM_LEGALCONFIRM_ADDED_CLIENT_SUCCEFULLY'));
				$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&id='.$urlid, false));
			}
			else{
				$this->setMessage(JText::_('COM_LEGALCONFIRM_ADDED_CLIENT_FAILED'));
				$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&id='.$urlid, false));
			}
	 }
	}
	/*
	 * function for saving the auditor notes
	 * @params postdata
	 */
	public function editauditornotes()
	{
		$postdata = JRequest::get('post');
		$clientid = JRequest::getVar('id');
		$model = $this->getModel();
		$resultid = $model->editAuditornotes($postdata);
		if($resultid)
		{
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&layout=editsuccessnotes&resultidnotes='.$resultid.'&id='.$clientid, false));
		}
		else{
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&layout=editsuccessnotes&id='.$clientid, false));
		}
	}
	/*
	 * function for making the entry for the mail to be sent in lawfirm_proposal_notify table when initiate confirmation will be done.
	 * @params clientid, proposalid
	 */
	private function proposalNotify($auditorid, $clientid, $proposalid)
	{
		$model = $this->getModel();
		$result = $model->makeNotifyEntry($proposalid, $clientid);
		return $result;
	}
	/*
	 * function for initiating the confirmation.
	 * @params clientid
	 */
	public function initiateconfirmation()
	{
		$clientid = JRequest::getVar('id');
		$model = $this->getModel();
		$result = $model->initiateconfirmation($clientid);
		if($result == 0)
		{
			$this->setMessage(JText::_('COM_LEGALCONFIRM_INITIATE_ADD_FIRST_LAWFIRM_CONFIRMATION'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=lawfirmslog&id='.$clientid, false));
		}elseif($result == 2){
			$this->setMessage(JText::_('COM_LEGALCONFIRM_INITIATE_LAWFIRM_CONFIRMATION_PAYMENT_FAIELD_TRY_AGAIN'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=lawfirmslog&id='.$clientid, false));
		}elseif($result == 3){
			$this->setMessage(JText::_('COM_LEGALCONFIRM_INITIATE_LAWFIRM_CONFIRMATION_PAYMENT_SUCCESS_BUT_MAIL_FAILED'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=lawfirmslog&id='.$clientid, false));
		}else{
			$this->setMessage(JText::_('COM_LEGALCONFIRM_INITIATE_LAWFIRM_CONFIRMATION_SUCCESS'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=lawfirmslog&id='.$clientid, false));
		}
	}
	/*
	 * function for saving the template..
	 */
	public function setTemplate()
	{
		$clientid = JRequest::getVar('id'); 
		$app = JFactory::getApplication();
		$templateid = JRequest::getVar('templateid');
		$templateContent = $_POST['tempate-input'];
		$decodetemplateContent = htmlspecialchars($templateContent);
		$model = $this->getModel();
		$resultid = $model->saveTemplate($templateContent, $clientid, $templateid);
		$app->setUserState('com_legalconfirm.selectedlawfirms.templateid', $resultid);
		if($resultid)
		{
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clienttemplate&layout=savetemplate&tmpl=component&templateid='.$resultid.'&id='.$clientid, false));
		}
	}
	/*
	 * function for removing the lawfirm from session..
	 * when a user wants to remove
	 */
	public function removelawfirm()
	{

		$removeLawfirmid = (int)JRequest::getVar('lawfirm_removed');
		$clientid = JRequest::getVar('id');
		$app = JFactory::getApplication();
		$this->ajaxrequestdata = $app->getUserState('com_legalconfirm.selectedlawfirmoffices.data');
		$this->ajaxrequestclientid = $app->getUserState('com_legalconfirm.clientprofile.clientid');
		$true = false;
		$tempdata = array();
		$i = 0;
		foreach ($this->ajaxrequestdata as $removedarray)
		{
			if($removeLawfirmid == $removedarray['lawfirm'])
			{
				unset($removedarray);
				$true = true;
			}else
			{
				$tempdata[$i]['lawfirm'] = $removedarray['lawfirm'];
				$tempdata[$i]['lawfirmoffices'] = $removedarray['lawfirmoffices'];
				$i++;
			}
		}
		$app->setUserState('com_legalconfirm.selectedlawfirmoffices.data',$tempdata);
        $this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientlawfirmadd&id='.$clientid, false));
	}
	/*
	 * function for cheking the any request is pending, and user wants to move to next screen
	 * if pending then we will keep the user on same step
	 */
	public function checkrequestpending()
	{
		$clientid = JRequest::getVar('id');
		$app = JFactory::getApplication();
		$model = $this->getModel();
		$result = $model->getrequestpendingid($clientid);
		if(($result > 0)) //if clause is for checking any request is not pending.
		 {
			$this->setMessage(JText::_('COM_LEGALCONFIRM_FIRST_COMPLETE_THEPENDING _REQUEST'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientslog&id='.$clientid, false));
		 }else{//checking to sending the request to signer..
		 	$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=lawfirmslog&id='.$clientid, false));
		 }
	}
}
