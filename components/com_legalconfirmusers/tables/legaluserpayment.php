<?php

/**
 * @version     1.0.0
 * @package     com_legalconfirmusers
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Abhishek Gupta <abhishek.gupta@daffodilsw.com> - http://
 */
// No direct access
defined('_JEXEC') or die;

/**
 * legaluser Table class
 */
class LegaluserpaymentTablelegaluser extends JTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__users_payment_detail', 'id', $db);
    }

   
    

}
