<?php
namespace zoneroot\fbconnect;

use \Facebook\GraphUser;

class fbdata extends graphUser
{
	public function getProfilePicture($width = null, $height = null)
	{
		$url = 'http://graph.facebook.com/'. $this->getProperty('id') .'/picture';
		if ($width & $height)
			$url .= '?width='. $width .'&height='. $height;
		return $url; 
	}
}
?>