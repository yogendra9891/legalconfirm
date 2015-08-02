<?php
/**
 * @package			Easy QuickIcons
 * @version			$Id: view.html.php 86 2012-10-27 13:28:19Z allan $
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
class EasyquickiconsViewEasyquickicons extends JView
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $manifest;
	
		/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   1.6
	 */
	function display($tpl = null)
	{
		//get the Model data
		$items 		= $this->get('Items');
		$pagination = $this->get('Pagination');
		$state 		= $this->get('State');
		$manifest 	= $this->get('VersionInfo');
	
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign data to the view
	
		$this->items 		= $items;
		$this->pagination 	= $pagination;
		$this->state 		= $state;
		$this->manifest		= $manifest;
		
				//set the document
		$this->setDocument();
		//load the toolbar
		$this->addToolBar();
		
		parent::display($tpl);
	}
	protected function addToolBar(){
		
		$state	= $this->get('State');
		$canDo	= EasyquickiconsHelper::getActions();
		
		$layout = JRequest::getCmd('layout', 'default');
		
		JToolBarHelper::title( JText::_( 'COM_EASYQUICKICONS_TOOLBAR' ), 'easyquickicons' );
		$icons = EasyquickiconsHelper::checkIcons();
		
		if($layout != 'welcome'){
			if($icons){
				if ($canDo->get('core.create')) {
					JToolBarHelper::addNew('easyquickicon.add');
				}
			}
			if ($canDo->get('core.edit')) {
				JToolBarHelper::editList('easyquickicon.edit');
			}
			if ($canDo->get('core.edit.state')) {
				if ($state->get('filter.state') != 2){
					JToolBarHelper::divider();
					JToolBarHelper::publish('easyquickicons.publish', 'JTOOLBAR_ENABLE', true);
					JToolBarHelper::unpublish('easyquickicons.unpublish', 'JTOOLBAR_DISABLE', true);
				}
				if ($state->get('filter.state') != -1 ) {
					JToolBarHelper::divider();
					if ($state->get('filter.state') != 2) {
						JToolBarHelper::archiveList('easyquickicons.archive');
					}
					elseif ($state->get('filter.state') == 2) {
						JToolBarHelper::unarchiveList('easyquickicons.publish', 'JTOOLBAR_UNARCHIVE');
					}
				}
			}
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
				JToolBarHelper::deleteList('', 'easyquickicons.delete', 'JTOOLBAR_EMPTY_TRASH');
				JToolBarHelper::divider();
			} elseif ($canDo->get('core.edit.state')) {
				JToolBarHelper::trash('easyquickicons.trash');
				JToolBarHelper::divider();
			}
			if ($canDo->get('core.admin')) {
				JToolBarHelper::preferences('com_easyquickicons');
				JToolBarHelper::divider();
			}
			$bar=& JToolBar::getInstance( 'toolbar' );
			$bar->appendButton( 'Help', 'help', 'JTOOLBAR_HELP', 'http://www.awynesoft.com/documentations/easy-quickicons-documentation.html', 640, 480 );
			
			JToolBarHelper::divider();
			JToolBarHelper::custom( 'easyquickicons.gopro', 'upgrade.png', 'upgrade.png', 'COM_EASYQUICKICONS_TOOLBAR_GO_PRO', false, false );
		} else {
			
			JToolBarHelper::back('COM_EASYQUICKICONS', 'index.php?option='.JRequest::getCmd('option'));
		}
		
	}
	protected function setDocument(){
		
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_( 'COM_EASYQUICKICONS_DOCUMENT_TITLE' ) );
	}
}