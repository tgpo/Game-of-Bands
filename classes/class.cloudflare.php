<?php
/**
 * Creates a PHP interface to the CloudFlare API
 * 
 * Don't let the CF_TOKEN_KEY be exposed!
 * 
 * 
 * Cobbled together with some code from: http://www.bhagwad.com/blog/2013/technology/using-php-with-wordpress-to-purge-a-single-file-from-cloudflare.html/
 * API: https://www.cloudflare.com/docs/client-api.html
 * 
 * @author clonemeagain@gmail.com
 * @version 1
 */
class CloudFlare {
	
	const DOMAIN = 'gameofbands.co';
	/**
	 * API key from account page.
	 * @var string
	 */
	const TOKEN_KEY = ''; 
	/**
	 * email/username on CF account
	 * @var string
	 */
	const ACCOUNT = ''; 
	/**
	 * Endpoint of the API
	 * @var unknown
	 */
	const ENDPOINT = 'https://www.cloudflare.com/api_json.html';
			
	const TECH_EMAIL = 'clonemeagain@gmail.com'; // Where we send notifications.
	const NOTIFICATION_SUBJECT = "[GOB] File purged from CF";
	const NOTIFICATIONS_ON = true; // Might want to disable notifications after testing.
	
	/**
	 * Purge a file cached by CloudFlare from their cache.
	 * This is usually used because something has changed, and we want the changes
	 * to be reflected now.
	 * @param string $uri eg: /path/to/file.js
	 */
	static function purge($uri=false) {
		
		if(!$uri){
			$uri = '/'; //homepage.
		}
		
		$fields = array (
				'a' => 'zone_file_purge',
				'tkn' => self::TOKEN_KEY,
				'email' => self::ACCOUNT,
				'z' => self::DOMAIN,
				'url' => self::DOMAIN . $uri
		);
		
		// url-ify the data for the POST
		foreach ( $fields as $key => $value ) {
			$fields_string .= $key . '=' . $value . '&';
		}
		rtrim ( $fields_string, '&' );
		
		// open connection
		$ch = curl_init ();
		
		// set the url, number of POST vars, POST data
		curl_setopt ( $ch, CURLOPT_URL, self::ENDPOINT );
		curl_setopt ( $ch, CURLOPT_POST, count ( $fields ) );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields_string );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		
		// execute post
		$result = curl_exec ( $ch );
		
		// close connection
		curl_close ( $ch );
		
		if(self::NOTIFICATIONS_ON)
			mail ( self::TECH_EMAIL, self::NOTIFICATION_SUBJECT , $result );
	}
}