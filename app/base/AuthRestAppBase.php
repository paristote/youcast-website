<?php
namespace base;

/**
 *
 */
class AuthRestAppBase extends RestAppBase
{
    /**
     * Username provided in the Authorization header of the request
     */
	protected $username;

	/**
	 * Stops the execution of the program and returns a 401 response
	 * @param Object $f3  F3
	 * @param String $msg Error message to display as response.
	 */
	function error401($f3, $msg)
	{
		echo $msg;
		$f3->status(401);
		exit;
	}
	/**
	 * Called before any other method.
     * Makes sure the request is authenticated, with the Authorization header.
     * If yes, store the given username.
     * Otherwise, send a 401 response.
	 * @param Object $f3 F3
	 */
	function beforeRoute($f3) {

		$auth = $f3->get('HEADERS.Authorization');
        error_log("Authorization: ".$auth);
		if (!isset($auth)) $this->error401($f3, "Authentication required"); // No Authorization header found
		$a = explode(" ", $auth);
		if (count($a) < 2) $this->error401($f3, "Authentication required"); // Incorrect header
		$cred = base64_decode($a[1]);
		$a = explode(":", $cred);
		if (count($a) < 2) $this->error401($f3, "Authentication required"); // Incorrect header
		$user = $a[0];
        $pass = $a[1];
        $dbuser = $this->userdb->getUserById($user);
    	if ($dbuser === NULL || $dbuser->active === "false" || !$this->hasher->CheckPassword($pass,$dbuser->password))
        {
            error_log("Cred: ".$cred);
			$this->error401($f3, "You cannot access this resource"); // Incorrect credentials
        }

		$this->username = $user;

		parent::beforeRoute($f3);
	}
}

?>