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
 * View class for a list of Legalconfirm Initiation Transactions.
 */
class LegalconfirmViewReports extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}
        
		$this->addToolbar();
        
        $input = JFactory::getApplication()->input;
        $view = $input->getCmd('view', '');
        LegalconfirmHelper::addSubmenu($view);
        
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.'/helpers/legalconfirm.php';

		$state	= $this->get('State');
		$input = JFactory::getApplication()->input;
        $view = $input->getCmd('view', '');
        if($view == 'lawfirms')
    	JToolBarHelper::title(JText::_('COM_LEGALCONFIRM_TITLE_LAWFIRMS'), 'lawfirms.png');
    	else
    	JToolBarHelper::title(JText::_('COM_LEGALCONFIRM_TITTLE_REPORTS'), 'reports.png');
    	//adding a custom button for reports
        JToolBarHelper::customX('reports.generatereports', 'save.png', '',
                                                       'JTOOLBAR_REPORTS', true);
	}
	/*
	 * function for finding the auditor name
	 * @params auditorid
	 */
	public function auditorname($lid)
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$clientid = JRequest::getVar('id');
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.name');
		$query->from('#__users as a');
		$query->where('a.id = '.$lid);
		$db->setQuery($query);
		$db->query();
		$profilename = $db->loadResult();
		return $profilename;
	}
	/*
	 * function for finding the client name
	 * @params clientid
	 */
	public function clientname($cid)
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$clientid = JRequest::getVar('id');
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.company');
		$query->from('#__auditorclients as a');
		$query->where('a.id = '.$cid);
		$db->setQuery($query);
		$db->query();
		$clientname = $db->loadResult();
		return $clientname;
	}
	
}
