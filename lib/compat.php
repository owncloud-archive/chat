<?php
if(!function_exists('vendor_script')){
	function vendor_script($app, $files){
		if(is_array($files)) {
			foreach($files as $file) {
				\OCP\Util::addScript('chat', '../vendor/' . $file);
			}
		} else {
			\OCP\Util::addScript('chat', '../vendor/' . $files);
		}
	}
}

if(!function_exists('vendor_style')){
	function vendor_style($app, $files){
		if(is_array($files)) {
			foreach($files as $file) {
				\OCP\Util::addStyle('chat', '../vendor/' . $file);
			}
		} else {
			\OCP\Util::addStyle('chat', '../vendor/' . $files);
		}
	}
}
