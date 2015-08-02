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
 * View class for a list of Legalconfirm.
 */
class LegalconfirmViewLawfirms extends JView
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
		$this->client		= $this->get('Profile'); 
		//getting the signer related to the cliented..
		$this->signer = $this->get('Signer');  
		//finding the offices of a selected law firms.....
		if(JRequest::getVar('layout') == 'lawoffices')
		{  
		   $this->offices = $app->getUserState('com_legalconfirm.lawfirmoffices.data');
		}
		
        $this->state		= $this->get('State');
        $this->items		= $this->get('Items');
        $this->pagination	= $this->get('Pagination');
        $this->params       = $app->getParams('com_legalconfirm');
       
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
	
}
