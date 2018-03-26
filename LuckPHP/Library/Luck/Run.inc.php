<?php
/**
 * @Author: LuckPHP
 * @Explain: run
 */

session_start();
header("Content-Type:text/html;charset=utf-8");
error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

defined('APP_NAME') OR define('APP_NAME', 'APP');
define('APP_PATH', ROOT_PATH.APP_NAME.'/');
define('APP_HOME_PATH', APP_PATH.'Home/');
define('APP_CONFIG_PATH', APP_PATH.'Config/');
define('APP_TEMPS_PATH', APP_PATH.'Temps/');
define('APP_HOME_CONTROLLER_PATH', APP_HOME_PATH.'Controller/');
define('APP_HOME_MODEL_PATH', APP_HOME_PATH.'Model/');
define('APP_HOME_CHECK_PATH', APP_HOME_PATH.'Check/');
define('APP_HOME_VIEW_PATH', APP_HOME_PATH.'View/');
define('APP_TEMPS_TEMP_C_PATH', APP_TEMPS_PATH.'temp_c/');
define('APP_TEMPS_CACHE_PATH', APP_TEMPS_PATH.'cache/');
//项目配置文件(优先级)
file_exists(APP_CONFIG_PATH.'Config.php') ? $config = require APP_CONFIG_PATH.'Config.php' : NULL; 
if(!empty($config)) {
	foreach ($config as $key => $value) {
		define($key, $value); 
	}
}
//模板输出公共页面
define('LUCK_TPL_COMTEMP', LUCK_LIBS_PATH.'Comtemp/');
//外部可重写配置
defined('TPL_CACHE')  OR define('TPL_CACHE', 0);
defined('TPL_CACHE_LIFETIME')  OR define('TPL_CACHE_LIFETIME', 0);
defined('TPL_LEFT_BOX') OR define('TPL_LEFT_BOX', '{{');
defined('TPL_RIGHT_BOX') OR define('TPL_RIGHT_BOX', '}}');
defined('TPL_SUFFIX') OR define('TPL_SUFFIX', '.html');
defined('URL_SUFFIX') OR define('URL_SUFFIX', '.html');
file_exists(APP_PATH) ? NULL : require LUCK_LIBS_PATH.'Luck/Create.php';
defined('__PUBLIC__') OR define('__PUBLIC__', '/'.APP_NAME.'/Home/View/Public/');

require LUCK_LIBS_PATH.'Luck/Autoload.php';
use Luck\Luck\Dispatcher;
Dispatcher::dispatcher();