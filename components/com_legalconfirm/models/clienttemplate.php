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
require_once JPATH_COMPONENT.'/tables/clientsigner.php';
require_once JPATH_COMPONENT.'/tables/auditorclients.php';
/**
 * Methods supporting Legalconfirm client Profile.
 */
class LegalconfirmModelClienttemplate extends JModelList {

	/**
	 * Constructor.
	 *
	 * @param    array    An optional associative array of configuration settings.
	 * @see        JController
	 * @since    1.6
	 */
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
             );
        }
		parent::__construct($config);
	}
		
   /*
    * function for getting the client profile..
    */
	public function getProfile()
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$clientid = JRequest::getVar('id');
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.*, a.id as clientid');
		$query->from('#__auditorclients as a');
		$query->where('a.id = '.$clientid);
		$query->where('a.lid = '.$user->id);
		$db->setQuery($query);
		$db->query();
		$resultProfile = $db->loadObject(); 
		return $resultProfile;
	}
	/*
	 * function for getting the signer of the current user and requested client..
	 * @params clientid , userid
	 */
	public function getSigner()
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$clientid = JRequest::getVar('id');
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('c.*');
		$query->from('#__clientsigner as c');
		$query->where('c.cid = '.$clientid);
		$query->where('c.lid = '.$user->id);
		$db->setQuery($query);
		$db->query();
		$resultSigner = $db->loadObject(); 
		return $resultSigner;
	}
	
}
