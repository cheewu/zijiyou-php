<?php


class Flicker {
	
	/**
	 * flicker api key
	 * @var string
	 */
	private $api_key;
	
	/**
	 * flicker api
	 * @var string
	 */
	private $api_url;
	
	/**
	 * return type
	 * @var string
	 */
	private $foramt;
	/**
	 * __construct
	 */
	public function __construct() {
		global $_SC;
		$this->api_key = $_SC['flicker']['api_key'];
		$this->api_secert = $_SC['flicker']['api_secert'];
		$this->api_url = $_SC['flicker']['api_url'];
		
		$this->format = $_SC['flicker']['format'];
		$this->auth_url = $_SC['flicker']['auth_url'];
	}
	
	/**
	 * api request
	 * @param array $param
	 */
	public function go($param = array()) {
		global $_SGLOBAL;
		if(isset($param['lat'])) {
			$key = $param['lat'].$param['lon'];
		} else {
			$key = md5($param['text']);
		}
		$key = 'flicker_'.$key;
		$val = $_SGLOBAL['m']->get($key);
		
	    if( $val ) { return $val; }//get the value
		
	    //no found
	    if( $_SGLOBAL['m']->getResultCode() == Memcached::RES_NOTFOUND ) {
	        /* the key does not exist */
	        $param_default = array(
				'api_key' => $this->api_key,
				'format' => $this->format,
			);
			$param = array_merge($param_default, $param);
			
			$this->sign($param);
			
			$url = $this->api_url."?".http_build_query($param);
			$res_string = shttp_request($url, array('timeout' => 0));
			$res = preg_replace("#jsonFlickrApi\((.*)\)#i", '$1', $res_string);
	        $res = json_decode($res, true);
	        $_SGLOBAL['m']->set($key, $res);
	        
	        return $res;
	    }else{
	        /* log error */
	    }
		
		return array();
	}
	
	/**
	 * get poi pics
	 * @param array $poi_item
	 */
	public function get_poi_pic($poi_item) {
		$param = array(
			'method' => 'flickr.photos.search',
			'auth_token' => '72157629642190479-09565cd2248fa635',
			'safe_search' => 3,
	//		'place_id' => $places['places']['place'][0]['place_id'],
			'per_page' => 1,
			'page' => 1,
			'is_getty' => true,
			'in_gallery' => true,
	//		'sort' => 'date-taken-desc',
	//		'min_upload_date' => time() - 10 * 365 * 24 * 3600,
			'accuracy' => 10,
			'radius' => 10,
	//		'tags' => $poi['name'],
	//		'text' => $poi['name'],
	//		'contacts' => $poi['name'],
		);
		if(!empty($poi_item['center'])) {
			list($param['lat'], $param['lon']) = $poi_item['center'];
		} else {
			$param['text'] = $poi_item['name'];
		}
		
		$photos = $this->go($param);
		return @$photos['photos']['photo'] ?: array();
	}
	
	
	
	public function auth() {
		$param = array(
			'perms' => 'read',
			'api_key' => $this->api_key,
		);
		$this->sign($param);
		$url = $this->auth_url.'?'.http_build_query($param);
		pr($url);
	}
	
	public function sign(&$param) {
		//$sign_str = $this->oauth_url;
		ksort($param);
		$sign_str = "";
		foreach($param AS $key => $value) {
			$sign_str .= $key.$value;
		}
		
		$param['api_sig'] = md5($this->api_secert.$sign_str);
		
	}
	
}