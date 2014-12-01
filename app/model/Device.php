<?php
namespace Model;

class Device
{
	public $deviceId; // String
	public $deviceName; // String
	public $username; // String
	public $connected; // String (true|false)
    public $type; // String (ios|android)

	function __construct() {
		$this->connected = "false";
	}
}

?>