<script src="/admin/xmas/xmas.js" type="text/javascript"></script>
<h1>Manage Text Fragments</h1>
<?php
require_once ( $dir . '/../src/gob_user.php'); 
mod_check (); //forces script to be loaded using admin semantics
$directory = $dir . '/../src/fragments/';
foreach(glob($directory . '*.inc') as $f){
	$t = file_get_contents($f);
	$f = str_replace($directory,'',$f);
	echo '<div>
		<h3><a href="#' . $f . '">' .$f.'</a></h3>
			<textarea style="width:100%;height:200px;">' . $t . '</textarea>
			<input type="button" class="fragment_save" value="Save"/>
		</div>';
}
