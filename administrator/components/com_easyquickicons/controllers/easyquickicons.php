<?php
/**
 * @package			Easy QuickIcons
 * @version			$Id: easyquickicons.php 95 2012-10-27 13:54:47Z allan $
 *
 * @author			Allan <allan@awynesoft.com>
 * @link			http://www.awynesoft.com
 * @copyright		Copyright (C) 2012 AwyneSoft.com All Rights Reserved
 * @license			GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');
 
/**
 * Easyquickicons Controller
 */
class EasyquickiconsControllerEasyquickicons extends JControllerAdmin
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Define standard task mappings.

		// Value = 0
		
		$this->registerTask('gopro', 'goPro');
	
	}
	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Easyquickicon', $prefix = 'EasyquickiconsModel', $config = array('ignore_request' => true)) 
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	public function goPro(){
		$this->setRedirect('http://www.awynesoft.com/index.php?option=com_content&view=articles&id=11');
	}

}