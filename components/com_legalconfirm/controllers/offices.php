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
 * Auditors controller class.
 */
class LegalconfirmControllerOffices extends LegalconfirmController
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Offices', $prefix = 'LegalconfirmModel')
	{
	
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	/**
	 * Method to change status of user
	 * @Author Abhishek
	 * @param userid
	 */
	public function edit(){
		$document	= JFactory::getDocument();
		$data = JRequest::getVar('id');
		//get model
		$model = $this->getModel();
		//call function of model
		$result = $model->editoffice($data);
		$app = JFactory::getApplication();
		$app->setUserState('', $result);
		}
		
		/**
		 * Method to edit offices
		 */
		public function addoffice(){
			$data = JRequest::get('post');
		
			if($data['id'] == ""){
			
		    $this->setMessage(JText::sprintf('COM_LEGALFIRM_OFFICES_ADD_FAILED','warning'));
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=offices', false));
			return false;
			}
			//get model
		    $model = $this->getModel();
		    //call function of model
		    $result = $model->addoffice($data);
		    if(!$result){
		    $this->setMessage(JText::sprintf('COM_LEGALFIRM_OFFICES_ADD_FAILED', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=offices&layout=edit&task=edit&id='.$data['id'], false));
			return false;
		    }else{
		    	$this->setMessage(JText::sprintf('COM_LEGALFIRM_OFFICES_ADD_SUCCESS', $model->getError()), 'success');
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=offices&layout=edit&task=edit&id='.$data['id'], false));
			return true;
		    }
		}
		
		/**
		 * Method to add new office
		 */
		public function addnewoffice(){
			$data = JRequest::get('post');
			//get model
		    $model = $this->getModel();
		    //call function of model
		    $result = $model->addnewoffice($data);
		   if(!$result){
		    $this->setMessage(JText::sprintf('COM_LEGALFIRM_OFFICES_ADD_FAILED', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=offices&layout=addoffice', false));
			return false;
		    }else{
		    	$this->setMessage(JText::sprintf('COM_LEGALFIRM_NEWOFFICES_ADD_SUCCESS', $model->getError()), 'success');
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=offices', false));
			return true;
		    }
		}
		
		
		/**
		 * Method to activate the office
		 * @param office id
		 */
		public function unBlockOffice(){
			
			$office_id = JRequest::getVar('id');
			//get model
		    $model = $this->getModel();
		    //call function of model
		    $result = $model->unBlockOffice($office_id);
		    if($result){
		    $this->setMessage(JText::sprintf('COM_LEGALFIRM_ACTIVATED_SUCCESS', $model->getError()), 'success');
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=offices', false));
		    }else{
		    $this->setMessage(JText::sprintf('COM_LEGALFIRM_ACTIVATED_FAILED', $model->getError()), 'success');
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=offices', false));
		    }
			
		}
}
