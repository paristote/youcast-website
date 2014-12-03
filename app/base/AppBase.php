<?php
namespace base;

/**
 * The base controller of the application
 * Extend this class to define a page's controller.
 * Extend AuthAppBase for an authenticated page.
 * Extend RestAppBase for a REST interface.
 * Extend AuthRestAppBase for an authenticated REST interface.
 */
class AppBase {

	/**
	 * Holds the connection to the videos collection.
	 */
	public $videodb;
	/**
	 * Holds the connection to the devices collection.
	 */
	public $devicedb;
    /**
     * Holds the connection to the users collection.
     */
    public $userdb;
    /**
     * Password hasher and verifier
     */
    public $hasher;
	/**
	 * Constructor
	 * Init the $f3 and the $db variables
	 */
	function __construct() {
		$this->videodb = new \DB\VideoDBHelper();
		$this->devicedb = new \DB\DeviceDBHelper();
        $this->userdb = new \DB\UserDBHelper();
        $this->hasher = new \PasswordHash(10, FALSE);
	}

	/**
	 * Called before transferring control to the method specified in the route.
	 * Set the default layout, that can be changed in children classes.
     * @param Object $f3 F3
	 */
	function beforeRoute($f3) {
		$msg = $f3->get('GET.msg');
		if (isset($msg))
		{
			$alert = \Alert::get($msg);
            $f3->set('alertType', $alert->type);
            $f3->set('alertMsg', $alert->text);
		}
		$f3->set('layout','layout.htm');
		$f3->set('pageTitle', 'YouCast');
		$f3->set('format','text/html');
	}

	/**
	 * Called after transferring control to the method specified in the route
	 * Render the specified layout in the specified format (cf beforeRoute)
	 * Possible formats: text/html , application/json , etc
     * @param Object $f3 F3
	 */
	function afterRoute($f3) {
		echo \Template::instance()->render($f3->get('layout'), $f3->get('format'));
	}
}

?>
