<?php

// no direct access
defined('_JEXEC') or die;

class plgSystemCheckproposal extends JPlugin
{
	public function onAfterInitialise ()
	{
		$result = $this->setTaskStatusAsExpired();
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
