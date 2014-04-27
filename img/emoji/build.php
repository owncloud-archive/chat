<?php
// Build the smiley list for in the app.js file
echo "smilies: { ";
foreach (glob("*.png") as $emoji) {
	$name = substr($emoji, 0, -4);
	echo "':" . $name . ":' : '/apps/chat/img/emoji/" .$emoji . "' ,\n";
}
echo "}\n";