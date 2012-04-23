<?php
class ajax extends controller {
	public function relate() {
		global $_SGLOBAL;
		$q = $_GET['q'];
		$query = array('name' => array('$regex' => "^{$q}"));
		$poi_res    = $_SGLOBAL['db']->POI_select($query, array('name'), null, 9);
		$region_res = $_SGLOBAL['db']->Region_select($query, array('name'), null, 9);
		$res = array_merge($poi_res, $region_res);
		$relate_word = array();
		foreach($res AS $value){
			$relate_word[] = $value['name'];
		}
		sort($relate_word);
		if(!empty($relate_word)){
			foreach($relate_word AS $key => $value){
				if($key >= 8 ){break;}
				echo $value."\n";
			}
		}
	}
}