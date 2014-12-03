<?php
namespace model;

class VideoStatus
{
    const Registered = 0; // Registered on the website
    const Scheduled  = 1; // Scheduled for download on a device
    const Started    = 2; // Download on device started
    const Done       = 3; // Download successfull
    const Failed     = 10; // Download failed


	public static $labels   = array("0" => "Registered",
							 "1" => "Scheduled for download",
							 "2" => "Downloading",
							 "3" => "Download successfull",
							 "10" => "Download failed");

	// Glyphicons
    // public static $icons = array("0" => "th-list",
    // 						 "1" => "time",
    // 						 "2" => "cloud-download",
    // 						 "3" => "ok",
    // 						 "10" => "remove");

	// Font Awesome
    public static $icons    = array("0" => "bookmark-o",
    						 "1" => "clock-o",
    						 "2" => "download",
    						 "3" => "check",
    						 "10" => "warning");

}

class Video
{
	public $videoId; // String
	public $videoTitle; // String
	public $username; // String
	public $downloadCount; // int
	public $status; // int (cf VideoStatus)
    public $addedDate; // Timestamp

	function __construct()
	{
       $this->downloadCount = 0;
       $this->status = VideoStatus::Registered;
   	}
    
    /**
     * Transforms the added date from a timestamp format to a pretty format Y-m-d
     * Example: 2014-11-14
     * @returns String the addedDate property in a pretty format
     */
    function prettyDate()
    {
        return date("Y-m-d", $this->addedDate+0);
    }
}

?>