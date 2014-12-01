<?php
namespace Base;

/**
 *
 */
class RestAppBase extends AppBase
{
	/**
	 * Called before any other method.
     * Set the response format as JSON and a default empty content.
	 * @param Object $f3 F3.
	 */
	function beforeRoute($f3) {
		// no need to call the parent's beforeRoute
		// just override the layout and format parameters
		$f3->set('layout', 'empty.json');
		$f3->set('format','application/json');
	}
}

?>