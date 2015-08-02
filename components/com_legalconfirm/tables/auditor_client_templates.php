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
 * Auditor_client_templates Table class using for the saving the template
 */
class LegalconfirmTableAuditor_client_templates extends JTable {
	/**
	 * Constructor
	 *
	 * @param JDatabase A database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__auditor_client_templates', 'id', $db);
	}

}
