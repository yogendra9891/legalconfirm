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
class LegalconfirmControllerUserprofile extends LegalconfirmController
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Userprofile', $prefix = 'LegalconfirmModel')
	{
	
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	/**
	 * Method to get user detail
	 * @Author Abhishek
	 * @param userid
	 */
	public function getuserdetail(){
		$model = $this->getModel();
		$model->getuserdetail();
	}
	
	/**
	 * Method to update the profile of user
	 */
	public function save(){
		$data = JRequest::get('POST');
		//get model object
		$model = $this->getModel();
		$result = $model->save($data);
		if(!$result){
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=userprofile', false));
		}
			$this->setMessage(JText::sprintf('COM_LEGALUSERS_PROFILE_UPDATED', $model->getError()), 'success');
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=userprofile', false));
	}
	/**
	 * Method to add new office
	 */
	public function addoffice(){
		$data = JRequest::get('POST');
		
		//get model object
		$model = $this->getModel();
	   $result = $model->addoffice($data);
	  $message = '';
		if(!$result){
			$message = 'unsuccess';
			$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=userprofile', false));
		}else{
			$message = 'success';
		}
		//close model window
		
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=userprofile&layout=success&msgtype='.$message, false));
	}
	
/**
	 * Method to check admin email
	 * @param new user email
	 * @return boolean
	 */
	public function checkAdminEmail(){
		$data = JRequest::getVar('personal');
		
		//get model object
		//get model object
		$model = $this->getModel();
	    $result = $model->checkAdminEmail($data);
	    //check for return
	    if($result == "invalid_email"){
	    $this->setMessage(JText::sprintf('COM_LEGALCONFIRM_INVALID_EMAIL', $model->getError()), 'error');
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=userprofile&layout=changeadmin&tmpl=component', false));
        return false;
	    }elseif($result == "email_exist"){
	    	$this->setMessage(JText::sprintf('COM_LEGALCONFIRM_EMAIL_EXIST', $model->getError()), 'error');
		    $this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=userprofile&layout=changeadmin&tmpl=component', false));
        }elseif($result == "sent"){
	    	$this->setMessage(JText::sprintf('COM_LEGALCONFIRM_MAIL_SENT_ADMIN', $model->getError()), 'success');
		    $this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=userprofile&layout=changeadmin&tmpl=component', false));
        }
        else{
        	  $this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=userprofile&layout=changeadmin&tmpl=component', false));
        }
	}
	
		
}
