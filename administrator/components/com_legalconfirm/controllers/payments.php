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
/**
 * Payments Detail controller class for admin of transaction.
 */
class LegalconfirmControllerPayments extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'payments', $prefix = 'LegalconfirmModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}  
    /*
     * public function for saving the payment detail data
     */
	public function save()
	{
		$postdata = JRequest::get('post');
		$model = $this->getModel();
		$result = $model->savePaymentDetail($postdata);
		$this->setMessage(JText::_('COM_LEGALCONFIRM_PAYMENT_DETAIL_EDIT_SUCCESS'));
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm&view=payments',false));
	}
	/*
	 * function for calcel
	 */
	public function cancel()
	{
		$this->setRedirect(JRoute::_('index.php?option=com_legalconfirm',false));
	}
}