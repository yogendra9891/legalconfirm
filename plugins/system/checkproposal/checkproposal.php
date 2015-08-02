<?php

// no direct access
defined('_JEXEC') or die;

class plgSystemCheckproposal extends JPlugin
{
	public function onAfterInitialise ()
	{
		$result = $this->setTaskStatusAsExpired();
		
		
		$user = JFactory::getUser();
			$task = JRequest::getVar('task');
		$componentName = JRequest::getVar('option');
		if($user->id != "" && ($componentName !='com_legalconfirmusers' && $componentName !='com_content' && $componentName !='com_contact'&& ($task != 'lawfirmadmin.activateoffice' && $task != 'auditoradmin.activateoffice'))){
		
			//allowed groupId
			$allowgroup = array(10,12,13);
			//get login user groupid
			foreach($user->groups as $key=>$value){
				$gid = $value;
			}
			
			if(in_array($gid,$allowgroup)){
				//check for users has activated office
				$db = JFactory::getDBO();
				$query = "SELECT a.status FROM #__users_office as a JOIN #__employee_office as b ON a.id = b.office_id WHERE b.empid = ".$user->id;
				$db->setQuery($query);
				$db->query();
				$result = $db->loadResult();
				if($result == 0){
					$app = JFactory::getApplication();
                    $app->redirect('index.php?option=com_legalconfirmusers&view=remind&layout=accessforbidden&type=1', "");
				}
			}
		}
		
		return true;
	}


	/**
	 * Method to check the if task has expired for the lawfirm
	 * if task has expired then set the task status as 2
	 */
	public function setTaskStatusAsExpired(){
		$db = JFactory::getDBO();
		$query = "UPDATE #__lawfirm_assignproposal as a set a.taskstatus = '2'
        	          WHERE date_add(`assigndate`,INTERVAL 2 MONTH) <= NOW() AND a.taskstatus != '1'";
		$db->setQuery($query);
		if(!$db->query()){
			return false;
		}
		return true;
			
	}
}

?>
