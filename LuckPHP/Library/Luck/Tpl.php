<?php
/**
 * @Author: LuckPHP
 * @Explain: Tpl
 */
namespace Luck\Luck;
use Luck\Luck\Tpl\Smarty;
use Luck\Tool\Tool;
class Tpl extends Smarty
{
	static private $_instance;
	
	static public function getInstance()
	{
		if (!(self::$_instance instanceof self)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	private function __construct()
	{
		if(!is_writable(ROOT_PATH)) {
			exit('LuckPHP_ERROR：模板目录或编译目录或缓存目录创建不成功！<br/>请手动创建以下目录：'.APP_HOME_VIEW_PATH.', '.APP_TEMPS_TEMP_C_PATH.', '.APP_TEMPS_CACHE_PATH);
		}
		file_exists(APP_TEMPS_PATH) ? NULL : mkdir(APP_TEMPS_PATH);
		file_exists(APP_TEMPS_TEMP_C_PATH) ? NULL : mkdir(APP_TEMPS_TEMP_C_PATH);
		file_exists(APP_TEMPS_CACHE_PATH) ? NULL : mkdir(APP_TEMPS_CACHE_PATH);
		$this->setConfigs();
	}

	private function setConfigs()
	{
		$this->template_dir = APP_HOME_VIEW_PATH;
		$this->compile_dir = APP_TEMPS_TEMP_C_PATH;
		$this->cache_dir = APP_TEMPS_CACHE_PATH;
		$this->caching = TPL_CACHE;
		$this->cache_lifetime = TPL_CACHE_LIFETIME;
		$this->left_delimiter = TPL_LEFT_BOX;
		$this->right_delimiter = TPL_RIGHT_BOX;
	}
	
	/**
     * success方法
     * @param $info 成功提示信息
     * @param $url 跳转的URL
     * 单个值，不提示信息，直接跳转
     * @return void
     */
	public function success($info = '', $url = '')
	{
		if($info != '' && $url != '') {
			$this->assign('message', $info);
			$this->assign('url', $url);
			$this->display(LUCK_TPL_COMTEMP.'success.html');
		} else {
			header('Location:'.$info);
		}
		exit();
	}
	
	/**
     * error方法
     * @param $info 错误提示信息
     * @param $url 跳转的URL
     * @return void
     */
	public function error($info = '', $url = '')
	{
		empty($url) ? $url = Tool::getPrevPage() : $url = $url;
		empty($info) ? $info = 'error' : $info = $info;
		$this->assign('message', $info);
		$this->assign('prev',$url);
		$this->display(LUCK_TPL_COMTEMP.'error.html');
		exit();
	}

	/**
     * Ajax方式返回数据到客户端
     * @param $data 要返回的数据
     * @param $status 要返回的status 脚本处理是否成功
     * @return void
     */
    public function ajaxReturn($status, $data, $type = '')
    {
        if(empty($type)){
            $type  = 'JSON';
        }
        switch (strtoupper($type)){
            case 'JSON' :
                //返回JSON数据格式到客户端 包含状态信息
                //header('Content-Type:application/json; charset=utf-8');
                $data = json_encode(array("status"=>$status, "info"=>$data));
                break;
        }
        exit($data);
    }
	
}
 
 
 