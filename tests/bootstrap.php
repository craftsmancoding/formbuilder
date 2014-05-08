<?php
require_once dirname(dirname(__FILE__)).'/vendor/autoload.php';


function my_custom_parser($tpl,$args) {
    $file = dirname(__FILE__).'/tpls/'.$tpl;

	if (is_file($file)) {
		ob_start();
		extract($args);
		include $file;
		return ob_get_clean();
	}
}


function trim_html($str) {
    $str = preg_replace('/class=""/','',$str);
    $str = preg_replace('/\s+>/','>',$str);
    $str = preg_replace('/\s+/',' ',$str);
    return trim($str);
}