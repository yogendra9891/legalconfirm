<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Registration view class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @since		1.6
 */
class LegalconfirmusersViewRegistration extends JViewLegacy
{
	protected $data;
	protected $form;
	protected $params;
	protected $state;

	/**
	 * Method to display the view.
	 *
	 * @param	string	The template file to include
	 * @since	1.6
	 */
	public function display($tpl = null)
	{
		 $document =& JFactory::getDocument();
		  $document->addScript(JURI::base(). 'components/com_legalconfirmusers/assets/js/jquery.min.js');
		 $document->addScript(JURI::base(). 'components/com_legalconfirmusers/assets/js/validation.js');
		 $document->addStyleSheet(JURI::base(). 'components/com_legalconfirmusers/assets/css/style.css');
		 $document->addStyleSheet(JURI::base(). 'components/com_legalconfirmusers/assets/css/css.css');
		 $document->addStyleSheet('components/com_legalconfirmusers/assets/css/form.css');
		$document->addScript('components/com_legalconfirmusers/assets/js/jquery.min.js');
		// Get the view data.
		$this->data		= $this->get('Data');
		
				
		$this->form		= $this->get('Form');
		$this->state	= $this->get('State');
		$this->params	= $this->state->get('params');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Check for layout override
		$active = JFactory::getApplication()->getMenu()->getActive();
		if (isset($active->query['layout'])) {
			$this->setLayout($active->query['layout']);
		}

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

		$this->prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document.
	 *
	 * @since	1.6
	 */
	protected function prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$title 		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('COM_USERS_REGISTRATION'));
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
	 * @Author:Abhishek
	 * Method to get office list according to the firm admin.
	 * @param array $data
	 */
	
	public function getOffice($data){
		 //get firm admin id
		 //get db object
		 $db = JFactory::getDbo();
		 $firm_array = explode('@',$data);
		 $firm_name = $firm_array['1'];
		 
		 //check for admin firm 
		 //get admin id
		 $result = $this->getfirmadminid($firm_name);
		 //firm admin id
		 $firmadmin_id = $result;
		 $query = "SELECT id,office_title from #__users_office where lid = ".$db->Quote($firmadmin_id)." AND status = '1'";
		 $db->setQuery($query);
		 $db->query();
		 $result = $db->loadObjectlist();
		 return $result;
		
	}
	
	/**
	 * @Author:Abhishek
	 * Method to get firm admin id
	 * @param:firm name
	 */
	public function getfirmadminid($firm_name){
		 $db = JFactory::getDbo();
		 $query = "SELECT MIN(id) as ms FROM #__users as a where a.email like '%@".$firm_name."'";
		 $db->setQuery($query);
		 $db->query();
		 $result = $db->loadResult();
		 return $result;
	}
	
    /**
	 * @Author:Abhishek
	 * get firm name
	 */
	public function getFirmName($data){
		//get firm admin id
		 //get db object
		 $db = JFactory::getDbo();
		 $firm_array = explode('@',$data);
		 $firm_name = $firm_array['1'];
		 
		 //check for admin firm 
		 //get admin id
		 $result = $this->getfirmadminid($firm_name);
		 
		 //firm admin id
		 $firmadmin_id = $result;
		 $query = "SELECT `accounting_firm` from #__users_profile_detail where lid = ".$db->Quote($firmadmin_id);
		 $db->setQuery($query);
		 $db->query();
		 $result = $db->loadObject();
		 return $result;
	}
	
	/**
	 * @Author:Abhishek
	 * Get terms and conditions content from database
	 */
	public function getTermsContent(){
		//get db object
		$db = JFactory::getDBO();
		$query = "SELECT introtext FROM #__content where id = 1";
		$db->setQuery($query);
		$db->query();
		$data = $db->loadResult();
		return $data;
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
