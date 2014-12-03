<?php

include_once('app/curl.php');

/**
 * Operations related to the user's home page.
 * Authenticated:yes
 */
class YouCast extends \base\AuthAppBase
{

    /**
     * Home page of the connected user.
     * @param Object $f3 F3
     */
    function home($f3)
    {
    	$username = $f3->get('SESSION.user_id');
    	$myVideos = $this->videodb->getVideosByUsername($username, false);
//        TODO activate choice of device when submitting a video URL
//        $usersDevices = $this->devicedb->getDevicesOfUsername($username);
//        $deviceCount = count($usersDevices);
//        $defaultDevice = NULL;
//        $myDevices = NULL;
//        if ($deviceCount >= 1)
//        {
//            $defaultDevice = $usersDevices[0];
//            $f3->set('defaultDevice', $defaultDevice);
//            if ($deviceCount >= 2)
//            {
//                $myDevices = array();
//                for ($i = 1; $i < $deviceCount; $i++) {
//                    $myDevices[] = $usersDevices[$i];
//                }
//                $f3->set('myDevices', $myDevices);
//            }
//        }
    	$f3->set('myvideos', $myVideos);
    	$f3->set('statusIcons', \model\VideoStatus::$icons);
    	$f3->set('statusLabels', \model\VideoStatus::$labels);        
        $f3->set('mainContent', 'myvideos.htm');
    	$f3->set('content', 'home.htm');
    }

    /**
     * Form to submit a new video.
     * @param O $f3 F3
     */
    function submit($f3)
	{
        $videoUrl = trim($_POST['video_url']);
		if(!empty($videoUrl)) {
			$videoId = $this->stripVideoId($videoUrl);
            if ($videoId != NULL)
            {
                $username = $f3->get('SESSION.user_id');
                $title = $this->getVideoTitle($videoId);
                $video = new \model\Video();
                $video->videoId = $videoId;
                $video->username = $username;
                $video->videoTitle = $title;
                $video->addedDate = time();
                $video->status = \model\VideoStatus::Registered;
                $this->videodb->saveVideo($video);
                $f3->reroute('/?msg=success-video-submit');
            }
		}
        $f3->reroute('/?msg=error-video-submit');
	}
    /**
     * Deletes a video.
     * Takes a username and video id as POST params.
     * @param Object $f3 F3
     */
    function deleteVideo($f3)
    {
        $currentUser = $f3->get('SESSION.user_id');
        $requestingUser = $f3->get('PARAMS.username');
        $videoId = $f3->get('PARAMS.videoId');
        
        if ($currentUser === $requestingUser && isset($videoId))
        {
            $video = $this->videodb->deleteVideoByUsernameAndId($currentUser, $videoId);
            if ($video)
                $f3->reroute('/?msg=success-video-delete');
        }
        $f3->reroute('/?msg=error-video-delete');
    }
    /**
     * Resets the video's status to Registered so it will be downloaded again.
     * Takes username and video id as POST parameters.
     * @param [[Type]] $f3 [[Description]]
     */
    function resetVideo($f3)
    {
        $currentUser = $f3->get('SESSION.user_id');
        $requestingUser = $f3->get('PARAMS.username');
        $videoId = $f3->get('PARAMS.videoId');
        
        if ($currentUser === $requestingUser && isset($videoId))
        {
            $updated = $this->videodb->resetVideoStatusByUsernameAndId($currentUser, $videoId);
            if ($updated)
                $f3->reroute('/?msg=success-video-reset');
        }
        $f3->reroute('/?msg=error-video-reset');
    }

    /**
     * Page that lists the devices registered for the current user.
     * Takes a username as path param.
     * @param Object $f3 F3
     */
    function myDevices($f3)
    {
        $currentUser = $f3->get('SESSION.user_id');
        $requestingUser = $f3->get('PARAMS.username');
        if ($currentUser === $requestingUser)
        {
            $myDevices = $this->devicedb->getDevicesOfUsername($currentUser);
            $types = array("android" => "android", "ios" => "apple");
            $f3->set('deviceTypes', $types);
            $f3->set('myDevices', $myDevices);
            $f3->set('mainContent', 'mydevices.htm');
            $f3->set('content', 'home.htm');
        }
    }
    /**
     * Deletes the device of the user.
     * Takes a username and a deviceId as path params.
     * @param Object $f3 F3
     */
    function deleteDevice($f3)
    {
        $currentUser = $f3->get('SESSION.user_id');
        $requestingUser = $f3->get('PARAMS.username');
        $deviceId = $f3->get('PARAMS.deviceId');
        if ($currentUser === $requestingUser && isset($deviceId))
        {
            $device = $this->devicedb->deleteDeviceOfUser($currentUser, $deviceId);
            if ($device)
                $f3->reroute('/pages/devices/'.$currentUser.'/?msg=success-device-delete');
        }
        $f3->reroute('/pages/devices/'.$currentUser.'/?msg=error-device-delete');
    }
    
	/**
	  * UTILS
	  */
    
	/**
	 * Strips the Youtube video's ID from its URL.
	 * @param   String $videoUrl the video URL
	 * @returns String the video ID
	 */
	function stripVideoId($videoUrl)
	{
		if(isset($videoUrl)) {
			$url = parse_url($videoUrl);
			$videoId = NULL;
			if( is_array($url) && count($url)>0 && isset($url['query']) && !empty($url['query']) ){
				$params = explode('&',$url['query']);
				if( is_array($params) && count($params) > 0 ){
					foreach( $params as $p ){
						$pattern = '/^v\=/';
						if( preg_match($pattern, $p) ){
							$videoId = preg_replace($pattern,'',$p);
							break;
						}
					}
				}
				if( !$videoId ) { // No VideoId found
					return NULL;
				}
			} else { // Invalid URL
				return NULL;
			}
		} else { // No URL given
			return NULL;
		}
		return $videoId;
	}

	/**
	 * Gets the title of the video identified by the given ID.
	 * @param   String $videoId the ID of the video.
	 * @returns String the video title or an empty String if nothing was found.
	 */
	function getVideoTitle($videoId)
	{
		// Get the video info page for this video id
		$videoInfo = curlGet('http://www.youtube.com/get_video_info?&video_id='. $videoId);
		// Extract the video title from the response
		$title = '';
		parse_str($videoInfo);
		if ($title == '') $title = $videoId;
		return $title;
	}

}

?>