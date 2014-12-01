<?php
namespace DB;

/**
 * Helper to access the devices collection.
 */
class DeviceDBHelper extends CommonDBHelper
{
	/**
	 * Save a new device in the collection.
	 * @param Object $deviceObj the Device object to save.
	 */
	public function saveDevice($deviceObj)
	{
		$devices = new \DB\Mongo\Mapper($this->db,'devices');
		$devices->deviceId = $deviceObj->deviceId;
		$devices->deviceName = $deviceObj->deviceName;
		$devices->username = $deviceObj->username;
		$devices->connected = $deviceObj->connected;
        $devices->type = $deviceObj->type;
		$devices->save();
	}

	/**
	 * TODO Update the given device in the collection, with the new values.
	 * @param Object $deviceObj the Device object with updated values.
	 */
	public function updateDevice($deviceObj)
	{
        $devices = new \DB\Mongo\Mapper($this->db,'devices');
        $devices->load(array('username' => $deviceObj->username, 'deviceId' => $deviceObj->deviceId));
        if (!$devices->dry()) {
            $devices->deviceName = $deviceObj->deviceName;
            $devices->connected = $deviceObj->connected;
            $devices->update();
        }
	}
    
    /**
     * Deletes the device specified by username and id.
     * @param String $username the owner of the device
     * @param String $deviceId the Id of the device
     * @returns the deleted Device object
     */
    public function deleteDeviceOfUser($username, $deviceId)
    {
        $devices = new \DB\Mongo\Mapper($this->db,'devices');
        $devices->load(array('username' => $username, 'deviceId' => $deviceId));

        if (!$devices->dry()) {
            $d = new \Model\Device();
			$d->deviceId = $devices->get('deviceId');
			$d->deviceName = $devices->get('deviceName');
			$d->username = $devices->get('username');
			$d->connected = $devices->get('connected');
            $d->type = $devices->get('type');
            if ($devices->erase())
                return $d;
        }
        return NULL;
    }

	/**
	 * Loads the devices of the given user.
	 * @param   String $username the user.
	 * @returns Array  the array of Device objects.
	 */
	public function getDevicesOfUsername($username)
	{
		$devices = new \DB\Mongo\Mapper($this->db,'devices');
		$devices->load(array('username' => $username));
		$usersDevices = array();
		while (!$devices->dry())
		{
			$d = new \Model\Device();
			$d->deviceId = $devices->get('deviceId');
			$d->deviceName = $devices->get('deviceName');
			$d->username = $devices->get('username');
			$d->connected = $devices->get('connected');
            $d->type = $devices->get('type');
			$usersDevices[] = $d;
			$devices->next();
		}
		return $usersDevices;
	}
    
    /**
     * Get the device of the username
     * @param   String $username the user who owns the device
     * @param   String $id       the id of the device
     * @returns Object the Device object
     */
    public function getDeviceOfUsernameById($username, $id)
    {
        $devices = new \DB\Mongo\Mapper($this->db,'devices');
		$devices->load(array('username' => $username, 'deviceId' => $id));
        $d = NULL;
        if (!$devices->dry()) {
            $d = new \Model\Device();
			$d->deviceId = $devices->get('deviceId');
			$d->deviceName = $devices->get('deviceName');
			$d->username = $devices->get('username');
			$d->connected = $devices->get('connected');
            $d->type = $devices->get('type');
        }
        return $d;
    }
    
    /**
     * Check if a device is registered for a user
     * @param   String  $username the user who owns the device
     * @param   String  $id       the id of the device
     * @returns Boolean true if the device is registered, false otherwise
     */
    public function isDeviceRegisteredForUser($username, $id) {
        $devices = new \DB\Mongo\Mapper($this->db,'devices');
		$devices->load(array('username' => $username, 'deviceId' => $id));
        return (!$devices->dry());
    }
}

?>