<?php
// Build the smiley list for in the app.js file
echo "[\n";
foreach (glob("*.png") as $emoji) {
	$name = substr($emoji, 0, -4);
	echo "{ 'name' : ":' . $name . ':", "url" : "OC.imagePath('chat', 'emoji/ ." . $emoji . "} ,\n";
}
echo "]\n";