<?php
// must be included, not called directly.
if(!defined('INDEX'))
	die("Nope.");

mod_check();

// Remove all cached files.. forcing the system to regenerate them on next access.
array_map("unlink",glob('../../src/cache/*.html'));

// Inform mod that cache has been cleared.
?>
<h3>Cache cleared.</h3>