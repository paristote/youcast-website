<?php

/**
 * The REST interface to access Device resources.
 */
class Device extends \base\AuthRestAppBase
{
	/**
	 * GET interface.
     * Takes a username path param.
     * Returns the devices represented in JSON.
	 * @param Object $f3 F3
	 */
	function get($f3) {
		$username = $f3->get('PARAMS.username');
        $deviceId = $f3->get('PARAMS.deviceId');
        if ($username && $deviceId)
          $devices = array($this->devicedb->getDeviceOfUsernameById($username, $deviceId));
        else if ($username)
		  $devices = $this->devicedb->getDevicesOfUsername($username);
		if ($devices != NULL) {
			$f3->set('devicesArray', $devices);
			$f3->set('length', count($devices));
			$f3->set('layout', 'devices.json');
		} else {
			$f3->set('layout', 'empty.json');
			// $f3->status(404);
		}
	}

	/**
	 * POST interface.
     * Takes a username path param.
     * Saves the Device passed in BODY in the db if one doesn't
     * already exist with this username and id.
	 * @param Object $f3 F3
	 */
	function post($f3) {
        $username = $f3->get('PARAMS.username');
        $inputJSON = $f3->get('BODY');
        $input= json_decode( $inputJSON, TRUE ); //convert JSON into array
        $deviceId = $input['id'];
        $deviceObj = new \model\Device();
        $deviceObj->deviceId = $deviceId;
        $deviceObj->deviceName = $input['name'];
        $deviceObj->username = $username;
        $deviceObj->connected = $input['connected'];
        $deviceObj->type = $input['type'];
        if (!$this->devicedb->isDeviceRegisteredForUser($username, $deviceId)) {
            $this->devicedb->saveDevice($deviceObj);
        }
        $devices = array($deviceObj);
        $f3->set('devicesArray', $devices);
        $f3->set('layout', 'devices.json');
        $f3->set('length', count($devices));
	}


	function put($f3) {}
    function delete($f3) {}
}

?>