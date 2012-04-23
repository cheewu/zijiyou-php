<?php
/**
 * Core Frame Class 
 * 
 * url.php
 * 
 * @author  & HouRui
 * @since 2011-11
 */
class controller {
	
	/**
	 * 魔术装载配置文件函数
	 */
	public function __call($name, $arguments) {
		$matches = array();
		if (preg_match_all("/^(.*)_load$/", $name, $matches)) {
			$file_arr = is_array($arguments[0]) ? $arguments[0] : array($arguments[0]);
			foreach ($file_arr AS $value) {
				$path = A_ROOT . $matches[1][0] . DIRECTORY_SEPARATOR . $value;
				fr_pre_include_file($path);
			}
		}
	}
	
	public function view($param = array(), $tpl_name = '') {
		foreach($param AS $_k => $_v) {
			$$_k = $_v;
		}
		require template($tpl_name);
	}
	
}