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
 * Lawfirms list controller class.
 */
class LegalconfirmControllerLawfirms extends LegalconfirmController
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Lawfirms', $prefix = 'LegalconfirmModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	/*
	 * function for finding the lawfirm offices list for a law firm
	 * @params  lawfirmid, clientid..
	 */
	public function lawfirmoffices()
	{
		$app = JFactory::getApplication();
		$lawfirmidarray = JRequest::getVar('cid');
		$clientid = JRequest::getVar('id');
		$lawfirmid = $lawfirmidarray[0];
		$model = $this->getModel();
		$lawFirmOffies = $model->getOffices($lawfirmid);
		$app->setUserState('com_legalconfirm.lawfirmoffices.data', $lawFirmOffies);
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=lawfirms&tmpl=component&layout=lawoffices&id='.$clientid, false));

	}

}