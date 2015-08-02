<?php

/**
 * @version     1.0.0
 * @package     com_legalconfirm
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      yogendra <yogendra.singh@daffodilsw.com> - http://
 */
// No direct access
defined('_JEXEC') or die;

/**
 * initiation_payment_record Table class (Table for saving the data on initiating the confirmation, save the payment amount and details..)
 */
class LegalconfirmTableInitiation_payment_record extends JTable {
	/**
	 * Constructor
	 *
	 * @param JDatabase A database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__initiation_payment_record', 'id', $db);
	}

}
