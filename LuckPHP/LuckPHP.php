<?php
/**
 * @Author: LuckPHP
 * @Explain: Main
 */

const THINK_VERSION = '1.0';
define('ROOT_PATH', substr(dirname(__FILE__),0,-7));
define('LUCK_PATH', ROOT_PATH.'LuckPHP/');
define('LUCK_LIBS_PATH', ROOT_PATH.'LuckPHP/Library/');
const EXT = '.php';
require LUCK_LIBS_PATH.'Luck/Run.inc.php';
