<?php
namespace base;

/**
 *
 */
class AdminAppBase extends AppBase
{
	/**
	 * Called before any other method.
     * Make sure the user is authenticated, otherwise redirect to /logout
	 * @param Object $f3 F3
	 */
	function beforeRoute($f3) {
		$username = $f3->get('SESSION.user_id');
		$password = $f3->get('SESSION.password');
        
		if (!isset($username) || !isset($password) || $username !== $f3->get('admin_id'))
			// Invalid session
			$f3->reroute('/logout?msg=error-unauthorized');
		
        $f3->set('admin_id', $username);
        
		parent::beforeRoute($f3);
        
        $f3->set('content', 'adminlayout.htm');
		$f3->set('pageTitle', 'Admin :: YouCast');
	}
}

?>