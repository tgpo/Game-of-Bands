<?php session_start(); ?>
<?php
	if(@$_SERVER['PATH_INFO'] == "/logout") {
		session_destroy();
		header('Location: ../index.php');
		die;
	}
?>
 
<?php
if (isset($_GET["error"]))
{
    echo("<pre>OAuth Error: " . $_GET["error"]."\n");
    echo('<a href="index.php">Retry</a></pre>');
    die;
}

$authorizeUrl = 'https://ssl.reddit.com/api/v1/authorize';
$accessTokenUrl = 'https://ssl.reddit.com/api/v1/access_token';
$clientId = 'xxxclientIDxxx';
$clientSecret = 'xxxclientSecretxxx';

$redirectUrl = "http://gameofbands.co/login.php";

require("includes/Client.php");
require("includes/GrantType/IGrantType.php");
require("includes/GrantType/AuthorizationCode.php");

$client = new OAuth2\Client($clientId, $clientSecret, OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);

if (isset($_GET["code"]))
{
	$_SESSION['GOB']['code']=$_GET["code"];
	$_SESSION['GOB']['loggedin']=true;
}

if (!isset($_SESSION['GOB']['loggedin']))
{
	$_SESSION['GOB']['loggedin']=false;
}
	
if ($_SESSION['GOB']['loggedin'])
{
    $params = array("code" => $_SESSION['GOB']['code'], "redirect_uri" => $redirectUrl);

    $response = $client->getAccessToken($accessTokenUrl, "authorization_code", $params);
	
    $accessTokenResult = $response["result"];
	
	if (isset($_SESSION['GOB']['token'])) {
		$client->setAccessToken($_SESSION['GOB']['token']);
	} else {
		$client->setAccessToken($accessTokenResult["access_token"]);
		$_SESSION['GOB']['token'] = $accessTokenResult["access_token"];
	}
	
    $client->setAccessTokenType(OAuth2\Client::ACCESS_TOKEN_BEARER);

    $response = $client->fetch("https://oauth.reddit.com/api/v1/me.json");
	$_SESSION['GOB']['name'] = $response["result"]["name"];
	$_SESSION['GOB']['karma'] = $response["result"]["link_karma"];
	
	
	$response = $client->fetch("https://oauth.reddit.com/r/gameofbands/about.json");
	$_SESSION['GOB']['ismod'] = $response["result"]['data']['user_is_moderator'];
	
	header('Location: index.php');
	
} else {
	$authUrl = $client->getAuthenticationUrl($authorizeUrl, $redirectUrl, array("scope" => "identity,read", "state" => "SomeUnguessableValue"));
	header('Location: '.$authUrl);
}
?>