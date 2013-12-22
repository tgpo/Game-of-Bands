<?php
/**
* Game of Bands Class for round start and end functions
* Useage: 
*   $gob = new gob();
*   $gob->postSignups('34');
*   $gob->postWinners();
*/

class gob{
    private $mainsubreddit = null;
    private $_db = null;
    
    public function __construct($username = null, $password = null, $subreddit = null){
        $this->_db = database_connect();
        $this->mainsubreddit = $subreddit;
        $reddit = new reddit($username, $password);
        $this->_reddit = $reddit;
    }
    
    /**
    * Post Signups
    *
    * Post signup thread to Reddit
    * @param int $round Round number to post about
    */
    public function postSignups($round){

        $postTemplate = "Reply to the appropriate comment to sign up for this round of Game of Bands.
    
You may sign up for multiple roles, but you will only be selected for one. Only direct replies to the 'sign up comments' will be considered as signing up. Any direct reply to the 'sign up comments' will be considered a sign up, no matter what the comment says. If you change your mind about a sign up please delete your comment.

Press 1 to be returned to the main menu.";

        $response = $this->_reddit->createStory('Signups for Round ' . $round . '; all roles.', '', $this->mainsubreddit, $postTemplate);
        
        $postTemplate = "Post theme ideas here. Up and down votes are considered in selecting a winner. Keep in mind that a theme must apply to **all the disciplines** in a team. Nominations which do not meet this criteria, or that have been done previously, will be removed.";
        $response = $this->_reddit->createStory('Theme voting post for Round ' . $round . '.', '', $this->mainsubreddit, $postTemplate);
        
        /* Find our new posts and save their IDs for future use */
        sleep(3);
        $getredditlisting = $this->_reddit->getListing($this->mainsubreddit,5);
        $getredditlisting = $getredditlisting->data->children;
        
        $signupID = titleSearch($getredditlisting,'Signups for Round ' . $round . '; all roles.');
        $themeID = titleSearch($getredditlisting,'Theme voting post for Round ' . $round . '.');
        
        /* Post our Signup Comments */
        $response = $this->_reddit->addComment($signupID, 'Musicians Reply Here');
        $response = $this->_reddit->addComment($signupID, 'Lyricists Reply Here');
        $response = $this->_reddit->addComment($signupID, 'Vocalists Reply Here');
        
        /* Find our new comments and save their IDs for future use */
        sleep(3);
        $commentpool = $this->_reddit->getcomments($this->mainsubreddit,$signupID,5);
        $commentpool = $commentpool->data->children;
        
        $musiciansSignuppostID = commentSearch($commentpool,'Musicians Reply Here');
        $lyricistsSignuppostID = commentSearch($commentpool,'Lyricists Reply Here');
        $vocalistSignuppostID = commentSearch($commentpool,'Vocalists Reply Here');

        // Save our data to the databse    
        $query = $this->_db->prepare('INSERT INTO rounds (number, theme, signupID, musiciansSignupID, lyricistsSignupID, vocalistSignupID, themeID) VALUES (:round, NULL, :signupID, :musiciansSignuppostID, :lyricistsSignuppostID, :vocalistSignuppostID, :themeID)');
        $query->execute(array('round' => $round, 'signupID' => $signupID, 'musiciansSignuppostID' => $musiciansSignuppostID, 'lyricistsSignuppostID' => $lyricistsSignuppostID, 'vocalistSignuppostID' => $vocalistSignuppostID, 'themeID' => $themeID));

    }

}

?>