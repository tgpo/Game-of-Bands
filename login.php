<?php
session_start ();

$authorizeUrl = 'https://ssl.reddit.com/api/v1/authorize';
$accessTokenUrl = 'https://ssl.reddit.com/api/v1/access_token';
$clientId = 'nQqfxY21eTYtAA'; // 'xAMRDDFMwt-nMQ'; //second setup is Aaron's API Key. uses incorrect redirect url http://ftp.gameofbands.co/login.php
$clientSecret = 'czjY0dv6poPpEdOioL1sf9AzfHQ'; // 'y9T32qEbGa5brqcIdrNL_EqF6Uo';
$redirectUrl = "http://gameofbands.co/login.php";

$user_url = "https://oauth.reddit.com/api/v1/me";
$subreddit_url = "https://oauth.reddit.com/r/gameofbands/about.json";

$USER_AGENT = array (
		'User-Agent' => 'GameOfBands.co/User-Login-Agent v1.0' 
);

if (@$_SERVER ['PATH_INFO'] == "/logout") {
	logout ();
}

// only generate state code once per session.
if (! isset ( $_SESSION ['state'] )) {
	$_SESSION ['state'] = gen_random_string (); 
}

// We need to make sure we aren't using too many requests from the Reddit API: https://github.com/reddit/reddit/wiki/API
$reddit_headers = new GOB_Header ();

if (isset ( $_GET ["error"] )) {
	echo ("<pre>OAuth Error: " . $_GET ["error"] . "\n");
	echo ('<a href="index.php">Retry</a></pre>');
	die ();
}

require ("lib/Client.php");
require ("lib/GrantType/IGrantType.php");
require ("lib/GrantType/AuthorizationCode.php");

$client = new OAuth2\Client ( $clientId, $clientSecret, 
		OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC );

if (isset ( $_GET ["code"] )) {
	$_SESSION ['GOB'] ['code'] = $_GET ["code"];
	if ($_GET ['state'] == $_SESSION ['state']) {
		$_SESSION ['GOB'] ['state'] = $_SESSION ['state'];
	} else {
		logout ( "CSRF: Token is invalid!." ); // By calling logout here, we are trashing the session, so future attempts can work.
	}
	$_SESSION ['GOB'] ['loggedin'] = true;
} else {
	$authUrl = $client->getAuthenticationUrl ( $authorizeUrl, $redirectUrl, 
			array (
					'response_type' => 'code',
					'duration' => 'temporary',
					'client_id' => $clientId,
					"scope" => "identity,read",
					"state" => $_SESSION ['state'] 
			) );
	header ( 'Location: ' . $authUrl );
	die ( "Redirecting.." );
}

if (! isset ( $_SESSION ['GOB'] ['loggedin'] )) {
	$_SESSION ['GOB'] ['loggedin'] = false;
}
else {
	$params = array (
			'code' => $_SESSION ['GOB'] ['code'],
			'redirect_uri' => $redirectUrl,
			'state' => $_SESSION ['state'] 
	);
	
	$response = $client->getAccessToken ( $accessTokenUrl, 
			"authorization_code", $params );
	if (! is_array ( $response ['result'] )) {
		logout ( "Invalid token response.." );
	}
	$accessTokenResult = $response ["result"];
	$client->setAccessToken ( $accessTokenResult ["access_token"] );
	$_SESSION ['GOB'] ['token'] = $accessTokenResult ["access_token"]; // why ? are we likely to refresh this?
	$client->setAccessTokenType ( OAuth2\Client::ACCESS_TOKEN_BEARER );
	
	$response = $client->fetch ( $user_url, array (), 'GET', $USER_AGENT );
	if (! is_array ( $response ['result'] )) {
		logout ( 
				"<h3>We are currently unable to load your user-data from Reddit.com!</h3><p>Please bear with us while we work on this error!.</p>" );
	}
	$_SESSION ['GOB'] ['name'] = $response ["result"] ["name"];
	$_SESSION ['GOB'] ['karma'] = $response ["result"] ["link_karma"];
	
	// We've made an API request, so we can check the headers reddit have sent
	check_rate_limits ();
	
	$response = $client->fetch ( $subreddit_url, array (), 'GET', $USER_AGENT );
	if (! is_array ( $response ['result'] )) {
		logout ( "U wot mate?!?: Please <a href=\"mailto:clonemeagain@gmail.com\">contact us immediately</a> if you are seeing this error!" );
	}
	$_SESSION ['GOB'] ['ismod'] = $response ["result"] ['data'] ['user_is_moderator'];
	
	$name = $_SESSION ['GOB'] ['name'];
	$is_mod = $_SESSION ['GOB'] ['ismod'];
	$banned = false;
	
	require ('src/query.php');
	$db = database_connect ();
	
	$query = $db->query ( "SELECT * FROM bandits WHERE name = '$name'" );
	$bandit = $query->fetch ();
	
	if (! $bandit ['name']) {
		$query = $db->query ( "INSERT INTO bandits (name, is_mod, banned) VALUES ('$name', '$is_mod', '$banned')" );
	}
	// Return user to last page navigated to.
	if(isset($_SESSION['last'])){
		header ( 'Location: http://gameofbands.co' . $_SESSION['last']);
	}else{
		header ( 'Location: /index.php' );
	}
}


/**
 * We need to make a random string which we send with our auth request, this value is then returned with the response,
 * We can therefore validate that each user has returned "their" authentication correctly and distinctly!
 * 
 * @param number $length        	
 * @return string
 */
function gen_random_string($length = 16) {
	$c = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
	$r = '';
	for($i = 0; $i < $length; $i ++) {$r .= $c [rand ( 0, strlen ( $c ) - 1 )];}
	return $r;
}

/**
 * Reddit requires you check your API usage, they notify you of ratelimiting via http response headers with the responses.
 * This class contains the data and functions to parse the headers and determine if we have available requests.
 * If the time till reset is small, it simply pauses execution for a few seconds.. hopefully it won't be too obstructive.
 * TODO: Abstract this into the Client class, possibly submit our changes back to the community so others can do it.
 * 
 * @author Aaron Were
 */
function check_rate_limits() {
	global $reddit_headers;
	// We have made an API request, check the response headers to see how many we have left
	if (! $reddit_headers->check_usage ()) {
		// OK, so something went wrong, or we are using waaay too many requests, like we've become like Super Popular and might need some other auth scheme!
		$backoff_timer = $reddit_headers->get_backoff_time ();
		$user = (isset($_SESSION ['GOB'] ['name'])) ? ' ' . $_SESSION ['GOB'] ['name'] . ', ' : ',';
		logout (
		"<h1>Woah</h1>
		<p><strong>Something is either really awesome, or really bad right now</strong>. I'm sorry$user please wait $backoff_timer seconds & <a href=\"/login\">try again</a>." );
	}
	//error_log($reddit_headers);
}
class GOB_Header {
	private $headers = array (
			'X-Ratelimit-Used' => 'ratelimit_used', // Approximate number of requests used in this period
			'X-Ratelimit-Remaining' => 'ratelimit_remaining', // Approximate number of requests left to use
			'X-Ratelimit-Reset' => 'ratelimit_reset'  // Approximate number of seconds to end of period
		);
	private $ratelimit_used, $ratelimit_remaining, $ratelimit_reset;
	public function __construct() {
		$this->ratelimit_remaining = 60; // Setup defaults.. optimistic!
		$this->ratelimit_used = 0;
		$this->ratelimit_reset = 60;
	}
	public function __toString(){
		return 'RL-Used: ' . $this->ratelimit_used . ', RL-Remaining: ' . $this->ratelimit_remaining . ', RL-reset-time: ' . $this->ratelimit_reset;
	}
	
	public function addHeader($header) {
		// Parse the header from "header: value" into array('header' => 'value')
		foreach ( http_parse_headers ( $header ) as $h => $value ) {
			// There are probably lots of headers that we aren't interested in, only save the reddit specific rate-limiting ones.
			if (array_key_exists ( $h, $this->headers )) {
				$this->$headers [$h] = $value;
			}
		}
	}
	/**
	 * Determine if the current API usage is within bounds
	 * @return boolean 'True' if within bounds, 'False' if probably going over or about to!
	 */
	public function check_usage() {
		if ($this->ratelimit_remaining < 5){ // less than 5 left means lots of people are attempting to login at once, we've used 55+ requests in 60s!
			if($this->ratelimit_reset < 5){
				sleep($this->ratelimit_reset);
				error_log("Paused execution due to limited available requests and short reset timer interval.. ");
				return true;
			}else{
				// back-off
				error_log("RATE LIMIT REACHED!!! --- Should probably start batching these requests.");
				return false;
		 }
		 	// Just log for now
		 	error_log("RedditAPI requests left: " . $this->ratelimit_remaining . ', resets in: ' . $this->ratelimit_reset);
		}
		// Probably OK, there are at least 5 requests available this minute.
		return true;		
	}
	public function get_backoff_time() {
		return $this->ratelimit_reset;
	}
}

/**
 * Formalize the logout, so we can call it from multiple places.
 */
function logout($msg = false) {
	session_destroy ();
	if ($msg) {
		die ( $msg );
	} else {
		header ( 'Location: ../index.php' );
		die ();
	}
}

/// http://php.net/manual/en/function.http-parse-headers.php
function http_parse_headers($raw_headers)
{
	$headers = array();
	$key = ''; // [+]

	foreach(explode("\n", $raw_headers) as $i => $h)
	{
		$h = explode(':', $h, 2);

		if (isset($h[1]))
		{
			if (!isset($headers[$h[0]]))
				$headers[$h[0]] = trim($h[1]);
			elseif (is_array($headers[$h[0]]))
			{
				// $tmp = array_merge($headers[$h[0]], array(trim($h[1]))); // [-]
				// $headers[$h[0]] = $tmp; // [-]
				$headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1]))); // [+]
			}
			else
			{
				// $tmp = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [-]
				// $headers[$h[0]] = $tmp; // [-]
				$headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [+]
			}

			$key = $h[0]; // [+]
		}
		else // [+]
		{ // [+]
			if (substr($h[0], 0, 1) == "\t") // [+]
				$headers[$key] .= "\r\n\t".trim($h[0]); // [+]
			elseif (!$key) // [+]
			$headers[0] = trim($h[0]);trim($h[0]); // [+]
		} // [+]
	}

	return $headers;
}
?>
