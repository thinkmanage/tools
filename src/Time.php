<?php

namespace thinkmanage\tools;

use think\facade\Config;

//时间操作类
class Time{
	/*
	year
	month
	day
	hour
	minute
	seconds
	*/
	//一个指定时间（时间戳）的(年/月)第一天 00:00:00
	public static function firstDay(string $type='M',$time=null,$fmt=null,$offset=''){
		if($time == null){
			$time = time();
		}
		//非时间戳时间
		if(!is_int($time)){
			$time = strtotime($time);
		}
		if($type == 'M'){
			$type = 'Y-m-01 00:00:00';
		}else{
			$type = 'Y-01-01 00:00:00';
		}
		if($offset){
			$offset = ' '.$offset;
		}
		$time = strtotime(date($type, $time).$offset);
		if($fmt){
			return date($fmt, $time);
		}else{
			return $time;
		}
	}
	
	//一个指定时间（时间戳）的(年/月)最后一天 23:59:59
	public static function lastDay(string $type='M',$time=null,$fmt=null,$offset=''){
		if($time == null){
			$time = time();
		}
		//非时间戳时间
		if(!is_int($time)){
			$time = strtotime($time);
		}
		if($type == 'M'){
			$type = 'month';
		}else{
			$type = 'year';
		}
		if($offset){
			$offset = ' '.$offset;
		}
		$time = strtotime(
			date('Y-m-d 23:59:59',
				strtotime(
					date('Y-'.($type=='year'?'01':'m').'-01',$time).' +1 '.$type.' -1 day'
				)
			).$offset
		);
		if($fmt){
			return date($fmt, $time);
		}else{
			return $time;
		}
	}
	
	//一个时间（时间戳）的当日第一秒
	public static function firstSeconds($time=null,$fmt=null){
		if($time == null){
			$time = time();
		}
		//非时间戳时间
		if(!is_int($time)){
			$time = strtotime($time);
		}
		$time = strtotime(date('Y-m-d 00:00:00',$time));
		if($fmt){
			return date($fmt, $time);
		}else{
			return $time;
		}
	}
	
	//一个时间（时间戳）的当日最后一秒
	public static function lastSeconds($time=null,$fmt=null){
		if($time == null){
			$time = time();
		}
		//非时间戳时间
		if(!is_int($time)){
			$time = strtotime($time);
		}
		$time = strtotime(date('Y-m-d 23:59:59',$time));
		if($fmt){
			return date($fmt, $time);
		}else{
			return $time;
		}
	}
	
	//偏移时间
	public static function offsetTime(string $offset='',$time=null,$fmt=null){
		if($time == null){
			$time = time();
		}
		//非时间戳时间
		if(!is_int($time)){
			$time = strtotime($time);
		}
		if($offset){
			$offset = ' '.$offset;
		}
		$time = strtotime(date('Y-m-d H:i:s',$time).$offset);
		if($fmt){
			return date($fmt, $time);
		}else{
			return $time;
		}
	}
}