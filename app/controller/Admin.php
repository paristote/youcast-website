<?php

class Admin extends \Base\AdminAppBase
{
    function dashboard($f3)
    {
        $f3->set('adminContent', 'admin.htm');
    }
    
    function allUsers($f3)
    {
        // filter users by status (all, new, active, inactive)
        $active = array('all'=>'', 'new'=>'', 'active'=>'', 'inactive'=>'');
        $display = $f3->get('GET.display');
        if (isset($display) && isset($active[$display]))
        {
            $active[$display] = 'active';
        } else 
        {
            $active['all'] = 'active';
            $display = 'all';
        }

        $users = $this->userdb->getAllUsers($display);
        $f3->set('active', $active);
        $f3->set('allUsers', $users);
        $f3->set('adminContent', 'adminusers.htm');
    }
    
    function allDevices($f3)
    {
        $f3->set('adminContent', 'admin.htm');
    }
    
    function deleteUser($f3)
    {
        $userId = $f3->get('PARAMS.username');
        if (isset($userId))
        {
            $deletedUser = $this->userdb->deleteUser($userId);
            if ($deletedUser !== NULL)
                $f3->reroute('/admin/pages/users?msg=success-delete-user');
        }
        $f3->reroute('/admin/pages/users?msg=error-delete-user');
    }
    
    function activateUser($f3)
    {
        $userId = $f3->get('PARAMS.username');
        if (isset($userId))
        {
            // TODO send an email to the user with a link to a temporary page to let him set his password
            if ($this->userdb->activateUser($userId))
                $f3->reroute('/admin/pages/users?msg=success-activate-user');
        }
        $f3->reroute('/admin/pages/users?msg=error-activate-user');
    }
    
    function editUserForm($f3)
    {
        $userId = $f3->get('PARAMS.username');
        if (isset($userId))
        {
            $user = $this->userdb->getUserById($userId);
            if ($user) 
            {
                $f3->set('user', $user);
                $f3->set('adminContent', 'adminedituser.htm');
            }
            else
            {
                $f3->reroute('/admin/pages/users?msg=error-edit-user');
            }
        }
        else
        {
            $f3->reroute('/admin/pages/users?msg=error-edit-user');
        }
    }
    
    function editUser($f3)
    {
        $userId = $f3->get('PARAMS.username');
        if (isset($userId))
        {
            $active = $f3->get('POST.inputActive');
            $user = new \Model\User();
            $user->userId = $userId;
            $user->email = $f3->get('POST.inputEmail');
            $user->active = isset($active) ? 'true' : 'false';
            if ($this->userdb->updateUser($user))
                $f3->reroute('/admin/pages/users?msg=success-edit-user');
            else
                $f3->reroute('/admin/pages/users?msg=error-edit-user');
        } 
        else
        {
            $f3->reroute('/admin/pages/users?msg=error-edit-user');
        }
    }
}

?>