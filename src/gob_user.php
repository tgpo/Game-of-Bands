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

function get_bandit_links(){
    $links = get_username() . '<span class="karma">(' . get_karma() . ')</span>';
    $links .= ' | ' . '<a href="/bandit/';
    $links .=  get_username() . '">' . 'My Profile' . '</a>';
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
