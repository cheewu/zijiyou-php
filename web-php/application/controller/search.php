<?php
class search extends controller {
	
	/**
	 * query
	 * @var string
	 */
	private $q;
	
	/**
	 * index
	 */
	public function index() {
		$this->q = $_GET['q'];
		$this->do_query();
		shttp_redirect("/");
		pr($_GET);
	}
	
	/**
	 * return query res
	 */
	private function do_query() {
		global $_SGLOBAL;
		$q = explode(" @", $this->q);
		$query['name'] = $q[0];
		!empty($q[1]) && $query['area'] = trim($q[1]);
		
		$region = $_SGLOBAL['db']->Region_select_one($query);
		if(!empty($region['_id'])) {
			if(isset($region['category']) && in_array($region['category'], array('province', 'country'))) {
				shttp_redirect("/state/".strval($region['_id']));
			} 
			shttp_redirect("/region/".strval($region['_id']));
		}
		$poi = $_SGLOBAL['db']->POI_select_one($query);
		!empty($poi['_id']) && shttp_redirect("/poi/".strval($poi['_id']));
	}
}