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
		pr($_GET);
	}
	
	/**
	 * return query res
	 */
	private function do_query() {
		global $_SGLOBAL;
		$region = $_SGLOBAL['db']->Region_select_one(array('name' => array('$regex' => $this->q)));
		!empty($region['_id']) && shttp_redirect("/region/".(string)$region['_id']);
		$poi = $_SGLOBAL['db']->POI_select_one(array('name' => array('$regex' => $this->q)));
		!empty($poi['_id']) && shttp_redirect("/poi/".(string)$poi['_id']);
	}
}