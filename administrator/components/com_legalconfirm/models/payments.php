<?php
/**
 * @version     1.0.0
 * @package     com_legalconfirm
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Abhishek Gupta <abhishek.gupta@daffodilsw.com> - http://
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
require_once JPATH_COMPONENT.'/tables/admin_payment_detail.php';
/**
 * Payments Detail controller class for admin of transaction.
 */
class LegalconfirmModelPayments extends JModelList
{

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array();
        }
       parent::__construct($config);
    }
	/*
	 * function for getting the payment detail
	 */
    public function getItem()
    {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->from('#__admin_payment_detail as a');
		$db->setQuery($query);
		$db->query();
		$paymentdata = $db->loadObject();
		return $paymentdata;
    	
    }
	/*
	 * function for saving the data
	 * @params postdata
	 */
	public function savePaymentDetail($postdata)
	{
		$paymentdetail = &JTable::getInstance('Admin_payment_detail', 'LegalconfirmTable');
		$paymentdetail->load($postdata['id']);
		if (!$paymentdetail->save($postdata)) {
			$this->setError(JText::sprintf('COM_LEGALCONFIRM_PAYMENT_DETAIL_EDIT_ERROR', $paymentdetail->getError()));
			return false;
		}
		return true;
	}
	
}