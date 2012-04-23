<?php
class Solr{
	
	/**
	 * config 
	 * @var array
	 */
	private $config;
	
	/**
	 * query param
	 * @var array
	 */
	private $q;
	
	/**
	 * basic param
	 * @var array
	 */
	private $p;
	
	/**
	 * url
	 * @var string
	 */
	private $url;
	
	/**
	 * __construct()
	 */
	public function __construct() {
		global $_SC, $_SCONFIG;
		$this->config = $_SC['Solr'];
	}
	
	/**
	 * 魔术函数查询数据库
	 * @param string $name
	 * @param array $arguments
	 */
	public function __call($function_name, $arguments) {
		/* __CALL */
		$matches = array();
		// append_(basic|query)_url(append_part)
		if( preg_match_all("/^(.*)_url_append$/i", $function_name, $matches) ){
			return $this->url_append($arguments[0], $matches[1][0]);
		}		
	}
	
	/**
	 * 初始化变量
	 */
	public function init() {
		$this->basic_url = "";
		$this->basic_url_append($this->config['host'].':'.$this->config['port'])
			 ->basic_url_append($this->config['basic_path']);
	}
	
	/**
	 * 将请求参数解析进$q
	 * @param array() $extra_options 除了get之外的附加参数
	 * @param array() $basic_param 默认为get参数
	 */
	public function parse_request($extra_param = array(), $basic_param = array()) {
		// default is $_GET
		empty($basic_param) && $basic_param = $_GET;
		// merge param
		if(!empty($extra_param)){
			$basic_param = array_merge($basic_param, $extra_param);
		}
		// basic param
		$this->p = $basic_param;
		
		$solr_default_param = $this->config['default_param'];
		// multi
		$this->p['pg'] = @$this->p['pg'] ?: $solr_default_param['pg'];
		$this->q['rows'] = @$this->p['ps'] ?: $solr_default_param['ps'];
		$this->q['start'] = ($this->p['pg'] - 1) * $this->q['rows'];
		// query
		!empty($this->p['query_words']) && $this->q['q'] = $this->p['query_words'];		
	}
	/**
	 * 发起请求
	 * @return array() 返回查询结果
	 */
	public function do_request() {
		// generate url
		$this->generate_request_url();
		// debug
		//pr($this->url);
		// curl request
		$request_res = shttp_request($this->url, array('timeout' => 5));
		// fliter wrong char
		$request_res = iconv("utf-8", "utf-8//ignore", $request_res);
		// simple parser
		$simple_xml_obj = new SimpleXMLElement ($request_res);
		// simplexml to array
		return simplexml_to_array($simple_xml_obj);
	}
	
	/**
	 * 生成查询url
	 */
	private function generate_request_url() {
		// init
		$this->init();
		// path type
		$path_type = $this->p['solr_type'].'_path';
		// append path
		$this->basic_url_append($this->config[$path_type])
			 ->query_url_append(http_build_query($this->q));
		return $this;
	}
	
	/**
	 * append url
	 * @param string $append_part
	 * @param string (basic|query) $part_type
	 */
	public function url_append($append_part, $part_type) {
		// 去除两端空格
		$append_part = trim($append_part);
		// 去除两端/
		$append_part = trim($append_part, "/");
		// link symbol
		$link_symbol = ($part_type == 'basic') ? '/' : '?';
		// append
		!empty($this->url) && $this->url .= $link_symbol;
		$this->url .= $append_part;
		
		return $this;
	}
}