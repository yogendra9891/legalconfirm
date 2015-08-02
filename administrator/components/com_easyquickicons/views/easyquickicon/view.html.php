<?php
/**
 * @package			Easy QuickIcons
 * @version			$Id: view.html.php 90 2012-10-27 13:31:12Z allan $
 * @author			Allan <allan@awynesoft.com>
 * @link			http://www.awynesoft.com
 * @copyright		Copyright (C) 2012 AwyneSoft.com All Rights Reserved
 * @license			GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );
/**
 * @subpackage Components
 */
class EasyquickiconsViewEasyquickicon extends JView
{
	protected $item;
	protected $form;
	protected $state;
	
	function display($tpl = null)
	{
		// Assign the model data to the view
		$this->form 	= $this->get('Form');
		$this->item 	= $this->get('Item');
		$this->state	= $this->get('State');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		//set the document
		$this->setDocument();
		//load the toolbar
		$this->addToolBar();
		
		parent::display($tpl);
	}
	protected function addToolBar(){
		
		$input = JFactory::getApplication()->input;
		$input->set('hidemainmenu', true);
		
		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$canDo		= EasyquickiconsHelper::getActions();
		
		JToolBarHelper::title($isNew ? JText::_('COM_EASYQUICKICONS_TOOLBAR_NEW')
		                             : JText::_('COM_EASYQUICKICONS_TOOLBAR_EDIT') , 'easyquickicons');
		// If not checked out, can save the item.
		if ($canDo->get('core.edit')) {
			JToolBarHelper::apply('easyquickicon.apply');
			JToolBarHelper::save('easyquickicon.save');
		}
		if ($canDo->get('core.edit') && $canDo->get('core.create')) {
			JToolBarHelper::save2new('easyquickicon.save2new');
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::save2copy('easyquickicon.save2copy');
		}                             
		JToolBarHelper::cancel('easyquickicon.cancel', $isNew ? 'JTOOLBAR_CANCEL'
		                                                   : 'JTOOLBAR_CLOSE');
		
		
	}
	protected function setDocument(){
		
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_( 'COM_EASYQUICKICONS_DOCUMENT_TITLE' ));
	}
}