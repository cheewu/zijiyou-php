<?php
class MongoHandle {
	
	/**
	 * connection
	 * @var object Mongo
	 */
	public $con;
	
	/**
	 * database
	 * @var object MongoDB
	 */
	public $db;
	
	/**
	 * collection
	 * @var object MongoCollection
	 */
	private $collection;
	
	/**
	 * cursor
	 * @var object MongoCursor
	 */
	private $cursor;
	
	/**
	 * start timestamp
	 * @var int
	 */
	private $start_time;
	
	/**
	 * query
	 * @var array()
	 */
	private $query;
	
	/**
	 * field
	 * @var array()
	 */
	private $field;
	
	/**
	 * order
	 * @var array()
	 */	
	private $order;
	
	/**
	 * pagesize
	 * @var int
	 */	
	private $pagesize;
	
	/**
	 * page
	 * @var int
	 */	
	private $page;
	
	/**
	 * update
	 * @var array()
	 */	
	private $update;
	
	/**
	 * insert
	 * @var array()
	 */	
	private $insert;
	
	/**
	 * delete
	 * @var array()
	 */	
	private $delete;
	
	/**
	 * res count
	 * @var int
	 */
	public $res_count;
	
	/**
	 * __construct
	 * @param string $server
	 * @param array $options
	 */
	public function __construct($server, $dbname, $options = array()) {
		$default_options = array("connect" => TRUE);
		$this->con = new Mongo($server, array_merge($default_options, $options));
		$this->db = $this->con->selectDB($dbname);
	}
	
	/**
	 * turn database
	 * @param string $db_name
	 */
	public function selectDB($dbname) {
		$this->db = $this->con->selectDB($dbname);
	}
	
	/**
	 * 魔术函数查询数据库
	 * @param string $name
	 * @param array $arguments
	 */
	public function __call($function_name, $arguments) {
		global $_SGLOBAL;
		// debug recorder
		$this->start_time = microtime(true);
		// init
		$this->init();
		
		/* __CALL */
		$matches = array();
		
		// <collection>_select($query = null, $fields = null, $order = null, $pagesize = null, $page = 1)
		if( preg_match_all("/^(.*)_select_(one)$/i", $function_name, $matches) || 
			preg_match_all("/^(.*)_select$/i", $function_name, $matches) ) {
			
			$this->collection = $this->db->selectCollection($matches[1][0]);
			$is_one = (isset($matches[2][0]) && $matches[2][0] == 'one') ? true : false;
			
			// argument
			$argument_names = array('query', 'field', 'order', 'pagesize', 'page');
			// parse argument
			$this->parse_arguments($arguments, $argument_names);
			
			// find cursor
			$this->cursor = $this->collection->find($this->query, $this->field);
			// res count
			$this->res_count = $this->cursor->count();
			// order
			$this->cursor = $this->cursor->sort($this->order);
			
			if($is_one) {
				$this->cursor = $this->cursor->limit(1);
				$res = $this->object_to_array();
				return isset($res[0]) ? $res[0] : null;
			}
			// default
			$pagesize = (!empty($arguments[3]) && $arguments[3] > 0) ? intval($arguments[3]) : -1;
			$pagenum = (!empty($arguments[4]) && $arguments[4] > 0) ? intval($arguments[4]) : 1;
			// skip
			$skip = $this->pagesize * ($this->page -1);
			$skip < 0 && $skip = 0;
			$this->cursor = $this->cursor->skip($skip);
			// limit
			$this->pagesize > 0 && $this->cursor = $this->cursor->limit($this->pagesize);
			
			return $this->object_to_array();
			
			//$_SGLOBAL['debug_info']['mongo'][] = array('mongo_query' => $debug_mongo_query, 'time_cost' => (microtime(true) - $this->start_time));
			//return $this->objectid_to_string($res);
		}
		

		// <table>_update($update, $query = null)
		if( preg_match_all("/^(.*)_update$/i", $function_name, $matches) ) {
			$this->collection = $this->db->$matches[1][0];
			// argument name
			$argument_names = array('update', 'query');
			// parse argument
			$this->parse_arguments($arguments, $argument_names);
			// update must not be empty
			empty($this->update) && die("update must not be empty");
			
			return $this->collection->update($this->query, array('$set' => $this->update));
		}
		
		// * <table>_delete($query)
		if( preg_match_all("/^(.*)_delete$/i", $function_name, $matches)  ) {
			$this->collection = $this->db->$matches[1][0];
			// argument name
			$argument_names = array('query');
			// parse argument
			$this->parse_arguments($arguments, $argument_names);
			// delete query must not be empty
			empty($this->delete) && die("update must not be empty");
			
			return $this->collection->remove($this->query, true);
		}
		
		// * <table>_insert($insert) 
		if( preg_match_all("/^(.*)_insert$/i", $function_name, $matches) ) {
			$this->collection = $this->mongodb->$matches[1][0];
			// argument name
			$argument_names = array('insert');
			// parse argument
			$this->parse_arguments($arguments, $argument_names);
			
			return $this->collection->insert($this->insert);
		}
		
		// * <table>_distinct_by_<column>($distinct)
		if( preg_match_all("/^(.*)_distinct$/i", $function_name, $matches) ) {
			$res = $this->db->command(array("distinct" => $matches[1][0], "key" => $matches[2][0]));
			return $res['values'];
		}
		
		// * <table>_geo_select($center, $radius, $top, $query)
		if( preg_match_all("/^(.*)_geo_query$/i", $function_name, $matches) ) {
			$command = array(
				'geoNear' => $matches[1][0],
				'near' => $arguments[0], 
				
				'includeLocs' => true, 
			);
			isset($arguments[1]) && $command['maxDistance'] = floatval($arguments[1]);
			isset($arguments[2]) && $command['num'] = intval($arguments[2]);
			isset($arguments[3]) && $command['query'] = $arguments[3];
			$res = $this->db->command($command);
			return $res['results'];
		}
		
		/* /__CALL */
		
		/* not found method */
		die("[mongo_handle] Can't Find Method <b>{$function_name}</b>.\n");
	}
	
	/**
	 * parse arguments
	 * @param array() $arguments
	 * @param array() $argument_names
	 */
	private function parse_arguments($arguments, $argument_names) {
		foreach($argument_names AS $index => $argument_name) {
			!empty($arguments[$index]) && $this->$argument_name = $arguments[$index];
		}
	}
	
	/**
	 * init query
	 */
	private function init() {
		$this->query = array();
		$this->field = array();
		$this->order = array();
		$this->update = array();
		$this->insert = array();
		$this->delete = array();
		$this->page = 1;
		$this->pagesize = -1;
	}
	
	/**
	 * 将mongodb查询结果对象转换为数组
	 * @param object $mongo_res_object
	 * @return array()
	 */
	private function object_to_array() {
		return array_values(iterator_to_array($this->cursor));
	}
	/**
	 * 从tripfm中搜索相关关键词，并唯一化
	 * @param string $keyword1
	 * @return array() relate_words
	 */
	public function get_relative_keyword($keyword1) {	
		$result = $sig_tmp = array();
		$res = $this->con->tripfm->RelaKeyword->find(array('keyword1' => array('$regex' => $keyword1)))->sort(array('relate' => -1));
		$res = $this->object_to_array($res);
		foreach($res AS $value){
			if(!isset($sig_tmp[$value['keyword2']])){
				$result[$value['category2']][] = $value;
				$sig_tmp[$value['keyword2']] = true;
			}
		}
		unset($sig_tmp);
		return $result;
	}
}