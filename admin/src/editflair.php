<?php
if( !defined('INDEX') ) {
    header('Location: ../index.php');
    die;

}

require_once( 'includes/gob_admin.php' );
require_once( '../src/secrets.php' );
require_once( '../src/query.php' );
require_once('../lib/reddit.php');
$reddit = new reddit($reddit_user, $reddit_password);
$db = database_connect();

  $strStylesheet = $reddit->getStylesheet('gameofbands');
  $newFlairCSS = $db->query("SELECT css FROM flair");

  foreach ($newFlairCSS as $oldflair) {
    $strStylesheet = str_replace($oldflair['css'],"",$strStylesheet);
  }

echo "<pre>";
  $bandits = $db->query('SELECT TeamWins, MusicWins, VocalsWins, LyricsWins, name FROM bandits ORDER BY name ASC');
  foreach ($bandits as $bandit) {
    if($bandit['TeamWins'] + $bandit['MusicWins'] + $bandit['VocalsWins'] + $bandit['LyricsWins']){
      $css = 'a[href*="/' . $bandit['name'] . '"]:after {position: relative; left: 3px; top: 4px; content:' . admin_get_flair_image('Team', $bandit['TeamWins']) . admin_get_flair_image('Music', $bandit['MusicWins'])  . admin_get_flair_image('Vocals', $bandit['VocalsWins'])  . admin_get_flair_image('Lyrics', $bandit['LyricsWins']) . ';}
';
      $name = $bandit['name'];
      $query = $db->query("SELECT * FROM flair WHERE name = '$name'");
      $flairLookup = $query->fetch();

      if(!$flairLookup['name']){
        $flairQuery = $db->prepare('INSERT INTO flair (name, css) VALUES (:name, :css)');
        $flairQuery->execute(array('name' => $name, 'css' => $css));
      } else {
        $flairQuery = $db->prepare('UPDATE flair SET css = :css WHERE id = :id');
        $flairQuery->execute(array('css' => $css, 'id' => $flairLookup['id']));
      }
      
      
      echo $css;
    }
  }

echo "</pre>";

function admin_get_flair_image($type,$count){ //cannot redeclare this function.
  $thisFlair = '';
  
  if(!is_null($count)){
    switch($count){
      case $count >= 5:
        $thisFlair =  ' url(%%' . $type . 'Silver%%)';
        break;
      case $count < 5:
        $thisFlair =  ' url(%%' . $type . 'Bronze%%)';
        break;
    }
    
    if($count >1 && $count < 5){
      $thisFlair .= ' url(%%x' . $count . '%%)';
    }
  }

  return $thisFlair;
}


?>
<?php



?>

<h2>Edit CSS</h2>
<pre>
<?php
  $newFlair ="";
  $newFlairCSS = $db->query("SELECT css FROM flair");

  foreach ($newFlairCSS as $oldflair) {
    $newFlair .= ' ' . $oldflair['css'];
  }

  $strStylesheet .= $newFlair;

echo $strStylesheet; ?>
</pre>