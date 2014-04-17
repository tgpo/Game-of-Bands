<?php
session_start();

if ( !isset($_SESSION['GOB']) )
{
    $_SESSION['GOB'] = array();
    $_SESSION['GOB']['loggedin']=false;

}

function is_loggedin(){
    return ($_SESSION['GOB']['loggedin'] ? true : false);

}

function loggedin_check($page = 'index.php'){
    if ( !$_SESSION['GOB']['loggedin'] )
    {
        header('Location: ' . $page);
        die;

    }

}

function is_mod(){
    return ($_SESSION['GOB']['ismod'] ? true : false);

}

function get_username(){
    return $_SESSION['GOB']['name'];

}

function write_username(){
    echo get_username();

}

function get_karma(){
    return $_SESSION['GOB']['karma'];

}

function write_karma(){
    echo get_karma();

}

function get_flair($bandit){
    $db = database_connect();
    $query = $db->prepare('SELECT TeamWins, MusicWins, VocalWins, LyricsWins FROM bandits WHERE name=:bandit');
    $query->execute(array('bandit' => $bandit));
    $bandit  = $query->fetch();
  
    return get_flair_image('Team', $bandit['TeamWins']) . get_flair_image('Music', $bandit['MusicWins'])  . get_flair_image('Vocal', $bandit['VocalWins'])  . get_flair_image('Lyrics', $bandit['LyricsWins']);
}

function get_flair_image($type,$count){
  $bronzeTeam = 'http://c.thumbs.redditmedia.com/pxwT1Arg6202jhiJ.png';
  $bronzeMusic = 'http://d.thumbs.redditmedia.com/0O_wB02wZakV8XGu.png';
  $bronzeVocal = 'http://b.thumbs.redditmedia.com/b8QrLlLq0k-ijZe7.png';
  $bronzeLyrics = 'http://d.thumbs.redditmedia.com/AKDIfCqdJBOS08Qo.png';

  $silverTeam = 'http://d.thumbs.redditmedia.com/K0yjBDQiMfU8JXyK.png';
  $silverMusic = 'http://e.thumbs.redditmedia.com/1a7Ev3Q72HFLnSQp.png';
  $silverVocal = 'http://b.thumbs.redditmedia.com/dIhv4mWmkOulSC0U.png';
  $silverLyrics = 'http://f.thumbs.redditmedia.com/Mu4gnG0pAxLy7IYe.png';

  $count2 = 'http://c.thumbs.redditmedia.com/EQzLcCoWVJuY3p5V.png';
  $count3 = 'http://f.thumbs.redditmedia.com/tZVybu45bw_Qg_65.png';
  $count4 = 'http://b.thumbs.redditmedia.com/_9R5CDRomoOSTOa1.png';
    
  $thisFlair = '';
  
  if(!is_null($count)){
    switch($count){
      case $count > 5:
        $flair = 'silver' . $type;
        $thisFlair .= create_flair_image_tag($$flair);
        break;
      case $count < 5:
        $flair = 'bronze' . $type;
        $thisFlair .= create_flair_image_tag($$flair);
        break;
    }
    
    if($count >1 && $count < 5){
      $mycount = 'count' . $count;
      $thisFlair .= create_flair_image_tag($$mycount);
    }
  }

  return $thisFlair;
}

function create_flair_image_tag($imgURL){
    return '<img src="' . $imgURL . '" />';
  
}

function get_bandit_links(){
    $links = get_username() . '<span class="karma"> ' . get_flair(get_username()) . '</span>';
    $links .= ' | ' . '<a href="/bandit/';
    $links .=  get_username() . '">' . 'My Profile' . '</a>';
    $links .= ' | ' . '<a href="/irc">IRC</a>';
    $links .= ' | ' . '<a href="/user_submitsong">' . 'Submit Song' . '</a>' . ' | ';
    return $links;

}

function write_edit_controls(){
  echo '&nbsp;[<a href="/edit_profile">Edit</a>]';

}

function write_bandit_profile($bandit){

  $db = database_connect();
  $query = $db->prepare('SELECT soundcloud_url, tools, website FROM bandits WHERE name=:bandit');
  $query->execute(array('bandit' => $bandit));
  $webname = "Website";
  $scname = "Soundcloud";
  $tname = "Tools/Gear";
  $noentry = "Not specified";

  while($row = $query->fetch(PDO::FETCH_ASSOC)){

          $scu = $row['soundcloud_url']=="" ? $noentry  :  $row['soundcloud_url'];
          $url = $row['website']=="" ? $noentry : $row['website'];	  
          $gear = $row['tools']=="" ? $noentry  : $row['tools'];

          $link_bandit_soundcloud = $scu==$noentry ? 
          '<td>' . $scname . '</td><td>' . $scu . '</td>'     :   '<td>' . $scname . '</td><td><a href="' . $scu . '" target="_blank">' . $scu . '</a></td>'; 

          $link_bandit_website = $url==$noentry ?
           '<td>' . $webname . '</td><td>' . $url . '</td>'   :   '<td>' . $webname . '</td><td><a href="' . $url . '" target="_blank">' . $url . '</a></td>';
   
           
          
          echo '<table> 
				<th colspan="2">Bandit Info</th>
				<tr>'
				      .  $link_bandit_soundcloud  .
				'</tr>
				<tr>'
			              .  $link_bandit_website  .
				'</tr>
				<tr>
					<td>' . $tname . '</td><td>' . $gear . '</td>
				</tr>
			</table><br />';

  }

}

?>
