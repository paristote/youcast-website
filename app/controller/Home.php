<?php

/**
 * Operations related to the Home / Landing page.
 * Authenticated:no
 */
class Home extends \Base\AppBase
{
    /**
     * Landing page
     * @param Object $f3 F3
     */
    function landing($f3)
    {
    	$f3->set('content', 'landing.htm');
    }

    /**
     * Authenticate user whose credentials are given by POST
     * @param Object $f3 F3
     */
    function auth($f3)
    {
    	$username = $f3->get('POST.username');
    	$password = $f3->get('POST.password');
        $user = $this->userdb->getUserById($username);
    	if ($user === NULL || $user->active === "false" || !$this->hasher->CheckPassword($password,$user->password))
    	{
    		$f3->reroute('/hello?msg=error-signin');	
    	}
    	$f3->set('SESSION.user_id', $username);
		$f3->set('SESSION.password', $user->password);
    	$f3->reroute('/?msg=success-signin');
    }
    
    /**
     * Creates an inactive user with the given email for an invitation.
     * @param Object $f3 F3
     */
    function invite($f3)
    {
        $email = trim($f3->get('POST.user_email'));
        if (empty($email))
            $f3->reroute('/hello?msg=error-invite');
        
        $user = $this->userdb->getUserById($email);
        if (!$user) {
            $userObj = new \Model\User();
            $userObj->userId = $email;
            $userObj->email = $email;
            $userObj->password = "";
            $userObj->active = "false";
            $userObj->dateCreated = time();
            $this->userdb->saveUser($userObj);
            $f3->reroute('/thankyou');
        } else {
            $f3->reroute('/hello?msg=info-petit-malin');
        }
    }
    
    /**
     * Displays the Thank You page, after a user requested an invitation
     * @param Object $f3 F3
     */
    function thankyou($f3)
    {
        $f3->set('content', 'thankyou.htm');
    }

    /**
     * Signs the current user out
     * @param Object $f3 F3
     */
    function logout($f3)
    {
    	$f3->clear('SESSION');
        $getMsg = $f3->get('GET.msg');
        if (!isset($getMsg))
            $getMsg = "success-signout";
		$f3->reroute('/hello?msg='.$getMsg);
    }

}

?>