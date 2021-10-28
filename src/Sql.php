<?php

namespace thinkmanage\tools;

use think\facade\Config;
use think\facade\Db;

//Sql操作类
class Sql{
	
	/**
	 * 执行Sql
	 *
	 * @param string $sql
	 * @param string $currentPre
	 * @param string $originalPre
	 * @return boolean
	 */
	public static function run(string $sql,string $newPre = '',string $oldPre = ''){
		try {
			$sql = self::parse($sql, $newPre, $oldPre);
			foreach($sql as $v){
				if($v !=''){
					Db::execute($v);
				}
			}
		} catch (Exception $e) {
			throw new \think\Exception($e->getMessage());
		}
		return true;
	}
	
	/**
	 * 从文件执行Sql
	 *
	 * @param string $sql
	 * @param string $currentPre
	 * @param string $originalPre
	 * @return boolean		 
	 * @return boolean
	 */
	public static function runByFile(string $path,string $newPre = '',string $oldPre = ''){
		//判断文件是否存在
		if (!is_file($path)){
			throw new \think\Exception('文件不存在');
		}
		try {
			// 获取文件文件的内容
			$sql = file_get_contents($path);
			// 对文件的表前缀替换
			self::run($sql, $newPre, $oldPre);
		} catch (Exception $e) {
			throw new \think\Exception($e->getMessage());
		}
		return true;
	}
	
	/**
	 * 预处理Sql
	 *
	 * @param string $sql
	 * @param string $currentPre
	 * @param string $originalPre
	 * @return string
	 */
	public static function parse( $sql, $newPre = '', $oldPre = 'tm_'){
		if ($newPre == '') {
			$newPre = Config::get('database.connections.main.prefix','tm_');
		}
		// 前缀替换
		if ($oldPre != $newPre) {
			$sql = str_replace($oldPre, $newPre, $sql);
		}
		// 编码替换
		$sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);
		// 防止编码异常引起的问题
		$sql = str_replace("\r", "\n", $sql);
		// 语句存储
		$ret = [];
		// 计数
		$num = 0;
		// 分割sql语句
		$sqlList = explode(";\n", trim($sql));
		// 清空Sql
		unset($sql);
		// 遍历语句
		foreach ($sqlList as $query) {
			$ret[$num] = '';
			$queryList = explode("\n", trim($query));
			$queryList = array_filter($queryList);
			foreach ($queryList as $v) {
				$str = substr($v, 0, 1);
				if ($str != '#' && $str != '-') {
					$ret[$num] .= $query;
				}
			}
			$num ++;
		}
		return $ret;
	}
	
}