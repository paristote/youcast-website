<?php

/**
 *
 */
class UserSettings extends \base\AuthAppBase
{
    /**
     * Settings Home page
     * @param Object $f3 F3
     */
    function settings($f3)
    {
        $f3->set('mainContent', 'changepass.htm');
    	$f3->set('content', 'home.htm');   
    }
    
    /**
     * Change password action method
     * Redirects to the Settings home page after execution
     * @param Object $f3 F3
     */
    function changePassword($f3)
    {
        $currentPassword = trim($f3->get('POST.currentPassword'));
        $newPassword = trim($f3->get('POST.newPassword'));
        $confirmPassword = trim($f3->get('POST.confirmPassword'));
        $currentUser = $f3->get('SESSION.user_id');
        
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword))
        {
            // empty input field(s)
            $f3->reroute('/pages/settings/'.$currentUser.'?msg=error-missing-password-fields');
        }
        else
        {
            $user = $this->userdb->getUserById($currentUser);
            if (!isset($user))
            {
                // trying to change the password of a different user :S
                $f3->reroute('/pages/settings/'.$currentUser.'?msg=error-wrong-user');
            }
            else
            {
                if (!$this->hasher->CheckPassword($currentPassword,$user->password))
                {
                    // incorrect current password
                    $f3->reroute('/pages/settings/'.$currentUser.'?msg=error-wrong-password');
                }
                else
                {
                    if ($newPassword !== $confirmPassword)
                    {
                        // confirmation password doesn't match the new password
                        $f3->reroute('/pages/settings/'.$currentUser.'?msg=error-different-password');
                    }
                    else
                    {
                        $user->password = $this->hasher->HashPassword($newPassword);
                        if ($this->userdb->updateUser($user))
                            $f3->reroute('/pages/settings/'.$currentUser.'?msg=success-change-password');
                        else
                            $f3->reroute('/pages/settings/'.$currentUser.'?msg=error-change-password');
                    }
                }
            }
        }
        
    }
}

?>