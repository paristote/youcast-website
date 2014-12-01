<?php
namespace Base;

/**
 *
 */
class AuthAppBase extends AppBase
{
	/**
	 * Called before any other method.
     * Make sure the user is authenticated, otherwise redirect to /logout
	 * @param Object $f3 F3
	 */
	function beforeRoute($f3) {
		$username = $f3->get('SESSION.user_id');
		$password = $f3->get('SESSION.password');
		if (!isset($username) || !isset($password)) {
			// Invalid session
            $path = $f3->get('PATH');
            if (empty($path) || $path === "/")
                $f3->reroute('/hello'); // don't display an error because the request pointed at /
            else
                $f3->reroute('/logout?msg=error-unauthorized');
        }
		
        $f3->set('user_id', $username);
        
        if ($username === $f3->get('admin_id')) {
            $f3->set('isAdmin', true);
        }

		parent::beforeRoute($f3);
	}
}

?>