<?php
  if(!defined('INDEX')) {
	header('Location: ../index.php');
	die;
  }
  
  $song=$_GET['song'];
  if (!$song) {
    header("Location: /index.php"); // revert to index
    exit();
  }

  require_once('query.php');

  $db    = database_connect();
  $query = $db->prepare('SELECT * FROM songs WHERE id=:song and approved=1');
  $query->execute(array('song' => $song));
  $song  = $query->fetch();
  
  $round  = $song['round'];
  
  $roundDetails = query_round_details($db, $round);
  
?>

<aside id="otherviews">
  <a href='/index.php' class="returnhome">Return to song library</a>
</aside>

<h2 id="songTitle" data-songid="<?php echo $song['id']; ?>"><?php echo $song['name'];?></h2>
<h3>This song was created for Game of Bands round <?php echo $round  .  ' : "'  .  $roundDetails['theme']  .  '"'; ?> </h3>

<div id="putTheWidgetHere"></div>
  <script type="text/JavaScript">
  // SC code loaded via homepage, so, we should be able to simple find the first link and click it.
$(document).ready(function(){
	// Load the song-url
	var url = $('#sc-link').attr('href');
	// remove previous player
	$('#votingWidget').remove();
	// Create the iframe for the new widget.
	$('<iframe>', {
	   src: 'https://w.soundcloud.com/player/?url=' + url, 
	   id:  'sc-song-widget',
	   frameborder: 0,
	   scrolling: 'no',
	   height: '166',
	   width: "100%",
	}).appendTo(document.getElementById("putTheWidgetHere")); 
	// Make the new widget.
	var widget = SC.Widget('sc-song-widget');
	widget.bind(SC.Widget.Events.READY,function() {widget.play(); });
	widget.load(url, { // Tell the widget what URL we want to play
		auto_play: true,
		sharing: true,
		liking: true,
		show_artwork: true, 
	});
});
	</script>
	<p class="extlink"><a id="sc-link" href="<?php echo $song['url'];?>" class="listen">View on SoundCloud</a></p>

<?php
// Display table with this song.
display_songs(array($song));


$lyrics = $song['lyricsheet'];

// Display lyrics if available
if($lyrics) {
	echo "<h4>";
	echo "Lyrics:";
	echo "</h4>";
	echo "<div class='lyrics'>";
	echo nl2br($lyrics);
	echo "</div>";
}
else {
	$mailSafeSongTitle  =  urlencode($song['name']);
	$mailSafeSongURL    =  urlencode("http://www.gameofbands.co/song/"  .  $song['id']);
	$mailSafeMusicBanditLink   =  urlencode("http://www.reddit.com/message/compose/?to="  .  $song['music']);
	$mailSafeLyricsBanditLink  =  urlencode("http://www.reddit.com/message/compose/?to="  .  $song['lyrics']);
	$mailSafeVocalsBanditLink  =  urlencode("http://www.reddit.com/message/compose/?to="  .  $song['vocals']);
	
	$missingLyricsLink  =  '<h4>';
	$missingLyricsLink .=  '<a href="mailto:retrotheft@gameofbands.co?subject=Missing%20lyrics%20for%20song%20';
	$missingLyricsLink .=  $song['id']  .  '&body='  .  'name:%20'  .  $mailSafeSongTitle  .  '\nlink:%20';
	$missingLyricsLink .=  $mailSafeSongURL  .  "\n";
	$missingLyricsLink .=  'Musician:%20'  .  $mailSafeMusicBanditLink  .  '\n';
	$missingLyricsLink .=  'Lyricist:%20'  .  $mailSafeMusicBanditLink  .  '\n';
	$missingLyricsLink .=  'Vocalist:%20'  .  $mailSafeMusicBanditLink  .  '\n';
	$missingLyricsLink .=  '">Report Missing Lyricsheet</a></h4>';

        echo $missingLyricsLink;
	
}
?>
