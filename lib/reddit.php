<?php
/**
* Reddit PHP SDK
* http://drupalcode.org/project/reddit_api.git/blob/075d5ca6b6b0a6333f9b5d741f665ec8eaadd078:/inc/reddit_api-php-sdk.inc
*
* Provides a SDK for accessing the Reddit APIs
* Useage: 
*   $reddit = new reddit();
*   $reddit->login("USERNAME", "PASSWORD");
*   $user = $reddit->getUser();
*/
class reddit{
    //private $apiHost = "http://www.reddit.com/api";
    private $apiHost = "https://ssl.reddit.com/api";
    private $modHash = null;
    private $session = null;
    
    /**
    * Class Constructor
    *
    * Construct the class and simultaneously log a user in.
    * @link https://github.com/reddit/reddit/wiki/API%3A-login
    * @param string $username The username to be logged into
    * @param string $password The password to be used to log in
    */
    public function __construct($username = null, $password = null){
        $urlLogin = "{$this->apiHost}/login/$username";
        
        $postData = sprintf("api_type=json&user=%s&passwd=%s",
                            $username,
                            $password);
        $response = $this->runCurl($urlLogin, $postData);
        
        if (count($response->json->errors) > 0){
            return "login error";    
        } else {
            $this->modHash = $response->json->data->modhash;   
            $this->session = $response->json->data->cookie;
            return $this->modHash;
        }
    }
    
    /**
    * Create new story
    *
    * Creates a new story on a particular subreddit
    * @link https://github.com/reddit/reddit/wiki/API%3A-submit
    * @param string $title The title of the story
    * @param string $link The link that the story should forward to
    * @param string $subreddit The subreddit where the story should be added
    */
    public function createStory($title = null, $link = null, $subreddit = null, $text = null){
        $urlSubmit = "{$this->apiHost}/submit";
        
        //data checks and pre-setup
        if ($title == null || $subreddit == null){ return null; }
        $kind = ($link == null) ? "self" : "link";
        
        $postData = sprintf("uh=%s&kind=%s&sr=%s&title=%s&r=%s&text=%s&renderstyle=html",
                            $this->modHash,
                            $kind,
                            $subreddit,
                            urlencode($title),
                            $subreddit,
							$text);
        
        //if link was present, add to POST data             
        if ($link != null){ $postData .= "&url=" . urlencode($link); }
    
        $response = $this->runCurl($urlSubmit, $postData);
		return $response;
        
        if ($response->jquery[18][3][0] == "that link has already been submitted"){
            return $response->jquery[18][3][0];
        }
    }
    
    /**
    * Site Admin
    *
    * Create or configure a subreddit
    * @link http://www.reddit.com/dev/api#POST_api_site_admin
    * @param array $settings The settings to be set for the subreddit
    * @param string $subreddit The subreddit where the story should be added
    */
    public function siteAdmin($subreddit = null, $allow_top = null, $api_type = null, $comment_score_hide_mins = null, $css_on_cname = null, $description = null, $exclude_banned_modqueue = null, $header_title = null, $lang = null, $link_type = null, $name = null, $over_18 = null, $public_description = null, $public_traffic = null, $show_cname_sidebar = null, $show_media = null, $spam_comments = null, $spam_links = null, $spam_selfposts = null, $submit_link_label = null, $submit_text = null, $submit_text_label = null, $title = null, $type = null, $wiki_edit_age = null, $wiki_edit_karma = null, $wikimode = null){
        $urlSubmit = "{$this->apiHost}/site_admin";
        
        //data checks and pre-setup
        if ($subreddit == null){ return null; }
      
      
        $postData = sprintf("uh=%s&sr=%s&allow_top=%s&api_type=%s&comment_score_hide_mins=%s&css_on_cname=%s&description=%s&exclude_banned_modqueue=%s&header-title=%s&lang=%s&link_type=%s&name=%s&over_18=%s&public_description=%s&public_traffic=%s&show_cname_sidebar=%s&show_media=%s&spam_comments=%s&spam_links=%s&spam_selfposts=%s&submit_link_label=%s&submit_text=%s&submit_text_label=%s&title=%s&type=%s&wiki_edit_age=%s&wiki_edit_karma=%s&wikimode=%s",
                            $this->modHash,
                            $subreddit,
                            $allow_top,
                            $api_type,
                            $comment_score_hide_mins,
                            $css_on_cname,
                            $description,
                            $exclude_banned_modqueue,
                            $header_title,
                            $lang,
                            $link_type,
                            $name,
                            $over_18,
                            $public_description,
                            $public_traffic,
                            $show_cname_sidebar,
                            $show_media,
                            $spam_comments,
                            $spam_links,
                            $spam_selfposts,
                            $submit_link_label,
                            $submit_text,
                            $submit_text_label,
                            $title,
                            $type,
                            $wiki_edit_age,
                            $wiki_edit_karma,
                            $wikimode
                           );
        
        $response = $this->runCurl($urlSubmit, $postData);
		return $response;
    }
    
    /**
    * Get Subreddit Settings
    *
    * Get the current settings of a subreddit.
    * @link http://www.reddit.com/dev/api#GET_r_{subreddit}_about_edit.json
    * @param string $sr The subreddit to get settings from
    */
    public function getAboutSettings($sr){
		if ($sr) {
			$settings = "http://www.reddit.com/r/{$sr}/about/edit.json";
		}
        $response = $this->runCurl($settings);
		
		return $response->data;
    }
    
    /**
    * Get user
    *
    * Get data for the current user
    * @link https://github.com/reddit/reddit/wiki/API%3A-me.json
    */
    public function getUser(){
        $urlUser = "{$this->apiHost}/me.json";
        return $this->runCurl($urlUser);
    }
	
    /**
    * Get stylesheet
    *
    * Get data for the subreddit's stylesheet
    * @link http://www.reddit.com/dev/api#GET_stylesheet
    */
    public function getStylesheet($sr){
		if ($sr) {
			$stylesheet = "http://www.reddit.com/r/{$sr}/about/stylesheet.json";
		}
        $response = $this->runCurl($stylesheet);
		
		return $response->data->stylesheet;
    }
	
	/***************************************************************************
	* Function: Send Message To User
	* Description: Send a message to a user.
	* API: https://github.com/reddit/reddit/wiki/API%3A-compose
	* Params: to (string): The username to send the message to.
	*         subject (string): The subject of the message.
	*         body (string): The message contents.
	***************************************************************************/
	public function sendMessage($to, $subject, $body){
		$response = null;
		if ($to && $subject && $body){
			$urlMessage = "{$this->apiHost}/compose";
			$postData = sprintf("to=%s&subject=%s&text=%s&uh=%s",
				$to,
				$subject,
				$body,
				$this->modHash);
			$response = $this->runCurl($urlMessage, $postData);
		}
		return $response;
	}
	
	/***************************************************************************
	* Function: readMessage
	* Description: Mark a Message as Read
	* API: http://www.reddit.com/dev/api#POST_api_read_message
	* Params: id (string): The fullname of the message to mark as read
	***************************************************************************/
	public function readMessage($id){
		$response = null;
		if ($id){
			$urlMessage = "{$this->apiHost}/read_message";
			$postData = sprintf("id=%s&uh=%s",
				$id,
				$this->modHash);
			$response = $this->runCurl($urlMessage, $postData);
		}
		return $response;
	}
    
    /**
    * Get user subscriptions
    *
    * Get the subscriptions that the user is subscribed to
    * @link https://github.com/reddit/reddit/wiki/API%3A-mine.json
    */
    public function getSubscriptions(){
        $urlSubscriptions = "http://www.reddit.com/reddits/mine.json";
        return $this->runCurl($urlSubscriptions);
    }
	
    /**
    * Get user inbox messages
    *
    * Get the inbox messages for a user
    */
    public function getInboxMessages(){
        $urlInboxMessages = "http://www.reddit.com/message/messages/.json";
        return $this->runCurl($urlInboxMessages);
    }
    
    /**
    * Get listing
    *
    * Get the listing of submissions from a subreddit
    * @link http://www.reddit.com/dev/api#GET_listing
    * @param string $sr The subreddit name. Ex: technology, limit (integer): The number of posts to gather
    */
    public function getListing($sr, $limit = 5){
        $limit = (isset($limit)) ? "?limit=".$limit : "";
        if($sr == 'home' || $sr == 'reddit' || !isset($sr)){
            $urlListing = "http://www.reddit.com/.json{$limit}";
        } else {
            $urlListing = "http://www.reddit.com/r/{$sr}/.json{$limit}";
        }
        return $this->runCurl($urlListing);
    }
	
    /**
    * Get Comments
    *
    * Get the listing of submissions from a subreddit
    * @link http://www.reddit.com/dev/api#GET_listing
    * @param string $sr The subreddit name. Ex: technology, limit (integer): The number of posts to gather
    */
    public function getcomments($sr, $limit = 5){
        $limit = (isset($limit)) ? "?limit=".$limit : "";
        if($sr == 'home' || $sr == 'reddit' || !isset($sr)){
            $urlListing = "http://www.reddit.com/.json{$limit}";
        } else {
            $urlListing = "http://www.reddit.com/r/{$sr}/comments/.json{$limit}";
        }
        return $this->runCurl($urlListing);
    }
	
    /**
    * Get Post Comments
    *
    * Get the listing of submissions from a subreddit
    * @link http://www.reddit.com/dev/api#GET_listing
    * @param string $sr The subreddit name. Ex: technology, limit (integer): The number of posts to gather
    */
    public function getpostcomments($sr,$postID, $limit = 5){
        $limit = (isset($limit)) ? "?limit=".$limit : "";
        if($sr == 'home' || $sr == 'reddit' || !isset($sr)){
            $urlListing = "http://www.reddit.com/.json{$limit}";
        } else {
            $urlListing = "http://www.reddit.com/r/{$sr}/comments/{$postID}/.json{$limit}";
        }
        return $this->runCurl($urlListing);
    }
	
    /**
    * Get Comment Replies
    *
    * Get the listing of submissions from a subreddit
    * @link http://www.reddit.com/dev/api#GET_listing
    * @param string $sr The subreddit name. Ex: technology, limit (integer): The number of posts to gather
    */
    public function getcommentreplies($sr,$postID,$commentID){
        $urlListing = "http://www.reddit.com/r/{$sr}/comments/{$postID}/.json?sort=new&comment={$commentID}";
        return $this->runCurl($urlListing);
    }
    
    /**
    * Get page information
    *
    * Get information on a URLs submission on Reddit
    * @link https://github.com/reddit/reddit/wiki/API%3A-info.json
    * @param string $url The URL to get information for
    */
    public function getPageInfo($ID,$type){
        $response = null;
        if ($ID){
            $urlInfo = "{$this->apiHost}/info.json?" . $type . "=" . urlencode($ID);
            $response = $this->runCurl($urlInfo);
        }
        return $response;
    }
    
    /**
    * Get Raw JSON
    *
    * Get Raw JSON for a reddit permalink
    * @param string $permalink permalink to get raw JSON for
    */
    public function getRawJSON($permalink){
        $urlListing = "http://www.reddit.com/{$permalink}.json";
        return $this->runCurl($urlListing);
    }  
         
    /**
    * Save post
    *
    * Save a post to your account.  Save feeds:
    * http://www.reddit.com/saved/.xml
    * http://www.reddit.com/saved/.json
    * @link https://github.com/reddit/reddit/wiki/API%3A-save
    * @param string $name the full name of the post to save (name parameter
    *                     in the getSubscriptions() return value)
    */
    public function savePost($name){
        $response = null;
        if ($name){
            $urlSave = "{$this->apiHost}/save";
            $postData = sprintf("id=%s&uh=%s", $name, $this->modHash);
            $response = $this->runCurl($urlSave, $postData);
        }
        return $response;
    }
    
    /**
    * Unsave post
    *
    * Unsave a saved post from your account
    * @link https://github.com/reddit/reddit/wiki/API%3A-unsave
    * @param string $name the full name of the post to unsave (name parameter
    *                     in the getSubscriptions() return value)
    */
    public function unsavePost($name){
        $response = null;
        if ($name){
            $urlUnsave = "{$this->apiHost}/unsave";
            $postData = sprintf("id=%s&uh=%s", $name, $this->modHash);
            $response = $this->runCurl($urlUnsave, $postData);
        }
        return $response;
    }
    
    /**
    * Get saved posts
    *
    * Get the listing of a user's saved posts 
    * @param string $username the desired user. Must be already authenticated.
    */
    public function getSaved($username){
        return $this->runCurl("http://www.reddit.com/user/".$username."/saved.json");
    }
    
    /**
    * Hide post
    *
    * Hide a post on your account
    * @link https://github.com/reddit/reddit/wiki/API%3A-hide
    * @param string $name The full name of the post to hide (name parameter
    *                     in the getSubscriptions() return value)
    */
    public function hidePost($name){
        $response = null;
        if ($name){
            $urlHide = "{$this->apiHost}/hide";
            $postData = sprintf("id=%s&uh=%s", $name, $this->modHash);
            $response = $this->runCurl($urlHide, $postData);
        }
        return $response;
    }
    
    /**
    * Unhide post
    *
    * Unhide a hidden post on your account
    * @link https://github.com/reddit/reddit/wiki/API%3A-unhide
    * @param string $name The full name of the post to unhide (name parameter
    *                     in the getSubscriptions() return value)
    */
    public function unhidePost($name){
        $response = null;
        if ($name){
            $urlUnhide = "{$this->apiHost}/unhide";
            $postData = sprintf("id=%s&uh=%s", $name, $this->modHash);
            $response = $this->runCurl($urlUnhide, $postData);
        }
        return $response;
    }
    
    /**
    * Share a post
    *
    * E-Mail a post to someone
    * @link https://github.com/reddit/reddit/wiki/API
    * @param string $name The full name of the post to share (name parameter
    *                     in the getSubscriptions() return value)
    * @param string $shareFrom The name of the person sharing the story
    * @param string $replyTo The e-mail the sharee should respond to
    * @param string $shareTo The e-mail the story should be sent to
    * @param string $message The e-mail message
    */
    public function sharePost($name, $shareFrom, $replyTo, $shareTo, $message){
        $urlShare = "{$this->apiHost}/share";
        $postData = sprintf("parent=%s&share_from=%s&replyto=%s&share_to=%s&message=%s&uh=%s",
                            $name,
                            $shareFrom,
                            $replyTo,
                            $shareTo,
                            $message,
                            $this->modHash);
        
        $response = $this->runCurl($urlShare, $postData);
        return $response;
    }
    
    /**
    * Add new comment
    *
    * Add a new comment to a story
    * @link https://github.com/reddit/reddit/wiki/API%3A-comment
    * @param string $name The full name of the post to comment (name parameter
    *                     in the getSubscriptions() return value)
    * @param string $text The comment markup
    */
    public function addComment($name, $text){
        $response = null;
        if ($name && $text){
            $urlComment = "{$this->apiHost}/comment";
            $postData = sprintf("thing_id=%s&text=%s&uh=%s",
                                $name,
                                $text,
                                $this->modHash);
            $response = $this->runCurl($urlComment, $postData);
        }
        return $response;
    }
    
    /**
    * Vote on a story
    *
    * Adds a vote (up / down / neutral) on a story
    * @link https://github.com/reddit/reddit/wiki/API%3A-vote
    * @param string $name The full name of the post to vote on (name parameter
    *                     in the getSubscriptions() return value)
    * @param int $vote The vote to be made (1 = upvote, 0 = no vote,
    *                  -1 = downvote)
    */
    public function addVote($name, $vote = 1){
        $response = null;
        if ($name){
            $urlVote = "{$this->apiHost}/vote";
            $postData = sprintf("id=%s&dir=%s&uh=%s", $name, $vote, $this->modHash);
            $response = $this->runCurl($urlVote, $postData);
        }
        return $response;
    }
    
    /**
    * Set flair
    *
    * Set or clear a user's flair in a subreddit
    * @link https://github.com/reddit/reddit/wiki/API%3A-flair
    * @param string $subreddit The subreddit to use
    * @param string $user The name of the user
    * @param string $text Flair text to assign
    * @param string $cssClass CSS class to assign to the flair text
    */
    public function setFlair($subreddit, $user, $text, $cssClass){
        $urlFlair = "{$this->apiHost}/flair";
        $postData = sprintf("r=%s&name=%s&text=%s&css_class=%s&uh=%s",
                            $subreddit,
                            $user,
                            $text,
                            $cssClass,
                            $this->modHash);
        $response = $this->runCurl($urlFlair, $postData);
        return $response;
    }
    
    /**
    * Get flair list
    *
    * Download the flair assignments of a subreddit
    * @link https://github.com/reddit/reddit/wiki/API%3A-flairlist
    * @param string $subreddit The subreddit to use
    * @param int $limit The maximum number of items to return (max 1000)
    * @param string $after Return entries starting after this user
    * @param string $before Return entries starting before this user
    */
	public function getFlairList($subreddit, $limit = 100, $after = NULL){
		$urlFlairList = "http://www.reddit.com/r/$subreddit/api/flairlist.json?";
		$postData = sprintf("&limit=%s&uh=%s",
			$limit,
			$this->modHash);
		if (!is_null($after)) {
			$postData .= sprintf('&after=%s', $after);
		}
		$urlFlairList = $urlFlairList . $postData;
		$response = $this->runCurl($urlFlairList);
		return $response;
	}
    
    /**
    * Set flair CSV file
    *
    * Post a CSV file of flair settings to a subreddit
    * @link https://github.com/reddit/reddit/wiki/API%3A-flaircsv
    * @param string $subreddit The subreddit to use
    * @param string $flairCSV CSV file contents, up to 100 lines
    */
    public function setFlairCSV($subreddit, $flairCSV){
        $urlFlairCSV = "{$this->apiHost}/flaircsv.json";
        $postData = sprintf("r=%s&flair_csv=%s&uh=%s",
                            $subreddit,
                            $flairCSV,
                            $this->modHash);
        $response = $this->runCurl($urlFlairCSV, $postData);
        return $response;
    }
    
    /**
    * cURL request
    *
    * General cURL request function for GET and POST
    * @link URL
    * @param string $url URL to be requested
    * @param string $postVals NVP string to be send with POST request
    */
    private function runCurl($url, $postVals = null){
        $ch = curl_init($url);
        
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIE => "reddit_session={$this->session}",
            CURLOPT_TIMEOUT => 3
        );
        
        if ($postVals != null){
            $options[CURLOPT_POSTFIELDS] = $postVals;
            $options[CURLOPT_CUSTOMREQUEST] = "POST";  
        }
        
        curl_setopt_array($ch, $options);
        
        $response = json_decode(curl_exec($ch));
        curl_close($ch);
        
        return $response;
    }
}
?>