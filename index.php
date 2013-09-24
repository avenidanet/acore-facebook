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
$app = new acore;
$config = Settings::Init();
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Facebook App | ACORE</title>
	<?php A::script('jquery,validate','js/');?>
	<!-- Estilos y javascript comunes -->
</head>
<body>
<?php 
$app->facebook->header();
$config->fb_url = "https://www.facebook.com/".$app->facebook->info('id')."?sk=app_".$config->fb_apikey;
if($app->facebook->info('app_data') == 'dato'){
	include 'acFacebook/invite.php';
}else{
	if($app->facebook->info('liked')){
		$fb = new Facebook(array(
				'appId'  => $config->fb_apikey,
				'secret' => $config->fb_secret
		));
		if($app->facebook->authorized($fb) && $app->facebook->registred()){
			include 'acFacebook/autorizado.php';
		}else{
			include 'acFacebook/no_autorizado.php';
		}
	}else{
		include 'acFacebook/no_liker.php';
	}	
}
$app->facebook->footer();?>	
</body>
</html>