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
		if (!isset($auth)) $this->error401($f3, "No Authorization header found"); // No Authorization header found
		$a = explode(" ", $auth);
		if (count($a) < 2) $this->error401($f3, "Incorrect Authorization header"); // Header is not "Basic abcxyz"
		$cred = base64_decode($a[1]);
		$a = explode(":", $cred);
		if (count($a) < 2) $this->error401($f3, "Incorrect Authorization credentials"); // Incorrect credentials
		$user = $a[0];
        $pass = $a[1];
        $dbuser = $this->userdb->getUserById($user);
    	if ($dbuser === NULL || $dbuser->active === "false" || !$this->hasher->CheckPassword($pass,$dbuser->password))
			$this->error401($f3, "You cannot access this resource"); // Incorrect credentials

		$this->username = $user;

		parent::beforeRoute($f3);
	}
}

?>