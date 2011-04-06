<?php
//Require Abraham Williams' OAuth Library
require_once('twitteroauth/twitteroauth.php');

//Require the CONFIG file
require_once("config.php");

/**
 * 
 * @author Fabio Alves
 * fabioalves101@gmail.com
 * @fabioperalves
 * Please keep these data. Thanks
 * 
 */

class TwitterConnect
{
	public static $CONSUMER_KEY = CONSUMER_KEY;
	public static $CONSUMER_SECRET = CONSUMER_SECRET;
	
	public static $OAUTH_CALLBACK = 'connect.php?getAccessToken';
	public static $SITE_URL_BASE = SITE_URL_BASE;

	public function __construct()
	{
		session_start();
	}

	public function getRequestToken()
	{
		try {
			$connection = new TwitterOAuth(TwitterConnect::$CONSUMER_KEY, TwitterConnect::$CONSUMER_SECRET);

			/* Get temporary credentials. */
			$request_token = $connection->getRequestToken(TwitterConnect::$SITE_URL_BASE.TwitterConnect::$OAUTH_CALLBACK);

			// Save the credentials in a temporary session
			$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
			$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

			if ($connection->http_code == 200) {
				// Get the authorization and redirect to twitter
				$url = $connection->getAuthorizeURL($token);
				
				header('Location: ' . $url);
			}else {
				throw new Exception();
			}
		} catch (Exception $e) {
			throw new Exception('Conexão ao Twitter não realizada. Atualize sua página ou tente novamente. '.$e->getMessage());
		}
	}

	public function getAccessToken()
	{
		try {
			// If the OAuth Token expire, redirect to logout
			if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
				$_SESSION['oauth_status'] = 'oldtoken';
				$this->clearSessions();				
			}

			// Create TwitterOAuth object with app client credentials
			$connection = new TwitterOAuth(TwitterConnect::$CONSUMER_KEY, TwitterConnect::$CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

			// Take the access token
			$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

			// Save the access token in a session. You can save in a database if you want.
			$_SESSION['access_token'] = $access_token;

			// Clean OAuth info sessions
			unset($_SESSION['oauth_token']);
			unset($_SESSION['oauth_token_secret']);

			if (200 == $connection->http_code) {
				// Set user status to verified
				$_SESSION['status'] = 'verified';
				header('Location: ./index.php');
			} else {
				$this->clearSessions();
			}
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}

	}

	//Clean all sessions
	public function clearSessions()
	{	
		session_start();
		session_destroy();

		// After clean sessions, redirect to connect page.
		header('Location: ./connect.php');

	}

	//Get user credentials
	public function getCredentials()
	{	
		// Clear sessions if the session expire.
		if (empty($_SESSION['access_token'])){
			$this->clearSessions();
		}
		// Save access token.
		$access_token = $_SESSION['access_token'];

		// Create a TwitterOAuth object with app client credentials
		$connection = new TwitterOAuth(TwitterConnect::$CONSUMER_KEY, TwitterConnect::$CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
		

		// Call the API method
		$content = $connection->get('account/verify_credentials');

		return $content;
	}





}