<?php
require_once __DIR__ . '/lib/src/Facebook/autoload.php';

class FacebookAPI
{
    public $LoginUrl = "";
    var $FacebookObject;
	public $Helper="";
    function __construct()
    {
		session_start();
        $this->FacebookObject = new \Facebook\Facebook([
            'app_id' => 'Enter your app id',
            'app_secret' => 'enter your app secret',
            'default_graph_version' => 'v2.10',
        ]);
		$this->FacebookObject->Helper = $this->FacebookObject->getRedirectLoginHelper();
    }
	function facebookLogin(){
		try {	
			if (isset($_SESSION['facebook_access_token'])) {
				$AccessToken = $_SESSION['facebook_access_token'];
			} else {
				$AccessToken = $this->FacebookObject->Helper->getAccessToken();
			}
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}
		if (isset($AccessToken)) {
			if (isset($_SESSION['facebook_access_token'])) {
				header("location:Album.php");
			} else {	
				$this->checkAccessToken($AccessToken);
			}
			if (isset($_GET['code'])) {
				header('Location:Album.php');
			}
		}
		else{
			$this->LoginUrl=$this->getLoginUrl();
		}
	}
	
	function getLoginUrl(){
		$Permissions = ['email','user_photos'];
		return $this->FacebookObject->Helper->getLoginUrl('http://localhost/fbjasmin/', $Permissions);
	}
    function checkAccessToken($AccessToken)
    {
        $_SESSION['facebook_access_token'] = (string)$AccessToken;
        $OAuth2Client = $this->FacebookObject->getOAuth2Client();
        $longLivedAccessToken = $OAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
        $_SESSION['facebook_access_token'] = (string)$longLivedAccessToken;
        $this->FacebookObject->setDefaultAccessToken($_SESSION['facebook_access_token']);
    }
	function getUserInfo(){
		 try {
			$ProfileRequest = $this->FacebookObject->get('/me?fields=picture.width(200).height(200),id,name,cover');
			return $ProfileRequest->getGraphNode()->asArray();
		} catch (Facebook\Exceptions\FacebookResponseException $e) {
			echo 'Graph returned an error: ' . $e->getMessage();
			header("Location: ./");
			exit;
		} catch (Facebook\Exceptions\FacebookSDKException $e) {
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}
	}
	function getUserAlbums($UserId){
		try{
			$UserAlbumsResponse = $this->FacebookObject->get("/" . $UserId . "/albums?fields=picture,name,id");
			return $UserAlbumsResponse->getGraphEdge()->asArray();
		} catch (Facebook\Exceptions\FacebookResponseException $e) {
			echo 'Graph returned an error: ' . $e->getMessage();
			header("Location: ./");
			exit;
		} catch (Facebook\Exceptions\FacebookSDKException $e) {
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}
	}
	function getUserAlbumImages($AlbumId){
		try{
			$UserAlbumImageResponse = $this->FacebookObject->get("/" . $AlbumId . "/photos?fields=source,name,id&");
				return $UserAlbumImageResponse->getGraphEdge();
		} catch (Facebook\Exceptions\FacebookResponseException $e) {
			echo 'Graph returned an error: ' . $e->getMessage();
			header("Location: ./");
			exit;
		} catch (Facebook\Exceptions\FacebookSDKException $e) {
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}
	}
	function nextAlbum($album)
	{
			return $this->FacebookObject->next($album);
	}
	function previousAlbum($album)
	{
			return $this->FacebookObject->previous($album);
	}
}

?>