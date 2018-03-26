<?php
/**
 * @Author: LuckPHP 
 * @Explain: Autoload
 */
function __autoload($className)
{
	$name = strstr($className, '\\', true);
	$className = str_replace('\\', '/', $className);
	if(in_array($name,array('Luck', 'Tool')) || is_dir(LUCK_LIBS_PATH.$name) && !empty($name)){ 
        $className = substr($className ,  5);
        $path  =  LUCK_LIBS_PATH;
		$fileName = $path.$className.EXT;	
	} elseif (substr($className, -10) == 'Controller') {
		$fileName = APP_HOME_CONTROLLER_PATH.$className.EXT;
	} elseif (substr($className, -5) == 'Model') {
		$fileName = APP_HOME_MODEL_PATH.$className.EXT;
	} elseif(substr($className, -5) == 'Check') {
		$fileName = APP_HOME_CHECK_PATH.$className.EXT;
	}
	if(file_exists($fileName)) {
		require $fileName;
	} else {
		exit('LuckPHP_ERROR：自动加载错误，未找到'.$className.'');
	}
}