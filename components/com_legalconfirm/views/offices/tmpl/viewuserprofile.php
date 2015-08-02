<?php
/**
 * @category	Model
 * @package		Mobileapp
 * @subpackage	viewuserprofile
 * @copyright (C) 2013 by Daffodil Abhishek - All rights reserved!
 * @license		GNU/GPL
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'helpers' . DS . 'friends.php' );

class MobileappModelViewuserprofile extends JModel
{
	/**
	 * method to view users profile
	 * @params form data
	 * @return array
	 */
	function viewuserprofile($data1)
	{ 
		$userId = $data1['uid'];
		$myid = $data1['sessionid'];
		 //check for user login
	    $chksession = $this->checksession($myid);
	    if(!$chksession){
	    	 $error = array('errorCode'=>JText::_('ERRCODE_SESSION_DESTROYED_LOGIN_AGAIN'),'result'=>'','message'=>JText::_('MSG_SESSION_DESTROYED_LOGIN_AGAIN'));
       		 return $error;
       		 exit;
	    }
	    else{
	    	 $myid = $chksession;
	    	  $data1['sessionid'] = $chksession;
	    }
	    
	    //end to check session
		$profileType = "COMMUNITY_DEFAULT_PROFILE";
		
		
		$db			=& $this->getDBO();
		$data		= array();
		
		// Return with empty data
		if($userId == null || $userId == '')
		{
			$error = array('errorCode'=>JText::_('ERRCODE_ACCESS_FORBIDDEN'),'result'=>'','message'=>JText::_('MSG_ACCESS_FORBIDDEN'));
	            return $error;
			exit;
		}

		$user		=& JFactory::getUser($userId);
		
		if($user->id == null){
				$error = array('errorCode'=>JText::_('ERRCODE_USER_DOES_NOT_EXIST'),'result'=>'','message'=>JText::_('MSG_USER_DOES_NOT_EXIST'));
	            return $error;
			exit;
		}
		
		$data['id']		= $user->id;
		$data['name']	= $user->name;
		$data['email']	= $user->email;

		// Attach custom fields into the user object
		$query	= 'SELECT field.*, value.'.$db->nameQuote('value').',value.'.$db->nameQuote('access')
				. ' FROM ' . $db->nameQuote('#__community_fields') . ' AS field '
				. ' LEFT JOIN ' . $db->nameQuote('#__community_fields_values') . ' AS value '
 				. ' ON field.'.$db->nameQuote('id').'=value.'.$db->nameQuote('field_id').' AND value.'.$db->nameQuote('user_id').'=' . $db->Quote($userId)
				. ' WHERE field.'.$db->nameQuote('published').'=' . $db->Quote('1') . ' AND '
 				. ' field.'.$db->nameQuote('visible').'>=' . $db->Quote('1');
 		
 		// Build proper query for multiple profile types.
		if( $profileType != COMMUNITY_DEFAULT_PROFILE )
		{
			$query2	= 'SELECT '.$db->nameQuote('field_id').' FROM ' . $db->nameQuote( '#__community_profiles_fields' )
					. ' WHERE ' . $db->nameQuote( 'parent' ) . '=' . $db->Quote( $profileType );
			$db->setQuery( $query2 );
			$filterIds	= $db->loadResultArray();

			if( empty( $filterIds ) )
			{
				$data['fields']	= array();
				return $data;
			}
			
			$query	.= ' AND field.'.$db->nameQuote('id').' IN (' . implode( ',' , $filterIds ) . ')';
		}
		
		$query	.= ' ORDER BY field.'.$db->nameQuote('ordering');
		
		$db->setQuery( $query );

		$result	= $db->loadAssocList();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

		//let's check the viewer's relation to the profile he/she's about to see
		$visitor = CFactory::getUser($myid);
		$access_limit = 0;
		
    		$isfriend = $visitor->isFriendWith($user->id);
		
		//let's set the maximum access limit viewer can go
		if($visitor->id > 0){
			$access_limit = PRIVACY_MEMBERS;
		}
		
		if($isfriend){
			$access_limit = PRIVACY_FRIENDS;
		}
		
		if($visitor->id == $user->id && $visitor->id != 0){ 
			$access_limit = PRIVACY_PRIVATE;
		}
		//=====================================

		$data['fields']	= array();
		for($i = 0; $i < count($result); $i++){

			// We know that the groups will definitely be correct in ordering.			
			if($result[$i]['type'] == 'group') {
				$group	= $result[$i]['name'];
				$this->_getResultData($data, $result, $i, $group);
			}
			
			// Re-arrange options to be an array by splitting them into an array
			$this->_getArrangedOptions($result, $i);

			// Only append non group type into the returning data as we don't
			// allow users to edit or change the group stuffs.
			if($result[$i]['type'] != 'group'){
				if($result[$i]['access'] <= $access_limit){ //check privacy access here
					if(!isset($group))
						$data['fields']['ungrouped'][]	= $result[$i];
					else
						$data['fields'][$group][]	= $result[$i];
				}
			}
		}
		
		$name = $data['name'];
		$email = $data['email'];
	foreach($data['fields']['Basic Information'] as $single){
			$single['display'][] = array('mandatory'=>$single['required'],'fieldlength'=>'');
			$basicinfo[] = array('display'=>$single['name'],'value'=>$single['value'],'access'=>$single['access'],'fieldType'=>$single['type'],'validation'=>$single['display']);
		}
	  foreach($data['fields']['Contact Information'] as $single){
	  	   $single['display'][] = array('mandatory'=>$single['required'],'fieldlength'=>'');
			$contactinfo[] =  array('display'=>$single['name'],'value'=>$single['value'],'access'=>$single['access'],'fieldType'=>$single['type'],'validation'=>$single['display']);
		}
	  foreach($data['fields']['About Me'] as $single){
	  	  $single['display'][] = array('mandatory'=>$single['required'],'fieldlength'=>'');
			$aboutme[] =  array('display'=>$single['name'],'value'=>$single['value'],'access'=>$single['access'],'fieldType'=>$single['type'],'validation'=>$single['display']);
		}
		
		
		$friendquery = "select count(*) from #__community_connection where connect_from =".$db->quote($myid)." AND connect_to =".$db->quote($userId)." AND status = 1";
	    $db->setQuery($friendquery);
		$friendcount = $db->loadResult();

//$isfriendchk = CFriendsHelper::isConnected ( $userId, $myid );
 $friendquery = "select count(*) from #__community_connection where connect_from =".$db->quote($myid)." AND connect_to =".$db->quote($userId)." AND status = 1";
	    	$db->setQuery($friendquery);
			$isfriend = $db->loadResult();

//if($isfriendchk){
//$isfriend="1";
//}else{
//$isfriend="0";
//}
		
		$blockedquery = "select count(*) from #__community_blocklist where userid =".$db->quote($myid)." AND blocked_userid  =".$db->quote($userId);
		$db->setQuery($blockedquery);
		$blockedcount = $db->loadResult();
		
		 $sessionvalidationr[] = array('mandatory'=>'');
		 $sessionidr =  array('display'=>'','value'=>$userId,'access'=>'','fieldType'=>'field','validation'=>$sessionvalidationr);
		 
		 $profileimgvalidationr[] = array('mandatory'=>'');
		 $profileimgr =  array('display'=>'Profile Image','value'=>$profileimg,'access'=>'','fieldType'=>'field','validation'=>$profileimgvalidationr);

		 $emailvalidationr[] = array('mandatory'=>'');
		 $emailr =  array('display'=>'Email Address','value'=>$email,'access'=>'','fieldType'=>'field','validation'=>$emailvalidationr);
		 
		 $namevalidationr[] = array('mandatory'=>'');
		 $namer =  array('display'=>'Name','value'=>$name,'access'=>'','fieldType'=>'field','validation'=>$validationr);
		
		 $user		=& CFactory::getUser($userId);
	 	 
	 	// get total group
		$groupsModel	= CFactory::getModel( 'groups' );
		$totalgroups    = $groupsModel->getGroupsCount( $user->id );

		// get total friend
		$friendsModel = CFactory::getModel('friends');
		$totalfriends = $this->getmyfriends($user->id);
		
		// get total photos
		$photosModel	= CFactory::getModel('photos');
		$totalphotos    = $photosModel->getPhotosCount( $user->id );

		// get total activities
		$activitiesModel = CFactory::getModel('activities');
		$totalactivities = $activitiesModel->getActivityCount( $user->id );
		if($totalactivities == ""){
			$totalactivities = 0;
		}
		
		$profile_thumb = $user->_thumb;
//		if($profile_thumb == ""){
//			$profile_thumb= "components/com_mobileapp/assets/noimage.png";
//		}
//		$profile_avatar = $user->_avatar;
//		if($profile_thumb == ""){
//			$profile_thumb= "components/com_mobileapp/assets/noimage.png";
//		}

	if($profile_thumb == ""){
			$profile_thumb= JURI::root()."components/com_mobileapp/assets/noimage.png";
		}else{
			$profile_thumb= JURI::root().$profile_thumb;
		}
		
		$profile_avatar = $user->_avatar;
		if($profile_avatar == ""){
			$profile_avatar= JURI::root()."components/com_mobileapp/assets/noimage.png";
		}else{
			$profile_avatar= JURI::root().$profile_avatar;
		}
		
	   $myparams = explode(PHP_EOL,$user->_cparams->_raw);
		foreach($myparams as $myparam){
			$param = explode('=',$myparam);
		  if($param[0]=="profileVideo"){
				$profile_videoid = $param[1];
			}
		  if($param[0]=="privacyPhotoView"){
				$privacyphoto = $param[1];
			}
		  if($param[0]=="privacyVideoView"){
				$privacyvideo = $param[1];
			}
			
		}
	   $query_video = "select path,thumb from #__community_videos where id=".$db->quote($profile_videoid);
	   $db->setQuery($query_video);
	   $result_video = $db->loadObject();
		
	   //get video image path
	   if($result_video->thumb != ""){
	   	$pos = strpos($result_video->thumb, "http");
	   	
	   	if($pos===false){
	    $video_thumb = JURI::root().$result_video->thumb;
	   	}else{
	   			$video_thumb =$result_video->thumb;
	   	 
	   	}
	   }
	   //get video path
	   if($result_video->path != ""){
	   	$pos = strpos($result_video->path, "http");
	   	
	   	if($pos===false){
	    $video_path = JURI::root().$result_video->path;
	   	}else{
	   			$video_path =$result_video->path;
	   	 
	   	}
	   }
	   
		
		 $params = $user->_cparams->_raw;
      	 $params_array = explode(PHP_EOL,$params);
      	 $profilelike =1;
      	 foreach($params_array as $param){
      	 	  $param1 = explode("=",$param);
      	 	  if($param1[0] == "profileLikes"){
      	 	  	$profilelike = $param1[1];
      	 	  }
      	 }
     
		 if($profilelike==0){
		 	$canlike = "0";
		 }
		 else{
		 	$canlike = "1";
		 }
		 $like_unlike_status = $this->getuserlikecount($userId,'profile',$userId);
		 $islike=$like_unlike_status['islike'];
	     $isdislike=$like_unlike_status['isdislike'];
	     $videocount = $this->getVideosCount( $userId, $videoType = VIDEO_USER_TYPE );
		 $val_count = $this->getlikecount($userId,'profile');
		
		 $result = array('basicinfo'=>$basicinfo,'contactinfo'=>$contactinfo,'aboutme'=>$aboutme,'uid'=>$userId,'username'=>$user->username,'likecount'=>$val_count['likecount'],
	 'dislikecount'=>$val_count['dislikecount'],'islike'=>$islike,'isdislike'=>$isdislike,'canlike'=>$canlike,
	 'activitycount'=>$totalactivities,'groupcount'=>$totalgroups,'friendcount'=>$totalfriends,'photocount'=>$totalphotos,'videocount'=>$videocount,
	                   'isfriend'=>(string)$isfriend,'isblocked'=>$blockedcount,'profile_thumb'=>$profile_thumb,'profile_avatar'=>$profile_avatar,
		 'video'=>$video_path,'video_thumb'=>$video_thumb,'photoprivacy'=>$privacyphoto,'videoprivacy'=>$privacyvideo);
		 
		
		  
		 $info = array('errorCode'=>JText::_('ERRCODE_SUCCESS'),'result'=>$result,'message'=>JText::_('MSG_SUCCESS'));
	     
		
	return $info;
		
		//$info = array('name'=>$name,'email'=>$email,'basicinfo'=>$basicinfo,'contactinfo'=>$contactinfo,'aboutme'=>$aboutme,'isfriend'=>$friendcount,'isblocked'=>$blockedcount);
		//return $info;
    }
       
		
		  private function _getResultData(&$data, &$result, &$i, &$group){
				
				if(!isset($data['fields'][$group])){
					// Initialize the groups.
					$data['fields'][$group]	= array();
				}
	                  
         }
           private function _getArrangedOptions(&$result, &$i){

            if(isset($result[$i]['options']) && $result[$i]['options'] != '')
            {
                $options	= $result[$i]['options'];
                $options	= explode("\n", $options);

                array_walk($options, array( 'JString' , 'trim' ) );

                $result[$i]['options']	= $options;
            }

          }
          
          public function getlikecount($like_uid,$element)
	{ 
		
		$db		=& $this->getDBO();
		
		$like_type = $element;
		$like_id = $like_uid;
		
		$sql = "select * from #__community_likes where uid=".$db->quote($like_id)." and element =".$db->quote($like_type);
		
		$query = $db->setQuery($sql);
		$result = $db->loadObject();
		
	    $like = $result->like;
	    $dislike = $result->dislike;
	    
	 if($like==""){
	    	$like = 0;
	    }
	    else{
	    $likearray = explode(",",$like);
	    $like = count($likearray);
	    }
	    
	 if($dislike==""){
	    	$dislike = 0;
	    }
	    else{
	    $dislikearray = explode(",",$dislike);
	    $dislike = count($dislikearray);
	    }
	    
	    $result1 = array('likecount'=>$like,'dislikecount'=>$dislike);
	    return $result1;
	 }       

	  public function getuserlikecount($like_uid,$element,$uid)
	  { 
		$db		=& $this->getDBO();
		$like_type = $element;
		$like_id = $like_uid;
		$sql = "select * from #__community_likes where uid=".$db->quote($like_id)." and element =".$db->quote($like_type);
		$query = $db->setQuery($sql);
		$result = $db->loadObject();
		$like = $result->like;
	    $dislike = $result->dislike;
	 if($like==""){
	    	$likearray = array();
	    }
	    else{
	    $likearray = explode(",",$like);
	    $like = count($likearray);
	    }
	    
	   if (in_array($uid, $likearray)) {
         $like_status = "true";
	    }else{
	    	 $like_status = "false";
	    }
	
	 if($dislike==""){
	    	$dislikearray = array();
	    }
	    else{
	    $dislikearray = explode(",",$dislike);
	    $dislike = count($dislikearray);
	    }
	    
	   if (in_array($uid, $dislikearray)) {
         $dislike_status = "true";
	    }else{
	    	 $dislike_status = "false";
	    }
	    
	    $result2 = array('islike'=>$like_status,'isdislike'=>$dislike_status);
	    return $result2;
	 }       
	 
	public function getVideosCount( $userId, $videoType = VIDEO_USER_TYPE )
	{
		if ($userId==0) return 0;
		
		$db		= $this->getDBO();
		
		$query	= 'SELECT COUNT(1) FROM ' 
				. $db->nameQuote( '#__community_videos' ) . ' AS a '
				. 'WHERE ' . $db->nameQuote('creator').'=' . $db->Quote( $userId ) . ' '
				. 'AND ' . $db->nameQuote('creator_type').'=' . $db->Quote( $videoType ).' '
				. 'AND status = '.$db->quote('ready');
				
				
		
		$db->setQuery( $query );
		$count	= $db->loadResult();
		
		return $count;
	}     
          
           /**
	  * Check for user login session
	  * @param user login session
	  * @return string
	  */
      function checksession($usession){
				//get db object
				$db = JFactory::getDBO();
				$query = "select userid from jos_session where 	session_id = ".$db->Quote($usession);
				$db->setQuery($query);
				$db->query();
				$resultid = $db->loadResult();
				
				if($resultid == ""){
					return false;
				}
				else{
//update session table with current time
			//get current time
			$current_time = time();
			$query = "Update #__session set time = ".$db->quote($current_time)." where session_id = ".$db->quote($usession);
			$db->setQuery($query);
			$db->query();
			//return userid
					return $resultid;
				}
				
			}

public function getmyfriends($id)
        {
        	   $db	= JFactory::getDBO();
            $query	= 'SELECT DISTINCT(a.'. $db->nameQuote('connect_to').') AS '. $db->nameQuote('id')
					.' FROM ' . $db->nameQuote('#__community_connection') . ' AS a '
					.' INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS b '
					.' ON a.'. $db->nameQuote('connect_from').'=' . $db->Quote( $id ) . ' '
					.' AND a.'. $db->nameQuote('connect_to').'=b.'. $db->nameQuote('id')
					.' AND b.'. $db->nameQuote('block').'!= 1'
					.' AND a.'. $db->nameQuote('status').'=' . $db->Quote( 1 );
            $db->setQuery( $query );
            
			$friends	= $db->loadResultArray();
			//check for block user
foreach($friends as $friend){
$query = "select count(id) from #__community_blocklist where userid = ".$db->Quote($id)." and blocked_userid = ".$db->Quote($friend);
$db->setQuery($query);
$db->query();
$result = $db->loadResult();
if($result == 0){
$friendscount[] = array($friend);
}
}

			return count($friendscount);
        }

	
    

}
