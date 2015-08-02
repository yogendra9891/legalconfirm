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
class LegalconfirmViewUserprofile extends JView
{
	protected $state;
    protected $item;
    protected $form;
    protected $params;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$app                = JFactory::getApplication();
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
		}
		$this->state = $this->get('State');
		$this->item	= $this->get('Data');
		
        if(JRequest::getVar('msgtype') != '')
        {
        	$app->enqueueMessage('successfully saved');
        	$app->redirect('index.php?option=com_legalconfirmusers&view=login');
        }
		
		//$this->params       = $app->getParams('com_legalconfirm');

		// Check for errors.
//		if (count($errors = $this->get('Errors'))) {;
//		throw new Exception(implode("\n", $errors));
//		}

		//$this->_prepareDocument();
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
	
/**
	 * Method to get usa states
	 */
	public function getUsaStates(){
		$model = $this->getModel('userprofile');
	    $usastates = $model->getUsaStates();
	    return $usastates;
	}
}
