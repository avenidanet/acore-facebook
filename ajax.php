<?php
/**
 * ACM OneFile (facebook) Modulo para crear apps en Facebook.
 *
 * @author Brian Salazar [Avenidanet]
 * @link http://www.avenidanet.com
 * @copyright Brian Salazar 2006-2013
 * @license http://mit-license.org
 *
 */

include 'acore.php';
include 'facebook.php';
$app = new acore();
$config = Settings::Init();

//Fix session facebook
if(isset($_POST['session'])){
	session_id($_POST['session']);
	session_start();
}

$app->facebook;
$fb = new Facebook(array(
		'appId'  => $config->fb_apikey,
		'secret' => $config->fb_secret
));

if($app->facebook->authorized($fb)){
	//User authorized
	A::log($app->facebook->user);
}else{
	echo "User not authorized.";
}
?>