<?php

namespace thinkmanage\tools;

use think\facade\Config;

//字符串操作类
class Str{
	
	/**
	 * 字符串编码转换
	 * 
	 * @param string $str 字符串
	 * @param string $oldCode 原始编码
	 * @param string $newCode 输出编码
	 * @return string
	 */
	public static function changCode(string $str,string $oldCode,string $newCode){
		if($oldCode == $newCode){
			return $str;
		}
		return iconv($oldCode,$newCode."//IGNORE",$str);
	}
	
}