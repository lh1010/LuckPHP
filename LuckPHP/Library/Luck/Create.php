<?php
/**
 * @Author: LuckPHP 
 * @Explain: 创建基础文件
 */
!is_writable(ROOT_PATH) ? exit(ROOT_PATH.' 权限状态：不可写入！') : NULL;
//创建使用文件夹
file_exists(APP_PATH) ? NULL : mkdir(APP_PATH);
file_exists(APP_HOME_PATH) ? NULL : mkdir(APP_HOME_PATH);
file_exists(APP_CONFIG_PATH) ? NULL : mkdir(APP_CONFIG_PATH);
file_exists(APP_TEMPS_PATH) ? NULL : mkdir(APP_TEMPS_PATH);
file_exists(APP_HOME_CONTROLLER_PATH) ? NULL : mkdir(APP_HOME_CONTROLLER_PATH);
file_exists(APP_HOME_MODEL_PATH) ? NULL : mkdir(APP_HOME_MODEL_PATH);
file_exists(APP_HOME_CHECK_PATH) ? NULL : mkdir(APP_HOME_CHECK_PATH);
file_exists(APP_HOME_VIEW_PATH) ? NULL : mkdir(APP_HOME_VIEW_PATH);
file_exists(APP_TEMPS_TEMP_C_PATH) ? NULL : mkdir(APP_TEMPS_TEMP_C_PATH);
file_exists(APP_TEMPS_CACHE_PATH) ? NULL : mkdir(APP_TEMPS_CACHE_PATH);


//创建IndexController.php
$IndexController = APP_HOME_CONTROLLER_PATH.'IndexController.php'; 
$myFile = fopen($IndexController, 'w+'); //打开文件指针，创建文件 
$content = '';
$content .= '<?php'."\n";
$content .= '/**'."\n";
$content .= ' * @Author: LuckPHP'."\n";
$content .= ' * @Explain: Explain'."\n";
$content .= ' */'."\n";
$content .= 'use Luck\Luck\Controller;'."\n";
$content .= 'class IndexController extends Controller'."\n";
$content .= '{'."\n";
$content .= '	public function index()'."\n";
$content .= '   {'."\n";
$content .= '		echo \'<title>欢迎使用 LuckPHP</title><style>*{padding: 0; margin: 0; }body{ background: #fff; font-family: "微软雅黑"; color: #333; font-size: 16px; }.system-message{ padding: 24px 48px; }.system-message h1{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 12px; }.system-message .success{padding-top: 10px; font-size: 16px; color: green}.system-message .jump{padding-top: 10px;font-size: 12px;}.system-message .jump a{color: #333;}</style><div class="system-message"><h1>:)</h1><p class="success">欢迎使用 LuckPHP</p></div>\';'."\n";
$content .= '	}'."\n";
$content .= ''."\n";
$content .= '}'."\n";
$content .= ''."\n";
$content .= '?>'."\n";
fwrite($myFile, $content);
fclose($myFile);

//创建Config.php
$config = APP_CONFIG_PATH.'Config.php';
$myFile = fopen($config, 'w+'); //打开文件指针，创建文件 
$content = '';
$content .= '<?php'."\n";
$content .= '/**'."\n";
$content .= ' * @Author: LuckPHP'."\n";
$content .= ' * @Explain: Configs'."\n";
$content .= ' */'."\n";
$content .= 'return array('."\n";
$content .= ''."\n";
$content .= ')';
$content .= ''."\n";
$content .= '?>';
fwrite($myFile, $content);
fclose($myFile);

//创建各个目录index.html文件
$index = APP_CONFIG_PATH.'index.html';
$myFile = fopen($index, 'w+'); //打开文件指针，创建文件
fclose($myFile); 
$index = APP_HOME_CONTROLLER_PATH.'index.html';
$myFile = fopen($index, 'w+'); //打开文件指针，创建文件
fclose($myFile); 
$index = APP_HOME_MODEL_PATH.'index.html';
$myFile = fopen($index, 'w+'); //打开文件指针，创建文件
fclose($myFile); 
$index = APP_HOME_CHECK_PATH.'index.html';
$myFile = fopen($index, 'w+'); //打开文件指针，创建文件
fclose($myFile); 
$index = APP_HOME_VIEW_PATH.'index.html';
$myFile = fopen($index, 'w+'); //打开文件指针，创建文件
fclose($myFile); 
$index = APP_TEMPS_TEMP_C_PATH.'index.html';
$myFile = fopen($index, 'w+'); //打开文件指针，创建文件
fclose($myFile); 
$index = APP_TEMPS_CACHE_PATH.'index.html';
$myFile = fopen($index, 'w+'); //打开文件指针，创建文件
fclose($myFile);  
 
?>