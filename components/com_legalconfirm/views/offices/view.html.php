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
class LegalconfirmViewOffices extends JView
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
        $app   = JFactory::getApplication();
       $document =& JFactory::getDocument();
       	$document->addScript(JURI::base(). 'components/com_legalconfirm/assets/js/jquery.min.js');
		 $document->addScript(JURI::base(). 'components/com_legalconfirm/assets/js/validation.js');
		 $document->addStyleSheet(JURI::base(). 'components/com_legalconfirm/assets/css/style.css');
		 $document->addStyleSheet(JURI::base(). 'components/com_legalconfirm/assets/css/css.css');
		 $document->addStyleSheet('components/com_legalconfirm/assets/css/form.css');
        $config = JFactory::getConfig(); 
        $user = JFactory::getuser();
        $auditor = $config->getValue('config.auditor'); 
	   if($user->id < 0 || $user->id == ''){
			$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_LOGIN_FIRST'));
			$app->redirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
		}elseif(!(@in_array($auditor, $user->groups))){
			$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_LOGIN_WRONG_TRY'));
			$app->redirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
		}
        $this->state		= $this->get('State');
        //get layout
        $layout = JRequest::getVar('layout');
        if($layout == "edit"){
         $this->items		= $this->get('data');
        }else{
        $this->items		= $this->get('Items');
        $this->pagination	= $this->get('Pagination');
       
        }
        
         $this->params       = $app->getParams('com_legalconfirm');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {;
            throw new Exception(implode("\n", $errors));
        }
        
        $this->_prepareDocument();
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
		$query->select('a.id, a.company');
		$query->from('#__auditorclients as a');
		$query->where('a.lid = '.$user->id);
		$query->order('a.previewdate DESC'); 
		$db->setQuery($query);
		$db->query();
		$result = $db->loadObject();
		return $result;
	}
	/**
	 * Method to get usa states
	 */
	public function getUsaStates(){
		$model = $this->getModel('Offices');
	    $usastates = $model->getUsaStates();
	    return $usastates;
	}
}
