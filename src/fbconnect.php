<?php
namespace zoneroot\fbconnect;

use \Facebook\FacebookSession;
use \Facebook\FacebookRequest;
use \Facebook\FacebookRedirectLoginHelper;

class fbconnect
{
	private $appId;
	private $appSecret;
	private $callbackUrl;
	private $pageUrl;
	private $session;

	public function __construct($appId, $appSecret, $callbackUrl, $pageUrl)
	{
		$this->appId = $appId;
		$this->appSecret = $appSecret;
		$this->callbackUrl = $callbackUrl;
		$this->pageUrl = $pageUrl;
		FacebookSession::setDefaultApplication($appId, $appSecret);
	}

	public function getAccess($token)
	{
		if (!$this->getAccessToken())
			if (!$token)
				return '<script>window.fbAsyncInit=function(){FB.getLoginStatus(function(response){statusChangeCallback(response);});};</script>';
			else
			{
				$this->setAccessToken($token);
				header('Location: ' . $this->pageUrl);
			}		
	}

	public function displayButton($ifLogged = false)
	{
		if ($ifLogged & $this->isLogged())
			return false;
		else
			return '<div id="fb-root"></div><script>function statusChangeCallback(response){if(response.status==="connected"){window.location="'. $this->callbackUrl .'"+response.authResponse.accessToken;}};function checkLoginState(){FB.getLoginStatus(function(response){statusChangeCallback(response);});};(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(d.getElementById(id))return;js=d.createElement(s);js.id=id;js.src="//connect.facebook.net/fr_FR/sdk.js#xfbml=1&appId='. $this->appId .'&version=v2.0";fjs.parentNode.insertBefore(js, fjs);}(document,"script","facebook-jssdk"));</script><div class="fb-login-button" data-max-rows="1" data-size="xlarge" data-show-faces="true" data-auto-logout-link="true" data-scope="email" onlogin="checkLoginState"></div>';
	}

	private function setAccessToken($token)
	{
		$_SESSION['fbAccessToken'] = $token;
	}

	private function getAccessToken()
	{
		if (isset($_SESSION['fbAccessToken']))
		{
			$this->session = new FacebookSession($_SESSION['fbAccessToken']);
			return $_SESSION['fbAccessToken'];
		}	
		else 
			return false;
	}

	private function isLogged()
	{
		if (isset($_SESSION['fbAccessToken']))
			return true;
		else
			return false;
	}

	public function logout()
	{
		if (isset($_SESSION['fbAccessToken']))
			unset($_SESSION['fbAccessToken']);
		if (isset($_SESSION['fbData']))
			unset($_SESSION['fbData']);
	}

	public function getData()
	{
		if (isset($_SESSION['fbData']))
			return unserialize($_SESSION['fbData']);
		else
		{
			$request = new FacebookRequest($this->session, 'GET', '/me');
			$profile = $request->execute()->getGraphObject('\zoneroot\fbconnect\fbdata');
			$_SESSION['fbData'] = serialize($profile);
			return $profile;
		}
		
	}

	public function reRequest()
	{
		$helper = new FacebookRedirectLoginHelper("http://local.dev/projets/zoneroot/fbconnect/test.php");
		echo '<a href="'.$helper->getReRequestUrl(['email']).'">rerequest</a>';
	}
}
?>