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
 * View class for a deshboard of auditors clients.
 */
class LegalconfirmViewAuditors extends JView
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
		$app = JFactory::getApplication();
		$user = JFactory::getuser();
		$config = JFactory::getConfig();
		$doc = JFactory::getDocument();
        $doc->addScript(JURI::base(). 'components/com_legalconfirm/assets/js/jquery.min.js');
		//$app = JFactory::getApplication();
		$auditor_emp = $config->getValue('config.auditor_emp');
		if($user->id < 0 || $user->id == ''){
			$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_LOGIN_FIRST'));
			$app->redirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
		}elseif(!(@in_array($auditor_emp, $user->groups))){
			$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_LOGIN_WRONG_TRY'));
			$app->redirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
		}else{}
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		//checking for the client adding(success/unsuccess) from client view..
		if(JRequest::getVar('message') != '')
		{
			if(JRequest::getVar('message') == 'success'){
				$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_ADD_CLIENT_SUCCESS'), 'message');
			}elseif(JRequest::getVar('message') == 'unsuccess'){
				$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_ADD_CLIENT_ERROR'), 'message');
			}
			$app->redirect(JRoute::_('index.php?option=com_legalconfirm&view=auditors', false));
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
	 * function for finding the latest viewed client by current user means Auditor
	 *
	 */
	public function rececentViewedClient() {

		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$query = $db->getQuery(true);
		// Select the required fields from the table.
		$query->select('a.*, a.id as clientid');
        $query->select(' b.*');
        $query->from('`#__auditorclients` AS a');
        $query->join('LEFT', $db->quoteName('#__clientsigner').' AS b ON a.id = b.cid');
		$query->where('a.lid = '.$user->id);
		$query->order('a.previewdate DESC LIMIT 3');
		$db->setQuery($query);
		$db->query(); 
		$result = $db->loadObjectList();
		return $result;
	}
}
