<?php
namespace db;

/**
 * Helper to access the user collection.
 */
class UserDBHelper extends CommonDBHelper
{
    /**
     * Saves the User into db.
     * @param Object $userObj the user
     */
    public function saveUser($userObj)
    {
        $users = new \DB\Mongo\Mapper($this->db,'users');
        $users->userId = $userObj->userId;
        $users->email = $userObj->email;
        $users->password = $userObj->password;
        $users->active = $userObj->active;
        $users->dateCreated = $userObj->dateCreated;
        return ($users->save());
    }
    
    /**
     * Get the user identified by its id
     * @param String $userId the id of the user
     * @returns Object the user object
     */
    public function getUserById($userId)
    {
        $users = new \DB\Mongo\Mapper($this->db,'users');
        $users->load(array('userId' => $userId));
        if (!$users->dry()) {
            $u = new \model\User();
            $u->userId = $users->userId;
            $u->email = $users->email;
            $u->password = $users->password;
            $u->active = $users->active;
            $u->dateCreated = $users->dateCreated;
            return $u;
        } else {
            return NULL;
        }
    }
    
    /**
     * Returns all users from the DB, the most recently created first
     * @param String $filter type of users to retrieve, one of all, new, active, inactive
     * @param Number $p the index of the page to return (0-based)
     * @returns Array an array of User objects
     */
    public function getAllUsers($filter = 'all', $p = 0)
    {
        $filters = array(
            'all'      => array(),
            'new'      => array('dateCreated' => array('$gt' => (time()-(24 * 60 * 60)))),
            'active'   => array('active' => 'true'),
            'inactive' => array('active' => 'false')
        );
        if (!array_key_exists($filter, $filters)) $type = 'all';
        $users = new \DB\Mongo\Mapper($this->db,'users');
        $result = $users->paginate($p, 15, $filters[$filter], array('order' => array('dateCreated' => -1)));
        $array = $result['subset'];
        $allusers = array();
        foreach($array as $user)
        {
            $u = new \model\User();
            $u->userId = $user->userId;
            $u->email = $user->email;
//            $u->password = $users->password;
            $u->active = $user->active;
            $u->dateCreated = $user->dateCreated;
            $allusers[] = $u;
        }
        
        return $allusers;
    }
    
    /**
     * Deletes the user with the given ID
     * @param   String $userId the user ID
     * @returns Object the deleted user
     */
    public function deleteUser($userId)
    {
        $users = new \DB\Mongo\Mapper($this->db,'users');
        $users->load(array('userId' => $userId));
        $u = NULL;
        if (!$users->dry()) {
            $u = new \model\User();
            $u->userId = $users->userId;
            $u->email = $users->email;
            $u->password = $users->password;
            $u->active = $users->active;
            $u->dateCreated = $users->dateCreated;
            $users->erase();
        }
        return $u;
    }
    
    /**
     * Updates the email and the status (active or not) of the user
     * @param Object $userObj the User object with new values
     * @returns Boolean true if the user was updated
     */
    public function updateUser($userObj)
    {
        $users = new \DB\Mongo\Mapper($this->db,'users');
        $users->load(array('userId' => $userObj->userId));
        if (!$users->dry()) {
            $users->email = $userObj->email;
            $users->active = $userObj->active;
            if (isset($userObj->password))
                $users->password = $userObj->password;
            return ($users->save());
        }
        return false;
    }
    
    /**
     * Activate and sets the default password to the user
     * @param   String  $userId ID of the user
     * @returns Boolean true if the user was activated
     */
    public function activateUser($userId)
    {
        $users = new \DB\Mongo\Mapper($this->db,'users');
        $users->load(array('userId' => $userId));
        $u = NULL;
        if (!$users->dry()) {
            $users->active = "true";
            $users->password = '$2a$10$jvnAIVVN05QL4qGe/36sz.lnVsPtaDrAD8ZLVXm6INpW4Qlua1Oda';
            return ($users->update());
        }
        return false;
    }
}

?>