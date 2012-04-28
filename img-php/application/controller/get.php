<?php
class get extends controller {
	
	public function pic($type, $id) {
		global $_SC, $_SGLOBAL;
		$dirname = $_SC['img_dir'].strtolower($type).DIRECTORY_SEPARATOR;
		$filename = $id."_1.png";
		if(is_file($dirname.$filename)) {
			header("Content-Type: image/png");
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
				echo file_get_contents($dirname.$filename);
				exit;
			}
			header("HTTP/1.1 404 Not Found");
			exit;
		}
	}
	
	public function article($_id, $index, $width, $height) {
		global $_SGLOBAL;
		$res = $_SGLOBAL['pagedb']->Article_select_one(array('_id' => new MongoID($_id)), array('images', 'url'));
		$matches = array();
		preg_match("#real_src\s*=\s*[\"']([^\"]*)[\"']#", $res['images'][$index], $matches) || 
		preg_match("#src\s*=\s*[\"']([^\"]*)[\"']#", $res['images'][$index], $matches);
		$_SGLOBAL['imager']->get($matches[1], $width, $height, $res['url'], true);
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