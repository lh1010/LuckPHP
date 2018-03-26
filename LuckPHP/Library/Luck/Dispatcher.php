<?php
/**
 * @Author: LuckPHP 
 * @Explain: 内置Dispatcher类 完成URL解析、路由和调度
 */
namespace Luck\Luck;
class Dispatcher
{
	static private $obj = null;
	static private $instance;
	
	static public function getInstance()
	{
		if(!(self::$instance instanceof self)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	static public function dispatcher()
	{
		$X = '';
		$PATH_INFO = '';
		if(!empty($_SERVER['QUERY_STRING'])) {
			$PATH_INFO = $_SERVER['QUERY_STRING'];
			$X = 1;
		}
		if(!empty($_SERVER['PATH_INFO'])) {
			$PATH_INFO = $_SERVER['PATH_INFO'];
			$X = 2;
		}
		$depr = '/';
		$pattern = '/(\&)/i'; //替换&
		preg_match($pattern,$PATH_INFO) ? $PATH_INFO = preg_replace($pattern, $depr, $PATH_INFO) : NULL;
		$pattern = '/(\=)/i'; //替换=
		preg_match($pattern,$PATH_INFO) ? $PATH_INFO = preg_replace($pattern, $depr, $PATH_INFO) : NULL;
		$pattern = '/(\_)/i'; //替换_
		preg_match($pattern,$PATH_INFO) ? $PATH_INFO = preg_replace($pattern, $depr, $PATH_INFO) : NULL;
		$pattern = '/(\-)/i'; //替换-
		preg_match($pattern,$PATH_INFO) ? $PATH_INFO = preg_replace($pattern, $depr, $PATH_INFO) : NULL;
		$URL_SUFFIX = URL_SUFFIX; //URL后缀
		empty($URL_SUFFIX) ? $pattern = '//' : $pattern = '/('.$URL_SUFFIX.')/';
		preg_match($pattern,$PATH_INFO) ? $PATH_INFO = preg_replace($pattern, '', $PATH_INFO) : NULL;
		define('__INFO__',trim($PATH_INFO,'/'),TRUE);
		$paths = explode($depr,__INFO__);
		empty($PATH_INFO) ? define('__A__','Index')  : define('__A__',array_shift($paths));
		$_M = array_shift($paths);
		empty($_M) ? define('__M__','index') : define('__M__',$_M);
		//解析剩余url
		$var  =  array();
		preg_replace_callback('/(\w+)\/([^\/]+)/', function($match) use(&$var){$var[$match[1]]=strip_tags($match[2]);}, implode('/',$paths)); 
		$_GET   =  array_merge($var,$_GET);
		if($X == 1) {
			unset($_GET[$PATH_INFO]);
		}
		$Action = self::setA(__A__);
		self::setM(__M__,$Action);
		//保证$_REQUEST正常取值
        $_REQUEST = array_merge($_POST,$_GET);
	}

	static public function setA($a)
	{
		empty($a) ? $a = 'Index' : NULL;
		if(!file_exists(APP_HOME_PATH.'Controller/'.ucfirst($a).'Controller'.EXT)) $a = 'Index';
		eval('self::$obj = new '.ucfirst($a).'Controller();');
		return self::$obj;
	}

	static public function setM($m,$Action)
	{
		method_exists($Action, $m) ? $Action->$m() : $Action->index();
	}

}