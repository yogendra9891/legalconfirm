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
 * View class for a clients log.
 */
class LegalconfirmViewClientslog extends JView
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
		$user = JFactory::getuser();
		$config = JFactory::getConfig(); 
		$doc = JFactory::getDocument();
        $doc->addScript(JURI::base(). 'components/com_legalconfirm/assets/js/jquery.min.js');
        $doc->addScript(JURI::base(). 'components/com_legalconfirm/assets/js/validation.js');
		$auditor_emp = $config->getValue('config.auditor_emp'); 
		if($user->id < 0 || $user->id == ''){
			$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_LOGIN_FIRST'));
			$app->redirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
		}elseif(!(@in_array($auditor_emp, $user->groups))){
			$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_LOGIN_WRONG_TRY'));
			$app->redirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
		}else{}

		$doc->addStyleSheet('components/com_legalconfirm/assets/css/form.css');
		$doc->addStyleSheet(JURI::base(). 'templates/legalconfirm/css/style.css');
        //geting items, pagination etc..
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State'); 

		parent::display($tpl);
	}


	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$title	= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('com_legalconfirm_DEFAULT_PAGE_TITLE'));
		}
		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}    
   /*
    * function for finding the lawfirms of a proposal
    * @params proposalid and clientid
    */ 	
	public function findLawfirms($pid, $clientid)
	{
		// Create a new query object.
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$query = $db->getQuery(true);	
		$query->select('a.*');
		$query->from('#__lawfirm_assignproposal as a');
		$query->where('a.pid = '.$pid);
		$query->where('a.cid = '.$clientid);
		$db->setQuery($query);
		$db->query();
		$resultlawfirms = $db->loadObjectList();
		return $resultlawfirms;	
	}
	/*
	 * function for finding the lawfirmname from user_profile_detail
	 * @params lawfirmid
	 */
	public function lawfirmname($lawfirmid)
	{
		// Create a new query object.
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$query = $db->getQuery(true);	
		$query->select('a.accounting_firm');
		$query->from('#__users_profile_detail as a');
		$query->where('a.lid = '.$lawfirmid);
		$db->setQuery($query);
		$db->query();
		$lawfirmname = $db->loadResult();
		return $lawfirmname;	
	}
	/*
	 * finding the lawfirm employee name to whom the task is assigned by the lawfirm partner or himself
	 * @params employee id
	 */
	public function employeename($empid)
	{
		// Create a new query object.
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$query = $db->getQuery(true);	
		$query->select('a.emp_title');
		$query->from('#__users_profile_detail as a');
		$query->where('a.lid = '.$empid);
		$db->setQuery($query);
		$db->query();
		$empname = $db->loadResult();
		return $empname;	
	}
	/*
	 * function for finding the template type and its response.
	 * @params ais(assign_proposal id)
	 */
	public function findtemplateresponse($asid)
	{
		// Create a new query object.
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$query = $db->getQuery(true);	
		$query->select('a.*');
		$query->from('#__lawfirm_employee_mailtemplate as a');
		$query->where('a.aid = '.$asid);
		$db->setQuery($query);
		$db->query();
		$templateresult = $db->loadObject();
		return $templateresult;	
	}
		/*
	 * function for getting the client profile..
	 */
	public function getProfile($clientid)
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
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
}
