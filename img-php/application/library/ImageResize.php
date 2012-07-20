<?php
/**
 * main img function
 */
class ImageResize {
	
	/**
	 * 图片格式
	 * @var string
	 */
	public $img_type = 'png';
	
	/**
	 * Imagic Object
	 * @var object
	 */
	public $imagick;
	
	/**
	 * url px
	 * @var srting
	 */	
	public $original_filename;
	
	/**
	 * cache filename
	 * @var string
	 */
	public $cache_filename;
	
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
	 * base dirname
	 * @var string
	 */
	public $base_dirname;
	
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
	public function __construct($dirname) { 
	    $this->base_dirname = rtrim($dirname, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
	}
	
	/**
	 * get image
	 * @param sting $url
	 * @param int $width
	 * @param int $height
	 * @param string $http_refer
	 * @param bool $is_cut
	 */
	public function get($filename, $width, $height, $is_cut = false) {
	    if (empty($filename)) {
	        $this->error_triger();
	    }
	    $this->original_filename = $filename;
	    $this->width = intval($width);
		$this->height = intval($height);
	    $this->parse_filename();
	    if(isset($_GET['debug'])) {
		    pr($this, $_SERVER);
		}
	    if (!is_file($this->original_filename)) {
	        $this->error_triger();
	    }
	    if (is_file($this->cache_filename)) {
	        $this->output();
	    }
		$this->is_cut = $is_cut;
		$this->load();
		$this->resize();
		$this->output();
	}
	
	
	/**
	 * 输出图片
	 */
	public function output() {
		$etag = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : "";
		if($this->etag == $etag) {
			header('Etag:'.$etag, true, 304); 
		} else {
			header('Etag:'.$this->etag); 
		}
		$image_stat = getimagesize($this->cache_filename);
		list(, $img_type) = explode('/', $image_stat['mime']);
		header("Last-Modified: " . gmdate("D, d M Y 00:00:00", time()) . " GMT");
		header("Content-Type: image/{$img_type}");
		echo file_get_contents($this->cache_filename);
		exit;
	}
	
	/**
	 * load img
	 */
	public function load() {
	    $image_stat = getimagesize($this->original_filename);
		if($image_stat === false) {
		    $this->error_triger();
		}
		$this->imagick = new Imagick($this->original_filename);
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
				//$origin_width 	= $this->imagick->getimagewidth();
				//$origin_height 	= $this->imagick->getimageheight();
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
			$this->cache_in($this->cache_filename);
			return;
		} while(0);
		exec("ln -s {$this->original_filename} ".
				   "{$this->cache_filename}");
	}
	
	/**
	 * 拼凑本地缓存路径
	 */
	function parse_filename() {
	    $this->cache_filename = $this->base_dirname.basename($this->original_filename)."_{$this->width}x{$this->height}";
	}
	
	/**
	 * 缓存图片
	 */
	function cache_in($filename) {
		//再次解析本地路径
		$dirname = dirname($filename);
		!is_dir($dirname) && mkdir($dirname, 0755, true);
		!is_file($filename) && $this->imagick->writeimage($filename);
	}
	
	/**
	 * img error trigger 404
	 */
	function error_triger() {
		header("HTTP/1.1 404 Not Found");exit;
	}
	
}