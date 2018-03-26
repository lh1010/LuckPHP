<?php
/**
 * 验证类
 */
namespace Luck\Tool;
class Validata {
	
	/**
	 * 验证数据是否为空
	 */
	static public function isEmpty($data) {
		return empty($data) ? true : false;
	}
	
	/**
	 * 验证数据是否相等一致
	 */
	static public function ifEqual($str,$otherstr) {
		if ($str == $otherstr) return true ;
		return false;
	}

	/**
	 * 长度是否合法
	 * @param 需要比较的数据  $data
	 * @param 长度   $length
	 * @param 大小 $flag
	 * @return boolean
	 */
	static public function checkLength($data, $length, $flag) {
		if ($flag == '<') {
			if (mb_strlen(trim($data),'utf-8') < $length) return true;
			return false;
		} elseif ($flag == '>') {
			if (mb_strlen(trim($data),'utf-8') > $length) return true;
			return false;
		} elseif ($flag == '=') {
			if (mb_strlen(trim($data),'utf-8') != $length) return true;
			return false;
		} else {
			Tool::alertBack('EROOR：参数传递的错误，必须是min,max！');
		}
	}
	
	
	/**
	 * 验证用户是否登录
	 */
	static public function checkLogin() {
		if(!empty($_COOKIE['uid']) AND !empty($_COOKIE['user']) AND !empty($_COOKIE['pwd']) AND !empty($_COOKIE['dist'])) {
			return true;
		} else {
			return false;
		}
	}
	
	
}