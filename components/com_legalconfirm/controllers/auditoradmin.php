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
class LegalconfirmControllerAuditoradmin extends LegalconfirmController
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Auditoradmin', $prefix = 'LegalconfirmModel')
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
		$model = $this->getModel('Auditoradmin', 'LegalconfirmModel');
		$result = $model->activate($userId);
		if($result){
		$this->setMessage(JText::_('User status changed successfully'));
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=auditoradmin', false));
		}
		}

    /**
     * Method to activate the office
     *
     */
    public function activateoffice(){
     //get model object
     $model = $this->getModel('Auditoradmin', 'LegalconfirmModel');
     $result = $model->activateOffice();
     $app = JFactory::getApplication();
     //
     //check for response
     if($result == "empty_token"){
       $app->enqueueMessage(JText::_('EMPTY_TOKEN'), 'error');
       $app->redirect('index.php', "");
     }elseif($result == "invalid_token"){
       $app->enqueueMessage(JText::_('INVALID_TOKEN'), 'error');
       $app->redirect('index.php', "");
     }elseif($result == "success"){
       $app->enqueueMessage(JText::_('ACTIVATED_OFFICE'), 'success');
       $app->redirect('index.php', "");
     }else{
       $app->enqueueMessage(JText::_('INVALID_TOKEN'), 'error');
       $app->redirect('index.php', "");
    }

    }
}
