<?php
class article extends controller {
	
	public $etag = 'zijiyou';
	
	public function pic($type, $id) {
		global $_SC, $_SGLOBAL;
		$etag = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : "";
		$dirname = $_SC['img_dir'].strtolower($type).DIRECTORY_SEPARATOR;
		$filename = $id."_1.png";
		if(is_file($dirname.$filename)) {
			if($this->etag == $etag) {
				header('Etag:'.$etag, true, 304); 
			} else {
				header('Etag:'.$this->etag); 
			}
			header("Content-Type: image/png");
			header("Last-Modified: " . gmdate("D, d M Y 00:00:00", time()) . " GMT");
			echo file_get_contents($dirname.$filename);
		} else {
			$func = (strtolower($type) == 'region') ? 'Region' : 'POI';
			$func .= '_select_one';
			$item = $_SGLOBAL['db']->$func(array('_id' => new MongoID($id)));
			$pic = $_SGLOBAL['flicker']->get_pic($item);
			if(empty($pic)) {
				header("HTTP/1.1 404 Not Found");
				exit;
			}
			$url = flicker_photo_url($pic[0], 'q');
			if($this->_cache($type, $id, $url)) {
				header("Content-Type: image/png");
				header('Etag:'.$this->etag); 
				header("Last-Modified: " . gmdate("D, d M Y 00:00:00", time()) . " GMT");
				echo file_get_contents($dirname.$filename);
				exit;
			}
			header("HTTP/1.1 404 Not Found");
			exit;
		}
	}
	
	public function get($hex_crc32, $width, $height) {
		global $_SC, $_SGLOBAL;
		$filename = $_SGLOBAL['m']->get($_SC['img_cache_prefix'].$hex_crc32);
		$_SGLOBAL['imager']->get($filename, $width, $height, true);
	}
	
	private function _cache($type, $id, $url) {
		global $_SC;
			clearstatcache();
			$dirname = $_SC['img_dir'].strtolower($type).DIRECTORY_SEPARATOR;
			!is_dir($dirname) && mkdir($dirname, 0755, true);
			$filename = $id."_1.png";
		try {
			$imagick = new Imagick();
			$imagick->setformat('png');
		} catch (ImagickException $e) {
			return false;
		}
		$fp = fopen("php://temp", "wrb");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		//follow header
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);    
    	//maximum amount of HTTP redirections to follow
		curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_exec($ch);
		// http 200正确 否则返回空
		if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == '200') {
			fseek($fp, 0);
		} 
		try {
			$imagick->readImageFile($fp);
			$imagick->writeimage($dirname.$filename);
			return true;
		} catch (ImagickException $e) {
			return false;
		}	
		return false;
	}
	
}