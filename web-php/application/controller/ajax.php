<?php
class ajax extends controller {
	public function relate() {
		global $_SGLOBAL;
		$q = $_GET['q'];
		$query = array('name' => array('$regex' => "^{$q}"));
		$poi_res = $_SGLOBAL['db']->POI_select($query, array('name', 'regionId'), null, 20);
		foreach ($poi_res AS &$poi) {
			if(!empty($poi['regionId'])) {
				$region = $_SGLOBAL['db']->Region_select_one(array('_id' => new MongoID($poi['regionId'])), array('name'));
				if(!empty($region)) {
					$poi['area'] = $region['name'];
				}
			}
		}
		unset($poi);
		$region_res = $_SGLOBAL['db']->Region_select($query, array('name', 'area'), null, 20);
		$res = array_merge($poi_res, $region_res);
		$relate_word = array();
		foreach($res AS $value){
			$tmp_keyword = $value['name'];
			!empty($value['area']) && $value['area'] = trim($value['area']);
			!empty($value['area']) && $tmp_keyword .= ' @'.$value['area'];
			if(in_array($tmp_keyword, $relate_word)) { continue; }
			$relate_word[] = $tmp_keyword;
		}
		sort($relate_word);
		if(!empty($relate_word)){
			foreach($relate_word AS $key => $value){
				if($key >= 8 ){break;}
				echo $value."\n";
			}
		}
	}
	
	public function region($region_id) {
		global $_SGLOBAL;
		$region = $_SGLOBAL['db']->Region_select_one(array('_id' => new MongoID($region_id)));
		/* correlation */
		$correlation = $_SGLOBAL['pagedb']->correlation_select_one(array('name' => $region['name']));
		$data = array();
		empty($correlation['correlation']) && $correlation['correlation'] = array();
		arsort($correlation['correlation']);
		foreach ($correlation['correlation'] AS $category => $item_arr) {
		    if(!in_array(strtolower($category), 
		        array('product', 'food', 'item', 'note', 'people'))) { continue; }
			$data_node = array();
			$count = 0;
			foreach ($item_arr AS $item_name => $item_score) {
			    if ($count ++ > 5) { break; }
				$data_node[] = array(
					"name" => $item_name,
					"size" => sqrt($item_score), //log10($item_score + 5) * 10,
				);
			}
			$data[] = array(
				"name"     => $category,
				"children" => $data_node,
			);
		}
	    if(isset($_GET['debug']) && $_GET['debug'] == 1) {
		    pr($data);
		    exit;
		}
		echo(json_encode(array(
			"name"     => "correlation",
			"children" => $data,
		)));
	}
}