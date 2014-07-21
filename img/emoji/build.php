<?php
// Build the smiley list for in the app.js file
echo "smilies: { ";
foreach (glob("*.png") as $emoji) {
	$name = substr($emoji, 0, -4);
	echo "':" . $name . ":' : OC.imagePath('chat', '". $emoji . "/+1.png') ,\n";
}
echo "}\n";