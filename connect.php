<?php
//Require the connection class
require_once('twitterconnect.php');

//Instantiate the TwitterConnect class
$twitterconn = new TwitterConnect();

//Get temporary oauth token
if(isset($_GET["getRequestToken"]))
{	
	$twitterconn->getRequestToken();
}

//Get access token
if(isset($_GET["getAccessToken"]))
{		
	$twitterconn->getAccessToken();
}

//Clean sessions - logout
if(isset($_GET["disconnect"]))
{
	$twitterconn->clearSessions();
}

//Verify if CONSUMER KEY OR CONSUMER SERCRET are empty.
if (TwitterConnect::$CONSUMER_KEY === '' || TwitterConnect::$CONSUMER_SECRET === '') {
  echo 'You need a consumer key and a consumer secret to use this code. To get one, go to <a href="https://twitter.com/apps">https://twitter.com/apps</a>';
  exit;
}

//Link to sign in with twitter
$content = '
<p><a href="connect.php?disconnect">Click  here</a> to clean old sessions.</p><br><br>
      
      To access, click on the image:<br/>
<a href="./connect.php?getRequestToken"><img src="./images/darker.png" alt="Sign in with Twitter" border="0"/></a>';
 
echo $content; 
?>




