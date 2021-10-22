<?php
declare (strict_types = 1);

/**
 * 加密函数
 *
 * @param string $content 需要加密的字符串
 * @param string $seed 密钥
 * @param string $type 加密方式
 * @return string 返回加密结果
 */
function tmEncrypt($content,$seed='',$type='def'){
	return thinkmanage\tools\Encrypt::encrypt($content,$seed,$type);
}

/**
 * 解密函数
 *
 * @param string $content 需要解密的字符串
 * @param string $seed 密钥
 * @param string $type 解密方式
 * @return string 返回解密结果
 */
function tmDecrypt($content,$seed='',$type='def'){
	return thinkmanage\tools\Encrypt::decrypt($content,$seed,$type);
}

/**
 * 列表转树
 *
 * @param string $list 列表
 * @param string $opt 配置
 * @return array 返回转化的树
 */
function tmListToTree($list,$opt=[]){
	return thinkmanage\tools\Arr::listToTree($list,$opt);
}

/**
 * 树列表转
 *
 * @param string $tree 列表
 * @param string $opt 配置
 * @return array 返回转化的树
 */
function tmTreeToList($tree,$opt=[]){
	$opt = array_merge([
		//主键下标名称
		'primary_key'=>'id',
		//子节点下标名称
		'child_key'=>'children',
	],$opt);
	return thinkmanage\tools\Arr::treeToList($tree,$opt);
}
