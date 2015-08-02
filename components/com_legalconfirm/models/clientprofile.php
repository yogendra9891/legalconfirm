<?php
/**
 * @version     1.0.0
 * @package     com_legalconfirm
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Abhishek Gupta <abhishek.gupta@daffodilsw.com> - http://
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
require_once JPATH_COMPONENT.'/helpers/legalconfirm.php';
require_once JPATH_COMPONENT.'/tables/clientsigner.php';
require_once JPATH_COMPONENT.'/tables/auditorclients.php';
require_once JPATH_COMPONENT.'/tables/clientproposals.php';
require_once JPATH_COMPONENT.'/tables/proposallawfirm.php';
require_once JPATH_COMPONENT.'/tables/auditornotes.php';
require_once JPATH_COMPONENT.'/tables/lawfirm_assignproposal.php';
require_once JPATH_COMPONENT.'/tables/lawfirm_proposal_notify.php';
require_once JPATH_COMPONENT.'/tables/auditor_client_templates.php';
require_once JPATH_COMPONENT.'/tables/initiation_payment_record.php';
require_once JPATH_COMPONENT.'/library/tcpdf_include.php';
require_once JPATH_COMPONENT.'/library/tcpdf.php';
require_once JPATH_COMPONENT.'/library/fpdf.php';
/**
 * Methods supporting Legalconfirm client Profile.
 */
class LegalconfirmModelClientprofile extends JModelList {

	/**
	 * Constructor.
	 *
	 * @param    array    An optional associative array of configuration settings.
	 * @see        JController
	 * @since    1.6
	 */
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
                'accounting_firm', 'b.accounting_firm',
                'email', 'a.email'
                );
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null) {

		// Initialise variables.
		$app = JFactory::getApplication();

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit', $limit);

		$limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
		$this->setState('list.start', $limitstart);
		//set the state for sorting the column..
		$ordering =  $this->setState('list.ordering', JRequest::getVar('filter_order'));
		$direction =  $this->setState('list.direction', JRequest::getVar('filter_order_Dir'));
		$searchfltr = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');
		$this->setState('filter.search', $searchfltr);
		// List state information.
		parent::populateState($ordering, $direction);
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id.= ':' . $this->getState('filter.search');
		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery() {
		// Create a new query object.
		$db = $this->getDbo();
		$user = JFactory::getUser();
		$query = $db->getQuery(true);
		$this->populateState();
		//function for finding the already requested proposal to the same client..
		$clientid = JRequest::getVar('id');
		$requestedLawfirms = array();
		$requestedLawfirms = $this->getLawfirmsGetting();
		$anewarray = array();
		foreach ($requestedLawfirms as $newarray)
		{
			$anewarray[] = $newarray->lawfirmid;
		}  //making comma seperated string fom array...
		$comma_separated = implode(",", @$anewarray);
		$incompletetasklawfirm = array();
		$incompletetasklawfirm = $this->getLawfirmIncompleteTask();
		$bnewarray = array();
		foreach ($incompletetasklawfirm as $cnewarray)
		{
			$bnewarray[] = $cnewarray->lawfirmid;
		}  //making comma seperated string fom array...
		$comma_separated_incomplete = implode(",", @$bnewarray);
		$config = JFactory::getConfig();
		$lawfirm_group = $config->getValue('lawfirm');
		// Select the required fields from the table.
		$query->select(
		$this->getState(
                        'list.select', 'a.*, a.id as lawfirmid'
                        )
                        );
                        $query->select(' b.*');
                        $query->from('`#__users` AS a');
                        $query->join('INNER', $db->quoteName('#__users_profile_detail').' AS b ON a.id = b.lid');
                        $query->join('INNER', $db->quoteName('#__user_usergroup_map').' AS c ON a.id = c.user_id');
                        $query->where('c.group_id = '.$lawfirm_group);
                        $query->where('a.block=' . '0');
                        if($comma_separated != '')
                        {
                        	$query->where('a.id NOT IN('.$comma_separated.')');
                        }
                        if($comma_separated_incomplete != '')
                        {
                        	$query->where('a.id NOT IN('.$comma_separated_incomplete.')');
                        }
                        
                        // Filter by search in title
                        $search = $this->getState('filter.search');
                        if (!empty($search)) {
                        	if (stripos($search, 'id:') === 0) {
                        		$query->where('a.id = '.(int) substr($search, 3));
                        	} else {
                        		$search = $db->Quote('%'.$db->escape($search, true).'%');
                        	}
                        	$query->where('b.accounting_firm LIKE '.$search);
                        }
                        // Add the list ordering clause.
                        $orderCol = $this->getState('list.ordering', 'b.accounting_firm');
                        $orderDirn = $this->getState('list.direction', 'asc');
                        $query->order($db->escape($orderCol . ' ' . $orderDirn)); //echo $query; exit;
                        return $query;
	}

	/*
	 * function for finding the already requested lawfirms in a lawfirms
	 * @params clientid
	 */
	//	public function getRequestedLawfirms()
	//	{
	//		$app = JFactory::getApplication();
	//		$clientid = JRequest::getVar('id');
	//		$user = JFactory::getUser();
	//		$db = $this->getDbo();
	//		$date = JFactory::getDate();
	//		$currentdate = $date->toFormat();
	//		$query = $db->getQuery(true);
	//		$query->select('a.id as proposalid');
	//		$query->from('#__clientproposals as a');
	//		$query->where('a.cid = '.$clientid);
	//		$query->where('a.lid = '.$user->id);
	//		$query->where('a.status = '.'"0"');
	//		$query->where('DATE_ADD(a.requestdate, INTERVAL 2 MONTH)>NOW()');
	//		$db->setQuery($query);
	//		$db->query();
	//		$resultProposalid = $db->loadResult();
	//		//function for finding the lawfirmids.......
	//		$lawfirmids = $this->lawfirmsGetting($resultProposalid);
	//		return $lawfirmids;
	//	}
	
	/*
	* function for finding the lawfirms ids requested and not accepted by signer..
	* @params proposalid
	* function is also calling from view.html file for the same view(clientprofle)
	*/
	public function getLawfirmsGetting()
	{
		$clientid = JRequest::getVar('id');
		$db = $this->getDbo();
		$resultlawfirmsids = array();
		$query = $db->getQuery(true);
		$query->select('DISTINCT a.lawfirmid');
		$query->from('#__proposallawfirm as a');
		$query->join('INNER', '#__clientproposals as b ON a.pid = b.id');
		$query->where('a.status = '.'"0"');
		$query->where('b.cid = '.$clientid);
	//	$query->where('a.lawfirmid NOT IN(SELECT c.lawfirmid FROM #__lawfirm_assignproposal as c INNER JOIN #__clientproposals as d ON c.pid = d.id WHERE c.taskstatus ='.'"0"'.' AND d.cid ='. $clientid.')');
		$db->setQuery($query);
		$db->query(); //echo $query; exit;
		$resultlawfirmsids = $db->loadObjectList(); //print_r($resultlawfirmsids); exit;
		return $resultlawfirmsids;
	}
   /*
    * function for finding the lawfirms those task are not completed corrosponding to a particular client.
    * @params clientid
    */
	public function getLawfirmIncompleteTask()
	{
		$clientid = JRequest::getVar('id');
		$db = JFactory::getDbo();
		$resultlawfirmsids = array();
		$query = $db->getQuery(true);
		$query->select('DISTINCT a.lawfirmid');
		$query->from('#__lawfirm_assignproposal as a');
		$query->join('INNER', '#__clientproposals as b ON a.pid = b.id');
		$query->where('a.taskstatus = '.'"0"');
		$query->where('b.cid = '.$clientid);
		$db->setQuery($query);
		$db->query();
		$resultarry = $db->loadObjectList();
		return $resultarry;
	}
	/*
	 * function for getting the client profile..
	 */
	public function getProfile()
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$clientid = JRequest::getVar('id');
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.*, a.id as clientid');
		$query->from('#__auditorclients as a');
		$query->where('a.id = '.$clientid);
		$query->where('a.lid = '.$user->id);
		$db->setQuery($query);
		$db->query();
		$resultProfile = $db->loadObject();
		return $resultProfile;
	}
	/*
	 * function for getting the signer of the current user and requested client..
	 * @params clientid , userid
	 */
	public function getSigner()
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$clientid = JRequest::getVar('id');
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('c.*');
		$query->from('#__clientsigner as c');
		$query->where('c.cid = '.$clientid);
		$query->where('c.lid = '.$user->id);
		$db->setQuery($query);
		$db->query();
		$resultSigner = $db->loadObject();
		return $resultSigner;
	}
	/*
	 * function for finding the offices..of a law firm
	 * @params law firm id..
	 */
	public function getOffices($lawfirmid)
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('u.*');
		$query->from('#__users_office as u');
		$query->where('u.lid = '.$lawfirmid);
		$db->setQuery($query);
		$db->query();
		$resultLawoffices = $db->loadObjectList();
		return $resultLawoffices;
	}
	/*
	 * function for removing a singer of a client..
	 * @params signerid
	 */
	public function removeSigner($signerid, $clientid)
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query = 'DELETE from #__clientsigner where id = '.$signerid.' AND cid = '.$clientid; //echo $query; exit;
		$db->setQuery($query);
		$db->query();
	}
	/*
	 * function for adding a new signer
	 * @params post data from form
	 */
	public function addsigner($data)
	{
		$user = JFactory::getUser();
		$data['lid'] = $user->id;
		$clientsigner = &JTable::getInstance('Clientsigner', 'LegalconfirmTable');
		if (!$clientsigner->save($data)) {
			$this->setError(JText::sprintf('COM_LEGALCONFIRM_ADD_SIGNER_ERROR', $clientsigner->getError()));
			return false;
		}
		return $clientsigner->id;
	}
	/*
	 * function for adding a new signer
	 * @params post data from form
	 */
	public function editsigner($data)
	{
		$user = JFactory::getUser();
		$data['lid'] = $user->id;
		$clientsigner = &JTable::getInstance('Clientsigner', 'LegalconfirmTable');
		$clientsigner->load($data['signerid']);
		if (!$clientsigner->save($data)) {
			$this->setError(JText::sprintf('COM_LEGALCONFIRM_ADD_SIGNER_ERROR', $clientsigner->getError()));
			return false;
		}
		return $clientsigner->id;
	}
	/*
	 * function for edit a company profile
	 * @params postdata, clientid
	 */
	public function editcompany($data)
	{
		$user = JFactory::getUser();
		$data['lid'] = $user->id;
		$auditorclient = &JTable::getInstance('Auditorclients', 'LegalconfirmTable');
		$auditorclient->load($data['cid']);
		if (!$auditorclient->save($data)) {
			$this->setError(JText::sprintf('COM_LEGALCONFIRM_EDIT_COMPANY_ERROR', $auditorclient->getError()));
			return false;
		}
		return $auditorclient->id;

	}
	/*
	 * function for cheking the signer is available for the current user
	 * @params clientid
	 */
	public function checkSigner($clientid)
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('c.id');
		$query->from('#__clientsigner as c');
		$query->where('c.cid = '.$clientid);
		$query->where('c.lid = '.$user->id);
		$db->setQuery($query);
		$db->query();
		$resultsignerid = $db->loadResult();
		return $resultsignerid;
	}
	/*
	 * function for finding the lawfirm ids..
	 * @params lawoffice
	 */
	public function getLawfirmId($lawfirmofficeid)
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('u.lid');
		$query->from('#__users_office as u');
		$query->where('u.id = '.$lawfirmofficeid);
		$db->setQuery($query);
		$db->query();
		$resultlawfirmid = $db->loadResult();
		return $resultlawfirmid;
	}
	/*
	 * function for saving the request.....
	 * @params $requesteddata, clientid
	 */
	public function saveRequest($requestData, $clientid, $templateid)
	{
		$user = JFactory::getUser();
		$clientproposals = &JTable::getInstance('Clientproposals', 'LegalconfirmTable');
		$proposaltemplate = &JTable::getInstance('Auditor_client_templates', 'LegalconfirmTable');
		$proposaltemplate->load($templateid);
		$data = array();
		$data['lid'] = $user->id;
		$config =& JFactory::getConfig();
        $tzoffset = $config->getValue('config.offset');        
        $assign_date =& JFactory::getDate('',$tzoffset);
		$data['requestdate'] = $assign_date->toMySQL(true);
		$data['cid'] = $clientid;
		$data['template'] = $proposaltemplate->template;
		//		$id = '';
		//		$clientproposals->load($id);
		if(!$clientproposals->save($data))
		{
			$this->setError(JText::sprintf('COM_LEGALCONFIRM_CLIENT_REQUEST_MAKING _FAILED', $clientproposals->getError()));
			return false;
		}else{
			foreach($requestData as $temparray)
			{
				$data1               = array();
				$data1['pid']        =  $clientproposals->id;
				$data1['lawfirmid']  = $temparray['lawfirm'];
				$data1['lawoffices'] = $temparray['lawfirmoffices'];
				$proposallawfirm     = &JTable::getInstance('Proposallawfirm', 'LegalconfirmTable');
				if(!$proposallawfirm->save($data1))
				{
					$this->setError(JText::sprintf('COM_LEGALCONFIRM_CLIENT_REQUEST_MAKING_FAILED', $proposallawfirm->getError()));
					return false;
				}
			}
		}
		return $clientproposals->id;
	}
	/*
	 * function for making the client proposal deactivated..
	 * @params clientid
	 */
	public function makeDeactivateclientProposal($clientid)
	{
		$app   = JFactory::getApplication();
		$user  = JFactory::getUser();
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query1 = $db->getQuery(true);
		$query2 = $db->getQuery(true);
		$query1 = 'SELECT a.id from #__clientproposals as a where a.cid ='.$clientid.' AND a.status ='.'"0"';
		$db->setQuery($query1); 
		$db->query();
		$pidresult = $db->loadResult(); 
		//make the proposal_lawfirm table entries for lawfirm 3(ignored).
		$query2 =  'UPDATE #__proposallawfirm set status = '.'"3"'. ' where pid='.$pidresult.' AND status = '.'"0"';
		$db->setQuery($query2);
		$db->query(); 
		$query  = 'UPDATE #__clientproposals set token = '.'""'.', status = '.'"3"'. ' where cid='.$clientid.' AND status = '.'"0"';
		$db->setQuery($query);
		$db->query();
		return true;
	}
	/*
	 * function for finding the signer email the current user
	 * @params clientid
	 */
	public function emailSigner($clientid)
	{
		$user   = JFactory::getUser();
		$db     = $this->getDbo();
		$query  = $db->getQuery(true);
		$query->select('c.email');
		$query->from('#__clientsigner as c');
		$query->where('c.cid = '.$clientid);
		$query->where('c.lid = '.$user->id);
		$db->setQuery($query);
		$db->query();
		$signeremail = $db->loadResult();
		return $signeremail;
	}
	/*
	 * function for updating the proposal table by token.
	 */
	public function updateProposal($proposalid, $token)
	{
		$proposallawfirm = &JTable::getInstance('Clientproposals', 'LegalconfirmTable');
		$data = array();
		$data['token'] = $token;
		$proposallawfirm->load($proposalid);
		if(!$proposallawfirm->save($data))
		{
		 $this->setError(JText::sprintf('COM_LEGALCONFIRM_CLIENT_REQUEST_TOKEN_FAILED', $proposallawfirm->getError()));
		 return false;
		}
		return true;
	}
	/*
	 * function for finding the client information
	 * @params clienid
	 */
	public function clientInfo($clientid)
	{
		$user   = JFactory::getUser();
		$db     = $this->getDbo();
		$query  = $db->getQuery(true);
		$query->select('a.*, a.id as clientid');
		$query->from('#__auditorclients as a');
		$query->where('a.id = '.$clientid);
		$query->where('a.lid = '.$user->id);
		$db->setQuery($query);
		$db->query();
		$resultProfile = $db->loadObject();
		return $resultProfile;
	}
	/*
	 * function for finding the lawfirmname..
	 * @params lawfirms
	 */
	public function getLawfirmName($lawfirmid)
	{
		$db	 	= JFactory::getDbo();
		$user 	= JFactory::getUser();
		$query	= $db->getQuery(true);
		$query->select('a.accounting_firm');
		$query->from('#__users_profile_detail as a');
		$query->join('INNER','#__users as u ON a.lid = u.id');
		$query->where('a.lid = '.$lawfirmid);
		$db->setQuery($query);
		$db->query();
		$resultName = $db->loadResult();
		return $resultName;
	}
	/*
	 * function for finding the offices location address
	 * @params officeids
	 */
	public function getOfficeLocation($officesids)
	{
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$query = $db->getQuery(true);
		$query->select('CONCAT_WS(","'.",".' a.office_title, a.address, a.city, a.state, a.country)');
		$query->from('#__users_office as a');
		$query->where('a.id = '.$officesids);
		$db->setQuery($query);
		$db->query();
		$resultName = $db->loadResult();
		return $resultName;
	}
	/*
	 * function for accepting the request by client signer..
	 * @params clientid, proposalid, token
	 */
	public function acceptrequest($clientid, $proposalid, $token)
	{
		$proposallawfirm 	 = &JTable::getInstance('Clientproposals', 'LegalconfirmTable');
		$proposallawfirm->load($proposalid);
		$proposalrequestdate = $proposallawfirm->requestdate;
		$expirydate = strtotime("$proposalrequestdate +2 month"); //check for 2 months, if request is pending before two months then it assumed as expired..
		$config = & JFactory::getConfig();
        $tzoffset = $config->getValue('config.offset');        
        $assign_date = & JFactory::getDate('',$tzoffset);
		$currentdate = strtotime($assign_date->toMySQL(true));
		if($proposallawfirm->token == '' || $proposallawfirm->status == '2')
		{
			return 1;
		}
		elseif((($proposallawfirm->token != $token) || ($proposallawfirm->cid != $clientid)))
		{
			return 1;
		}
		elseif($expirydate < $currentdate)// it expires after two month of request date.....
		{
			return 4;
		}
		else{
			$data = array();
			$data['token'] = '';
			$data['status'] = '1';
			$data['responsedate'] = $assign_date->toMySQL(true);
			if(!$proposallawfirm->save($data))
			{
				return 3;
			}else{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query = 'UPDATE #__proposallawfirm set status ='.'"1"'.' where pid='.$proposalid;
				$db->setQuery($query);
				$db->query();
				return 2;
			}
		}
	}
	/*
	 * function for finding the auditor id
	 * @params proposalid
	 */
	public function getAuditorid($proposalid)
	{
		$proposallawfirm = &JTable::getInstance('Clientproposals', 'LegalconfirmTable');
		$proposallawfirm->load($proposalid);
		return $proposallawfirm->lid;
	}
	/*
	 * function for denied the request by client signer..
	 * @params clientid, proposalid, token
	 */
	public function denyrequest($clientid, $proposalid, $token)
	{
		$proposallawfirm = &JTable::getInstance('Clientproposals', 'LegalconfirmTable');
		$proposallawfirm->load($proposalid);
		$proposalrequestdate = $proposallawfirm->requestdate;
		$expirydate = strtotime("$proposalrequestdate +2 month");
		$config =& JFactory::getConfig();
        $tzoffset = $config->getValue('config.offset');        
        $assign_date =& JFactory::getDate('',$tzoffset);
		$currentdate = strtotime($assign_date->toMySQL(true));
		if($proposallawfirm->token == '' || $proposallawfirm->status == '2')
		{
			return 1;
		}
		elseif((($proposallawfirm->token != $token) || ($proposallawfirm->cid != $clientid)))
		{
			return 1;
		}
		elseif($expirydate < $currentdate)// it expires after two month of request date.....
		{
			return 4;
		}
		else{
			$data = array();
			$data['token'] = '';
			$data['status'] = '2';
			$date = JFactory::getDate();
			$data['responsedate'] = $assign_date->toMySQL(true);
			if(!$proposallawfirm->save($data))
			{
				return 3;
			}else{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query = 'UPDATE #__proposallawfirm set status ='.'"2"'.' where pid='.$proposalid;
				$db->setQuery($query);
				$db->query();
				return 2;
			}
		}
	}
	/*
	 * function for adding the notes.
	 * @params postdata
	 */
	public function addnotes($postdata)
	{
		$auditornotes = &JTable::getInstance('Auditornotes', 'LegalconfirmTable');
		$data = array();
		$user = JFactory::getUser();
		$data['cid'] = $postdata['clientidnotes'];
		$data['notes'] = $postdata['auditornotes'];
		$data['lid'] = $user->id;
		if(!$auditornotes->save($data))
		{
			$this->setError(JText::sprintf('COM_LEGALCONFIRM_CLIENT_ADD_NOTES_FAILED', $auditornotes->getError()));
			return fasle;
		}
		return $auditornotes->id;
	}
	/*
	 * function for saving the auditor notes for a client.
	 * @params postdata
	 *
	 */
	public function editAuditornotes($postdata)
	{
		$auditornotes = &JTable::getInstance('Auditornotes', 'LegalconfirmTable');
		$data = array();
		$user = JFactory::getUser();
		$data['cid'] = $postdata['cid'];
		$data['notes'] = $postdata['notes'];
		$data['lid'] = $user->id;
		$auditornotes->load($postdata['auditornotesid']);
		if(!$auditornotes->save($data))
		{
			$this->setError(JText::sprintf('COM_LEGALCONFIRM_CLIENT_EDIT_NOTES_FAILED', $auditornotes->getError()));
			return fasle;
		}
		return $auditornotes->id;
	}
	/*
	 * function for making the entry in  proposal_notify table when a signer accept a request 
	 * @params clientid, proposalid
	 */
	public function makeNotifyEntry($proposalid, $clientid)
	{
		$lawfirmids = array();
		$lawfirmids = $this->getLawfirmids($proposalid, $clientid);
		$config =& JFactory::getConfig();
        $tzoffset = $config->getValue('config.offset');        
        $assign_date =& JFactory::getDate('',$tzoffset);
		foreach($lawfirmids as $temparray)
		 {
				$data1 = array();
				$data1['pid'] =  $proposalid;
				$data1['cid'] =  $clientid;
				$data1['lawfirmid'] = $temparray->lawfirmid;
				$data1['date'] =  $assign_date->toMySQL(true);
				$data1['assigndate'] = $assign_date->toMySQL(true);
				$lawfirmproposalnotify = &JTable::getInstance('Lawfirm_proposal_notify', 'LegalconfirmTable');
				$lawfirmassignproposal = &JTable::getInstance('Lawfirm_assignproposal', 'LegalconfirmTable');
				if(!$lawfirmproposalnotify->save($data1))
				{
					$this->setError(JText::sprintf('COM_LEGALCONFIRM_CLIENT_REQUEST_NOTIFY_MAKING_FAILED', $lawfirmproposalnotify->getError()));
					return false;
				}
				else{
					if(!$lawfirmassignproposal->save($data1))
					{
						$this->setError(JText::sprintf('COM_LEGALCONFIRM_CLIENT_REQUEST_NOTIFY_MAKING_FAILED', $lawfirmassignproposal->getError()));
						return false;
					}
				}
		  }
		return true;
	}
	/*
	 * private function for finding the lawfirmids
	 * @params proposalid 
	 */
	private function getLawfirmids($proposalid, $clientid)
	{
		$db = JFactory::getdbo();
		$query = $db->getQuery(true);
		$query->select('a.lawfirmid');
		$query->from('#__proposallawfirm as a');
		$query->where('a.pid = '.$proposalid);
		$db->setQuery($query);
		$db->query();
		$lawfirmids = $db->loadObjectList();
		return $lawfirmids;
	}
	/*
	 * function for updating the token and status in clientproposals table, and status proposallawfirm table.
	 * @params clientid, proposalid, token  
	 */
	public function updateproposalstatus($clientid, $proposalid, $token)
	{
		$proposallawfirm = &JTable::getInstance('Clientproposals', 'LegalconfirmTable');
		$proposallawfirm->load($proposalid);
		$data = array();
		$data['token'] =  $token;
		$data['status'] = '0';
		if(!$proposallawfirm->save($data))
		{
			return false;
		}
		else{
			 $db = JFactory::getDbo();
			 $query = $db->getQuery(true);
			 $query = 'UPDATE #__proposallawfirm set status ='.'"0"'.' where pid='.$proposalid;
			 $db->setQuery($query);
			 $db->query();
			 return false;
		}
		return true;
	}
	/*
	 * function for making the payment and initiate the confirmation for the added lawfirms
	 * @params clientid
	 */
	public function initiateconfirmation($clientid)
	{
		$proposal = LegalconfirmHelper::getLatestproposal($clientid); //finding the latest proposal accepted by signer corrosponding the client 
		$db = JFactory::getdbo();
		$query = $db->getQuery(true);
		$query->select('a.lawfirmid');
		$query->from('#__lawfirm_proposal_notify as a');
		$query->where('a.cid = '.$clientid);
		$query->where('a.status = '.'"0"');
		$query->where('a.pid = '.$proposal);
		$db->setQuery($query);
		$db->query();
		$lawfirmids = $db->loadObjectList();
		if(!(count($lawfirmids))) // no lawfirms are avaiable for making the initiation id true
		{
			return 0;
		}
		$result = $this->makePayment($lawfirmids, $clientid);
		return $result;
	}
	/*
	 * function for making the lawfirms email ids and finding the current auditor payment detail
	 * @params lawfirms ids, clientid, auditor employee(current logged in user) 
	 */
	private function makePayment($lawfirmids, $clientid)
	{
		$app = JFactory::getApplication(); 
		$count = count($lawfirmids);
		$paymentstatus = LegalconfirmHelper::getPaymentstatus($count);
		if("SUCCESS" == strtoupper($paymentstatus["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($paymentstatus["ACK"])) {
			if(!(@$paymentstatus['TRANSACTIONID'] == ''))
			{    
				$transactionid = $paymentstatus['TRANSACTIONID'];
				$paidamount = $paymentstatus['AMT'];
				//do the mail process to the lawfirm admin, selected in the request making time..
				$mailresult = $this->mailToLawfirm($lawfirmids, $clientid, $transactionid, $paidamount);
			}else{
				return 2;
			}
		}else{
			return 2; // if payment process failed, do nothing just show error message.
		}
		return $mailresult;
	}
	/*
	 * function for sending the email to lawfirm admin..
	 * @params lawfirmids, clientid
	 */
	private function mailToLawfirm($lawfirmids, $clientid, $transactionid, $paidamount)
	{
	 	$proposal = LegalconfirmHelper::getLatestproposal($clientid);
	 	$text = LegalconfirmHelper::generateTemplateLawfirmRequest($proposal);
	 	$text = htmlspecialchars_decode($text);
	 	$emailRecipientarray = array(); 
	 	foreach($lawfirmids as $lawfirm)
	 	{
	 		$user = JFactory::getUser($lawfirm->lawfirmid);
	 		$emailRecipientarray[] = $user->email;
	 	} 
		$mail =& JFactory::getMailer();
		$app		= JFactory::getApplication();
		$mailfrom	= $app->getCfg('mailfrom');
		$fromname	= $app->getCfg('fromname');
		$mail->setSubject(JText::_('COM_LEGALCONFIRM_INITIATE_CONFIRMATION_MAIL'));
		$mail->setBody($text);
		$mail->IsHTML= true;
		$mail->ContentType = 'text/html';
		$joomla_config = new JConfig();
		$mail->addRecipient($emailRecipientarray);
		$mail->setSender($mailfrom, $fromname);
		$mailsentresult = $mail->Send();
	 	if(!$mailsentresult)
	 	{
	 		return 3;
	 	}else{
	 		//saving the payment detail..
	 		 $tid = $this->savePaymentDetail($clientid, $transactionid, $paidamount, $proposal);
	 		//making the dynamic pdf and send it into mail to auditor employee + auditor admin..
	 		 $this->sendEmailReportToEmployeeAdmin($tid); 
	 		 $db = JFactory::getDbo();
	 		 $db1 = JFactory::getDbo();
			 $query = $db->getQuery(true);
			 $query1 = $db1->getQuery(true);//mark updated table that showing the mail sent to the lawfirms admin..
			 $query = 'UPDATE #__lawfirm_proposal_notify set status ='.'"1"'.' where pid='.$proposal. ' AND cid = '.$clientid;
			 $query1 = 'UPDATE #__lawfirm_assignproposal set requestsent ='.'"1"'.' where pid='.$proposal. ' AND cid = '.$clientid;
			 $db->setQuery($query);
			 $db->query();
			 $db1->setQuery($query1);
			 $db1->query();
			 return 1;
	 	}
	}
	/*
	 * function for saving the payment detail after initiating the confirmation
	 * @params transactionid, amount, proposalid, clientid
	 */
	public function savePaymentDetail($clientid, $transactionid, $paidamount, $proposal)
	{
		$user = JFactory::getUser();
		$config =& JFactory::getConfig();
        $tzoffset = $config->getValue('config.offset');        
        $assign_date =& JFactory::getDate('',$tzoffset);
		$data = array();
		$data['cid'] = $clientid;
		$data['pid'] = $proposal;
		$data['amount'] = $paidamount; 
		$data['lid'] = $user->id; 
		$data['date'] = $assign_date->toMySQL(true); 
		$data['transaction_id'] = $transactionid; 
		$transactiontable = &JTable::getInstance('Initiation_payment_record', 'LegalconfirmTable');
		if(!$transactiontable->save($data))
		{
			$this->setError(JText::sprintf('COM_LEGALCONFIRM_CLIENT_TRANSACTION_DATA_SAVE_ERROR', $transactiontable->getError()));
			return false;
		}
		return $transactiontable->id;		
	}
	/*
	 * function for saving the template..
	 * @params templatecontent, clientid
	 */
	public function saveTemplate($templateContent, $clientid, $templateid)
	{
		$user = JFactory::getUser();
		$session = & JFactory::getSession();
		$session_id = $session->getId();
		$data = array();
		$data['cid'] = $clientid;
		$data['template'] = $templateContent;
		$data['session_id'] = $session_id; 
		$proposaltemplate = &JTable::getInstance('Auditor_client_templates', 'LegalconfirmTable');
		$proposaltemplate->load($templateid);
		if(!$proposaltemplate->save($data))
		{
			$this->setError(JText::sprintf('COM_LEGALCONFIRM_CLIENT_TEMPLATE_ADDING_ERROR', $proposaltemplate->getError()));
			return false;
		}
		return $proposaltemplate->id;
	}
	/*
	 * function for updating the template sentto column by 1
	 * means template is sent
	 */
	public function updateTemplateTemporary($templateid)
	{
		$data = array();
		$data['is_sent'] = '1';
		$proposaltemplate = &JTable::getInstance('Auditor_client_templates', 'LegalconfirmTable');
		$proposaltemplate->load($templateid);
		if(!$proposaltemplate->save($data))
		{
			$this->setError(JText::sprintf('COM_LEGALCONFIRM_CLIENT_TEMPLATE_UPDATING_ERROR', $proposaltemplate->getError()));
			return false;
		}
		return true;
	}
	/*
	 * function for getting the proposal template..
	 * @params proposalid
	 */
	public function getTemplate($proposalid)
	{
		$proposallawfirm = &JTable::getInstance('Clientproposals', 'LegalconfirmTable');
		$proposallawfirm->load($proposalid);
		return $proposallawfirm->template;
	}
   /*
    * function for seeing the pending initiation for a particular client
    * @params clientid
    */
	public function checkPendingInitiation($clientid)
	{
		$db = JFactory::getdbo();
		$query = $db->getQuery(true);
		$query->select('count(a.pid)');
		$query->from('#__lawfirm_assignproposal as a');
		$query->where('a.cid = '.$clientid);
		$query->where('a.requestsent = '.'"0"');
		$db->setQuery($query); 
		$db->query();
		$countproposalids = $db->loadResult();
		return $countproposalids;
	}
	/*
	 * function for sending the mail to auditor employee and auditor admin
	 * @params transactionid
	 */
	public function sendEmailReportToEmployeeAdmin($tid)
	{
		$db = JFactory::getDbo();
		$recorddetail = &JTable::getInstance('Initiation_payment_record', 'LegalconfirmTable');
		$recorddetail->load($tid);
		$paymentid = JText::_('COM_LEGALCONFIRM_PAYMENT_ID').' '.$recorddetail->id;
		$paymenamount = JText::_('COM_LEGALCONFIRM_PAYMENT_AMOUNT').' '.$recorddetail->amount;
		$auditorname = $this->auditorname($recorddetail->lid);
		$clientname = $this->clientname($recorddetail->cid);
		$Auditorname = JText::_('COM_LEGALCONFIRM_PAYMENT_AUDITOR_NAME').' '. $auditorname;
		$Clientname = JText::_('COM_LEGALCONFIRM_PAYMENT_CLIENT_NAME').' '.$clientname;
		$Transactiondate = JText::_('COM_LEGALCONFIRM_PAYMENT_TRANSACTION_DATE').' '.date('d/M/Y', strtotime($recorddetail->date));
		$config =& JFactory::getConfig();
        $tzoffset = $config->getValue('config.offset');        
        $assign_date =& JFactory::getDate('',$tzoffset);
		$date = JText::_('COM_LEGALCONFIRM_PAYMENT_DATE').' '.date('d/M/Y', strtotime($assign_date->toMySQL(true)));
		$TransactionId = JText::_('COM_LEGALCONFIRM_PAYMENT_TRANSACTION_ID').' '.$recorddetail->transaction_id;
		$tag_line = JText::_('COM_LEGALCONFIRM_TAG_LINE');
		$imagepath = JURI::root().'/templates/legalconfirm/images/logo.png';
		
        $pdf = new FPDF();
	    $pdf->AddPage();
		$pdf->Image($imagepath,150,10,30,0,'','');
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(150,15,$tag_line,0,1);
		$pdf->Cell(40,10,$paymentid,0,1);
		$pdf->Cell(40,10,$paymenamount,0,1);
		$pdf->Cell(40,10,$Auditorname,0,1);
		$pdf->Cell(40,10,$Clientname,0,1);
		$pdf->Cell(40,10,$Transactiondate,0,1);
		$pdf->Cell(40,10,$date,0,1);
		$pdf->Cell(40,10,$TransactionId,0,1);
			
	// email stuff (change data below)
	  $user = JFactory::getUser();
	  $to = $user->email;
	  $parentid = $this->parentid($user->id);
	  $parentobject = JFactory::getUser($parentid);
	  $parentemail =  $parentobject->email;
      $app		= JFactory::getApplication();
      $from = $app->getCfg('mailfrom'); 
	  $subject = "LLC Confirmation Report";
	  $message = "Please see the attachment for the report of the payment make on a confirmation of a proposal";
	
	  // a random hash will be necessary to send mixed content
	  $separator = '-=-=-'.md5(microtime()).'-=-=-';
	
	  // attachment name
	  $filename = "llc_report.pdf";
	
	  // Generate headers
	  $headers = "From: $from\r\n"
	           . "MIME-Version: 1.0\r\n"
	           . "Content-Type: multipart/mixed; boundary=\"$separator\"\r\n"
	           . "X-Mailer: PHP/" . phpversion();
	  // Generate body
	  $body = "This is a multipart message in MIME format\r\n"
	        . "--$separator\r\n"
	        . "Content-Type: text/html; charset=\"iso-8859-1\"\r\n"
	        . "\r\n"
	        . "$message\r\n"
	        . "--$separator\r\n"
	        . "Content-Type: application/pdf\r\n"
	        . "Content-Transfer-Encoding: base64\r\n"
	        . "Content-Disposition: attachment; filename=\"$filename\"\r\n"
	        . "\r\n"
	        . chunk_split(base64_encode($pdf->Output("", "S")))."\r\n"
	        . "--$separator--";

    // send message
    mail($to, $subject, $body, $headers);
    mail($parentemail, $subject, $body, $headers);
 }
 	/*
	 * function for finding the auditor name
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
 /*
  * function for finding the parent id of a user..
  */
	public function parentid($userid)
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.parent');
		$query->from('#__users_profile_detail as a');
		$query->where('a.lid = '.$userid);
		$db->setQuery($query);
		$db->query();
		$parentid = $db->loadResult();
		return $parentid;
	}
	/*
	 * public function for finding the lawfirmids
	 * @params proposalid, @clientid
	 */
	public function getlawfirm($proposalid, $clientid)
	{
		$db = JFactory::getdbo();
		$query = $db->getQuery(true);
		$query->select('a.lawfirmid');
		$query->from('#__proposallawfirm as a');
		$query->where('a.pid = '.$proposalid);
		$db->setQuery($query);
		$db->query();
		$lawfirmids = $db->loadObjectList();
		//return $lawfirmids;
		$lawfirmname = array();
		foreach ($lawfirmids as $lawfirmid)
		{
			$lawfirmname = $this->getLawfirmName($lawfirmid->lawfirmid);
		} 
		return $lawfirmname;
	}
	/*
	 * function for finding the auditing firm name..
	 */
	public function findAuditorFirmName($userid)
	{
		$db = JFactory::getDbo();
		$user = JFactory::getUser(); 
		$query = $db->getQuery(true);
		$query->select('a.accounting_firm');
		$query->from('#__users_profile_detail as a');
		$query->where('a.lid = '.$userid);
		$db->setQuery($query);
		$db->query(); 
		$result = $db->loadResult(); 
		return $result;
	}
	/*
	 * function for cheking any request is pending to 
	 * @params clientid
	 */
	public function getrequestpendingid($clientid)
	{
		$proposal = LegalconfirmHelper::getLatestproposal($clientid); //finding the latest proposal accepted by signer corrosponding the client 
		$db = JFactory::getdbo();
		$query = $db->getQuery(true);
		$query->select('a.lawfirmid');
		$query->from('#__lawfirm_proposal_notify as a');
		$query->where('a.cid = '.$clientid);
		$query->where('a.status = '.'"0"');
		$query->where('a.pid = '.$proposal);
		$db->setQuery($query);
		$db->query();
		$lawfirmids = $db->loadObjectList();
		if(!(count($lawfirmids))) // no lawfirms are avaiable for making the initiation id true
		{
			return 0;
		}
		else{
			return 1;
		}
	}
	
}
