<?php
if(!defined('IN_SYSTEM')) { exit('Access Denied'); }
/**
 * 
 * @author HouRui
 * @since 2011-11-28
 * 
 */
$_SCONFIG['preload'] = array(
	/**
	 * 'urlpath' => 'file' || 'url' => array('file1', 'file2', ...) 
	 * @param urlpath string 必须以 / 开头，既urlpath必须为url根目录,目录至多两级 
	 * 	      example: /urlpathA/urlpathB 或者 /urlpathA
	 * @param file string 为application目录下的文件位置
	 *        example: config/config_mail.php 
	 *                 include/mysqlDB.php
	 * 
	 * @example 1. /question/all 域下需要包含application/config/config_question.php
	 *             '/question/all' => 'config/config_question.php'
	 *          2. /question/all 域下需要包含application/config/config_question.php与application/config/config_answer.php两个文件
	 * 				'/question/all' => array(
	 * 										'config/config_question.php',
	 * 										'config/config_answer.php',)
	 * 					       
	 */ 
);