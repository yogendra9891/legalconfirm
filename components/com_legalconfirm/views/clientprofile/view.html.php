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
require_once JPATH_COMPONENT.'/helpers/legalconfirm.php';
/**
 * View class for a deshboard of client Profile.
 */
class LegalconfirmViewClientprofile extends JViewLegacy
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
        $doc->addScript(JURI::base(). 'components/com_legalconfirm/assets/js/jquery.min.js');
		$doc->addScript(JURI::base(). 'components/com_legalconfirm/assets/js/validation.js');
		$doc->addStyleSheet(JURI::base(). 'templates/legalconfirm/css/style.css');
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
        //updating the company review date...
        $this->updateReviewclient($clientid);
        $auditoruserid = $this->checkauditorid($clientid); 
        if(!($auditoruserid == $user->id)){ 
			$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_WRONG_AUDITOR_EDITING'));
			$app->redirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
        } 
        //geting client profile..
		$this->client		= $this->get('Profile'); 
		//getting the signer related to the cliented..
		$this->signer = $this->get('Signer');  
		if(JRequest::getVar('layout') == 'clientproposal')
		{
			$this->items		= $this->get('Items'); //finding the law firms..
			$this->pagination	= $this->get('Pagination');
			$this->state		= $this->get('State');
			$this->alreadySelectedLawfirm = $this->get('LawfirmsGetting');
		}
		//finding the offices of selected law firms.....
		if(JRequest::getVar('layout') == 'lawoffices')
		{  
		    $this->lawfirms = $app->getUserState('com_legalconfirm.lawfirms.data'); 
		}
		//editing a signer.....
		if(JRequest::getVar('layout') == 'editsigner')
		{
			$signerid = JRequest::getVar('signerid');
			if(!((int)$signerid == $this->signer->id))
			{
    			$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_WRONG_AUDITOR_SIGNER_EDITING'), 'notice');
    			$app->redirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
 			}
		}
		//checking for the signer adding(success/unsuccess)..
		if(JRequest::getVar('message') != '')
		{
			if(JRequest::getVar('message') == 'success'){
				$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_ADD_SIGNER_SUCCESS'), 'message');
			}elseif(JRequest::getVar('message') == 'unsuccess'){
				$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_ADD_SIGNER_ERROR'), 'message');
			}
			$app->redirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&id='.$clientid, false));
		}
		//checking for the company profile editing(success/unsuccess)..
		if(JRequest::getVar('companyeditmessage') != '')
		{
			if(JRequest::getVar('companyeditmessage') == 'success'){
				$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_EDIT_COMPANY_SUCCESS'), 'message');
			}elseif(JRequest::getVar('companyeditmessage') == 'unsuccess'){
				$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_EDIT_COMPANY_ERROR'), 'message');
			}
			$app->redirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&id='.$clientid, false));
		}
		//checking for the company profile editing(success/unsuccess)..
		if(JRequest::getVar('noteseditmessage') != '')
		{
			if(JRequest::getVar('noteseditmessage') == 'success'){
				$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_EDIT_NOTES_SUCCESS'), 'message');
			}elseif(JRequest::getVar('noteseditmessage') == 'unsuccess'){
				$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_EDIT_NOTES_ERROR'), 'message');
			}
			$app->redirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&id='.$clientid, false));
		}

		//checking for the template for a signer by editor editing(success/unsuccess)..
		if(JRequest::getVar('mailmessage') != '')
		{
			if(JRequest::getVar('mailmessage') == 'success'){
				$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_MAILTO_NON_MEMBER_SUCCESS'), 'message');
			}
			$app->redirect(JRoute::_('index.php?option=com_legalconfirm&view=clientprofile&id='.$clientid, false));
		}
		
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
	/*
	 * function for finding the lawfirm detail..
	 * @params lawfirmid
	 */
	public function findlawfirmdetail($newlawfirm)
	{
		$db = JFactory::getDbo(); 
		$query = $db->getQuery(true);
		$query->select( 'a.*, a.id as lawfirmid');
		$query->select(' b.*');
        $query->from('`#__users` AS a');
		$query->join('INNER', $db->quoteName('#__users_profile_detail').' AS b ON a.id = b.lid');
		$query->where('a.id = '.$newlawfirm);
		$db->setQuery($query);
		$db->query(); //echo $query; exit;
		$resultdata = $db->loadObject(); 
		return $resultdata;
	}
	/*
	 * function for updating the viewed time for a client
	 * @params clientid
	 */
	private function updateReviewclient($clientid)
	{
		$db = JFactory::getDbo(); 
		$query = $db->getQuery(true);
		$config =& JFactory::getConfig();
        $tzoffset = $config->getValue('config.offset');        
        $assign_date =& JFactory::getDate('',$tzoffset);
        $updatedate = $assign_date->toMySQL(true);
		$query = 'UPDATE #__auditorclients set previewdate = "'. $updatedate.'" where id='.$clientid;
		$db->setQuery($query); 
		$db->query();
		return true;
	}
	    /**
	 * Method to get usa states
	 */
	public function getUsaStates(){
		$db = JFactory::getDBO();
        $query = "SELECT * FROM #__legalconfirm_usastates";
        $db->setQuery($query);
        $db->query();
        $result = $db->loadObjectlist();
       
        return $result;
	}
}
