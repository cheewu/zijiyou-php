<?php
require 'common.php';

$ps = 5000;

foreach(array('Region', 'POI') AS $collection) {
	$count = $_SGLOBAL['db']->db->$collection->find()->count();
	$func = $collection."_select";
	for($i = 0; $i <= $count; $i ++) {
		$res = $_SGLOBAL['db']->$func(null, array('name', 'center'), null, $ps, $i);
		foreach($res AS $item) {
			
			$pic = $_SGLOBAL['flicker']->get_pic($item);
			if(empty($pic)) {continue;}
			$url = flicker_photo_url($pic[0], 'q');
			$dirname = $_SC['img_dir'].strtolower($collection).'/';
			!is_dir($dirname) && mkdir($dirname, 0755, true);
			$filename = strval($item['_id']).'_1.png';
			
			if(is_file($dirname.$filename)) {
				continue;
			}
			try {
				$imagick = new Imagick();
				$imagick->setformat('png');
			} catch (ImagickException $e) {
				continue;
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
			} catch (ImagickException $e) {
				continue;
			}
			echo "$collection {$item['name']}\n";
		}
	}
}


