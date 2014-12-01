<?php

class Alert
{
    public $type; // String (success|error|info|warning)
    public $code;  // String (e.g. signin, invite, video-delete)
    public $text; // String (message of the alert)
    
    function __construct() {
	}
    
    public static function get($code)
    {
        $m = new Alert;
        $params = explode("-", $code);
        
        if ($params[0] === "error")
        {
            $m->type = "error";
            $m->text = "Ouch !";
        }
        else if ($params[0] === "success")
        {
            $m->type = "success";
            $m->text = "Done :-)";
        }
        else if ($params[0] === "info")
        {
            $m->type = "info";
            $m->text = "Good to know";
        }
        else if ($params[0] === "warning")
        {
            $m->type = "warning";
            $m->text = "Careful !";
        }
        return $m;
    }
    
}

class AlertSuccess
{
    
}

?>