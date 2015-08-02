<?php
/**
 * @category	Model
 * @package		Mobileapp
 * @subpackage	sharevideo
 * @copyright (C) 2013 by Daffodil Abhishek - All rights reserved!
 * @license		GNU/GPL
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS .'libraries' . DS . 'activities.php' );
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS .'libraries' . DS . 'limits.php' );
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'helpers' . DS . 'owner.php' );

class MobileappModelSharevideo extends JModel
{
	/**
	 * method to share video
	 * @params form data
	 * @return array
	 */
	function sharevideo($data)
	{ 
		// Check for request forgeries
		
		$myid = $data['sessionid'];
		
		 //check for user login
	    $chksession = $this->checksession($myid);
	    if(!$chksession){
	    	 $error = array('errorCode'=>JText::_('ERRCODE_SESSION_DESTROYED_LOGIN_AGAIN'),'result'=>'','message'=>JText::_('MSG_SESSION_DESTROYED_LOGIN_AGAIN'));
       		 return $error;
       		 exit;
	    }
	    else{
	    	 $myid = $chksession;
	    	  $data['sessionid'] = $chksession;
	    }
	    
	    //end to check session
	    
		$message = $data['message'];
		$this->checkVideoAccess();
		
		$document	= JFactory::getDocument();
		$viewType	= $document->getType();
	
		
		// Preset the redirect url according to group type or user type
		CFactory::load('helpers' , 'videos');
		$mainframe	= JFactory::getApplication();
		$redirect	= CVideosHelper::getVideoReturnUrlFromRequest();
		$my			= CFactory::getUser($myid);
	if($my->id == 0)
		{
		  $error = array('errorCode'=>JTEXT::_('ERRCODE_ACCESS_FORBIDDEN'),'result'=>'','message'=>JTEXT::_('MSG_ACCESS_FORBIDDEN'));
		  return $error;
		}
		// @rule: Do not allow users to add more videos than they are allowed to
		//CFactory::load( 'libraries' , 'limits' );
		
		
			
		// Without CURL library, there's no way get the video information
		// remotely
		CFactory::load('helpers', 'remote');
		if (!CRemoteHelper::curlExists())
		{
			 $error = array('errorCode'=>JTEXT::_('ERRCODE_CURL_ERROR'),'result'=>'','message'=>JTEXT::_('MSG_CURL_ERROR'));
		    return $error;
		    exit;
		}
		
		
		// Determine if the video belongs to group or user and
		// assign specify value for checking accordingly
		$config			= CFactory::getConfig();
		$creatorType	= JRequest::getVar( 'creatortype' , VIDEO_USER_TYPE );
		$groupid 		= ($creatorType==VIDEO_GROUP_TYPE)? JRequest::getInt( 'groupid' , 0 ) : 0;
		list($creatorType, $videoLimit)	= $this->_manipulateParameter($groupid, $config);
		$group		=& JTable::getInstance( 'Group' , 'CTable' );

		$group->load( $groupid );
		if($group->approvals)
		{  
			$permission = 40;
		}
		$permission             = JRequest::getVar( 'permissions', '', 'POST' );
		
		// Do not allow video upload if user's video exceeded the limit
		CFactory::load('helpers' , 'limits' );
		$my = CFactory::getUser($myid);
		if(CLimitsHelper::exceededVideoUpload($my->id, $creatorType))
		{
			
			$error = array('errorCode'=>JTEXT::_('ERRCODE_VIDEOS_CREATION_LIMIT_ERROR'),'result'=>'','message'=>JTEXT::_('MSG_VIDEOS_CREATION_LIMIT_ERROR'));
		    return $error;
		    exit;
		}
		
		// Create the video object and save
		//$videoUrl = JRequest::getVar( 'videoLinkUrl' , '' );
		$videoUrl = $data['videolink'];
		if(empty($videoUrl))
		{
			
			$error = array('errorCode'=>JTEXT::_('ERRCODE_INVALID_VIDEO_LINKS'),'result'=>'','message'=>JTEXT::_('MSG_INVALID_VIDEO_LINKS'));
		    return $error;
		    exit;	
		}
		CFactory::load('libraries', 'videos');
		$videoLib 	= new CVideoLibrary();


		CFactory::load( 'models' , 'videos' );
		$video	= JTable::getInstance( 'Video' , 'CTable' );
		$isValid = $video->init( $videoUrl );
		
		if (!$isValid )
		{
			$error = array('errorCode'=>JTEXT::_('ERRCODE_NO_VIDEO_FOUND'),'result'=>'','message'=>JTEXT::_('MSG_NO_VIDEO_FOUND'));
		    return $error;
		    exit;	
		}
		

		$video->set('creator',		$my->id);
		$video->set('creator_type',	$creatorType);
		$video->set('permissions',	$permission);
		$video->set('category_id',	JRequest::getVar( 'category_id' , '1' , 'POST' ));
		$video->set('location',		JRequest::getVar( 'location' , '' , 'POST' ));
		$video->set('groupid',		$groupid);
		
		if (!$video->store())
		{
			$error = array('errorCode'=>JTEXT::_('ERRCODE_VIDEOS_ADD_LINK_FAILED'),'result'=>'','message'=>JTEXT::_('MSG_VIDEOS_ADD_LINK_FAILED'));
		    return $error;
		    exit;	
		   
		}
		//add notification: New group album is added
		if($video->groupid != 0){
			CFactory::load('libraries','notification');
			$group			=& JTable::getInstance( 'Group' , 'CTable' );
			$group->load( $video->groupid );

			$modelGroup			=& $this->getModel( 'groups' );
			$groupMembers		= array();
			$groupMembers 		= $modelGroup->getMembersId($video->groupid, true );

			$params			= new CParameter( '' );
			$params->set( 'title' , $video->title );
			$params->set('group' , $group->name );
			$params->set('group_url' , 'index.php?option=com_community&view=groups&task=viewgroup&groupid='.$group->id );
			$params->set('video' , $video->title );
			$params->set('video_url' , 'index.php?option=com_community&view=videos&task=videos&groupid='.$group->id.'&videoid='.$video->id );
			$params->set( 'url', 'index.php?option=com_community&view=videos&task=video&groupid='.$group->id.'&videoid='.$video->id);
			CNotificationLibrary::add( 'groups_create_video' , $my->id , $groupMembers , JText::sprintf('COM_COMMUNITY_GROUP_NEW_VIDEO_NOTIFICATION') , '' , 'groups.video' , $params);
		
		}

		// Trigger for onVideoCreate
		$this->_triggerEvent( 'onVideoCreate' , $video );
		
		// Fetch the thumbnail and store it locally, 
		// else we'll use the thumbnail remotely
		CError::assert($video->thumb, '', '!empty');
		$this->_fetchThumbnail($video->id);
		
		// Add activity logging
		$url	= $video->getViewUri(false);
		
		$act			= new stdClass();
		$act->cmd 		= 'videos.upload';
		$act->actor		= $my->id;
		$act->access            = $video->permissions;
		$act->target            = 0;
		$act->title		= JText::_('COM_COMMUNITY_ACTIVITIES_UPLOAD_VIDEO'); // since 2.4, sharing video will hide the title subject
		$act->app		= 'videos';
		$act->content           = '';
		$act->cid		= $video->id;
		$act->location          = $video->location;
		
		$act->comment_id 	= $video->id;
		$act->comment_type 	= 'videos';
		
		$act->like_id           = $video->id;
		$act->like_type         = 'videos';

                $act->groupid           = ($video->groupid != 0) ? $video->groupid : 0;
		
		$params = new CParameter('');
		$params->set( 'video_url', $url );
		
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add( $act , $params->toString() );
		
		// @rule: Add point when user adds a new video link
		CFactory::load( 'libraries' , 'userpoints' );
		CUserPoints::assignPoint('video.add', $video->creator);

		
		$type="video";
		$id=$video->id;
		$privacy=$data['access'];
		$target=$uid;
		$element="profile";
		$filter="active-profile";
		$attachment = array('type'=>$type,'id'=>$id,'privacy'=>$privacy,'target'=>$target,'element'=>$element,'filter'=>$filter);
		$result = $this->ajaxStreamAdd($message, $attachment,$my->id);
		
		
		//$this->cacheClean(array(COMMUNITY_CACHE_TAG_VIDEOS,COMMUNITY_CACHE_TAG_FRONTPAGE,COMMUNITY_CACHE_TAG_FEATURED,COMMUNITY_CACHE_TAG_VIDEOS_CAT,COMMUNITY_CACHE_TAG_ACTIVITIES,COMMUNITY_CACHE_TAG_GROUPS_DETAIL));

		// Redirect user to his/her video page
		//$message		= JText::sprintf('COM_COMMUNITY_VIDEOS_UPLOAD_SUCCESS', $video->title);
		//$mainframe->redirect( $redirect , $message );
	
		$error = array('errorCode'=>JTEXT::_('ERRCODE_SUCCESS'),'result'=>'true','message'=>JTEXT::_('MSG_SUCCESS'));
		return $error;
		    
	}
	
	public function ajaxStreamAdd($message, $attachment,$myid)
	{
		
		//$filter = JFilterInput::getInstance();
		//$message = $filter->clean($message, 'string');
		$streamHTML = '';
		// $attachment pending filter

		$cache	= CFactory::getFastCache();
		$cache->clean(array('activities'));

		$my = CFactory::getUser($myid);

		

		CFactory::load('libraries', 'activities');
		CFactory::load('libraries', 'userpoints');
		CFactory::load('helpers', 'linkgenerator');
		CFactory::load( 'libraries' , 'notification' );
                
		//@rule: In case someone bypasses the status in the html, we enforce the character limit.
		$config		= CFactory::getConfig();
		if( JString::strlen( $message ) > $config->get('statusmaxchar') )
		{
			$message	= JString::substr( $message , 0 , $config->get('statusmaxchar') );
		}

		$message	= JString::trim($message);
		
		//$message	= CStringHelper::escape($message);
		//$inputFilter = CFactory::getInputFilter(true);
		//$message = $inputFilter->clean($message);

		$objResponse	= new JAXResponse();
		
		$rawMessage	= $message;
		
		// @rule: Autolink hyperlinks
		$message	= CLinkGeneratorHelper::replaceURL( $message );
		
		// @rule: Autolink to users profile when message contains @username
		$message	= CLinkGeneratorHelper::replaceAliasURL( $message );
		$emailMessage	= CLinkGeneratorHelper::replaceAliasURL( $rawMessage, true );

		// @rule: Spam checks
		if( $config->get( 'antispam_akismet_status') )
		{
			CFactory::load( 'libraries' , 'spamfilter' );

			$filter	= CSpamFilter::getFilter();
			$filter->setAuthor( $my->getDisplayName() );
			$filter->setMessage( $message );
			$filter->setEmail( $my->email );
			$filter->setURL( CRoute::_('index.php?option=com_community&view=profile&userid=' . $my->id ) );
			$filter->setType( 'message' );
			$filter->setIP( $_SERVER['REMOTE_ADDR'] );

			if( $filter->isSpam() )
			{
				return "spam";
				exit;
			}
		}

		//$attachment	= json_decode($attachment, true);

		//respect wall setting before adding activity
		CFactory::load('helpers' , 'friends' );
		CFactory::load('helper', 'owner');
		
		// @todo: move permission checking based on the $attachment['element']
		/*
		if (!COwnerHelper::isCommunityAdmin() 
				&& isset($attachment['target']) 
				&& $config->get('lockprofilewalls') 
				&& !CFriendsHelper::isConnected( $my->id , $attachment['target'] )
				)
		 * 
		 */
		{
			//$objResponse->addScriptCall("alert('permission denied');");
			//return $objResponse->sendResponse();
		}

		/*
		$attachment['type'] = The content type, message/videos/photos/events
		$attachment['element'] = The owner, profile, groups,events
		
		*/
		
		switch($attachment['type'])
		{
			case "message":

				if(!empty($message))
				{
					switch( $attachment['element'] )
					{
					
						case 'profile':
							//only update user status if share messgage is on his profile
							if (COwnerHelper::isMine($my->id,$attachment['target']))
							{
								
								//save the message
								$status		=& $this->getModel('status');
								$status->update($my->id, $rawMessage, $attachment['privacy'] );
			
								//set user status for current session.
								$today		=& JFactory::getDate();
								$message2	= (empty($message)) ? ' ' : $message;
								$my->set( '_status' , $rawMessage );
								$my->set( '_posted_on' , $today->toMySQL());
								
								// Order of replacement
								$order   = array("\r\n", "\n", "\r");
								$replace = '<br />';
		
								// Processes \r\n's first so they aren't converted twice.
								$messageDisplay = str_replace($order, $replace, $message);
								$messageDisplay = CKses::kses($messageDisplay, CKses::allowed() );
								
								//update user status
								$objResponse->addScriptCall("joms.jQuery('#profile-status span#profile-status-message').html('" . addslashes( $messageDisplay ) . "');");
							}
		
							//push to activity stream
							$privacyParams	= $my->getParams();
							$act = new stdClass();
							$act->cmd          = 'profile.status.update';
							$act->actor        = $my->id;
							$act->target       = $attachment['target'];							
							$act->title			= $message;
							$act->content	   = '';
							$act->app		   = $attachment['element'];
							$act->cid		   = $my->id;
							$act->access	   = $attachment['privacy'];
							$act->comment_id   = CActivities::COMMENT_SELF;
							$act->comment_type = 'profile.status';
							$act->like_id 	   = CActivities::LIKE_SELF;
							$act->like_type    = 'profile.status';
		
							CActivityStream::add($act);
							CUserPoints::assignPoint('profile.status.update');
		
							$recipient = CFactory::getUser($attachment['target']);
							$params			= new CParameter( '' );
							$params->set( 'actorName' , $my->getDisplayName() );
							$params->set( 'recipientName', $recipient->getDisplayName());
							$params->set('url',CUrlHelper::userLink($act->target, false));
							$params->set('message',$message);

							CNotificationLibrary::add( 'profile_status_update' , $my->id , $attachment['target'] , JText::sprintf('COM_COMMUNITY_FRIEND_WALL_POST', $my->getDisplayName() ) , '' , 'wall.post' , $params);
						break;
							
						// Message posted from Group page
		            	case 'groups':
                                                        CFactory::load('libraries', 'groups');
							$groupLib	= new CGroups();
							$group		= JTable::getInstance( 'Group' , 'CTable' );
							$group->load( $attachment['target'] );
							
							// Permission check, only site admin and those who has
							// mark their attendance can post message
							if( !COwnerHelper::isCommunityAdmin() && !$group->isMember($my->id) )
							{
								return "denied";
								exit;
							}
							
							$act = new stdClass();
							$act->cmd          = 'groups.wall';
							$act->actor        = $my->id;
							$act->target       = 0;
							
							$act->title			= $message;
							$act->content	   = '';
							$act->app		   = 'groups.wall';
							$act->cid		   = $attachment['target'];						
							$act->groupid		= $group->id;
							$act->group_access	= $group->approvals;
							$act->eventid		= 0;
							$act->access	   = 0;
							$act->comment_id   = CActivities::COMMENT_SELF;
							$act->comment_type = 'groups.wall';
							$act->like_id 	   = CActivities::LIKE_SELF;
							$act->like_type    = 'groups.wall';
		
							CActivityStream::add($act);
							CUserPoints::assignPoint('profile.status.update');

							$recipient  = CFactory::getUser($attachment['target']);
							$params	    = new CParameter( '' );
							$params->set( 'message' , $emailMessage );
							$params->set( 'group', $group->name);
							$params->set( 'group_url', 'index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $group->id);
							$params->set( 'url' , CRoute::getExternalURL('index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $group->id, false ));
							
							//Get group member emails
							$model		= CFactory::getModel( 'Groups' );
							$members	= $model->getMembers( $attachment['target'] , null );
							
							$membersArray = array();
							if(!is_null($members)){
								foreach($members as $row)
								{
									if( $my->id != $row->id )
									{
										$membersArray[] = $row->id;
									}
								}
							}
						
		                    CNotificationLibrary::add( 'groups_wall_create' , $my->id , $membersArray , JText::sprintf('COM_COMMUNITY_NEW_WALL_POST_NOTIFICATION_EMAIL_SUBJECT',$my->getDisplayName() , $group->name ),'' , 'groups.post' , $params);
							
							// Add custom stream
							// Reload the stream with new stream data
							$streamHTML = $groupLib->getStreamHTML($group);
							
		            		break;
						
						// Message posted from Event page
						case 'events' :
							CFactory::load('libraries', 'events');
							$eventLib	= new CEvents();
							$event		= JTable::getInstance( 'Event' , 'CTable' );
							$event->load( $attachment['target'] );
							
							// Permission check, only site admin and those who has
							// mark their attendance can post message
							if( !COwnerHelper::isCommunityAdmin() 
									&& !$event->isMember($my->id) )
							{
								$error = array('errorCode'=>JText::_('ERRCODE_ACCESS_FORBIDDEN'),'result'=>'','message'=>JText::_('MSG_ACCESS_FORBIDDEN'));
	           					return $error;
								exit;
							}
							
							// If this is a group event, set the group object
							$groupid = ($event->type == 'group') ? $event->contentid : 0;
							CFactory::load('libraries', 'groups');
							$groupLib	= new CGroups();
							$group		= JTable::getInstance( 'Group' , 'CTable' );
							$group->load( $groupid );
							
							
							$act = new stdClass();
							$act->cmd               = 'events.wall';
							$act->actor             = $my->id;
							$act->target            = 0;
							$act->title             = $message;
							$act->content           = '';
							$act->app               = 'events.wall';
							$act->cid               = $attachment['target'];
							$act->groupid			= ($event->type == 'group') ? $event->contentid : 0;
							$act->group_access		= $group->approvals;
							$act->eventid			= $event->id;
							$act->event_access		= $event->permission;
							$act->access            = 0;
							$act->comment_id        = CActivities::COMMENT_SELF;
							$act->comment_type      = 'events.wall';
							$act->like_id           = CActivities::LIKE_SELF;
							$act->like_type         = 'events.wall';

							CActivityStream::add($act);
							
							// Reload the stream with new stream data
							$streamHTML = $eventLib->getStreamHTML($event);
							break;

					}
                    
                                        $objResponse->addScriptCall('__callback', '');
				}

				break;

			case "photo":
				switch( $attachment['element'] )
					{
					
						case 'profile':
							$photoId = $attachment['id'];
							$privacy = $attachment['privacy'];

							$photo	= JTable::getInstance('Photo', 'CTable');
							$photo->load($photoId);

							$photo->caption = $message;				
							$photo->permissions = $privacy;
							$photo->published = 1;
							$photo->status = 'ready';
							$photo->store();

							// Trigger onPhotoCreate
							CFactory::load( 'libraries' , 'apps' );
							$apps =& CAppPlugins::getInstance();
							$apps->loadApplications();
							$params = array();
							$params[] = &$photo;
							$apps->triggerEvent( 'onPhotoCreate' , $params );

							$album	= JTable::getInstance('Album', 'CTable');
							$album->load($photo->albumid);

							$act = new stdClass();
							$act->cmd 		= 'photo.upload';
							$act->actor   	= $my->id;
							$act->access	= $attachment['privacy'];
							$act->target  	= ($attachment['target']==$my->id) ? 0 : $attachment['target'];
							$act->title	  	= $message; //JText::sprintf('COM_COMMUNITY_ACTIVITIES_UPLOAD_PHOTO' , '{photo_url}', $album->name );
							$act->content	= ''; // Generated automatically by stream. No need to add anything 
							$act->app		= 'photos';
							$act->cid		= $album->id;
							$act->location	= $album->location;
							
							/* Comment and like for individual photo upload is linked
							 * to the photos itsel
							 */
							$act->comment_id   = $photo->id; //CActivities::COMMENT_SELF;
							$act->comment_type = 'photos';
							$act->like_id 	   = $photo->id; //CActivities::LIKE_SELF;
							$act->like_type    = 'photo';  // like type is 'photo' not 'photos'

							$albumUrl	= 'index.php?option=com_community&view=photos&task=album&albumid=' . $album->id .  '&userid=' . $my->id;
							$albumUrl	= CRoute::_($albumUrl);

							$photoUrl	= 'index.php?option=com_community&view=photos&task=photo&albumid=' . $album->id .  '&userid=' . $photo->creator . '#photoid=' . $photo->id;
							$photoUrl	= CRoute::_($photoUrl);

							$params = new CParameter('');
							$params->set('multiUrl'	, $albumUrl );
							$params->set('photoid'	, $photo->id);
							$params->set('action'	, 'upload' );
							$params->set('stream'	, '1' ); // this photo uploaded from status stream
							$params->set('photo_url', $photoUrl );

							// Add activity logging
							CFactory::load ( 'libraries', 'activities' );
							CActivityStream::add( $act , $params->toString() );

							// Add user points
							CFactory::load( 'libraries' , 'userpoints' );
							CUserPoints::assignPoint('photo.upload');

							//$objResponse->addScriptCall('__callback', JText::sprintf('COM_COMMUNITY_PHOTO_UPLOADED_SUCCESSFULLY', $photo->caption));
							break;
						case 'groups':
							CFactory::load('libraries', 'groups');
							$groupLib	= new CGroups();
							$group		= JTable::getInstance( 'Group' , 'CTable' );
							$group->load( $attachment['target'] );
							
							$photoId = $attachment['id'];
							$privacy = $group->approvals ? PRIVACY_GROUP_PRIVATE_ITEM : 0;;

							$photo	= JTable::getInstance('Photo', 'CTable');
							$photo->load($photoId);

							$photo->caption = $message;				
							$photo->permissions = $privacy;
							$photo->published = 1;
							$photo->status = 'ready';
							$photo->store();

							// Trigger onPhotoCreate
							CFactory::load( 'libraries' , 'apps' );
							$apps =& CAppPlugins::getInstance();
							$apps->loadApplications();
							$params = array();
							$params[] = &$photo;
							$apps->triggerEvent( 'onPhotoCreate' , $params );

							$album	= JTable::getInstance('Album', 'CTable');
							$album->load($photo->albumid);

							$act = new stdClass();
							$act->cmd 		= 'photo.upload';
							$act->actor   	= $my->id;
							$act->access	= $privacy;
							$act->target  	= ($attachment['target']==$my->id) ? 0 : $attachment['target'];
							$act->title	  	= $message; //JText::sprintf('COM_COMMUNITY_ACTIVITIES_UPLOAD_PHOTO' , '{photo_url}', $album->name );
							$act->content	= ''; // Generated automatically by stream. No need to add anything 
							$act->app		= 'photos';
							$act->cid		= $album->id;
							$act->location	= $album->location;
							
							$act->groupid		= $group->id;
							$act->group_access	= $group->approvals;
							$act->eventid		= 0;
							//$act->access		= $attachment['privacy'];
							
							/* Comment and like for individual photo upload is linked
							 * to the photos itsel
							 */
							$act->comment_id   = $photo->id; //CActivities::COMMENT_SELF;
							$act->comment_type = 'photos';
							$act->like_id 	   = $photo->id; //CActivities::LIKE_SELF;
							$act->like_type    = 'photo';  // like type is 'photo' not 'photos'

							$albumUrl	= 'index.php?option=com_community&view=photos&task=album&albumid=' . $album->id .  '&userid=' . $my->id;
							$albumUrl	= CRoute::_($albumUrl);

							$photoUrl	= 'index.php?option=com_community&view=photos&task=photo&albumid=' . $album->id .  '&userid=' . $photo->creator . '#photoid=' . $photo->id;
							$photoUrl	= CRoute::_($photoUrl);

							$params = new CParameter('');
							$params->set('multiUrl'	, $albumUrl );
							$params->set('photoid'	, $photo->id);
							$params->set('action'	, 'upload' );
							$params->set('stream'	, '1' ); // this photo uploaded from status stream
							$params->set('photo_url', $photoUrl );

							// Add activity logging
							CFactory::load ( 'libraries', 'activities' );
							CActivityStream::add( $act , $params->toString() );

							// Add user points
							CFactory::load( 'libraries' , 'userpoints' );
							CUserPoints::assignPoint('photo.upload');
							
							// Reload the stream with new stream data
							$streamHTML = $groupLib->getStreamHTML($group);

							$objResponse->addScriptCall('__callback', JText::sprintf('COM_COMMUNITY_PHOTO_UPLOADED_SUCCESSFULLY', $photo->caption));
							
							break;
					}
					
				break;

			case "video":
				
				switch( $attachment['element'] )
				{
				case 'profile':
					// attachment id
					$cid	 = $attachment['id'];
					$privacy = $attachment['privacy'];

					$video	= JTable::getInstance('Video', 'CTable');
					$video->load($cid);
					$video->status	= 'ready';
					$video->permissions = $privacy;
					$video->store();

					// Add activity logging
					$url	= $video->getViewUri(false);

					$act			= new stdClass();
					$act->cmd 		= 'videos.upload';
					$act->actor		= $my->id;
					$act->target	= ($attachment['target']==$my->id) ? 0 : $attachment['target'];
					$act->access	= $privacy;
					
					//filter empty message
					$act->title		= JText::sprintf('COM_COMMUNITY_ACTIVITIES_UPLOAD_VIDEO_WITH_MSG', $message);
					$act->app		= 'videos';
					$act->content	= '';
					$act->cid		= $video->id;
					$act->location	= $video->location;

					$act->comment_id 	= $video->id;
					$act->comment_type 	= 'videos';

					$act->like_id 	= $video->id;
					$act->like_type	= 'videos';				

					$params = new CParameter('');
					$params->set( 'video_url', $url );

					CFactory::load ( 'libraries', 'activities' );
					CActivityStream::add( $act , $params->toString() );

					// @rule: Add point when user adds a new video link
					CFactory::load( 'libraries' , 'userpoints' );
					CUserPoints::assignPoint('video.add', $video->creator);

					//$this->cacheClean(array(COMMUNITY_CACHE_TAG_VIDEOS,COMMUNITY_CACHE_TAG_FRONTPAGE,COMMUNITY_CACHE_TAG_FEATURED,COMMUNITY_CACHE_TAG_VIDEOS_CAT,COMMUNITY_CACHE_TAG_ACTIVITIES));

					//->addScriptCall('__callback', JText::sprintf('COM_COMMUNITY_VIDEOS_UPLOAD_SUCCESS', $video->title));

					break;
				case 'groups':
										// attachment id
					$cid	 = $attachment['id'];
					$privacy = 0; //$attachment['privacy'];

					$video	= JTable::getInstance('Video', 'CTable');
					$video->load($cid);
					$video->status	= 'ready';
					$video->groupid = $attachment['target'];
					$video->permissions = $privacy;
					$video->store();
					
					CFactory::load('libraries', 'groups');
					$groupLib	= new CGroups();
					$group		= JTable::getInstance( 'Group' , 'CTable' );
					$group->load( $attachment['target'] );

					// Add activity logging
					$url	= $video->getViewUri(false);

					$act			= new stdClass();
					$act->cmd 		= 'videos.upload';
					$act->actor		= $my->id;
					$act->target	= ($attachment['target']==$my->id) ? 0 : $attachment['target'];
					$act->access	= $privacy;
					
					//filter empty message
					$act->title		= $message;
					$act->app		= 'videos';
					$act->content	= '';
					$act->cid		= $video->id;
					$act->groupid	= $video->groupid;
					$act->group_access	= $group->approvals;
					$act->location	= $video->location;

					$act->comment_id 	= $video->id;
					$act->comment_type 	= 'videos';

					$act->like_id 	= $video->id;
					$act->like_type	= 'videos';				

					$params = new CParameter('');
					$params->set( 'video_url', $url );

					CFactory::load ( 'libraries', 'activities' );
					CActivityStream::add( $act , $params->toString() );

					// @rule: Add point when user adds a new video link
					CFactory::load( 'libraries' , 'userpoints' );
					CUserPoints::assignPoint('video.add', $video->creator);

					$this->cacheClean(array(COMMUNITY_CACHE_TAG_VIDEOS,COMMUNITY_CACHE_TAG_FRONTPAGE,COMMUNITY_CACHE_TAG_FEATURED,COMMUNITY_CACHE_TAG_VIDEOS_CAT,COMMUNITY_CACHE_TAG_ACTIVITIES));
					
					$objResponse->addScriptCall('__callback', JText::sprintf('COM_COMMUNITY_VIDEOS_UPLOAD_SUCCESS', $video->title));

					// Reload the stream with new stream data
					$streamHTML = $groupLib->getStreamHTML($group);
					
					break;
				}
				
				break;
				
			case "event":

				switch( $attachment['element'] )
				{
					case 'profile':
						require_once(COMMUNITY_COM_PATH.DS.'controllers'.DS.'events.php');

						$eventController = new CommunityEventsController();

						// Assign default values where necessary
						$attachment['description'] = $message;
						$attachment['ticket']      = 0;
						$attachment['offset']      = 0;

						$event = $eventController->ajaxCreate($attachment, $objResponse);

						$objResponse->addScriptCall('__callback', '');

						break;

					case 'groups':
						
						require_once(COMMUNITY_COM_PATH.DS.'controllers'.DS.'events.php');

						$eventController = new CommunityEventsController();

						CFactory::load('libraries', 'groups');
						$groupLib	= new CGroups();
						$group		= JTable::getInstance( 'Group' , 'CTable' );
						$group->load( $attachment['target'] );

						// Assign default values where necessary
						$attachment['description'] = $message;
						$attachment['ticket']      = 0;
						$attachment['offset']      = 0;

						$event = $eventController->ajaxCreate($attachment, $objResponse);

						$objResponse->addScriptCall('__callback', '');

						// Reload the stream with new stream data
						$streamHTML = $groupLib->getStreamHTML($group);
						break;
				}
                                   
				break;

			case "link":
				break;
		}
                
		// If no filter specified, we can assume it is for all
		if(!isset($attachment['filter'])){
			$attachment['filter'] = '';
		}
		
		

		return "true";
	}
	
	
public function checkVideoAccess()
	{
		$mainframe	= JFactory::getApplication();
		$config		= CFactory::getConfig();
		
		if (!$config->get('enablevideos'))
		{
			$redirect	= CRoute::_('index.php?option=com_community&view=frontpage', false);
			$mainframe->redirect($redirect, JText::_('COM_COMMUNITY_VIDEOS_DISABLED'), 'warning');
		}
		return true;
	}
	private function _manipulateParameter($groupid, $config)
	{
		if (empty($groupid))
		{
			$creatorType		= VIDEO_USER_TYPE;
			$videoLimit			= $config->get('videouploadlimit');
		} else {
			CFactory::load('helpers', 'group');
			$allowManageVideos	= CGroupHelper::allowManageVideo($groupid);
			CError::assert($allowManageVideos, '', '!empty', __FILE__ , __LINE__ );
			
			$creatorType		= VIDEO_GROUP_TYPE;
			$videoLimit			= $config->get( 'groupvideouploadlimit' );
		}
		
		return array($creatorType, $videoLimit);
	}
	
private function _triggerEvent( $event , $args )
	{
		// Trigger for onVideoCreate
		CFactory::load( 'libraries' , 'apps' );
		$apps   =& CAppPlugins::getInstance();
		$apps->loadApplications();
		$params		= array();
		$params[]	= & $args;
		$apps->triggerEvent( $event , $params );
	}
public function _fetchThumbnail($id=0, $returnThumb=false)
	{
		if (!COwnerHelper::isRegisteredUser()) return;
		if (!$id) return false;
		
		CFactory::load('models', 'videos'); 
		$table = JTable::getInstance( 'Video' , 'CTable' );
		$table->load($id);
		
		CFactory::load('helpers', 'videos');
		CFactory::load('libraries', 'videos');
		$config	= CFactory::getConfig();
		
		if ($table->type=='file')
		{
			// We can only recreate the thumbnail for local video file only
			// it's not possible to process remote video file with ffmpeg
			if ($table->storage != 'file')
			{
				$this->setError(JText::_('COM_COMMUNITY_INVALID_FILE_REQUEST') . ': ' . 'FFmpeg cannot process remote video.');
				return false;
			}
			
			$videoLib	= new CVideoLibrary();
			
			$videoFullPath	= JPATH::clean(JPATH_ROOT.DS.$table->path);
			if (!JFile::exists($videoFullPath))
			{
				return false;
			}

			// Read duration
			$videoInfo	= $videoLib->getVideoInfo($videoFullPath);

			if (!$videoInfo)
			{
				return false;
			}
			else
			{
				$videoFrame = CVideosHelper::formatDuration( (int) ($videoInfo['duration']['sec'] / 2), 'HH:MM:SS' );
				
				// Create thumbnail
				$oldThumb		= $table->thumb;
				$thumbFolder	= CVideoLibrary::getPath($table->creator, 'thumb');
				$thumbSize		= CVideoLibrary::thumbSize();
				$thumbFilename	= $videoLib->createVideoThumb($videoFullPath, $thumbFolder, $videoFrame, $thumbSize);
			}
			
			if (!$thumbFilename)
			{
				return false;
			}
		}
		else
		{
			CFactory::load('helpers', 'remote' );
			if (!CRemoteHelper::curlExists())
			{
				$this->setError(JText::_('COM_COMMUNITY_CURL_NOT_EXISTS'));
				return false;
			}
			
			$videoLib 	= new CVideoLibrary();
			$videoObj 	= $videoLib->getProvider($table->path);
			if ($videoObj==false)
			{
				$this->setError($videoLib->getError());
				return false;
			}
			if (!$videoObj->isValid())
			{
				$this->setError($videoObj->getError());
				return false;
			}
			
			$remoteThumb	= $videoObj->getThumbnail();
			$thumbData		= CRemoteHelper::getContent($remoteThumb , true );
			
			if (empty($thumbData))
			{
				$this->setError(JText::_('COM_COMMUNITY_INVALID_FILE_REQUEST') . ': ' . $remoteThumb);
				return false;
			}
			
			// split the header and body
			list( $headers, $body )	= explode( "\r\n\r\n", $thumbData, 2 );
			preg_match( '/Content-Type: image\/(.*)/i' , $headers , $matches );
			
			if( !empty( $matches) )
			{
				CFactory::load('helpers', 'file' );
				CFactory::load('helpers', 'image');
				
				$thumbPath		= CVideoLibrary::getPath($table->creator, 'thumb');
				$thumbFileName	=  CFileHelper::getRandomFilename($thumbPath);
				$tmpThumbPath	= $thumbPath . DS . $thumbFileName;
				if (!JFile::write($tmpThumbPath, $body))
				{
					$this->setError(JText::_('COM_COMMUNITY_INVALID_FILE_REQUEST') . ': ' . $thumbFileName);
					return false;
				}
				
				// We'll remove the old or none working thumbnail after this
				$oldThumb	= $table->thumb;
				
				// Get the image type first so we can determine what extensions to use
				$info		= getimagesize( $tmpThumbPath );
				$mime		= image_type_to_mime_type( $info[2]);
				$thumbExtension	= CImageHelper::getExtension( $mime );
				
				$thumbFilename	= $thumbFileName . $thumbExtension;
				$thumbPath	= $thumbPath . DS . $thumbFilename;
				if(!JFile::move($tmpThumbPath, $thumbPath))
				{
					$this->setError(JText::_('WARNFS_ERR02') . ': ' . $thumbFileName);
					return false;
				}
				
				// Resize the thumbnails
				//CImageHelper::resizeProportional( $thumbPath , $thumbPath , $mime , CVideoLibrary::thumbSize('width') , CVideoLibrary::thumbSize('height') );	
				
				list($width,$height) = explode('x',$config->get('videosThumbSize'));
				CImageHelper::resizeAspectRatio($thumbPath,$thumbPath,112,84); 
			}
			else
			{
				$this->setError(JText::_('COM_COMMUNITY_PHOTOS_IMAGE_NOT_PROVIDED_ERROR'));
				return false;
			}
		}
		
		// Update the DB with new thumbnail
		$thumb	= $config->get('videofolder') . '/'
				. VIDEO_FOLDER_NAME . '/'
				. $table->creator . '/'
				. VIDEO_THUMB_FOLDER_NAME . '/'
				. $thumbFilename;
		
		$table->set('thumb', $thumb);
		$table->store();
		
		// If this video storage is not on local, we move it to remote storage
		// and remove the old thumb if existed
		if (($table->storage != 'file')) // && ($table->storage == $storageType))
		{
			$config			= CFactory::getConfig();
			$storageType	= $config->getString('videostorage');
			CFactory::load('libraries', 'storage');
			$storage		= CStorage::getStorage($storageType);
			$storage->delete($oldThumb);
			
			$localThumb		= JPATH::clean(JPATH_ROOT.DS.$table->thumb);
			$tempThumbname	= JPATH::clean(JPATH_ROOT.DS.md5($table->thumb));
			if (JFile::exists($localThumb))
			{
				JFile::copy($localThumb, $tempThumbname);
			}
			if (JFile::exists($tempThumbname))
			{
				$storage->put($table->thumb, $tempThumbname);
				JFile::delete($localThumb);
				JFile::delete($tempThumbname);
			}
		} else {
			if (JFile::exists(JPATH_ROOT.DS.$oldThumb))
			{
				JFile::delete(JPATH_ROOT.DS.$oldThumb);
			}
		}
		
		
		if ($returnThumb)
		{
			return $table->getThumbnail();
		}
		return true;
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
}	
