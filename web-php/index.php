<?php
/**
 * index.php Main Entrance
 * 
 * @author  & HouRui
 * @since 2011-11
 * 
 */
/*
 * new use，会自动生成application的一系列文件夹
 * 定义为1，会新装
 */
define("INSTALL", 0);
/* 根目录 */
define('ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);
/* 系统目录 */
define('S_ROOT', ROOT.'system'.DIRECTORY_SEPARATOR);
/* 程序目录 */
define('A_ROOT', ROOT.'application'.DIRECTORY_SEPARATOR);
/* start */
require S_ROOT.'common.php';