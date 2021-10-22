<?php

namespace thinkmanage\tools;

//数组操作类
class Arr{
	
	/**
	 * 列表转树
	 *
	 * @param string $list 列表
	 * @param string $opt 配置
	 * @return array 返回转化的树
	 */
	public static function listToTree(array $list,array $opt=[]){
		$opt = array_merge([
			//主键下标名称
			'primary_key'=>'id',
			//父键下标名称
			'parent_key'=>'p_id',
			//子节点下标名称
			'child_key'=>'children',
			//根节点ID
			'root_id'=>0,
			//leaf属性是否显示
			'leaf_show'=>true,
		],$opt);
		$list = array_column($list, null, $opt['primary_key']);
		$tree = [];
		foreach($list as $item){
			//判断leaf属性是否显示
			if($opt['leaf_show']){
				//判断当前节点是否有 子节点 且子节点数据大于0
				if(isset($list[$item[$opt['primary_key']]][$opt['child_key']]) && count($list[$item[$opt['primary_key']]][$opt['child_key']])>0){
					$list[$item[$opt['primary_key']]]['leaf'] = false;
				}else{
					$list[$item[$opt['primary_key']]]['leaf'] = true;
				}
			}
			if(isset($list[$item[$opt['parent_key']]])){
				if($opt['leaf_show']){
					$list[$item[$opt['parent_key']]]['leaf'] = false;
				}
				if(!isset($list[$item[$opt['parent_key']]][$opt['child_key']])){
					$list[$item[$opt['parent_key']]][$opt['child_key']] = [];
				}
				$list[$item[$opt['parent_key']]][$opt['child_key']][] = &$list[$item[$opt['primary_key']]];
			}else{
				$tree[] = &$list[$item[$opt['primary_key']]];
			}
			continue;
		}
		return $tree;
	}
	
	/**
	 * 树列表转
	 *
	 * @param string $tree 列表
	 * @param string $opt 配置
	 * @return array 返回转化的树
	 */
	public static function treeToList(array $tree,array $opt=[]){
		$list = [];
		foreach($tree as $v) {
			if(isset($v[$opt['child_key']])){
				$temp = $v[$opt['child_key']];
				unset($v[$opt['child_key']]);
				$list[] = $v;
				$list = array_merge($list,self::treeToList($temp, $opt));
			}else{
				$list[] = $v;
			}
		}
		return $list;
	}
	
}