<?php
/**
 * @version     1.0.0
 * @package     com_lawfirm
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Abhishek Gupta <abhishek.gupta@daffodilsw.com> - http://
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Lawfirm controller class.
 */
class LawfirmControllerLawfirm extends JControllerForm
{

    function __construct() {
        $this->view_list = 'lawfirms';
        parent::__construct();
    }

}