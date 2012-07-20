<?php
/**
 * main img function
 */
class Image {
	
	/**
	 * 图片格式
	 * @var string
	 */
	public $img_type = 'png';
	
	/**
	 * 
	 */
	public $user_agent;
	
	/**
	 * Imagic Object
	 * @var object
	 */
	public $imagick;
	
	/**
	 * url px
	 * @var srting
	 */	
	public $url;
	
	/**
	 * width px
	 * @var int
	 */
	public $width;
	
	/**
	 * height px
	 * @var int
	 */	
	public $height;
	
	/**
	 * http_refer
	 * @var string
	 */
	public $http_refer = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
	
	/**
	 * img md5
	 * @var string
	 */
	public $url_md5;
	
	/**
	 * base dirname
	 * @var string
	 */
	public $base_dirname;
	
	/**
	 * dirname
	 * @var string
	 */
	public $dirname;
	
	/**
	 * 是否有缓存
	 * @var bool
	 */
	public $is_cached = false;
	
	/**
	 * 是否裁减
	 * @var bool
	 */
	public $is_cut;
	
	/**
	 * cache tag
	 * @var string
	 */
	public $etag = 'zijiyou';
	
	/**
	 * __construct
	 */
	public function __construct($img_dirname) {
		$img_dirname = rtrim($img_dirname, DIRECTORY_SEPARATOR);
		$img_dirname .= DIRECTORY_SEPARATOR;
		$this->base_dirname = $img_dirname;
	}
	
	/**
	 * get image
	 * @param sting $url
	 * @param int $width
	 * @param int $height
	 * @param string $http_refer
	 * @param bool $is_cut
	 */
	public function get($url, $width, $height, $http_refer = '', $is_cut = false) {
		$this->url = $url;
		$this->url_md5 = strtoupper(md5($this->url));
		$this->width = intval($width);
		$this->height = intval($height);
		$this->http_refer = trim($http_refer);
		$this->is_cut = $is_cut;
		if(isset($_GET['debug'])) {
		    pr($this, $_SERVER);
		}
		$this->load();
		!$this->is_cached && $this->resize();
		$this->out();
	}
	
	/**
	 * 输出图片
	 */
	public function out() {
		$etag = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : "";
		if($this->etag == $etag) {
			header('Etag:'.$etag, true, 304); 
		} else {
			header('Etag:'.$this->etag); 
		}
		header("Last-Modified: " . gmdate("D, d M Y 00:00:00", time()) . " GMT");
		if($this->is_cached) {
			$this->cache_out();
		} else {
			header("Content-Type: image/{$this->img_type}");
			echo $this->imagick->getImageBlob();
		}
	}
	
	/**
	 * load img
	 */
	public function load() {
		empty($this->url) && $this->error_triger();
		//md5 文件名
		if(is_file($this->get_cache_img_filename())) {
			$this->is_cached = true;
		} else {
			$this->imagick = new Imagick();
			$this->imagick->setformat($this->img_type);
			$fp = $this->_load_handle();
			empty($fp) && $this->error_triger();
			$this->imagick->readImageFile($fp);
			fclose($fp);unset($fp);
		}
		
	}
	
	/**
	 * 读取$fp
	 */
	private function _load_handle() {
		$fp = fopen("php://temp", "wrb");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		!empty($this->user_agent) && curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
		!empty($this->http_refer) && curl_setopt($ch, CURLOPT_REFERER, $this->http_refer); 
		//follow header
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);    
    	//maximum amount of HTTP redirections to follow
		curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_exec($ch);
		// http 200正确 否则返回空
		if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == '200') {
			fseek($fp, 0);
			return $fp;
		} 
		
		return null;
	}
	/**
	 * 改变图片大小
	 */
	function resize() {
		$origin_width 	= $this->imagick->getimagewidth();
		$origin_height 	= $this->imagick->getimageheight();
		($this->width <= 0 && $this->height <= 0) && $this->error_triger();
		do {
			if($origin_height <= $this->height && $origin_width <= $this->width) {
				break;
			}
			if($this->height == 0 && $origin_width < $this->width) {
				break;
			}
			if($this->width == 0 && $origin_height < $this->height) {
				break;
			}
			if($this->width == 0 || $this->height == 0) {
				$this->imagick->thumbnailimage($this->width, $this->height, false);
				$origin_width 	= $this->imagick->getimagewidth();
				$origin_height 	= $this->imagick->getimageheight();
				break;
				//$this->width == 0 && $this->width = $this->imagick->getimagewidth();
				//$this->height == 0 && $this->height = $this->imagick->getimageheight();
			} else {
				if($this->is_cut) {
					$offsite_x = $offsite_y = 0;
					if($this->width / $origin_width > $this->height / $origin_height) {
						$this->imagick->thumbnailimage($this->width, 0, false);
						$modi_height = $this->imagick->getimageheight();
						//$offsite_y = round(($modi_height - $this->height) / 2);
						$offsite_y = 0;
					} else {
						$this->imagick->thumbnailimage(0, $this->height, false);
						$modi_width = $this->imagick->getimagewidth();
						$offsite_x = round(($modi_width - $this->width) / 2);
					}
					$this->imagick->cropImage($this->width, $this->height, $offsite_x, $offsite_y);
				} else {
					$this->imagick->thumbnailimage($this->width, $this->height, true);
				}
			}
			// 非png图需要对背景进行填充
			if($this->img_type != 'png') {
				$filling_background = new Imagick();
				$filling_background->setformat($this->img_type);
				$filling_background->newimage($this->imagick->getimagewidth(), $this->imagick->getimageheight(), 'white');
				$filling_background->compositeimage($this->imagick, imagick::COMPOSITE_OVER, 0, 0);
				$this->imagick = $filling_background;
			}
			$this->cache_in($this->width, $this->height);
			return;
		} while(0);
		
		// 缓存路径
		$img_cache_path = $this->get_cache_img_filename($this->width, $this->height);
		$this->cache_in($origin_width, $origin_height);
		!is_file($img_cache_path) && exec("ln -s ".
										  "{$this->get_cache_img_filename($origin_width, $origin_height)} ".
										  "$img_cache_path");
	}
	
	/**
	 * 输出缓存图片
	 */
	function cache_out() {
		header("Content-Type: image/{$this->img_type}");
		echo file_get_contents($this->get_cache_img_filename());
	}
	
	
	/**
	 * 拼凑本地缓存路径
	 */
	function get_cache_img_filename($width = null, $height = null) {
		//md5 首字母文件夹
		$img_folder = substr($this->url_md5, 0, 1);
		if(is_null($width) && is_null($height)) {
			return $this->base_dirname.$img_folder.DIRECTORY_SEPARATOR.$this->url_md5."_{$this->width}x{$this->height}.{$this->img_type}";
		}
		
		return $this->base_dirname.$img_folder.DIRECTORY_SEPARATOR.$this->url_md5."_{$width}x{$height}.{$this->img_type}";
	}
	
	/**
	 * 缓存图片
	 */
	function cache_in($width, $height) {
		$filename = $this->get_cache_img_filename($width, $height);
		//再次解析本地路径
		$folder = dirname($filename);
		!is_dir($folder) && mkdir($folder, 0755, true);
		!is_file($filename) && $this->imagick->writeimage($filename);
	}
	
	/**
	 * img error trigger 404
	 */
	function error_triger() {
		header("HTTP/1.1 404 Not Found");exit;
	}
	
}