<?php
class get extends controller {
	
	public function pic($type, $id) {
		global $_SC;
		$dirname = $_SC['img_dir'];
		$filename = $id."_1.png";
		if(is_file($dirname.$filename)) {
			header("Content-Type: image/png");
			echo file_get_contents($dirname.$filename);
		} else {
			header("HTTP/1.1 404 Not Found");
		}
		
	}
	
}