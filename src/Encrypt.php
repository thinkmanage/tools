<?php

namespace thinkmanage\tools;

use think\facade\Config;

//加解密入口类
class Encrypt{
	/**
	 * 加密函数
	 *
	 * @param string $content 需要加密的字符串
	 * @param string $seed 密钥
	 * @param string $type 加密方式
	 * @return string 返回加密结果
	 */
	public static function encrypt($content,$seed='',$type='def'){
		if(!is_string($content)){
			throw new \Exception('只能对字符串类型进行加解密');
		}
		if (empty($content)) return $content;
		if (empty($seed)){
			$seed = Config::get('tmtools.encrypt_seed','');
		}
		$seed=md5($seed);
		switch ($type){
			case "def" :
				return \thinkmanage\tools\encrypt\Def::encrypt($content,$seed);
				break;
			default :
				throw new \Exception('加解密类型不存在');
				;
		}
	}
	
	/**
	 * 解密函数
	 *
	 * @param string $content 需要解密的字符串
	 * @param string $seed 密钥
	 * @param string $type 解密方式
	 * @return string 返回解密结果
	 */
	public static function decrypt($content,$seed='',$type='def'){
		if(!is_string($content)){
			throw new \Exception('只能对字符串类型进行加解密');
		}
		if (empty($content)) return $content;
		if (empty($seed)){
			$seed = Config::get('tmtools.encrypt_seed','');
		}
		$seed=md5($seed);
		switch ($type){
			case "def" :
				return \thinkmanage\tools\encrypt\Def::decrypt($content,$seed);
				break;
			default :
				throw new \Exception('加解密类型不存在');
				;
		}
	}
	
}