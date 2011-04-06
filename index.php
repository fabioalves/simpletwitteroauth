<?php
//Require the connection class
require_once('twitterconnect.php');

//Instantiate the TwitterConnect class
$twitterconn = new TwitterConnect();

//Get user credentials
$content = $twitterconn->getCredentials();

//Write user screen name
echo "Bem vindo @".$content->screen_name;
?>
<br/>
<!-- Logout the application -->
<a href="connect.php?disconnect">Clean Sessions</a>