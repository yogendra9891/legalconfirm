<?php
/**
 * @version     1.0.0
 * @package     com_legalconfirm
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Abhishek Gupta <abhishek.gupta@daffodilsw.com> - http://
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a selected office and lawfirms for a request.........
 */
class LegalconfirmViewClienttemplate extends JView
{
	protected $items;
	protected $pagination;
	protected $state;
    protected $params;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$app  = JFactory::getApplication(); 
		$user = JFactory::getUser(); 
		$config = JFactory::getConfig(); 
		$doc = JFactory::getDocument();
        $doc->addScript('components/com_legalconfirm/assets/js/jquery.min.js', $type = "text/javascript");
		$doc->addScript(JURI::base(). 'components/com_legalconfirm/assets/js/validation.js');
		$doc->addStyleSheet(JURI::base(). 'templates/legalconfirm/css/style.css');
		$doc->addStyleSheet(JURI::base(). 'components/com_legalconfirm/assets/css/css.css');
		$doc->addStyleSheet('components/com_legalconfirm/assets/css/form.css');
		$auditor_emp = $config->getValue('config.auditor_emp'); 
		if($user->id < 0 || $user->id == ''){
			$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_LOGIN_FIRST'));
			$app->redirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
		}elseif(!(@in_array($auditor_emp, $user->groups))){ 
			$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_LOGIN_WRONG_TRY'));
			$app->redirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
		}else{}
        // CHECKING IF A USER TRYING TO MAKE CHANGE IN ID IN URL and that client not belongs to that auditor..
        $clientid = JRequest::getVar('id'); 
        $auditoruserid = $this->checkauditorid($clientid); 
        if(!($auditoruserid == $user->id)){ 
			$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_WRONG_AUDITOR_EDITING'));
			$app->redirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
        } 
        //geting client profile..
		$this->client	= $this->get('Profile');  
		//getting the signer related to the cliented.. 
		$this->signer = $this->get('Signer');  
		//finding the offices of a selected law firms.....
	
       parent::display($tpl);
	}
    /*
     * function for checking the auditor id according to the client id..
     * this client is of this logged in user(auditor)
     */	
	private function checkauditorid($clientid)
	{
		$db = JFactory::getDbo();
		$user = JFactory::getUser(); 
		$query = $db->getQuery(true);
		$query->select('a.lid');
		$query->from('#__auditorclients as a');
		$query->where('a.id = '.$clientid);
		$db->setQuery($query);
		$db->query(); 
		$result = $db->loadResult(); 
		return $result;
	}
	/*
	 * function for finding the lawfirmname..
	 */
	public function getLawfirmName($lawfirmid)
	{
		$db = JFactory::getDbo();
		$user = JFactory::getUser(); 
		$query = $db->getQuery(true);
		$query->select('a.accounting_firm');
		$query->from('#__users_profile_detail as a');
		$query->join('INNER','#__users as u ON a.lid = u.id');
		$query->where('a.lid = '.$lawfirmid);
		$db->setQuery($query);
		$db->query(); 
		$resultName = $db->loadResult(); 
		return $resultName;
	}
	/*
	 * function for finding the offices location address
	 */
	public function getOfficeLocation($officesids)
	{
		$db = JFactory::getDbo();
		$user = JFactory::getUser(); 
		$query = $db->getQuery(true);
		$query->select('CONCAT_WS(","'.",".' a.office_title, a.address, a.city, a.state, a.country)');
		$query->from('#__users_office as a');
		$query->where('a.id = '.$officesids);
		$db->setQuery($query);
		$db->query();  
		$resultName = $db->loadResult(); 
		return $resultName;
	}
	/*
	 * function for finding the template if saved by the auditor
	 * @params sessionid, clientid
	 */
	public function findTemplate($session_id, $clientid)
	{
		$db = JFactory::getDbo();
		$user = JFactory::getUser(); 
		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->from('#__auditor_client_templates as a');
		$query->where('a.session_id = "'.$session_id.'"');
		$query->where('a.cid ='.$clientid);
		$query->where('a.is_sent ='.'"0"');
		$db->setQuery($query);
		$db->query();  
		$resultTemplate = $db->loadObject(); 
		return $resultTemplate;
	}
	
	/*
	 * function for finding the auditing firm name..
	 */
	public function findAuditorFirmName($userid)
	{
		$db = JFactory::getDbo();
		$user = JFactory::getUser(); 
		$query = $db->getQuery(true);
		$query->select('a.accounting_firm');
		$query->from('#__users_profile_detail as a');
		$query->where('a.lid = '.$userid);
		$db->setQuery($query);
		$db->query(); 
		$result = $db->loadResult(); 
		return $result;
	}

}
