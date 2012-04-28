<?php
class cache extends controller {
	
	public function get($md5, $width, $height) {
		global $_SC, $_SGLOBAL;
		$prefix = $_SC['img_cache_prefix'];
		$key = $prefix.$md5;
		$param = $_SGLOBAL['m']->get($key);
		//no found
	    if( $_SGLOBAL['m']->getResultCode() == Memcached::RES_NOTFOUND ) {
	        /* the key does not exist */
	        header("HTTP/1.1 404 Not Found");
	    }
	    list($url, $refer) = $param;
	    $_SGLOBAL['imager']->get($url, $width, $height, $refer, true);
	}	
}