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
class LawfirmViewLawfirmemp extends JView
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
		//style and js
		$app = JFactory::getApplication();
		$document =& JFactory::getDocument();

		$document->addScript(JURI::base(). 'components/com_legalconfirm/assets/js/jquery.min.js');
		$document->addScript(JURI::base(). 'components/com_lawfirm/assets/js/validation.js');
		$document->addStyleSheet(JURI::base(). 'components/com_legalconfirm/assets/css/style.css');
		$document->addStyleSheet(JURI::base(). 'components/com_legalconfirm/assets/css/css.css');
		$document->addStyleSheet('components/com_legalconfirm/assets/css/form.css');

		//check for login
		$config = JFactory::getConfig();
		$user = JFactory::getuser();
		$lawfirm_emp = $config->getValue('config.lawfirm_emp');
		if($user->id < 0 || $user->id == ''){
			$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_LOGIN_FIRST'));
			$app->redirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
		}elseif(!(@in_array($lawfirm_emp, $user->groups))){
			$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_LOGIN_WRONG_TRY'));
			$app->redirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
		}
		//get message
		$mssg = JRequest::getVar('message');
		$eid = JRequest::getVar('eid');
		if($mssg == "success"){
			 
			$app->setUserState( "mark_id", $eid );
			$app->enqueueMessage(JText::_('COM_LAWFIRM_TEMPLATE_SAVED_TOPARTNER'));
			//check for limit
			$limit = JRequest::getVar('limitstart');
			if($limit == "" || $limit == "0"){
				$app->redirect(JRoute::_('index.php?option=com_lawfirm&view=lawfirmemp&layout=lawfirmemptask', false));
			}else{
				$app->redirect(JRoute::_('index.php?option=com_lawfirm&view=lawfirmemp&layout=lawfirmemptask&limitstart='.$limit, false));
			}
		}
		//check for second time login
		$login_count = $this->getlogincount();
		//get template
		$layout  = JRequest::getVar('layout');
		if($layout == "lawfirmemptask"){
			$this->setLayout('lawemptask');
			$model = $this->getModel('Lawfirmemp','LawfirmModel');
			$this->state		= $this->get('State');
			$this->items = $model->getAssignedPinnedTask();
			$this->paginations	= $model->getPaginations();
		}
		//		if($login_count == "2"){
		//
		//			//check for employee has pinned the task
		//			$pintask_count = $this->getPinnedtask();
		//
		//			if($pintask_count >0 && !isset($_GET['limitstart'])){
		//				$this->setLayout('lawemptask');
		//				//get pinned task
		//				$model = $this->getModel('Lawfirmemp','LawfirmModel');
		//				$this->items = $model->getAssignedPinnedTask();
		//
		//			}
		//			else{
		//				$this->state		= $this->get('State');
		//				$this->items		= $this->get('Items');
		//				$this->pagination	= $this->get('Pagination');
		//
		//			}
		else{
			$this->state		= $this->get('State');
			$this->items		= $this->get('Items');
			$this->pagination	= $this->get('Pagination');

		}
		$this->params       = $app->getParams('com_lawfirm');
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {;
		throw new Exception(implode("\n", $errors));
		}

		$this->_prepareDocument();
			
		parent::display($tpl);
}

public function getFullDetail($tpl = null){

	//style and js
	$app = JFactory::getApplication();
	$document =& JFactory::getDocument();
        $this->logincheck();
	$document->addScript(JURI::base(). 'components/com_legalconfirm/assets/js/jquery.min.js');

	$document->addScript(JURI::base(). 'components/com_legalconfirm/assets/js/validation.js');
	$document->addStyleSheet(JURI::base(). 'components/com_legalconfirm/assets/css/style.css');
	$document->addStyleSheet(JURI::base(). 'components/com_legalconfirm/assets/css/css.css');
	$document->addStyleSheet('components/com_legalconfirm/assets/css/form.css');


	$app = JFactory::getApplication();
	$this->state= $this->state;
	$this->items = $this->get('data');
	$this->params = $app->getParams('com_lawfirm');
	// $this->_prepareDocument();
	parent::display($tpl);
}

/**
 * Method to show mail to lawfirm employee
 */
public function getmaildetail(){
	$this->logincheck();
	$this->item = $this->get('data');
	parent::display($tpl);
}

/**
 * Method to check mail template
 */
public function checktemplate(){
	$this->logincheck();
	$this->item = $this->get('data');
	parent::display($tpl);
}
/**
 * Method to get mail template of prepared task
 */
public function getmytemplate(){
	$this->logincheck();
	$this->item = $this->get('data');
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
 * Method to get propsal detail
 */
public function getProposalInfo($pid){
	$model = $this->getModel('Lawfirmemp');

	$result = $model->getProposalInfo($pid);
	return $result;
}

/**
 * Method to get pinned task
 *
 */
public function getPinnedtask(){
	//get current user login
	$user = JFactory::getUser();
	$userId = $user->id;
	//get user assign task
	$model = $this->getModel('Lawfirmemp');
	$assign_task_count = $model->getPinnedtask();
	return $assign_task_count;

}

/**
 * Method to get unassigned task
 */
public function getTaskAssignedByPartner(){
	//get user assign task
	$model = $this->getModel('Lawfirmemp');
	$unassign_task = $model->getTaskAssignedByPartner();
	return $unassign_task;
}

/**
 * Method to get login count
 */
public function getlogincount(){
	//get current login user
	$user = JFactory::getUser();
	$userId = $user->id;
	//get db object
	$db = JFactory::getDBO();
	$query = "SELECT a.login_count as logincount FROM #__lawfirm_login_count as a WHERE lid = ".$db->Quote($userId);

	$db->setQuery($query);
	$db->query();
	$result = $db->loadResult();
	return $result;
}

/*
 * Method to get mail template
 */
public function getMailTemplateByLawfirm($id){
	$model = $this->getModel('Lawfirmemp');
	$content = $model->getMailTemplateByLawfirm($id);
	return $content;
}
/**
 * Method to get detail for mail template
 * @param assign table id
 */
public function getInfo($aid){
	//get model object
	$model = $this->getModel('Lawfirmemp');
	$content = $model->getInfo($aid);
	return $content;
}

/**
 * Method to get lawfirm partners
 */
public function lawfirmpartner(){
	$model = $this->getModel('Lawfirmemp');
	$partners = $model->lawfirmpartner();
	return $partners;
}

/**
 * Method to get assigned partner for the parposal by lawfirm
 */
public function getassignedpartner($id){
	$model = $this->getModel('Lawfirmemp');
	$assign_partner = $model->getassignedpartner($id);
	return $assign_partner;
}

/**
 * Method to check for approval for proposal
 */
public function checkProposalForApproval($aid){
	$model = $this->getModel('Lawfirmemp');
	$is_pp_approve = $model->checkProposalForApproval($aid);
	return $is_pp_approve;
}

/**
 * Method to check the disapprove status of proposal
 */
public function checkDisapprove($id){
	$model = $this->getModel('Lawfirmemp');
	$is_pp_disapprove = $model->checkDisapprove($id);
	return $is_pp_disapprove;
}
/*
*function  for checking the user is logged-in 
*/
public function logincheck()
{
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();
		$user = JFactory::getuser();
		$lawfirm_emp = $config->getValue('config.lawfirm_emp');
		if($user->id < 0 || $user->id == ''){
			$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_LOGIN_FIRST'));
			$app->redirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
		}elseif(!(@in_array($lawfirm_emp, $user->groups))){
			$app->enqueueMessage(JText::_('COM_LEGALCONFIRM_LOGIN_WRONG_TRY'));
			$app->redirect(JRoute::_('index.php?option=com_legalconfirmusers&view=login', false));
		}
}
}
