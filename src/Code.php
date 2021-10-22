<?php

namespace thinkmanage\tools;

//代码操作类
class Code{
	
	/**
	 * 解析代码
	 * 
	 * @param string $str 字符串
	 * @param array $param 参数
	 * @return string
	 */
	public static function parse(string $str,array $param){
		if($str == ''){
			return $str;
		}
		return \think\facade\View::display($str,$param);
	}
	
	/**
	 * 执行代码
	 * 
	 * @param string $str 字符串
	 * @param array $param 参数
	 * @return mixed
	 */
	public static function run(string $str,array $param){
		$str = self::parse($str,$param);
		return eval('return '.$str.';');
	}
	
}