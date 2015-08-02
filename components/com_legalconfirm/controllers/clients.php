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
 * Auditors clients controller class.
 */
class LegalconfirmControllerClients extends LegalconfirmController
{
	
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Clients', $prefix = 'LegalconfirmModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
    /*
     * function for adding a new client..
     * @params post data
     */
	public function add()
	{
		JSession::checkToken('post') or jexit(JText::_('JInvalid_Token'));
		$app = JFactory::getApplication(); 
		$postdata = JRequest::get('post'); 
		$model	= $this->getModel('Clients', 'LegalconfirmModel');
		$resultid = $model->addclient($postdata);
		if($resultid){
			$this->setMessage(JText::_('COM_LEGALCONFIRM_ADD_CLIENT_SUCCESS'), 'message');
			}
		else{
			$this->setMessage(JText::_('COM_LEGALCONFIRM_ADD_CLIENT_ERROR'), 'error');
		}
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clients&layout=success&tmpl=component&resultid='.$resultid));
	
	}
	/*
	 * function for deleting the clients..
	 * @params seleted clients ids..
	 * 
	 */
	public function deleteclient()
	{
		JSession::checkToken('post') or jexit(JText::_('JInvalid_Token'));
		$app = JFactory::getApplication(); 
		$postdata = JRequest::getVar('cid'); 
		$model	= $this->getModel('Clients', 'LegalconfirmModel');
		$resultid = $model->deleteclient($postdata);
		$this->setMessage(JText::_('COM_LEGALCONFIRM_DELETE_CLIENT_SUCCESS'), 'message');
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=auditors', false));
	}
	/*
	 * function for handle the non-member that is the law firm partner
	 */
	public function nonmembersendmail()
	{
		$postdata = JRequest::get('post');
		$clientid = JRequest::getVar('id');
		$model = $this->getModel();
		$result = $model->sendEmailNonmember($postdata);
		$this->setMessage(JText::_('COM_LEGALCONFIRM_MAILTO_NON_MEMBER_SUCCESS'), 'message');
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=clientlawfirmadd&id='.$clientid, false));
	}
}