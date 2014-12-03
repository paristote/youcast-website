<?php
namespace db;

/**
 * Helper to access the videos collection.
 */
class VideoDBHelper extends CommonDBHelper
{
	/**
	 * Utility method that returns a Video object with the given values.
	 * @param   String $videoId  the video ID from Youtube
	 * @param   String $username the user registering the video
	 * @param   String $title    the video's title
	 * @param   Number $dl       the download count
	 * @param   Number $status   the number representing the status of the video
	 * @returns Object a new Video object.
	 */
	private function createVideoObject($videoId, $username, $title, $dl, $status, $date)
	{
		$f3 = \Base::instance();
		$video = new \Model\Video();
		$video->videoId = $videoId;
		$video->username = $username;
		$video->videoTitle = $title;
		$video->downloadCount = $dl;
		$video->status = $status;
        $video->addedDate = $date;
		return $video;
	}

	/**
     * DATABASE INTERACTIONS
     */
    
    /**
     * Save a new video in the collection.
     * @param Object $videoObj the Video to save.
     */
    public function saveVideo($videoObj)
    {
    	$dls = new \DB\Mongo\Mapper($this->db,'videos');
    	$dls->username = $videoObj->username;
    	$dls->videoId = $videoObj->videoId;
    	$dls->downloadCount = $videoObj->downloadCount;
    	$dls->videoTitle = $videoObj->videoTitle;
    	$dls->status = $videoObj->status;
        $dls->addedDate = $videoObj->addedDate;
		$dls->save();
    }

    /**
     * Update a video with new values in the collection.
     * @param Object $videoObj the Video object with updated values.
     */
    public function updateVideo($videoObj)
    {
    	$dls = new \DB\Mongo\Mapper($this->db,'videos');
    	$dls->load(array('videoId' => $videoObj->videoId, 'username' => $videoObj->username));
    	if (!$dls->dry()) {
    		$dls->downloadCount = $videoObj->downloadCount;
    		$dls->status = $videoObj->status;
    		$dls->update();
    	}
    }
    /**
     * Loads the video with the given ID and added by the given username.
     * @param   String $username the user who added this video
     * @param   String $videoId  the Youtube ID of the video
     * @returns Object a Video object
     */
    public function getVideoByUsernameAndId($username, $videoId)
    {
    	$dls = new \DB\Mongo\Mapper($this->db,'videos');
    	$dls->load(array('videoId' => $videoId, 'username' => $username));
    	$video = NULL;
    	if (!$dls->dry()) {
    		$video = $this->createVideoObject(
    			$dls->get('videoId'),
    			$dls->get('username'),
    			urlencode($dls->get('videoTitle')),
    			$dls->get('downloadCount'),
    			$dls->get('status'),
                $dls->get('addedDate'));
    	}
    	return $video;
    }
    /**
     * Loads the videos added by the given user.
     * @param   String  $username the user who added the videos.
     * @param   Boolean $newOnly  set true to return only videos not yet downloaded.
     * @returns Array   an array of Video objects.
     */
    public function getVideosByUsername($username, $newOnly)
    {
        // HACK : create a new Video obj to auto-load the Video.php file
        // which contains the VideoStatus class as well
        new \Model\Video();
        // thanks to this hack, the VideoStatus class can be used below
        // TODO separate the Video and VideoStatus classes into 2 files
    	$dls = new \DB\Mongo\Mapper($this->db,'videos');
    	if ($newOnly)
    		$dls->load(array('username' => $username, 'status' => \Model\VideoStatus::Registered));
    	else
    		$dls->load(array('username' => $username));
    	$videos = array();
    	while (!$dls->dry()) {
    		$video = $this->createVideoObject(
    			$dls->get('videoId'),
    			$dls->get('username'),
    			urlencode($dls->get('videoTitle')),
    			$dls->get('downloadCount'),
    			$dls->get('status'),
                $dls->get('addedDate'));
    		$videos[] = $video;
    		$dls->next();
    	}
    	return $videos;
    }
    
    /**
     * Deletes a video identified by its ID and its user.
     * @param   String $username the user
     * @param   String $id       the video ID
     * @returns Object the Video object if the deletion is successful, NULL otherwise
     */
    public function deleteVideoByUsernameAndId($username, $id)
    {
        $dls = new \DB\Mongo\Mapper($this->db,'videos');
    	$dls->load(array('videoId' => $id, 'username' => $username));
    	$video = NULL;
    	if (!$dls->dry()) {
    		$video = $this->createVideoObject(
    			$dls->get('videoId'),
    			$dls->get('username'),
    			urlencode($dls->get('videoTitle')),
    			$dls->get('downloadCount'),
    			$dls->get('status'),
                $dls->get('addedDate'));
            
            if ($dls->erase())
                return $video;
    	}
    	return NULL;
    }
    
    /**
     * Reset a video's status to Registered.
     * The video will be downloaded again on the devices.
     * @param   String  $username user
     * @param   String  $id       video ID
     * @returns Boolean true if the status was updated successfully
     */
    public function resetVideoStatusByUsernameAndId($username, $id)
    {
        // HACK : create a new Video obj to auto-load the Video.php file
        // which contains the VideoStatus class as well
        new \Model\Video();
        // thanks to this hack, the VideoStatus class can be used below
        // TODO separate the Video and VideoStatus classes into 2 files
        $dls = new \DB\Mongo\Mapper($this->db,'videos');
    	$dls->load(array('videoId' => $id, 'username' => $username));
        if (!$dls->dry())
        {
               $dls->set('status', \Model\VideoStatus::Registered);
               return $dls->save();
        }
        
        return false;
    }
}

?>