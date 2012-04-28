<?php
class cache extends controller {
	
	public function article($_id, $index, $width, $height) {
		global $_SGLOBAL;
		$res = $_SGLOBAL['pagedb']->Article_select_one(array('_id' => new MongoID($_id)), array('images', 'url'));
		$matches = array();
		preg_match("#real_src\s*=\s*[\"']([^\"]*)[\"']#", $res['images'][$index], $matches) || 
		preg_match("#src\s*=\s*[\"']([^\"]*)[\"']#", $res['images'][$index], $matches);
		$_SGLOBAL['imager']->get($matches[1], $width, $height, $res['url'], true);
	}
	
}