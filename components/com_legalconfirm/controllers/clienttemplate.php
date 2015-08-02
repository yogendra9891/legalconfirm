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
class LegalconfirmControllerClienttemplate extends LegalconfirmController
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
	 * function for checking the signer is available for current employee
	 * @params clientid
	 */
	public function checkSigner()
	{
		$clientid = JRequest::getVar('id'); 
		$model = $this->getModel();
		$signerid = $model->checkSigner($clientid); 
		if($signerid > 0 && $signerid != '')
		{
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clienttemplate&tmpl=component&id='.$clientid, false));
		}else{ 
			$this->setMessage(JText::_('COM_LEGALCONFIRM_EDIT_COMPANY_ERROR'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&tmpl=component&layout=signerfailed&id='.$clientid, false));
		}
	}
	/*
	 * function for checking the template id is exists in session and user wants to move to next step.
	 * if not then message will show for preparing the template
	 */
	public function checktemplate()
	{
		$clientid = JRequest::getVar('id');
		$app = JFactory::getApplication();
		$templateid = $app->getUserState('com_legalconfirm.selectedlawfirms.templateid');
		if(($templateid == '') && (!$templateid > 0)) //if clause is for checking the template is prepared or not.
		 {
			$this->setMessage(JText::_('COM_LEGALCONFIRM_FIRST_PREPARE_TEMPLATE'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientlawfirmadd&id='.$clientid, false));
		 }else{
		 	$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientslog&id='.$clientid, false));
		 }
	}
	
}