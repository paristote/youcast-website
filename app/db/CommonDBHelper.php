<?php
namespace DB;

/**
 *
 */
class CommonDBHelper
{
    /**
     * The connection to the DB
     */
	protected $db;

	/**
	 * Create the object and the connection to the DB
	 */
	function __construct()
	{
		$f3 = \Base::instance();
		$dbuser = $f3->get('dbuser');
		$dbpass = $f3->get('dbpass');
		$dbhost = $f3->get('dbhost');
		$dbname = $f3->get('dbname');
		$dbport = $f3->get('dbport');
		$connect = $dbhost.':'.$dbport.'/'.$dbname;
		if (!empty($dbuser) && !empty($dbpass)) {
			$connect = $dbuser.":".$dbpass."@".$connect;
		}
		$db = new \DB\Mongo("mongodb://".$connect, $dbname);
		// $db = new MongoClient("mongodb://".$connect);
		$this->db = $db;
	}

}

?>