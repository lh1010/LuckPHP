<?php
/**
 * Tool 工具类
 */
namespace Luck\Tool;
class Tool {		
	/**
	 * 获取当前完整URL
	 */
	static public function getUrl() {
		return 'http://'.$_SERVER['HTTP_HOST'].'?'.$_SERVER['QUERY_STRING'];
	}
	
	/**
	 * 转换时间格式
	 * @param 数据 $data(处理数据)
	 * @param 时间格式$format("Y-m-d H:i:s")
	 * @param 处理对象$object(如果不存在，直接转换$data格式)
	 */
	static public function setDate($data,$format,$object = null) {
		if(!empty($object)) {
			foreach ($object as $key=>$value) {
				if(!empty($value["$data"])) {
					$object[$key][$data] = date($format,$value["$data"]);
				}
			}
			return $object;
		} else {
			return date($format,$data);
		}
	}
	
	/**
	 * 隐藏手机号中间四位
	 * @param 处理数据$data
	 * @param 处理对象$object(如果不存在，直接返还$data处理结果)
	 */
	static public function setPhone($data,$object = null) {
		if(!empty($object)) {
			foreach ($object as $key=>$value) {
				if(!empty($value[$data])) {
					$object[$key][$data] = substr_replace($value[$data],'****',3,4);
				}
			}
			return $object;
		} else {
			return substr_replace($data,'****',3,4);
		}
	}
	
	/**
	 * 得到上一页
	 */
	static public function getPrevPage() {
		return empty($_SERVER["HTTP_REFERER"]) ? '' : $_SERVER["HTTP_REFERER"];
	}
	
	/**
	 * 获取客户端IP
	 */
	static public function getIp() {
		if (isset($_SERVER))
		{
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
				$realip = $_SERVER['HTTP_CLIENT_IP'];
			} else {
				$realip = $_SERVER['REMOTE_ADDR'];
			}
		} else {
			if (getenv("HTTP_X_FORWARDED_FOR")) {
				$realip = getenv( "HTTP_X_FORWARDED_FOR");
			} elseif (getenv("HTTP_CLIENT_IP")) {
				$realip = getenv("HTTP_CLIENT_IP");
			} else {
				$realip = getenv("REMOTE_ADDR");
			}
		}
		return $realip;
	}
	
	
	/**
	 * 弹窗返回
	 */
	static public function alertBack($_info) {
		echo "<script type='text/javascript'>alert('$_info');history.back();</script>";
		exit();
	}
	
	/**
	 * 弹窗关闭
	 */
	static public function alertClose($_info) {
		echo "<script type='text/javascript'>alert('$_info');close();</script>";
		exit();
	}
	
	/**
	 * 字符串截取
	 * @param 对象 $object(处理对象或字符串)
	 * @param 截取长度 $length
	 * @param 字符集 $encoding
	 * @param 键值 $field($object为数组时存在时,重新组装s.$field)
	 */
	 static public function subStr(&$object,$length,$encoding,$field=null) {
		 if($object) {
		 	if(is_array($object)) {
		 		foreach ($object as $key=>$value) {
		 			if (mb_strlen($value[$field],$encoding) > $length) {
		 				$object[$key]['s'.$field] = mb_substr($value[$field],0,$length,$encoding).'...';
		 			} else {
		 				$object[$key]['s'.$field] = $value[$field];
		 			}
		 		}
		 	} else {
		 		if (mb_strlen($object,$encoding) > $length) {
		 			return mb_substr($object,0,$length,$encoding).'...';
		 		} else {
		 			return $object;
		 		}
		 	}
		 } 		
	 }
	
	
	 
	 
	 /**
	  * base64_encode加密
	  */
	 static function setBaseEn($data) {
	 	return base64_encode($data);
	 }
	 
	 /**
	  * base64_decode解密
	  */
	 static function setBaseDe($data) {
	 	return base64_decode($data);
	 }
	
	

	
	
	
	
	
	
	
	
	
	
	
	/**
	 * 缩略图显示为空显示默认
	 */
	static public function setThumbnail(&$array,$field = null) {
		if(!empty($field)) {
			foreach ($array as $key=>$value) {
				if(empty($value[$field])) {
					$array[$key][$field] = '/public/images/nopic.png';
				}
			}
		} else {
			if(empty($array)) {
				$array = '/public/images/nopic.png';
			}
			return $array;
		}
	}
	
	
	/**
	 * 弹窗赋值关闭(上传专用)
	 */
	static public function alertOpenerClose($_info,$_path) {
		echo "<script type='text/javascript'>alert('$_info');</script>";
		echo "<script type='text/javascript'>opener.document.add.thumbnail.value='$_path';</script>";
		echo "<script type='text/javascript'>opener.document.add.pic.style.display='block';</script>";
				echo "<script type='text/javascript'>opener.document.add.pic.src='$_path';</script>";
				echo "<script type='text/javascript'>window.close();</script>";
						exit();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}