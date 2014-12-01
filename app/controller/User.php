<?php

/**
 * The REST interface to access User resources.
 */
class User extends \Base\AuthRestAppBase
{
	/**
	 * Ensures that the request is made with correct credentials.
     * Takes a username path parameter.
     * Returns a 200 response OK if the authentication was successfull.
     * Returns a 401 error response otherwise.
	 * @param Object $f3 F3.
	 */
	function connect($f3)
	{
		$user = $f3->get('PARAMS.username');
		if ($user === $this->username)
		{
			$f3->set('layout', 'userconnected.json');
		}
	}
}

?>