<?php
namespace model;

class User
{
    public $userId;
    public $email;
    public $password; // String encrypted with crypt()
    public $active; // String (true|false)
    public $dateCreated;
    
    function __construct() {
	}
}

?>