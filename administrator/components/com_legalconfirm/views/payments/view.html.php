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
 * View class for a save the payments detail for transactions.
 */
class LegalconfirmViewPayments extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
//		$this->state		= $this->get('State');
		$this->item		= $this->get('Item');
//		$this->pagination	= $this->get('Pagination');

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
    	elseif($view == 'reports') //for reports view.
    	JToolBarHelper::title(JText::_('COM_LEGALCONFIRM_TITTLE_REPORTS'), 'reports.png');
    	else
    	JToolBarHelper::title(JText::_('COM_LEGALCONFIRM_PAYMENTS'), 'payments.png');
    	//adding a custom button for reports
        JToolBarHelper::apply('payments.save', 'JTOOLBAR_APPLY');
        JToolBarHelper::cancel('payments.cancel', 'JTOOLBAR_CLOSE');
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
}
