<?php
// F3
// loads fat free framework in the variable $f3
$f3 = require('f3/base.php');

// CONFIG
// debug mode, remove before putting in prod
//$f3->set('DEBUG',3);
$f3->config('files/config/setup.cfg');
$f3->set('AUTOLOAD', 'app/;app/base/;app/controller/;app/db/;app/model/'); // loads all classes in the app/ directory
$f3->set('TEMP','files/tmp/'); // temp directory 
$f3->set('UI','views/;views/admin/;views/rest/'); // contains all the html views and json response formats

// ROUTES

// Home pages
// if user is not signed-in, will redirect to /signin
$f3->route('GET /', 'YouCast->home');
// contains a form that calls /auth
$f3->route('GET /hello', 'Home->landing');
// if sign-in is successfull, will redirect to /
// otherwise, will redirect to /hello and display an error message
$f3->route('POST /auth', 'Home->auth');
// handle the invitation requests
$f3->route('POST /inviteme', 'Home->invite');
$f3->route('GET /thankyou', 'Home->thankyou');
// will clear the session and redirect to /hello and display a success message
$f3->route('GET /logout', 'Home->logout');

// Videos management
// called when user submits a YT URL
$f3->route('POST /video/submit', 'YouCast->submit');
$f3->route('GET /actions/videos/d/@username/@videoId', 'YouCast->deleteVideo');
$f3->route('GET /actions/videos/r/@username/@videoId', 'YouCast->resetVideo');

// Devices management
$f3->route('GET /pages/devices/@username', 'YouCast->myDevices');
$f3->route('GET /actions/devices/d/@username/@deviceId', 'YouCast->deleteDevice');

// REST
// REST access to Videos with JSON format
$f3->map('/users/@username/videos', 'Video');
$f3->map('/users/@username/videos/@videoId', 'Video');
// REST access to Devices with JSON format
$f3->map('/users/@username/devices', 'Device');
$f3->map('/users/@username/devices/@deviceId', 'Device');
// REST access to Users with JSON format
$f3->route('POST /users/@username/connect', 'User->connect');

// Admin pages
$f3->route('GET /admin/init', 'Home->initadmin');
$f3->route('GET /admin/phpinfo', function($f3) {
   phpinfo(); 
});
$f3->route('GET /admin', 'Admin->dashboard');
$f3->route('GET /admin/pages/users', 'Admin->allUsers');
$f3->route('GET /admin/pages/users/e/@username', 'Admin->editUserForm');
$f3->route('GET /admin/pages/devices', 'Admin->allDevices');
$f3->route('GET /admin/actions/users/d/@username', 'Admin->deleteUser');
$f3->route('GET /admin/actions/users/a/@username', 'Admin->activateUser');
$f3->route('POST /admin/actions/users/e/@username', 'Admin->editUser');

$f3->run();
?>