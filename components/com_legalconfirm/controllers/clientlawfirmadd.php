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
 * clientLawfirmadd controller class.
 */
class LegalconfirmControllerClientlawfirmadd extends LegalconfirmController
{

	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Clientlawfirmadd', $prefix = 'LegalconfirmModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

}
