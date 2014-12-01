<?php

/**
 * The REST interface to access Video resources.
 */
class Video extends \Base\AuthRestAppBase
{
	/**
     * REST REQUEST HANDLERS
	 */

	/**
	 * GET interface.
     * Takes a videoId and a username path parameters.
     * Accepts a status query parameter:
     * - status=new : returns videos not yet downloaded
     * - status=failed : returns videos that failed to be downloaded
     * Returns an array of videos as JSON.
	 * @param Object $f3 F3
	 */
	function get($f3) {
		$videoId = $f3->get('PARAMS.videoId');
		$username = $f3->get('PARAMS.username');
		$newOnly = $f3->get('GET.status') === "new" ? true : false;
		$failedOnly = $f3->get('GET.status') === "failed" ? true : false;

		if ($videoId && $username)
			$videos = array($this->videodb->getVideoByUsernameAndId($username, $videoId));
		else if ($username)
			$videos = $this->videodb->getVideosByUsername($username, $newOnly);
		if ($videos != NULL) {
			$f3->set('videosArray', $videos);
			$f3->set('length', count($videos));
			$f3->set('layout', 'videos.json');
		} else {
			$f3->set('layout', 'empty.json');
			// $f3->status(404);
		}
	}

	/**
	 * PUT interface.
     * Takes a videoId and a username path parameters.
     * Takes a Video object as JSON in body.
     * Updates the video videoId with the values in the JSON body.
     * Returns the updated Video object as JSON.
	 * @param Object $f3 F3
	 */
	function put($f3) {
		$username = $f3->get('PARAMS.username'); 
		$videoId = $f3->get('PARAMS.videoId');
		$inputJSON = $f3->get('BODY');
        $input= json_decode( $inputJSON, TRUE ); //convert JSON into array
        $video = new \Model\Video();
        $video->videoTitle = '';
        $video->username = $username;
        $video->videoId = $videoId;
        $video->downloadCount = $input['downloadCount'];
        switch ($input['status']) {
        	case '1':
        		$video->status = \Model\VideoStatus::Scheduled;
        		break;
        	case '2':
        		$video->status = \Model\VideoStatus::Started;
        		break;
        	case '3':
        		$video->status = \Model\VideoStatus::Done;
        		break;
        	case '10':
        		$video->status = \Model\VideoStatus::Failed;
        		break;
        	default:
        		$video->status = \Model\VideoStatus::Registered;
        		break;
        }
        $this->videodb->updateVideo($video);
        $f3->set('videosArray', array($video));
		$f3->set('length', 1);
		$f3->set('layout', 'videos.json');
        // $f3->status(200);
	}

	// TODO
	function post($f3) {}
    function delete($f3) {}

    

}

?>