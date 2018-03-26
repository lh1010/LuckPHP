<?php
/**
 * 上传类
 * @author LuckPHP
 * 
 */
namespace Luck\Tool;
class Upload {
	private $path = '';  //上传文件存储位置
	private $returnPath = ''; //需返回上传路径
	private $error = array(); //上传错误信息
    private $returnArray = array(); //需返回信息数组

	//上传配置
	private $config = array(
		'maxsize' => 0, //上传文件大小限制 (0-不做限制)
		'exts' => array(), //上传文件后缀
		'uploadPath' => './Uploads/', //默认上传根路径
		'savePath' => '', //外部指定目录
		'saveExt'  => '', //文件保存后缀，空则使用原后缀
	);
	
    /**
     * 整合上传配置
     * @param array $config 上传配置
     * 
     */
	public function __construct($config = array()) {
        $this->config = array_merge($this->config, $config);
	}

	/**
	 * 上传文件
	 * @param $fileName 上传文件name 
	 * 
	 */
	public function upload($fileName = '') {
		if(empty($_FILES[$fileName])){
            $this->error = array("status"=>1.3, "info"=>'未找到上传的文件');
            return false;
        }
        //检查文件类型
        if(!$this->checkExt($_FILES[$fileName]['name'])) { 	
        	return false;
        }
        //检测文件大小
        if(!$this->checkSize($_FILES[$fileName]['size'])) {
        	return false;
        }
        //检测上传error
        if(!$this->checkError($_FILES[$fileName]['error'])) {
        	return false;
        }
        //检测上传目录
        if(!$this->checkPath()){
            return false;
        }
        //生成保存文件名
        $this->setNewName($_FILES[$fileName]['name']);
        //移动上传文件
        if(!$this->moveFile($_FILES[$fileName]['tmp_name'])) {
        	return false;
        }
        //返回
        $this->returnArray['path'] = $this->returnPath;
        $this->returnArray['size'] = round($_FILES[$fileName]['size'] / 1024, 1).'KB';
        return $this->returnArray;
	}

    /**
     * 检查文件类型是否合法
     * @param $fileType 上传文件临时路径 $_FILES['file']['tmp_name']     
     * @return boolean true = 检测通过，false = 检测失败
     */
	private function moveFile($fileTmp) {
		if (is_uploaded_file($fileTmp)) {
			if (!move_uploaded_file($fileTmp, $this->path)) {
                $this->error = array("status"=>1.6, "info"=>'移动上传临时文件失败');
				return false;
			}
		} else {
            $this->error = array("status"=>1.7, "info"=>'上传临时文件不存在');
			return false;
		}
		return true;
	}

    /**
     * 检查文件类型是否合法
     * @param $fileType 上传文件名字 $_FILES['file']['name']     
     * @return boolean true = 检测通过，false = 检测失败
     */
    private function checkExt($fileName) {
    	if(empty($this->config['exts'])) {
    		return true;
    	}
    	$ext = pathinfo($fileName, PATHINFO_EXTENSION);
    	if(!in_array(strtolower($ext), $this->config['exts'])) {
    		$this->error = array("status"=>1.4, "info"=>'上传文件格式不正确');
    		return false;
    	}
    	return true;
    }		

    /**
     * 检测上传文件大小
     * @return boolean true = 检测通过，false = 检测失败
     */
    private function checkSize($fileSize) {
    	if($this->config['maxsize'] == 0) {
    		return true;
    	}
    	if($this->config['maxsize'] * 1024 < $fileSize) {
            $this->error = array("status"=>1.5, "info"=>'上传文件不得超过' . $this->config['maxsize'] . 'KB');
    		return false;
    	}
    	return true;
    }	

    /**
     * 检测上传error
     * @param $fileError 上传文件名字 $_FILES['file']['error']
     * @return $this->path
     */
	private function checkError($fileError) {
		if (!empty($fileError)) {
			switch ($fileError) {
				case 1 :
                    $this->error = array("status"=>0.1, "info"=>'上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值');
					return false;
					break;
				case 2 :
                    $this->error = array("status"=>0.2, "info"=>'上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值');
					return false;
					break;
				case 3 :
                    $this->error = array("status"=>0.3, "info"=>'文件只有部分被上传');
					return false;
					break;
				case 4 :
                    $this->error = array("status"=>0.4, "info"=>'没有文件被上传');
					return false;
					break;
				default:
                    $this->error = array("status"=>0.5, "info"=>'上传未知错误');
					return false;
			}
		}
		return true;
	}    

    /**
     * 检测上传根目录
     * @return boolean true = 检测通过，false = 检测失败
     */
    private function checkPath(){
    	//设置上传主目录
        if(stripos($this->config['uploadPath'], ROOT_PATH) == false){
            $uploadPath = ROOT_PATH.ltrim($this->config['uploadPath'], '.');
        }
        //尝试创建上传主目录
        if (!is_dir($uploadPath) || !is_writeable($uploadPath)) {
			if (!mkdir(iconv("UTF-8", "utf-8", $uploadPath),0777,true)) {
                $this->error = array("status"=>1.1, "info"=>'上传目录创建失败！请尝试手动创建:'.$uploadPath);
           		return false;
			}
		}
		$this->path = $uploadPath . $this->config['savePath'] . date('Ymd').'/';
		$this->returnPath = ltrim($this->config['uploadPath'], '.').$this->config['savePath'] . date('Ymd').'/';
		//尝试创建上传子目录
        if (!is_dir($this->path) || !is_writeable($this->path)) {
			if (!mkdir(iconv("UTF-8", "utf-8", $this->path),0777,true)) {
                $this->error = array("status"=>1.2, "info"=>'上传目录创建失败！请尝试手动创建:'.$this->path);
           		return false;
			}
		}
		return true;
    }
	
    /**
     * 生成保存文件名
     * @param $fileName 上传文件名字 $_FILES['file']['name']
     * @return $this->path
     */
	private function setNewName($fileName) {
		$nameArr = explode('.',$fileName);
		empty($this->config['saveExt']) ? $postfix = $nameArr[count($nameArr)-1] : $postfix = $this->config['saveExt'];
		$newname = date('YmdHis').mt_rand(100,1000).'.'.$postfix;
		$this->path = $this->path . $newname;
		$this->returnPath = $this->returnPath . $newname;
	}

	//返回最后一次上传错误信息
    public function getError(){
        return $this->error;
    }
	
	
}