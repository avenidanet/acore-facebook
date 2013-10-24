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

$config = Settings::Init();
$config->debug = FALSE;
$config->host = 'localhost';
$config->user = '';
$config->pass = '$';
$config->database = '';

$config->fb_apikey = "";
$config->fb_secret = "";
$config->fb_table = "usuarios";
$config->fb_field = "fb_id";
$config->fb_scope = "publish_stream";

class acFacebook extends AbstractModule{

	public $user;

	//Custom methods
	
	
	//Facebook module
	private function base64_url_decode($input){
		return base64_decode(strtr($input, '-_', '+/'));
	}

	private function parse_signed_request() {
		$secret = $this->acore->fb_secret;
		$signed_request = $_POST['signed_request'];
		list($encoded_sig, $payload) = explode('.', $signed_request, 2);

		$sig = $this->base64_url_decode($encoded_sig);
		$data = json_decode($this->base64_url_decode($payload), true);

		if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
			error_log('Unknown algorithm. Expected HMAC-SHA256');
			return null;
		}

		$expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
		if ($sig !== $expected_sig) {
			error_log('Bad Signed JSON signature!');
			return null;
		}
		return $data;
	}

	public function install(){
		return "<a href='https://www.facebook.com/dialog/pagetab?app_id=".$this->acore->fb_apikey."&next=".$this->acore->fb_url."'>Instalar tab</a>";
	}

	public function info($data=''){
		if($data == 'liked' || $data == 'admin' || $data == 'id' || $data == 'app_data'){
			$result = $this->parse_signed_request();
			if($data == 'app_data'){
				return $result['app_data'];
			}else{
				return $result['page'][$data];
			}
		}else{
			echo "Use 'liked','admin','id','app_data'";
		}
	}

	public function request(){
		return $this->parse_signed_request();
	}

	public function header($init_script = '',$canvas=TRUE){
		$script = "<div id='fb-root'></div>
		<script>
				window.fbAsyncInit = function() {

		        FB.init({
		          appId: '".$this->acore->fb_apikey."',
		          status : true,
		          cookie: true,
		          xfbml: true,
		          oauth: true
		        });";

		//Canvas
		if($canvas=TRUE){
			$script .= "FB.Canvas.setAutoGrow();";
		}

		$script .= $init_script;
		$script .= "};

		      (function() {
		        var e = document.createElement('script'); e.async = true;
		        e.src = document.location.protocol +
		          '//connect.facebook.net/en_US/all.js';
		        document.getElementById('fb-root').appendChild(e);
		      }());
		</script>";
		echo $script;
	}

	public function footer(){
		$script = "<!-- NO FACEBOOK --><script>
			if (parent.location.href == self.location.href) {
				window.location.href = '".$this->acore->fb_url."';
			}";

		if(preg_match('/apps.facebook.com/',$_SERVER[HTTP_REFERER])){
			$script .= "top.location.href = '".$this->acore->fb_url."';";
		}

		$script .= "</script>";
		echo $script;
	}

	public function authorized($fb){
		$user = $fb->getUser();
		if ($user) {
			try {
				$this->user = $fb->api('/me');
				return TRUE;
			} catch (FacebookApiException $e) {
				$user = null;
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	public function btn_login($no_login,$no_permission,$with_permission){

		$permissions = explode(',',$this->acore->fb_scope);

		if(count($permissions) > 1){
			$scope_conditional = array();
			foreach ($permissions as $p){
				$scope_conditional[]='response.data[0].'.$p.'== 1';
			}
			$conditional = implode(' && ', $scope_conditional);
		}else{
			$conditional = 'response.data[0].'.$permissions[0].'== 1';
		}
		$script = "FB.login(function(response) {
		        	   if (response.authResponse) {
		        	     FB.api('/me', function(response) {
		        	       	FB.api('/me/permissions/', function(response) {
		        	    	   	if(".$conditional."){
		        	    		   	".$with_permission."
		        	    	   	}else{
									".$no_permission."
								}
		        	    	});
		        	     });
		        	   } else {
		          	     ".$no_login."
		        	   }
		}, {scope: '".$this->acore->fb_scope."'});";
		return $script;
	}

	public function registred(){
		$data = $this->model->querySelect($this->acore->fb_table,"*",$this->acore->fb_field." = :idfb",array('idfb'=>$this->user['id']));
		if(is_array($data) && count($data) > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}
}